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
        $this->load->model('Pet_manage', 'petmanage');
        $member_idx = $this->session->userdata('member_idx');

        $data['subscribe_list'] = $this->subscribe->getSubscribeGoodsPrice();
        $data['pets'] = $this->petmanage->getPets($member_idx);

        $this->load->view('common/header.html');
        $this->load->view('Subscribe/index.html', $data);
        $this->load->view('common/footer.html');
    }

    /**
     * 구독 추가
     */
    public function add()
    {
        $params = $this->input->get();
        $this->load->service('subscribe_service', '', true);

        $subscribe_idx = $this->subscribe_service->insert($params);

        if (!empty($subscribe_idx)) {
            redirect('/order/index/' . $subscribe_idx);
        } else {
            alert('오류가 발생했습니다. 구독 정보를 다시 선택해주세요.', '/subscribe');
        }
    }
}

