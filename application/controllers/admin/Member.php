<?php
/**
 * Created by PhpStorm.
 * User: kimeu
 * Date: 2018-08-04
 * Time: 오후 4:14
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Member extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('member_model', 'member');
    }

    public function index()
    {
        $this->lists();
    }

    public function lists()
    {

        $data = array();
        $where = array();

        // 회원 리스트 가져오는 model
        $data['list'] = $this->member->getMembers($where);
        $data['total_count'] = $this->member->getCount($where);

        $this->load->view('admin/common/header');
        $this->load->view('admin/member/lists', $data);
        $this->load->view('admin/common/footer');
    }
}