<?php

namespace DishCheng\ZwyApi\Models\Park;

use Carbon\Carbon;
use DishCheng\ZwyApi\Constant\ZwyConstant;
use DishCheng\ZwyApi\Exceptions\ZwyApiException;
use DishCheng\ZwyApi\Services\ZwyParkService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Request;
use Illuminate\Database\Eloquent\Model;

/**
 * 不关联数据表,laravel-admin数据来源外部api-
 * https://laravel-admin.org/docs/zh/model-grid-data
 * Class ZwyParkCity
 * @package App\Models\ZwyPark
 */
class ZwyApiParkViewProduct extends Model
{
    protected $primaryKey = 'productNo';
    protected $keyType = 'string';

    /**
     * 调用详情接口
     * @param $productNo
     * @return ZwyApiParkViewProduct
     * @throws \Exception
     */
    public function findOrFail($productNo)
    {
        $service = ZwyParkService::getInstance();
        $res = $service->getProductDetailInfo($productNo);
        if (!$res['status']) {
            throw new \Exception($res['msg']);
        }
        $data = $res['data']['product'];
        return self::newFromBuilder($data);
    }


    /**
     * @param null $perPage
     * @param array $columns
     * @param string $pageName
     * @param null $page
     * @return LengthAwarePaginator
     * @throws ZwyApiException
     */
    public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
    {
        $currentPage = $page ?: Paginator::resolveCurrentPage($pageName);
        $perPage = $perPage ?: $this->perPage;

        //获取数据数组
        $service = ZwyParkService::getInstance();
        $searchData = [
            'pageNum' => $perPage,
            'pageNo' => $currentPage,
        ];
        $request_arr = Request::except(['page']);
        if (!blank($request_arr)) {
            $searchData = array_merge($searchData, $request_arr);
        }
        $res = $service->getProductListInfo($searchData);
        if (!$res['status']) {
            throw new ZwyApiException($res['msg']);
        }
        $data = $res['data'];
        $totalCount = (int)$data['totalNum'];
        if ($totalCount == 1) {
            $movies = static::hydrate([$data['products']['product']]);
        } else {
            if (Arr::has($data, 'products.product')) {
                $movies = static::hydrate($data['products']['product']);
            } else {
                $movies = static::hydrate([]);
            }
        }
        return new LengthAwarePaginator($movies, $totalCount, $perPage, $currentPage);
    }


    /**
     * 调用单个产品状态接口
     * @return mixed
     * @throws \Exception
     */
    public function getStateAttribute()
    {
        $service = ZwyParkService::getInstance();
        $res = $service->getProductStateInfo($this->productNo);
        if (!$res['status']) {
            return $res['msg'];
        }
        //这个是获取单个所以直接返回第一个状态即可
        if (Arr::has($res, 'data.products.product')) {
            $records = $res['data']['products']['product'];
            return $records['productState'];
        }
    }

    public function getStateDescAttribute()
    {
        return isset(ZwyConstant::$zwyParkProductStateMap[$this->state]) ? ZwyConstant::$zwyParkProductStateMap[$this->state] : '未知';
    }


    /**
     * 针对 tree_id=12 的抢购预售产品的价格库存
     * @return mixed
     * @throws \Exception
     */
    public function getYsPriceArrAttribute()
    {
        $service = ZwyParkService::getInstance();
        $res = $service->getProductPriceInfo($this->productNo, Carbon::today()->toDateString());
        return $res;
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
