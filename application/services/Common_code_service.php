<?php
/**
 * Created by PhpStorm.
 * User: jungmin
 * Date: 2018-08-19
 * Time: 오후 3:10
 */

class Common_code_service extends MY_Service
{
    public static $subscribe_status = ['active' => '진행', 'cancel' => '취소', 'complete' => '완료', 'pause' => '일시정지'];
    public static $order_status = [
        'pay_pending' => '입금대기',
        'pay_complete' => '결제완료',
        'preparing' => '상품준비중',
        'ready' => '상품준비완료(출고중)',
        'shipping' => '배송중',
        'shipped' => '배송완료',
        'return' => '반품',
        'cancel' => '취소'
    ];

    public static $dog_kind = [];
    public static $cat_kind = [];

    public function __construct()
    {
        $this->load->model('Common_code', 'code_model');
    }

    public function getCode($name)
    {
        if (in_array($name, ['dog_kind', 'cat_kind']) && empty(self::${$name})) {
            $this->makeCode();
        }
        return !empty(self::${$name}) ? self::${$name} : [];
    }

    private function makeCode()
    {
        foreach ([1, 2] as $k => $parentCode) {
            $codes = $this->code_model->get_codes($parentCode);
            foreach ($codes as $idx => $code) {
                if ($parentCode == 1) {
                    self::$dog_kind[$code['code']] = $code['name'];
                } else {
                    self::$cat_kind[$code['code']] = $code['name'];
                }
            }
        }
    }
}