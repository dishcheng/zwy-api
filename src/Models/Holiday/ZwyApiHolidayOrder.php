<?php

namespace DishCheng\ZwyApi\Models\Holiday;

use DishCheng\ZwyApi\Exceptions\ZwyApiException;
use DishCheng\ZwyApi\Services\ZwyHolidayService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Request;


/**
 * 不关联数据表,laravel-admin数据来源外部api-
 * https://laravel-admin.org/docs/zh/model-grid-data
 * Class ZwyApiParkOrder
 * @package App\Models\ZwyApi\Holiday
 */
class ZwyApiHolidayOrder extends Model
{
    protected $primaryKey = 'orderId';
    protected $keyType = 'string';


    /**
     * 调用详情接口
     * @param $orderId
     * @return ZwyApiHolidayOrder
     * @throws \Exception
     */
    public function findOrFail($orderId)
    {
        $service = ZwyHolidayService::getInstance();

        $res = $service->getOrderDetailInfo($orderId);
        if (!$res['status']) {
            throw new ZwyApiException($res['msg']);
        }
        //这里返回的数据peoples在order外面，处理到order里面去
        $data = $res['data']['orders']['order'];
        if (isset($res['data']['orders']['peoples'])) {
            $data['people'] = $res['data']['orders']['peoples'];
        } else {
            $data['people'] = '';
        }
        return self::newFromBuilder($data);
    }

    /**
     * 调用列表接口
     * @return LengthAwarePaginator
     * @throws \Exception
     */
    public function paginate()
    {
        $perPage = Request::get('per_page', 20);
        $currentPage = Request::get('page', 1);
        //获取数据数组
        $service = ZwyHolidayService::getInstance();
        $searchData = [
            'pageNum' => $perPage,
            'pageNo' => $currentPage,
        ];
        $request_arr = Request::except(['per_page', 'page']);
        if (!blank($request_arr)) {
            $searchData = array_merge($searchData, $request_arr);
        }
        $res = $service->getOrderListInfo($searchData);
        if (!$res['status']) {
            throw new ZwyApiException($res['msg']);
        }
        $data = $res['data'];
        $totalCount = (int)$data['count'];
        $recordData = $data['orders']['order'];
        $dataList = $totalCount == 1 ? static::hydrate([$recordData]) : static::hydrate($recordData);
        return new LengthAwarePaginator($dataList, $totalCount, $perPage, $currentPage);
    }

    public static function with($relations)
    {
        return new static;
    }


}
