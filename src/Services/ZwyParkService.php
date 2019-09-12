<?php

namespace DishCheng\ZwyApi\Services;

//use DishCheng\ZwyApi\Services\ClientRequestService;
use DishCheng\ZwyApi\Traits\SinglePattern;

/**
 * 参照-分销系统api接口说明-门票&套票&打包产品分册-正式版带书签(20181023).pdf
 * Class ZwyParkService
 * @package App\Http\Service\Zwy
 */
class ZwyParkService extends ClientRequestService
{
    use SinglePattern;
    //存放实例对象
    protected static $_instance = [];

    public $request_config = [];

    /**
     * 第 2 章 产品列表接口
     * @param array $searchData
     * [
     *      'keyWrod'=>'关键字',
     *      'treedId'=>'产品类型 0 单一门票 1 套餐或联票',
     *      'cityName'=>'地区名称 支持按照省份或者城市名称 搜索，同时搜索多个地区使 用“,”分隔',
     *      'isPay'=>'支付方式 0 表示线下支付 1 表示在线支付',
     *      'isConfirm'=>'确认方式 0 表示系统自动确认订单 1 表示人工确认订单',
     *      'isExpress'=>'配送方式 int 不需要配送 需要配送',
     *      'viewId'=>'景点账户 String 分销系统系统提供的景点账户(获取参见景区接口)',
     *      'tagIds'=>'景点标签 分销系统系统提供的景点标 签编号(获取参见景区标签接 口) 支持多个标签查询用“,”分 隔',
     *      'location'=>'位置信息 格式: (经度，纬度，方圆距离) 默认距离 2 公里';ps:搜索某个位置周边 5 公里的产品 ?location= 108.29536000, 22.73270000,5000
     *      'pageNum'=>'一页显示多少条 默认 50 条，最多可设置 100 条',
     *      'pageNo'=>'默认第一页',
     *      'orderBy'=>'排序方式 0 按价格 1 按折扣 2 按销量 3 按推荐值 4 上架时间',
     *      'isPackage'=>'打包产品 是否打包产品，0 单独产品， 1 打包产品',
     * ]
     * @return array
     */
    public function getProductListInfo(array $searchData = [])
    {
        $path = 'api/list.jsp';
        return $this->zwy_get_request($path, $searchData);
    }


    /**
     * 第 3 章 产品详情接口
     * @param $productNo '产品号'
     * @return array
     */
    public function getProductDetailInfo($productNo)
    {
        $path = 'api/detail.jsp';
        $data = ['productNo' => $productNo];
        return $this->zwy_get_request($path, $data);
    }


    /**
     * 第 4 章 批量获取产品状态
     * @param $productNo '支持多个产品号查询，“，”分割
     * @return array
     */
    public function getProductStateInfo($productNo)
    {
        $path = 'api/getProductState.jsp';
        $data = ['productNo' => $productNo];
        return $this->zwy_get_request($path, $data);
    }


    /**
     * 第 5 章 产品价格日历接口
     * @param $productNo '产品编号
     * @param $travelDate '开始日期 yyyy-MM-dd
     * @param string $endTravelDate '结束日期 yyyy-MM-dd
     * @return array
     */
    public function getProductPriceInfo($productNo, $travelDate, $endTravelDate = '')
    {
        $path = 'api/price.jsp';
        $data = ['productNo' => $productNo, 'travelDate' => $travelDate];
        if (!blank($endTravelDate)) {
            $data = array_merge($data, ['endTravelDate' => $endTravelDate]);
        }
        return $this->zwy_get_request($path, $data);
    }


    /**
     * 第 6 章 订单保存接口
     * @return array
     * @example
     * 单独产品：
     * $sss = $service->createOrder([
     * 'travel_date' => '2019-06-01',
     * //            'end_travel_date' => 'sss',
     * //            'arrived_time'=>'sss',
     * 'info_id' => '8012459',
     * 'cust_id' => config('third_service.zwy.custId'),
     * //            'get_type' => '取票方式， 0 免费，1 快递费'
     * //            'order_source_id' => '对接方系统的订单流水号(唯一)'
     * //            'order_memo' => '对接方系统的订单流水号(唯一)'
     * 'num' => '1',
     * //            'user_id' => '对接方系统的订单流水号(唯一)'
     * 'link_man' => '蔡xx',
     * 'link_phone' => '13627227858',
     * 'link_email' => '33333333@qq.com',
     * ]);
     *
     *
     * 套餐产品：
     * $service = ZwyParkService::getInstance();
     *
     * $sss = $service->createOrder([
     * 'travel_date' => '2019-06-14 ',
     * 'end_travel_date' => '2019-06-14',
     * //            'arrived_time'=>'sss',
     * 'info_id' => '24743942',
     * 'cust_id' => config('third_service.zwy.custId'),
     * //            'get_type' => '取票方式， 0 免费，1 快递费'
     * //            'order_source_id' => '对接方系统的订单流水号(唯一)'
     * //            'order_memo' => '对接方系统的订单流水号(唯一)'
     * 'num' => '1',
     * //            'user_id' => '对接方系统的订单流水号(唯一)'
     * 'link_man' => '蔡xx',
     * 'link_phone' => '13627227858',
     * 'link_email' => '33333333@qq.com',
     * 'link_credit_no' => '421003192712121010',
     * //            'prods' => [
     * //                'prod' => [
     * //                    ['prod_id' => 's', 'prod_date' => '2018-02-1'],
     * //                    ['prod_id' => 's', 'prod_date' => '2018-02-1'],
     * //                ]
     * //            ]
     * ]);
     *
     * 返回：array:2 [▼
     * "status" => true
     * "data" => array:8 [▼
     * "status" => "1"
     * "msg" => "录入订单成功！本订单需要在线支付后才能有效！"
     * "error_state" => "10000"
     * "error_msg" => "录入订单成功！本订单需要在线支付后才能有效！"
     * "order_id" => "105133310"
     * "order_money" => "2.4"
     * "mem_order_money" => "1.3"
     * "order_state" => "1"
     * ]
     * ]
     */
    public function createOrder($data)
    {
        $path = 'api/order.jsp';
        return $this->zwy_post_request($path, $data);
    }


    /**
     * 第 7 章 订单详细接口
     * @param $orderId '订单号
     * @param string $orderSourceId '分销商订单号
     * @return array
     */
    public function getOrderDetailInfo($orderId, $orderSourceId = '')
    {
        $path = 'api/orderDetail.jsp';
        $data = ['orderId' => $orderId];
        if (!blank($orderSourceId)) {
            $data = array_merge($data, ['orderSourceId' => $orderSourceId]);
        }
        return $this->zwy_get_request($path, $data);
    }


    /**
     * 第 8 章 订单列表接口
     * @param array $searchData
     * [
     *     'keyWrod'=>'关键字 订单号、游客姓名、手机号码、 订单备注、产品名称',
     *     'userId'=>'分销渠道下单时传递的用户身份编码',
     *     'startDate'=>'下单开始日期 如果不输入为不限制时间 格式:2011-01-01',
     *     'endDate'=>'下单结束日期 如果不输入为不限制时间 格式:2011-01-01',
     *     'travelDate'=>'游玩日志 如果不输入为不限制时间 格式:2011-01-01',
     *     'isPay'=>'支付方式 0 表示线下支付 1 表示在线支付',
     *     'orderState'=>'订单状态 订单状态: 0 新订单 1 已确认 2 已成功 3 已取消 4 已完成。',
     *     'pageNum'=>'每页条数 默认 50，最大 100',
     *     'pageNo'=>'页码 默认1',
     * ]
     * @return array
     */
    public function getOrderListInfo(array $searchData = [])
    {
        $path = 'api/orderList.jsp';
        return $this->zwy_get_request($path, $searchData);
    }


    /**
     * 第 9 章 订单支付接口
     * @param $orderId
     * @return array
     */
    public function tellOrderPaid($orderId)
    {
        $path = 'api/pay.jsp';
        $data = ['orderId' => $orderId];
        return $this->zwy_get_request($path, $data);
    }


    /**
     * 第 10 章 订单取消接口
     * @param $orderId
     * @return array
     */
    public function cancelOrder($orderId)
    {
        $path = 'api/cancelOrder.jsp';
        $data = ['orderId' => $orderId];
        return $this->zwy_get_request($path, $data);
    }


    /**
     * 第 11 章 订单部分取消接口
     * @param $orderId '订单号
     * @param $num '取消人数
     * @return array
     */
    public function refundOrder($orderId, $num)
    {
        $path = 'api/refundOrder.jsp';
        $data = ['orderId' => $orderId, 'num' => $num];
        return $this->zwy_get_request($path, $data);
    }


    /**
     * 第 12 章 订单退改申请接口
     * @param $orderId '订单号
     * @param $changMemo '退改原因
     * @return array
     */
    public function changeApplyOrder($orderId, $changMemo = '')
    {
        $path = 'api/changeApplyOrder.jsp';
        $data = ['orderId' => $orderId];
        if (!blank($changMemo)) {
            $data = array_merge($data, ['changMemo' => $changMemo]);
        }
        return $this->zwy_get_request($path, $data);
    }

    /**
     * 第 13 章 批量获取订单状态
     * @param $orderId '支持多个订单号查询，“，”分割
     * @return array
     */
    public function getOrderState($orderId)
    {
        $path = 'api/getOrderState.jsp';
        $data = ['orderId' => $orderId];
        return $this->zwy_get_request($path, $data);
    }


    /**
     * 第14章 景区接口
     * @param array $searchData
     * [
     *      'cityName'=>'地区名称 支持按照省份或者城 市名称搜索，同时搜 索多个地区使用“,” 分隔',
     *      'location'=>'位置信息 格式: (经度，纬度，方 圆距离) 默认距离 2km',location=108.29536000,22.73270000,5000,
     *      'pageNo'=>'第几页,'
     *      'pageNum'=>'多少条,'
     * ]
     * @return array
     */
    public function getViewInfo(array $searchData = [])
    {
        $path = 'api/view.jsp';
        return $this->zwy_get_request($path, $searchData);
    }


    /**
     * 第15章 城市接口
     * @param array $searchData
     * [
     *      'cityName'=>'省份名称',
     *      'limit'=>'总共多少条 默认10条',
     * ]
     * @return array
     */
    public function getCityInfo(array $searchData = [])
    {
        $path = 'api/city.jsp';
        return $this->zwy_get_request($path, $searchData);
    }

    /**
     * 第 16 章 景区标签接口
     * @return array
     */
    public function getTagInfo()
    {
        $path = 'api/tag.jsp';
        return $this->zwy_get_request($path);
    }


    /**
     * 第 17 章 重发(重发短信)接口
     * @param $orderId '系统返回的订单编号
     * @param $mobile '重发的手机号码
     * @return array
     */
    public function resend($orderId, $mobile)
    {
        $path = 'api/tag.jsp';
        $data = ['orderId' => $orderId, 'mobile' => $mobile];
        return $this->zwy_get_request($path, $data);
    }

    /**
     * 第 18 章 异步同调通知推送
     * 我方提供地址
     */
}
