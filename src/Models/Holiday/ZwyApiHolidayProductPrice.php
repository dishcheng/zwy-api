<?php

namespace DishCheng\ZwyApi\Models\Holiday;

use DishCheng\ZwyApi\Services\ZwyHolidayService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Request;
use Illuminate\Database\Eloquent\Model;

/**
 * 不关联数据表,laravel-admin数据来源外部api-
 * https://laravel-admin.org/docs/zh/model-grid-data
 * Class ZwyApiHolidayProductPrice
 * @package App\Models\ZwyApi\Holiday
 */
class ZwyApiHolidayProductPrice extends Model
{

//    private $productNo;

    public function __construct(array $attributes = [])
    {
//        $this->productNo = $productNo;
        parent::__construct($attributes);
    }

    /**
     * 调用列表接口
     * @return LengthAwarePaginator
     * @throws \Exception
     */
    public static function all($columns = [])
    {
        //获取数据数组
        $service = ZwyHolidayService::getInstance();
        $searchData = Request::all();
        $res = $service->getPriceInfo(Request::get('productNo'), $searchData);
        if (!$res['status']) {
            throw new \Exception($res['msg']);
        }
        $prices = $res['data']['prices'];
        if (Arr::has($prices, 'price')) {
            if (Arr::has($prices['price'], 'date')) {
                //有date这个key表示只有一个item
                $records = [$prices['price']];
            } else {
                //多个item
                $records = $prices['price'];
            }
        } else {
            $records = [];
        }
        $movies = static::hydrate($records);
        return $movies;
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
