<?php
/**
 * Created by PhpStorm.
 * User: jungmin
 * Date: 2018-08-01
 * Time: 오후 10:19
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Order extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index($subscribe_idx = 0)
    {
        if (empty($subscribe_idx)) {
            alert('구독 정보가 전달되지 않았습니다.');
            return false;
        }

        $this->load->model('Card', 'card');
        $this->load->model('Subscribe_model', 'subscribe');

        $member_idx = $this->session->userdata('member_idx');

        $data = [];
        $data['address'] = $this->subscribe->getAddress($member_idx);
        $data['card_info'] = $this->card->getData($member_idx);
        $subscribe_info = $this->subscribe->getSubscribe(['subscribe_idx'=>$subscribe_idx]);
        $data['subscribe_info'] = array_shift($subscribe_info);

        $this->load->view('common/header.html');
        $this->load->view('Order/index.html', $data);
        $this->load->view('common/footer.html');
    }

    public function add(){


    }

    public function complete()
    {
        //   $this->load->model('Order_model', 'order');

        $data = [];
        $member_idx = $this->session->userdata('member_idx');
        //    $data['order_info'] = $this->order->getOrders(['where' => ['member_idx' => $member_idx]]);

        $this->load->view('common/header.html');
        $this->load->view('Order/complete.html', $data);
        $this->load->view('common/footer.html');
    }

}