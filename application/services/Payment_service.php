<?php

/**
 * Created by PhpStorm.
 * User: jungmin
 * Date: 2018-08-23
 * Time: 오후 11:06
 */
class Payment_service extends MY_Service
{
    private $member_idx = 0;
    private $order_idx = 0;
    private $paymentData = [];

    public function __construct()
    {
        $this->member_idx = $this->session->userdata('member_idx');
        if (empty($this->member_idx)) {
            alert('로그인이 필요합니다. ', '/member/login_form/');
            return false;
        }

        $this->load->model('payment_model', 'payment');
    }

    public function setPaymentData($data = [])
    {
        $this->paymentData = $data;
        $this->order_idx = $data['order_idx'];
        return $this;
    }

    public function getPaymentData()
    {
        return $this->paymentData;
    }

    public function add()
    {
        return $this->payment->insertPayment([
            'member_idx' => $this->member_idx,
            'order_idx' => $this->paymentData['order_idx'],
            'amount' => $this->paymentData['last_amount'],
            'pay_method' => !empty($this->paymentData['pay_method']) ? $this->paymentData['pay_method'] : 'card',
            'status' => 'pay_pending',
            'bill_fl' => 'n'
        ]);
    }
}