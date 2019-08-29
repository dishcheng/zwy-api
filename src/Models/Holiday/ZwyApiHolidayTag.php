<?php

namespace DishCheng\ZwyApi\Models\Holiday;

use DishCheng\ZwyApi\Exceptions\ZwyApiException;
use DishCheng\ZwyApi\Services\ZwyHolidayService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Request;
use Illuminate\Database\Eloquent\Model;

/**
 * 不关联数据表,laravel-admin数据来源外部api-
 * https://laravel-admin.org/docs/zh/model-grid-data
 * Class ZwyApiHolidayTag
 * @package App\Models\ZwyApiHolidayTag
 */
class ZwyApiHolidayTag extends Model
{
    public static function all($columns = ['*'])
    {
        //获取数据数组
        $service = ZwyHolidayService::getInstance();
        $res = $service->getTagInfo();
        if (!$res['status']) {
            throw new ZwyApiException($res['msg']);
        }
        $data = $res['data'];
        $movies = static::hydrate($data['tags']['tag']);
        return $movies;
    }

    public static function with($relations)
    {
        return new static;
    }
}
