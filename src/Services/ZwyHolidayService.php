<?php

namespace DishCheng\ZwyApi\Services;

use DishCheng\ZwyApi\Traits\SinglePattern;

/**
 * 参照 分销系统（度假）API接口说明v3.1.pdf
 * Class ZwyHolidayService
 * @package App\Http\Service\Zwy
 */
class ZwyHolidayService extends ClientRequestService
{
    use SinglePattern;
    //存放实例对象
    protected static $_instance = [];


//    /**
//     * 第 2 章 城市接口，数据与公园城市接口基本一致
//     * @param array $searchData
//     *
//     * $searchData 搜索参数结构，搜索什么传什么
//     * [
//     *      'cityName'=>'省份或城市名称',
//     *      'limit'=>'总共多少条,默认10条',
//     * ]
//     *
//     * @return array
//     */
//    public function getCityInfo(array $searchData = [])
//    {
//        $path = 'api/holiday/city.jsp';
//        return $this->zwy_get_request($path, $searchData);
//    }


    /**
     * 第 3 章 主题标签接口
     * @return array
     */
    public function getTagInfo()
    {
        $path = 'api/holiday/tag.jsp';
        return $this->zwy_get_request($path);
    }


    /**
     * 第 4 章 产品列表接口
     * @param array $searchData
     * $searchData 搜索参数结构，搜索什么传什么
     * [
     *      'keyWrod'=>'关键字 系统自动切词',
     *      'treeId'=>'产品类型 int 3 线路，目前只支持线路 该字段为留用。',
     *      'cityName'=>'地区名称 String 支持按照省份或者城市名称 搜索，同时搜索多个地区使 用“,”分隔',
     *      'cityId'=>'地区 id int 支持按照省份或者城市 id',
     *      'isPay'=>'支付方式  0 表示线下支付 1 表示在线支付',
     *      'isConfirm'=>'确认方式  0 表示系统自动确认订单 1 表示人工确认订单',
     *      'lineType'=>'出游方式 0 拼团 1 独立成团 2 自由行',
     *      'lineClass'=>'线路类型 0 地接线路 1 周边组团 2 国内组团 3 出境组团 4 港澳台组团',
     *      'tagId'=>'主题标签 String 分销系统系统提供的主题标签编号(获取参见主题标签 接口)',
     *      'tagName'=>'主题标签 String 分销系统系统提供的主题标 签名字(获取参见主题标签 接口)',
     *      'hotelGrade'=>'住宿等级 0 农家乐
     * 1 经济型酒店 2二星
     * 3 舒适型/商务型 4三星
     * 5 享受型
     * 6四星
     * 7 豪华型
     * 8五星
     * 9 超五星
     * 10 度假村
     * 11 未知
     * 12 客栈
     * 13 商务型酒店 14 三星以下
     * 15 家庭旅馆 16无
     * 17 准一星级
     * 18 一星级
     * 19 准二星级
     * 20 二星以下
     * 21 准三星
     * 22 准四星
     * 23 准五星
     * 24 六星
     * 25 准六星
     * 26 高档
     * 27 度假',
     *      'days'=>'行程天数 总共多少天的行程',
     *      'traffic'=>'交通方式 0 飞机 1 普通火车 2 汽车 3 动车或高铁 4 游轮 5 其他',
     *      'pageNum'=>'一页显示多少条 默认 50 条，最多可设置 100 条',
     *      'pageNo'=>'页数int 默认第一页',
     *      'orderBy'=>'排序方式int 0 按价格 1 按折扣 2 按销量 3 按推荐值 4 上架时间',
     * ]
     * @return array
     */
    public function getProductListInfo(array $searchData = [])
    {
        $path = 'api/holiday/productList.jsp';
        return $this->zwy_get_request($path, $searchData);
    }


    /**
     * 第 5 章 产品详情接口
     * @param $productNo '产品号,产品信息编号'
     * @return array
     */
    public function getProductDetailInfo($productNo)
    {
        $path = 'api/holiday/productDetail.jsp';
        $data = [
            'productNo' => $productNo
        ];
        return $this->zwy_get_request($path, $data);
    }


    /**
     * 第 6 章 批量获取产品状态
     * @param $productNo '产品号,支持多个产品号查询，“，”分割'
     * @return array
     */
    public function getProductStateInfo($productNo)
    {
        $path = 'api/holiday/getProductState.jsp';
        $data = [
            'productNo' => $productNo
        ];
        return $this->zwy_get_request($path, $data);
    }


    /**
     * 第 7 章 产品价格日历接口
     * @param $productNo '产品编号/产品唯一号'
     * @param array $searchData
     * [
     *      'planId'=>'价格计划编号 一个产品有多个价格计划',
     *      'travelDate'=>'开始日期,yyyy-MM-dd',
     *      'endTravelDate'=>'结束日期,yyyy-MM-dd',
     * ]
     * @return array
     */
    public function getPriceInfo($productNo, array $searchData = [])
    {
        $path = 'api/holiday/price.jsp';
        $data = [
            'productNo' => $productNo
        ];
        if (!blank($searchData)) {
            $data = array_merge($data, $searchData);
        }
        return $this->zwy_get_request($path, $data);
    }


    /**
     * 第 8 章 增加订单接口
     * @param $params
     * @return array
     */
    public function createOrder($params)
    {
        $path = 'api/holiday/order.jsp';
        return $this->zwy_post_request($path, $params);
    }


    /**
     * 第 9 章 订单详细接口
     * @param $orderId
     * @return array
     */
    public function getOrderDetailInfo($orderId)
    {
        $path = 'api/holiday/orderDetail.jsp';

        $data = ['orderId' => $orderId];
        return $this->zwy_get_request($path, $data);
    }

    /**
     * 第 10 章 订单列表接口
     * @param array $searchData
     * [
     *      'keyWrod'=>'关键字 订单号、游客姓名、手机号码、 订单备注、产品名称',
     *      'userId'=>'分销渠道下单时传递的用户身份编码',
     *      'startDate'=>'下单开始日期 如果不输入为不限制时间 格式:2011-01-01',
     *      'endDate'=>'下单结束日期 如果不输入为不限制时间 格式:2011-01-01',
     *      'travelDate'=>'游玩日志 如果不输入为不限制时间 格式:2011-01-01',
     *      'isPay'=>'0 表示线下支付 1 表示在线支付',
     *      'orderState'=>'订单状态:0 新订单 1 已确认 2 已成功 3 已取消 4 已完成。',
     *      'pageNum'=>'默认 50，最大 100',
     *      'pageNo'=>'默认1',
     * ]
     * @return array
     */
    public function getOrderListInfo(array $searchData = [])
    {
        $path = 'api/holiday/orderList.jsp';

        return $this->zwy_get_request($path, $searchData);
    }


    /**
     * 第 11 章 订单支付回调接口
     * @param $orderId '订单号/订单标识'
     * @return array
     */
    public function tellOrderPaid($orderId)
    {
        $path = 'api/holiday/pay.jsp';
        $data = ['orderId' => $orderId];
        return $this->zwy_get_request($path, $data);
    }

    /**
     * 第 12 章 订单取消接口
     * @param $orderId
     * @return array
     */
    public function cancelOrder($orderId)
    {
        $path = 'api/holiday/cancelOrder.jsp';
        $data = ['orderId' => $orderId];
        return $this->zwy_get_request($path, $data);
    }


    /**
     * 第 13 章 订单退改申请接口
     * @param $orderId '订单号/订单标识'
     * @param string $changeMemo 退改原因
     * @return array
     */
    public function changeApplyOrder($orderId, $changeMemo = '')
    {
        $path = 'api/holiday/changeApplyOrder.jsp';
        $data = ['orderId' => $orderId];
        if (!blank($changeMemo)) {
            $data = array_merge($data, ['changeMemo' => $changeMemo]);
        }
        return $this->zwy_get_request($path, $data);
    }


    /**
     * 第 14 章 批量获取订单状态
     * @param $orderId '产品号 支持通过订单号批量查询订单状态(多个订单号用“，”分割)
     * @return array
     */
    public function getOrderState($orderId)
    {
        $path = 'api/holiday/getOrderState.jsp';
        $data = ['orderId' => $orderId];
        return $this->zwy_get_request($path, $data);
    }


    /**
     * 第15章 重发接口
     * @param $orderId
     * @param $mobile
     * @return array
     */
    public function resend($orderId, $mobile)
    {
        $path = 'api/holiday/resend.jsp';
        $data = ['orderId' => $orderId, 'mobile' => $mobile];
        return $this->zwy_get_request($path, $data);
    }

    /**
     * 第 16 章 异步通知推送
     * 我方提供接口
     */


    /**
     * 第 17 章 会员注册接口
     * @param $mobile '手机号
     * @param $pass '密码，登陆用
     * @return array
     */
    public function userRegister($mobile, $pass)
    {
        $path = 'api/holiday/reg.jsp';
        $data = ['mobile' => $mobile, 'pass' => $pass];
        return $this->zwy_get_request($path, $data);
    }


    /**
     * 第 18 章 会员登陆接口
     * @param $mobile
     * @param $pass
     * @return array
     */
    public function userLogin($mobile, $pass)
    {
        $path = 'api/holiday/login.jsp';
        $data = ['mobile' => $mobile, 'pass' => $pass];
        return $this->zwy_get_request($path, $data);
    }
}