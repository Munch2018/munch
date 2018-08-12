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

    public function index()
    {
        $this->load->model('Card', 'card');

        $data = [];
        $member_idx = $this->session->userdata('member_idx');
        $data['card_info'] = $this->card->getData($member_idx);

        $this->load->view('common/header');
        $this->load->view('Order/index', $data);
        $this->load->view('common/footer');
    }

    public function complete()
    {
     //   $this->load->model('Order_model', 'order');

        $data = [];
        $member_idx = $this->session->userdata('member_idx');
    //    $data['order_info'] = $this->order->getOrders(['where' => ['member_idx' => $member_idx]]);

        $this->load->view('common/header');
        $this->load->view('Order/complete', $data);
        $this->load->view('common/footer');
    }

}