<?php

namespace DishCheng\ZwyApi\Services;

use DishCheng\ZwyApi\Exceptions\ZwyApiException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * 服务端请求自我游服务端
 * Class ClientRequestService
 * @package App\Http\Service
 */
class ClientRequestService
{
    const ZWY_ERROR_TITLE = '【ZWY ERROR】';
    const TimeOutSecond = 10;


    public $request_config = [];

    /**
     * 向自我游发起post请求，有子节点的post请求
     * @param $path
     * @param array $data
     * @param string $data_root_params
     * @param string $numeric_node
     * @return array
     */
    public function zwy_post_request($path, $data = [], $data_root_params = 'param', $numeric_node = 'detail')
    {
        $err_header = self::ZWY_ERROR_TITLE;
        try {
            $host = config('zwy_api.domain');
            if (blank($this->request_config)) {
                $request_data = [
                    'custId' => config('zwy_api.custId'),
                    'apikey' => config('zwy_api.apikey'),
                ];
            } else {
                $request_data = $this->request_config;
                if (Arr::has($request_data, 'host')) {
                    $host = $request_data['host'];
                    unset($request_data['host']);
                } else {
                    return [
                        'status' => false,
                        'msg' => 'host参数获取失败'
                    ];
                }
            }
            $url = $host . $path;
            $request_data_xml = self::xml_encode($data, $numeric_node);
            if (!blank($data_root_params)) {
                //如果第三个参数不为空
                $request_data[$data_root_params] = $request_data_xml;
            } else {
                //酒店支付不需要子节点
                $request_data = array_merge($request_data, $data);
            }
            $res = $this->post_request($url, $request_data, 'form_params');
            return self::handle_zwy_request($url, $request_data, $res, $err_header);
        } catch (\GuzzleHttp\Exception\GuzzleException $exception) {
            Log::emergency($exception->getMessage());
            return [
                'status' => false,
                'msg' => self::ZWY_ERROR_TITLE . 'NETWORK ERROR'
            ];
        }
    }


    /**
     * 向自由行发起get请求
     * @param $path
     * @param array $data
     * @param array $config
     * @param string $host
     * @return array
     */
    public function zwy_get_request($path, $data = [])
    {
        $err_header = self::ZWY_ERROR_TITLE;

        $host = config('zwy_api.domain');
        if (blank($this->request_config)) {
            $request_data = [
                'custId' => config('zwy_api.custId'),
                'apikey' => config('zwy_api.apikey'),
            ];
        } else {
            $request_data = $this->request_config;
            if (Arr::has($request_data, 'host')) {
                $host = $request_data['host'];
                unset($request_data['host']);
            } else {
                return [
                    'status' => false,
                    'msg' => 'host参数获取失败'
                ];
            }
        }
        if (!blank($data)) {
            $request_data = array_merge($request_data, $data);
        }
        try {
            $url = $host . $path;
            $res = $this->get_request($url, $request_data);
            return self::handle_zwy_request($url, $request_data, $res, $err_header);
        } catch (\GuzzleHttp\Exception\GuzzleException $exception) {
            $msg = $err_header . 'NETWORK ERROR';
            Log::emergency($msg . ':' . $exception->getMessage());
            return [
                'status' => false,
                'msg' => $msg
            ];
        }
    }


    /**
     * 处理自我游返回信息
     * @param $path '路径
     * @param $data '请求参数
     * @param \Psr\Http\Message\ResponseInterface $res '返回的content文本
     * @param $err_header 'header
     * @return array
     */
    public function handle_zwy_request($path, $data, \Psr\Http\Message\ResponseInterface $res, $err_header)
    {
        if ($res->getStatusCode() != 200) {
            $msg = $err_header . 'STATUS CODE WRONG：' . $res->getStatusCode();
            Log::emergency($msg, [
                'path' => $path,
                'request_data' => $data,
            ]);
            return [
                'status' => false,
                'msg' => $msg
            ];
        }

        $content = $res->getBody()->getContents();
        if ($this->xml_parser($content)) {
            $c = simplexml_load_string($content, 'SimpleXMLElement', LIBXML_NOCDATA);
            $resArr = xmlObjToArray($c);
            // 公园、度假使用status标示请求是否成功
            // 酒店使用success标示请求是否成功
            if (
                (Arr::has($resArr, 'status') && $resArr['status'] == '1') ||
                (Arr::has($resArr, 'success') && $resArr['success'] == '1')
            ) {
                return [
                    'status' => true,
                    'data' => $resArr,
                ];
            } else {
                //请求出错
                $msg = $err_header . 'STATUS WRONG';
//                Log::error($msg, [
//                    'path' => $path,
//                    'data' => $data,
//                    'res' => $resArr,
//                ]);
                if (Str::contains($resArr['msg'], '程序错误')) {
                    /**
                     * 这样做的原因是自我游那边返回的错误在包含程序错误的时候会把请求出去的参数带上去，
                     * 我又是直接抛回前端有点蛋疼，下面拿的错误有时候会在下单出现（极少数情况下）
                     *
                     * 程序错误，接收数据为：<?xml version="1.0" encoding="utf-8"?><root><travel_date>2019-09-27</travel_date><info_id>25525032</info_id><cust_id>2976618</cust_id><num>1</num><link_man>cai</link_man><link_phone>13627227858</link_phone></root>↵
                     * 抛出异常为：TransactionException in DbTransactionManager.java:commit()
                     */
                    return [
                        'status' => false,
                        'msg' => $msg . ':zwy程序错误' ?? '',
                    ];
                } else {
                    return [
                        'status' => false,
                        'msg' => $msg . ':' . $resArr['msg'] ?? '',
                    ];
                }

            }
        } else {
            //返回的不是xml，出错
            $msg = $err_header . 'NOT RETURN XML';
            Log::emergency($msg, [
                'path' => $path,
                'content' => $content,
                'request_data' => $data
            ]);
            return [
                'status' => false,
                'msg' => $msg
            ];
        }
    }


    /**
     * 发起post请求
     * @param $url
     * @param array $data
     * @param string $type
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function post_request($url, $data = [], $type = 'json')
    {
        $client = new \GuzzleHttp\Client();
        switch ($type) {
            case 'json':
                $res = $client->request('post', $url,
                    [
                        'verify' => false,
                        'headers' => [
                            'content-type' => 'text/html; charset=UTF-8',
                        ],
                        'json' => $data,
                        'connect_timeout' => self::TimeOutSecond,
                    ]);
                break;
            case 'form_params':
                $res = $client->request('post', $url,
                    [
                        'verify' => false,
                        'form_params' => $data,
                        'connect_timeout' => self::TimeOutSecond,
                    ]);
                break;
            case 'raw':
                $res = $client->request('post', $url,
                    [
                        'verify' => false,
                        'headers' => [
                            'content-type' => 'application/json',
                        ],
                        'body' => $data,
                        'connect_timeout' => self::TimeOutSecond,
                    ]);
                break;
            default:
                throw new \Exception('请求类型错误');
                break;
        }

        return $res;
    }


    /**
     * 发起get请求
     * @param $url
     * @param array $data
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get_request($url, $data = [])
    {
        $client = new \GuzzleHttp\Client();

        $res = $client->request('get', $url,
            [
                'verify' => false,
                'headers' => [
                    'content-type' => 'text/html; charset=UTF-8',
                ],
                'query' => $data,
                'connect_timeout' => self::TimeOutSecond,
            ]);
        return $res;
    }

    /**
     * 验证字符串是否是xml数据
     * @param $str
     * @return bool
     */
    public function xml_parser(string $str)
    {
        $xml_parser = xml_parser_create();
        if (!xml_parse($xml_parser, $str, true)) {
            xml_parser_free($xml_parser);
            return false;
        } else {
            return true;
        }
    }


    /**
     * @param mixed $data 数据
     * @param string $root 根节点名
     * @param string $encoding 数据编码
     * @param string $numeric_node 子节点名称
     *
     * @param string $root
     * @param string $encoding
     * @return string
     */
    static function xml_encode($data, $numeric_node = 'detail', $root = 'root', $encoding = 'utf-8')
    {
        $xml = '<?xml version="1.0" encoding="' . $encoding . '"?>';
        $xml .= '<' . $root . '>';
        $xml .= self::data_to_xml($data);
        $xml .= '</' . $root . '>';
        return $xml;
    }


    /**
     * 数据XML编码
     * @param $data
     * @param string $numeric_node
     * @return string
     */
    static function data_to_xml($data, $numeric_node = 'detail')
    {
        $xml = '';
        foreach ($data as $key => $val) {
            if (is_numeric($key)) {
                $xml .= "<$numeric_node>";
                $xml .= (is_array($val) || is_object($val)) ? self::data_to_xml($val) : $val;
                list($key,) = explode(' ', $key);
                $xml .= "</$numeric_node>";
            } else {
                $xml .= "<$key>";
                $xml .= (is_array($val) || is_object($val)) ? self::data_to_xml($val) : $val;
                list($key,) = explode(' ', $key);
                $xml .= "</$key>";
            }

        }
        return $xml;
    }
}
