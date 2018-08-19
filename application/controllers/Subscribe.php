<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Subscribe extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Subscribe_model', 'subscribe');
    }

    public function index()
    {
        $data['subscribe_list'] = $this->subscribe->getSubscribeGoodsPrice();
        $this->load->model('Pet_manage', 'petmanage');
        $member_idx = $this->session->userdata('member_idx');

        $data['pets'] = $this->petmanage->getPets($member_idx);

        $this->load->view('common/header.html');
        $this->load->view('Subscribe/index.html', $data);
        $this->load->view('common/footer.html');
    }

    public function order()
    {
        $this->load->model('order_model', 'order');

        $this->load->view('common/header.html');
        $this->load->view('Subscribe/order.html', $data);
        $this->load->view('common/footer.html');
    }

    public function add()
    {
        $params = $this->input->get();
        $subscribe_idx = 0;

        $goods =  $this->subscribe->getGoodsToBuy($params['pet_idx']);

        try {
            $this->subscribe->db->trans_begin();

            $member_idx = $this->session->userdata('member_idx');
            $subscribe_idx = $this->subscribe->insertSubscribe([
                'pet_idx' => $params['pet_idx'],
                'period' => $params['period'],
                'member_idx' => $member_idx,
                'goods_idx' => $goods[0]['goods_idx'],
                'buy_count' => 1
            ]);

            for ($sequence = 1; $sequence <= $params['period']; $sequence++) {
                $this->subscribe->insertSubscribeDetail([
                    'subscribe_idx' => $subscribe_idx,
                    'member_idx' => $member_idx,
                    'sequence' => $sequence,
                    'schedule_dt' => date('Y-m-d', strtotime("+" . $sequence . " month")),
                ]);
            }

            $this->subscribe->db->trans_complete();
        } catch (Exception $e) {
            $this->subscribe->db->trans_rollback();
            alert('오류가 발생했습니다. 구독 정보를 다시 선택해주세요.', '/subscribe');
        }

        redirect('/order/index/' . $subscribe_idx);
    }

    public function popupAddress()
    {

    }
}

