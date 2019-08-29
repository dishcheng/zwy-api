<?php

namespace DishCheng\ZwyApi\Models\Park;

use DishCheng\ZwyApi\Exceptions\ZwyApiException;
use DishCheng\ZwyApi\Services\ZwyParkService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Request;
use Illuminate\Database\Eloquent\Model;

/**
 * 不关联数据表,laravel-admin数据来源外部api-
 * https://laravel-admin.org/docs/zh/model-grid-data
 * Class ZwyParkCity
 * @package App\Models\ZwyPark
 */
class ZwyApiParkView extends Model
{
    public static function paginate($perPage = 20)
    {
        $perPage = Request::get('per_page', 20);
        $currentPage = Request::get('page', 1);
        //获取数据数组
        $service = ZwyParkService::getInstance();
        $searchData = ['pageNum' => $perPage, 'pageNo' => $currentPage];
        $request_arr = Request::except(['page']);
        if (!blank($request_arr)) {
            $searchData = array_merge($searchData, $request_arr);
        }
        $res = $service->getViewInfo($searchData);
        if (!$res['status']) {
            throw new ZwyApiException($res['msg']);
        }
        $data = $res['data'];
        $totalCount = (int)$data['totalNum'];
        $dataList = $totalCount == 1 ? static::hydrate([$data['views']['view']]) : static::hydrate($data['views']['view']);
        return new LengthAwarePaginator($dataList, $totalCount, $perPage, $currentPage);
    }

    public static function with($relations)
    {
        return new static;
    }

    public function where()
    {
        return $this->paginate();
    }
}
