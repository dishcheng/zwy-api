<?php

namespace DishCheng\ZwyApi;


use DishCheng\ZwyApi\Models\Hotel\ZwyApiHotelInfo;
use DishCheng\ZwyApi\Models\Hotel\ZwyApiHotelPrice;
use DishCheng\ZwyApi\Models\Park\ZwyApiParkCity;
use DishCheng\ZwyApi\Models\Park\ZwyApiParkOrder;
use DishCheng\ZwyApi\Models\Park\ZwyApiParkView;
use DishCheng\ZwyApi\Models\Park\ZwyApiParkViewProduct;
use DishCheng\ZwyApi\Models\Park\ZwyApiParkViewTag;
use Illuminate\Support\ServiceProvider;


class ZwyApiProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {

    }

    public function register()
    {
        /**
         * 公园
         */
        $this->app->singleton('zwy_api_park_city', function () {
            return new ZwyApiParkCity();
        });
        $this->app->singleton('zwy_api_park_order', function () {
            return new ZwyApiParkOrder();
        });
        $this->app->singleton('zwy_api_park_view', function () {
            return new ZwyApiParkView();
        });
        $this->app->singleton('zwy_api_park_view_product', function () {
            return new ZwyApiParkViewProduct();
        });
        $this->app->singleton('zwy_api_park_view_tag', function () {
            return new ZwyApiParkViewTag();
        });


        /**
         *酒店
         */
        $this->app->singleton('zwy_api_hotel_info', function () {
            return new ZwyApiHotelInfo();
        });
        $this->app->singleton('zwy_api_hotel_price', function () {
            return new ZwyApiHotelPrice();
        });
    }
}
