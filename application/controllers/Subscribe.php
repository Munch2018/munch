<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Subscribe extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->load->model('Pet_manage', 'petmanage');
        $member_idx = $this->session->userdata('member_idx');

        $data['pets'] = $this->petmanage->getPets($member_idx);

        $this->load->view('common/header');
        $this->load->view('Subscribe/index', $data);
        $this->load->view('common/footer');
    }
}
