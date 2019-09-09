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
class ZwyApiParkViewTag extends Model
{
    /**
     * 重写了，这里不分页
     * @param array $request_config
     * @return \Illuminate\Database\Eloquent\Collection|Model[]
     * @throws ZwyApiException
     */
    public static function all($request_config = [])
    {
        //获取数据数组
        $service = ZwyParkService::getInstance();
        if (!blank($request_config)) {
            $service->request_config = $request_config;
        }
        $res = $service->getTagInfo();
        if (!$res['status']) {
            throw new ZwyApiException($res['msg']);
        }
        $data = $res['data'];
        $tags = $data['tags']['tag'];
        return count($tags) == 1 ? static::hydrate([$tags]) : static::hydrate($tags);
    }

    public static function with($relations)
    {
        return new static;
    }
}
