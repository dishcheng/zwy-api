<?php

namespace DishCheng\ZwyApi\Models\Hotel;

use DishCheng\ZwyApi\Exceptions\ZwyApiException;
use DishCheng\ZwyApi\Services\ZwyHotelService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
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
     * @return \Illuminate\Database\Eloquent\Collection|Model[]
     * @throws ZwyApiException
     */
    public static function all($request_config = [])
    {
        $queryType = Request::get('queryType', 'hotelpriceall');
        $yearMonth = Request::get('yearMonth', date('Y-m'));
        //获取数据数组
        $service = ZwyHotelService::getInstance();
        if (!blank($service)) {
            $service->request_config = $request_config;
        }
        $searchData = Request::only(['hotelIds', 'roomtypeIds', 'productIds']);
        if (blank($searchData)) {
            throw new ZwyApiException('酒店IDS,房型IDS,产品IDS不能同时为空');
        }
        $request_arr = Request::except(['queryType', 'yearMonth', 'hotelIds', 'roomtypeIds', 'productIds']);
        if (!blank($request_arr)) {
            $searchData = array_merge($searchData, $request_arr);
        }
        $checkInDate = Carbon::parse($yearMonth)->startOfMonth()->toDateString();
        $checkOutDate = Carbon::parse($yearMonth)->endOfMonth()->toDateString();
        $res = $service->getPriceInfo($queryType, $checkInDate, $checkOutDate, $searchData);
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
            throw new ZwyApiException('返回数据没有rooms.room属性');
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
