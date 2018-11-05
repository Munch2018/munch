<?php
/**
 * Created by PhpStorm.
 * User: jungmin
 * Date: 2018-10-28
 * Time: 오후 10:20
 */

class PaymentResult  extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->load->model('payment_model', 'payment_model');

        $params  = $_POST;
        $params = json_decode($params);
        if ($params['imp_uid'] !== self::IMP_CODE) {
            return false;
        }
        if (!empty($params['merchant_uid'])) {
            $payment_idx = str_replace('pay_monthly_', '', $params['merchant_uid']);
        }

        $this->payment_model->updatePaymentResult([
            'status' => 'pay_complete',
            'payment_idx'=>$payment_idx
        ]);

        echo var_export($params);
        // imp_uid=imp_1234567890&merchant_uid=merchant_1234567890&status=ready
    }
}