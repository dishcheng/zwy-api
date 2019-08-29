<?php

namespace DishCheng\ZwyApi\Models\Hotel;

use App\Constant\Constant;
use DishCheng\ZwyApi\Exceptions\ZwyApiException;
use DishCheng\ZwyApi\Services\ZwyHolidayService;
use DishCheng\ZwyApi\Services\ZwyHotelService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Request;
use Illuminate\Database\Eloquent\Model;

/**
 * 不关联数据表,laravel-admin数据来源外部api-
 * https://laravel-admin.org/docs/zh/model-grid-data
 * Class ZwyParkCity
 * @package App\Models\ZwyPark
 */
class ZwyApiHotelInfo extends Model
{
    protected $primaryKey = 'hotelId';
    protected $keyType = 'string';


    /**
     * 1.1    获取自我游！！！单个！！！酒店/房型 基本信息
     * @param $productNo
     * @return ZwyApiHotelInfo
     * @throws ZwyApiException
     */
    public function findOrFail($productNo)
    {
        $service = ZwyHotelService::getInstance();
        $res = $service->getHotelAndRoomInfo($productNo);
        if (!$res['status']) {
            throw new ZwyApiException($res['msg']);
        }
        $data = $res['data']['hotels']['hotel'];
        return self::newFromBuilder($data);
    }


    /**
     * @return LengthAwarePaginator
     * @throws ZwyApiException
     */
    public static function paginate($perPage = 20)
    {
        $currentPage = Request::get('page', 1);
        //获取数据数组
        $service = ZwyHotelService::getInstance();
        $searchData = [
            'pageSize' => $perPage,
            'pageNo' => $currentPage,
        ];
        $request_arr = Request::except(['page']);
        if (!blank($request_arr)) {
            $searchData = array_merge($searchData, $request_arr);
        }
        $res = $service->getHotelInfo($searchData);
        if (!$res['status']) {
            throw new ZwyApiException($res['msg']);
        }
        $data = $res['data'];
        if (Arr::has($data, 'hotels.hotel')) {
            $recordData = $data['hotels']['hotel'];
            $totalCount = (int)$data['totalNum'];
            $dataList = $totalCount == 1 ? static::hydrate([$recordData]) : static::hydrate($recordData);
            return new LengthAwarePaginator($dataList, $totalCount, $perPage);
        } else {
            throw new ZwyApiException('返回数据没有hotels.hotel属性');
        }
    }

    public function where()
    {
        return $this->paginate();
    }

    /**
     * 房型 基本信息
     * @return array
     */
    public function getRoomsArrAttribute()
    {
        $rooms = $this->rooms;
        if (blank($rooms)) {
            return [];
        }
        if (Arr::has($rooms, 'room')) {
            $rooms = $rooms['room'];
            if (Arr::has($rooms, 'roomtypeid')) {
                //只有一个
                return [$rooms];
            } else {
                //有多个room
                return $rooms;
            }
        } else {
            return [];
        }
    }

    public static function with($relations)
    {
        return new static;
    }


}
