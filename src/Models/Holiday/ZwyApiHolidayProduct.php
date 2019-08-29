<?php

namespace DishCheng\ZwyApi\Models\Holiday;

use App\Constant\Constant;
use Carbon\Carbon;
use DishCheng\ZwyApi\Constant\ZwyConstant;
use DishCheng\ZwyApi\Exceptions\ZwyApiException;
use DishCheng\ZwyApi\Services\ZwyHolidayService;
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
class ZwyApiHolidayProduct extends Model
{
    protected $primaryKey = 'productNo';
    protected $keyType = 'string';


    /**
     * 调用详情接口
     * @param $productNo
     * @return ZwyApiHolidayProduct
     * @throws ZwyApiException
     */
    public function findOrFail($productNo)
    {
        $service = ZwyHolidayService::getInstance();

        $res = $service->getProductDetailInfo($productNo);
        if (!$res['status']) {
            throw new ZwyApiException($res['msg']);
        }

        $data = $res['data']['product'];
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
        $res = $service->getProductListInfo($searchData);
        if (!$res['status']) {
            throw new ZwyApiException($res['msg']);
        }

        $data = $res['data'];

        $dataList = (int)$data['totalNum'] == 1 ? static::hydrate([$data['products']['product']]) : static::hydrate($data['products']['product']);
        return new LengthAwarePaginator($dataList, $data['totalNum'], $perPage);
    }


    /**
     * 调用单个产品状态接口
     * @return mixed
     * @throws \Exception
     */
    public function getStateAttribute()
    {
        $service = ZwyHolidayService::getInstance();
        $res = $service->getProductStateInfo($this->productNo);
        if (!$res['status']) {
            return $res['msg'];
        }
        //这个是获取单个所以直接返回第一个状态即可
        if (Arr::has($res, 'data.products.product.productState')) {
            return $res['data']['products']['product']['productState'];
        } else {
            throw new ZwyApiException('没有data.products.product.productState节点');
        }
    }

    public function getStateDescAttribute()
    {
        return isset(ZwyConstant::$zwyHolidayProductStateMap[$this->state]) ? ZwyConstant::$zwyHolidayProductStateMap[$this->state] : '未知';
    }


    /**
     * 每日行程明细
     * @return array
     */
    public function getJourneyArrAttribute()
    {
        $journey = $this->journeys;
        if (blank($journey)) {
            return [];
        }
        if (Arr::has($journey, 'journey')) {
            $j = $journey['journey'];
            if (count($j) == count($j, 1)) {
                // 一维数组
                return [$j];
            } else {
                // 多维数组
                return $j;
            }
        } else {
            return [];
        }
    }


    /**
     * 24951186
     * 套餐明细
     * @return array
     */
    public function getPlanArrAttribute()
    {
        $plans = $this->plans;
        if (blank($plans)) {
            return [];
        }
        if (Arr::has($plans, 'plan')) {
            $j = $plans['plan'];
            if (Arr::has($j, 'remark')) {
                return [$j];
            } else {
                return $j;
            }
        } else {
            return [];
        }
    }


//    /**
//     * 针对 tree_id=12 的抢购预售产品的价格库存
//     * @return mixed
//     * @throws \Exception
//     */
//    public function getYsPriceArrAttribute()
//    {
//        $service = ZwyHolidayService::getInstance();
//        $res = $service->getProductPriceInfo($this->productNo, Carbon::today()->toDateString());
//        return $res;
//    }

    public static function with($relations)
    {
        return new static;
    }

    public function where()
    {
        return $this->paginate();
    }
}
