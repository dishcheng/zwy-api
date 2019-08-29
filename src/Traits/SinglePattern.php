<?php

namespace DishCheng\ZwyApi\Traits;

trait SinglePattern
{
    //构造函数私有，防止外部实例化
    private function __construct()
    {
    }

    //克隆方法私有，防止外部克隆
    private function __clone()
    {
    }

    /**
     * 外部可调用的实例化方法(单例)
     * @return static
     * @throws \Exception
     */
    public static function getInstance()
    {
        $classFullName = get_called_class();
        if (!class_exists($classFullName)) {
            throw new \Exception($classFullName . '不存在');
        }
        if (!key_exists($classFullName, self::$_instance)) {
            self::$_instance[$classFullName] = new static;
        }
        return self::$_instance[$classFullName];
    }
}