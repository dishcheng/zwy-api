<?php

namespace DishCheng\ZwyApi\Models\Hotel;

use App\Exceptions\ApiCommonException;
use DishCheng\ZwyApi\Exceptions\ZwyApiException;
use DishCheng\ZwyApi\Services\ZwyHotelService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

/**
 * 不关联数据表,laravel-admin数据来源外部api-
 * https://laravel-admin.org/docs/zh/model-grid-data
 * Class ZwyParkCity
 * @package App\Models\ZwyPark
 */
class ZwyApiHotelPrice extends Model
{


    /**
     * @param array $request_config
     * @param array $searchData
     * @return \Illuminate\Database\Eloquent\Collection|Model[]
     * @throws ZwyApiException
     */
    public static function all($request_config = [], $searchData = [])
    {
        //获取数据数组
        $service = ZwyHotelService::getInstance();
        if (!blank($service)) {
            $service->request_config = $request_config;
        }
        if (blank($searchData)) {
            $queryType = Request::get('queryType', 'hotelpriceall');
            $checkInDate = Request::get('checkInDate');
            $checkOutDate = Request::get('checkOutDate');
            if (blank($checkInDate) || blank($checkOutDate)) {
                throw new ZwyApiException('查询日期范围不能为空');
            }
            $searchData = Request::only(['hotelIds', 'roomtypeIds', 'productIds']);
            if (blank($searchData)) {
                throw new ZwyApiException('酒店IDS,房型IDS,产品IDS不能同时为空');
            }
            $request_arr = Request::except(['queryType', 'checkInDate', 'checkOutDate', 'hotelIds', 'roomtypeIds', 'productIds']);
            if (!blank($request_arr)) {
                $searchData = array_merge($searchData, $request_arr);
            }
            $res = $service->getPriceInfo($queryType, $checkInDate, $checkOutDate, $searchData);
        } else {
            if (!Arr::has($searchData, ['queryType', 'checkInDate', 'checkOutDate'])) {
                throw new ZwyApiException('缺少参数');
            }
            $res = $service->getPriceInfo(
                $searchData['queryType'],
                $searchData['checkInDate'],
                $searchData['checkOutDate'],
                Arr::except($searchData, ['queryType', 'checkInDate', 'checkOutDate']));
        }


        if (!$res['status']) {
            throw new ZwyApiException($res['msg']);
        }
        $data = $res['data'];
        if (Arr::has($data, 'rooms.room')) {
            $recordData = $data['rooms']['room'];
            if (Arr::has($recordData, 'hotelId')) {
                //只有一条记录
                $dataList = [$recordData];
            } else {
                //多条记录
                $dataList = $recordData;
            }
            return static::hydrate($dataList);
        } else {
            //产品不存在或者已下架居然状态返回1，真实日了狗了
            /**
             * {
             * "status_code": 200,
             * "message": "ok",
             * "data": {
             * "success": [
             * "1"
             * ],
             * "msg": [
             * "产品不存在或者已下架"
             * ]
             * }
             * }
             */
            throw new ZwyApiException($data['msg']);
        }
    }

    public function where()
    {
        return $this->paginate();
    }

    public static function with($relations)
    {
        return new static;
    }


}
