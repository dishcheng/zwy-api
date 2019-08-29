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
 * Class ZwyApiParkCity
 * @package App\Models\ZwyApi\Park
 */
class ZwyApiParkCity extends Model
{
    protected $primaryKey = 'cityId';
    protected $keyType = 'string';

    /**
     * 重写了，这里不分页
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection|Model[]|LengthAwarePaginator
     * @throws ZwyApiException
     */
    public static function all($columns = ['*'])
    {
        $limit = Request::get('limit', 1000);
        $cityName = Request::get('cityName');
        //获取数据数组
        $service = ZwyParkService::getInstance();
        $searchData = ['limit' => $limit, 'cityName' => $cityName];
        $res = $service->getCityInfo($searchData);
        if (!$res['status']) {
            throw new ZwyApiException($res['msg']);
        }
        $data = $res['data'];
        $city = $data['citys']['city'];
        $totalCount = count($city);
        return $totalCount == 1 ? static::hydrate([$city]) : static::hydrate($city);
    }

    public static function with($relations)
    {
        return new static;
    }
}
