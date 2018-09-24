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
        $this->load->model('Member_model', 'member');

        $member_idx = $this->session->userdata('member_idx');

        $data = [];
        $data['address'] = $this->member->getAddress(['member_idx' => $member_idx]);
        $data['card_info'] = $this->card->getData($member_idx);
        $subscribe_info = $this->subscribe->getSubscribe(['subscribe_idx' => $subscribe_idx]);
        $data['subscribe_info'] = array_shift($subscribe_info);

        $this->load->view('common/header.html');
        $this->load->view('Order/index.html', $data);
        $this->load->view('common/footer.html');
    }

    public function add()
    {
        $params = $this->input->post();
        $subscribe_idx = $params['subscribe_idx'];

        if (empty($subscribe_idx)) {
            alert('구독 정보가 전달되지 않았습니다. 재시도해주세요.');
            return false;
        }

        try {
            $this->load->service('order_service', '', true);
            $this->order_service->add($params);
        } catch (Exception $e) {
            alert('주문에 실패하였습니다. 재시도해주세요.');
            trigger_error($e->getMessage());
            return false;
        }

        redirect('/order/complete?subscribe_idx='.$subscribe_idx);
}

    public function complete()
    {
        $subscribe_idx = $_GET['subscribe_idx'];

        $this->load->model('member_model','member');
        $this->load->model('order_model', 'order');
        $this->load->service('common_code', '', true);
        $this->load->model('Subscribe_model', 'subscribe');

        $data = [];
        $member_idx = $this->session->userdata('member_idx');
        $data['order_info'] = $this->order->getOrderData([
            'member_idx' => $member_idx,
            'subscribe_idx' => $subscribe_idx
        ])[0];

        $data['order_status'] = $this->common_code->getCode('order_status');
        $data['subscribe_status'] = $this->common_code->getCode('subscribe_status');
        $data['subscribe'] = $this->subscribe->fetch_subscribe(['member_idx'=>$member_idx,'subscribe_idx'=>$subscribe_idx], 1, 0);

        $data['address_info'] = $this->member->getAddress(['member_idx'=>$member_idx,'address_idx'=>$data['order_info']['address_idx']])[0];
        $this->load->view('common/header.html');
        $this->load->view('Order/complete.html', $data);
        $this->load->view('common/footer.html');
    }

    public function popupAddress()
    {
        $this->load->model('member_model','member');
        $member_idx = $this->session->userdata('member_idx');
        $data['address_list'] = $this->member->getAddress(['member_idx'=>$member_idx]);

        if (!empty($data['address_list'])) {
            foreach ($data['address_list'] as $k => $val) {
                $data['address_list_json'][$val['address_idx']] = $val;
            }
        }
        $data['address_list_json'] = json_encode($data['address_list_json']);
        $this->load->view('Order/popup-address.phtml', $data);
    }
}