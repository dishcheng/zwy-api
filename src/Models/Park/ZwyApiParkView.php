<?php

namespace DishCheng\ZwyApi\Models\Park;

use DishCheng\ZwyApi\Exceptions\ZwyApiException;
use DishCheng\ZwyApi\Services\ZwyParkService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
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
    public $request_config = [];
    public $zwy_service;

    public function __construct(array $attributes = [])
    {
        $this->zwy_service = ZwyParkService::getInstance();
        parent::__construct($attributes);
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
        if (!blank($this->request_config)) {
            $this->zwy_service->request_config = $this->request_config;
        };
        $searchData = ['pageNum' => $perPage, 'pageNo' => $currentPage];
        $request_arr = Request::except(['page']);
        if (!blank($request_arr)) {
            $searchData = array_merge($searchData, $request_arr);
        }
        $res = $this->zwy_service->getViewInfo($searchData);
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
