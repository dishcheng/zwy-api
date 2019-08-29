<?php

namespace DishCheng\ZwyApi\Constant;


class ZwyConstant
{

    /**
     * 自我游公园产品treeId
     */
    const ZwyParkProductTicket = 0;
    const ZwyParkProductPackage = 1;
    const ZwyParkProductZyx = 2;
    const ZwyParkProductXl = 3;
    const ZwyParkProductYs = 12;
    public static $zwyParkProductTreeIdMap = [
        self::ZwyParkProductTicket => '门票',
        self::ZwyParkProductPackage => '套餐或打包产品',
        self::ZwyParkProductZyx => '自由行',
        self::ZwyParkProductXl => '线路',
        self::ZwyParkProductYs => '预售',
    ];


    /**
     * 自我游公园产品状态
     */
    const ZwyParkProductStateSelling = 0;
    const ZwyParkProductStateUnSell = 1;
    const ZwyParkProductStateDelete = 2;

    public static $zwyParkProductStateMap = [
        self::ZwyParkProductStateSelling => '在售',
        self::ZwyParkProductStateUnSell => '下架',
        self::ZwyParkProductStateDelete => '已删除',
    ];


    /**
     * 自我游公园订单状态
     */
    const ZwyParkOrderStateNewOrder = 0;
    const ZwyParkOrderStateConfirmed = 1;
    const ZwyParkOrderStateSucceed = 2;
    const ZwyParkOrderStateCancelled = 3;
    const ZwyParkOrderStateFinished = 4;

    public static $zwyParkOrderStateMap = [
        self::ZwyParkOrderStateNewOrder => '新订单',
        self::ZwyParkOrderStateConfirmed => '已确认',
        self::ZwyParkOrderStateSucceed => '已成功',
        self::ZwyParkOrderStateCancelled => '已取消',
        self::ZwyParkOrderStateFinished => '已完成',
    ];


    /**
     * 自我游酒店index=>value等级
     * @var array
     */
    public static $zwyHolidayProductHotelGradeMap = [
        '农家乐',
        '经济型酒店',
        '二星',
        '舒适型/商务型',
        '三星',
        '享受型',
        '四星',
        '豪华型',
        '五星',
        '超五星',
        '度假村',
        '未知',
        '客栈',
        '商务型酒店',
        '三星以下',
        '家庭旅馆',
        '无',
        '准一星级',
        '一星级',
        '准二星级',
        '二星以下',
        '准三星',
        '准四星',
        '准五星',
        '六星',
        '准六星',
        '高档',
        '度假',
    ];

    /**
     * 自我游-度假-产品-出游方式
     */
    const ZwyHolidayProductLineTypePingTuan = 0;
    const ZwyHolidayProductLineTypeDuLiChengTuan = 1;
    const ZwyHolidayProductLineTypeDuLiZyx = 2;
    public static $zwyHolidayProductLineTypeMap = [
        self::ZwyHolidayProductLineTypePingTuan => '拼团',
        self::ZwyHolidayProductLineTypeDuLiChengTuan => '独立成团',
        self::ZwyHolidayProductLineTypeDuLiZyx => '自由行',
    ];

    /**
     * 自我游-度假-产品-线路类型
     * @var array
     */
    public static $zwyHolidayProductLineClassMap = [
        '地接线路',
        '周边线路',
        '国内线路',
        '出境线路',
        '港澳台线路',
    ];

    /**
     * 自我游-度假-产品-交通方式
     * @var array
     */
    public static $zwyHolidayProductTrafficMap = [
        '飞机',
        '普通火车',
        '汽车',
        '动车或高铁',
        '游轮',
        '其他',
    ];


    /**
     * 自我游-度假-产品-状态
     */
    const ZwyHolidayProductStateSelling = 0;
    const ZwyHolidayProductStateUnSell = 1;
    const ZwyHolidayProductStateDelete = 2;
    const ZwyHolidayProductStateDelete2 = 3;

    public static $zwyHolidayProductStateMap = [
        self::ZwyHolidayProductStateSelling => '在售',
        self::ZwyHolidayProductStateUnSell => '下架',
        self::ZwyHolidayProductStateDelete => '已删除',
        self::ZwyHolidayProductStateDelete2 => '已删除',
    ];


    /**
     * 自我游-度假-产品-价格-状态
     */
    const ZwyHolidayProductPriceStateNormal = 0;
    const ZwyHolidayProductPriceStateStop = 1;
    const ZwyHolidayProductPriceStateExpire = 2;
    const ZwyHolidayProductPriceStateDelete = 3;
    public static $zwyHolidayProductPriceStateMap = [
        self::ZwyHolidayProductPriceStateNormal => '正常',
        self::ZwyHolidayProductPriceStateStop => '停收',
        self::ZwyHolidayProductPriceStateExpire => '过期',
        self::ZwyHolidayProductPriceStateDelete => '删除',
    ];

}
