<?php
/**
 * Created by PhpStorm.
 * User: jungmin
 * Date: 2018-08-12
 * Time: 오후 3:45
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Inventory extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url'));

        $this->load->model('Admin_goods_model', 'goods');
        $this->load->model('Admin_inventory', 'inventory');
    }

    public function index()
    {
        $history = $this->inventory->getList();
        $this->load->view('admin/common/header');
        $this->load->view('admin/inventory/list', compact('history'));
        $this->load->view('admin/common/footer');
    }

    public function addForm()
    {
        $goods_idx = $this->input->post('goods_idx');

        if (empty($goods_idx)) {
            alert('상품을 선택해주세요');
            return false;
        }

        $childGoods = $this->goods->getGoods(['use_fl' => 'y', 'goods_idx' => $goods_idx]);
        $this->load->view('admin/common/header');
        $this->load->view('admin/inventory/add-form', compact('childGoods'));
        $this->load->view('admin/common/footer');
    }

    public function add()
    {

    }

    public function selectGoods()
    {
        $childGoods = $this->goods->getChildGoods();
        $this->load->view('admin/common/header');
        $this->load->view('admin/inventory/select-goods', compact('childGoods'));
        $this->load->view('admin/common/footer');
    }


}