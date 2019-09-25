<?php

namespace DishCheng\ZwyApi\Services;

//use App\Http\Service\ClientRequestService;
use DishCheng\ZwyApi\Traits\SinglePattern;

/**
 * 参照 自我游酒店api文档2017-05-25.docx
 * Class ZwyHotelService
 * @package App\Http\Service\Zwy
 */
class ZwyHotelService extends ClientRequestService
{
    use SinglePattern;
    //存放实例对象
    protected static $_instance = [];

    public $request_config = [];

    /**
     * 3.1  获取自我游酒店/房型 基本信息
     * @param $hotelIds '酒店ID 自我游的酒店ID，支持多个查询，用“,”隔开,如1,2,3,4,5。每次请求最多查询20个。
     * @param int $is_super '是否优势酒店 1：优势产品
     * @return array
     */
    public function getHotelAndRoomInfo($hotelIds, $is_super = -1)
    {
        $path = 'api/hotel/getHotelAndRoomInfo.jsp';
        $data = ['hotelIds' => $hotelIds];
        if ($is_super != -1) {
            $data = array_merge($data, ['is_super' => $is_super]);
        }
        return $this->zwy_get_request($path, $data);
    }


    /**
     * 3.2 获取自我游酒店价格/房态/预订条款/修改条款
     * 一次最多只能抓取20家酒店的价格数据。
     * @param $queryType '查询类型 hotelpriceall申请+即时确认 hotelpricecomfirm只要即时确认
     * @param $checkInDate '查询日期范围开始
     * @param $checkOutDate '查询日期范围结束：时间范围最多一个月。如果超过一个月，则自我游自动截取
     * @param array $searchData
     * [
     *     //前三个参数不能同时为空
     *    'hotelIds'=>'自我游酒店ID，多个ID用逗号分开，优先级别最低',
     *    'roomtypeIds'=>'房型IDS 自我游房型ID，多个ID用逗号分开，优先级别第二',
     *    'productIds'=>'产品IDS 产品ID，多个ID用逗号分开，优先级别最高',
     *    'rateplanId'=>'价格计划ID，多个ID用逗号分开，优先级别最高',
     *
     *    'pricingtype'=>'定价类型，10全部，11前台现付，12预付。可不传，默认只查12',
     *    'prodInfo'=>'是否显示产品相关信息，1：显示产品的图片、说明、预订信息，可以为空',
     *    'is_super'=>'是否优势产品 1：优势产品',
     * ]
     * @return array
     */
    public function getPriceInfo($queryType, $checkInDate, $checkOutDate, array $searchData = [])
    {
        $path = 'api/hotel/price.jsp';
        $data = ['queryType' => $queryType, 'checkInDate' => $checkInDate, 'checkOutDate' => $checkOutDate];
        if (!blank($searchData)) {
            $data = array_merge($data, $searchData);
        }
        return $this->zwy_get_request($path, $data);
    }


    /**
     * 3.3 变价通知提示
     * 该接口调用一次查询最近5分钟有哪些房型的价格、房态、条款发生了变化。
     * 当客户端收到该通知后，可以调用 上述获取3.2价格接口（price.jsp），获取最新价格。以达到数据同步的目的。
     *
     * @param array $searchData
     * [
     *      'lastdate'=>'最后更新时间 yyyy-MM-dd H:i:s' 2016-10-17 16:53:00 不传就是默认倒退5分钟,传就是该值倒退5分钟,
     *      'is_super'=>'是否优势酒店', 是否优势酒店
     * ]
     * @return array
     */
    public function getPriceChangeInfo(array $searchData = [])
    {
        $path = 'api/hotel/priceChange.jsp';
        return $this->zwy_get_request($path, $searchData);
    }


    /**
     * 3.4 下单前检验最新价格、房态接口
     * 该接口由广州自我游提供，供客户调用 ，在客户下单前，最后检测即将生成的订单，价格、房态是否满足。
     * @param $hotelId
     * @param $roomTypeId '房型ID
     * @param $productId '产品ID
     * @param $ratePlanId '价格计划ID
     * @param $checkInDate '入住日期 yyyy-MM-dd
     * @param $checkOutDate '退房日期 yyyy-MM-dd
     * @return array
     */
    public function checkPrice($hotelId, $roomTypeId, $productId, $ratePlanId, $checkInDate, $checkOutDate)
    {
        $path = 'api/hotel/checkPrice.jsp';
        $data = [
            'hotelId' => $hotelId,
            'roomtypeid' => $roomTypeId,
            'productId' => $productId,
            'rateplanId' => $ratePlanId,
            'checkInDate' => $checkInDate,
            'checkOutDate' => $checkOutDate,
        ];
        return $this->zwy_get_request($path, $data);
    }

    /**
     * 3.5 新增订单 todo::
     * @param $data
     * @return array
     */
    public function orderAdd($data)
    {
        $path = 'api/hotel/orderAdd.jsp';
        return $this->zwy_post_request($path, $data, 'orderXml');
    }


    /**
     * 3.6 支付订单
     * @param $orderId '自我游订单号
     * @return array
     */
    public function tellOrderPaid($orderId)
    {
        $path = 'api/hotel/orderPay.jsp';
        $data = ['orderId' => $orderId];
        return $this->zwy_post_request($path, $data);
    }


    /**
     * 3.7 申请取消订单
     * 该接口由自我游提供，供客户调用 ，用于客户申请取消订单。
     * !!!!!!!（请注意申请成功并不代表订单取消成功）是否取消成功以自我游通知为准,见3.12。!!!!
     * @param $orderId '自我游订单号
     * @param $order_source_id '客户订单号
     * @param $remark '备注信息-取消原因
     * @return array
     */
    public function cancelOrder($orderId, $order_source_id, $remark)
    {
        $path = 'api/hotel/cancelOrder.jsp';
        $data = ['orderId' => $orderId, 'order_source_id' => $order_source_id, 'remark' => $remark];
        return $this->zwy_post_request($path, $data);
    }


    /**
     * 3.8 获取订单状态
     * 该接口由自我游提供，供客户调用 ，用于客户获取订单的确认状态。
     * @param $orderId
     * @param $order_source_id
     * @return array
     */
    public function getOrderInfo($orderId, $order_source_id)
    {
        $path = 'api/hotel/getOrderInfo.jsp';
        $data = ['orderId' => $orderId, 'order_source_id' => $order_source_id];
        return $this->zwy_get_request($path, $data);
    }

    /**
     * 3.9 获取附加品价格信息
     * 该接口通过房型、酒店获取相关的附加品信息。
     * @param $hotelId '酒店ID
     * @param $roomId '房型Id
     * @param $rateTypeId '价格类型id
     * @param $beginDate '开始日期
     * @param $endDate '结束日期
     * @return array
     */
    public function getConds($hotelId, $roomId, $rateTypeId, $beginDate, $endDate)
    {
        $path = 'api/hotel/getConds.jsp';
        $data = [
            'hotelid' => $hotelId,
            'roomid' => $roomId,
            'ratetypeid' => $rateTypeId,
            'begindate' => $beginDate,
            'enddate' => $endDate
        ];
        return $this->zwy_get_request($path, $data);
    }


    /**
     * 3.10 获取酒店信息接口
     * @param array $searchData
     * @return array
     */
    public function getHotelInfo(array $searchData = [])
    {
        $path = 'api/hotel/getHotelInfo.jsp';
        return $this->zwy_get_request($path, $searchData);
    }


    /**
     * 3.11 我方提供订单确认通知接口
     */

    /**
     * 3.12 我方提供订单取消通知接口
     */

    /**
     * 3.13 我方提供订单核销通知接口
     */

    /**
     * 3.14 我方提供订单安排通知接口
     */

    /**
     * 3.15 我方提供退改申请结果通知接口
     */
}
