<?php

namespace DishCheng\ZwyApi\Models\Park;

use App\Constant\Constant;
use DishCheng\ZwyApi\Exceptions\ZwyApiException;
use DishCheng\ZwyApi\Services\ZwyParkService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Request;
use Illuminate\Database\Eloquent\Model;


/**
 *  * 不关联数据表,laravel-admin数据来源外部api-
 * https://laravel-admin.org/docs/zh/model-grid-data
 * Class ZwyApiParkOrder
 * @package App\Models\ZwyApi\Park
 */
class ZwyApiParkOrder extends Model
{
    protected $primaryKey = 'orderId';
    protected $keyType = 'string';


    /**
     * 调用详情接口
     * @param $orderId
     * @return ZwyApiParkOrder
     * @throws \Exception
     */
    public function findOrFail($orderId)
    {
        $service = ZwyParkService::getInstance();

        $res = $service->getOrderDetailInfo($orderId);
        if (!$res['status']) {
            throw new \Exception($res['msg']);
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
        $res = $service->getOrderListInfo($searchData);
        if (!$res['status']) {
            throw new ZwyApiException($res['msg']);
        }
        $data = $res['data'];
        $totalCount = (int)$data['count'];
        $dataList = $totalCount == 1 ? static::hydrate([$data['orders']['order']]) : static::hydrate($data['orders']['order']);
        return new LengthAwarePaginator($dataList, $totalCount, $perPage, $currentPage);
    }

    public static function with($relations)
    {
        return new static;
    }


}
