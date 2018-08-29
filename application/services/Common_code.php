<?php
/**
 * Created by PhpStorm.
 * User: jungmin
 * Date: 2018-08-19
 * Time: 오후 3:10
 */

class Common_code extends MY_Service
{
    public static $subscribe_status = ['active' => '진행', 'cancel' => '취소', 'complete' => '완료', 'pause' => '일시정지'];
    public static $order_status = ['pending'=>'입금대기', 'cancel' => '취소', 'complete' => '결제완료',  'sendingComplete' =>'발송완료'];
    public function getCode($name)
    {
        return !empty(self::${$name}) ? self::${$name} : [];
    }
}