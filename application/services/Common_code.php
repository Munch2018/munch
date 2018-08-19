<?php
/**
 * Created by PhpStorm.
 * User: jungmin
 * Date: 2018-08-19
 * Time: 오후 3:10
 */

class Common_code extends MY_Service
{
    public static $subscribe_status = ['active' => '진행', 'cancel' => '취소', 'complete' => '완료'];

    public function getCode($name)
    {
        return !empty(self::${$name}) ? self::${$name} : [];
    }
}