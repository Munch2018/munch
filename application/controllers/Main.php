<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->load->model('Goods', 'goods');
        $parentGoods = $this->goods->getParentGoods();
        $childGoods = $this->goods->getChildGoods(['goods_use_fl' => 'y', 'package_fl'=>'n', 'use_fl'=>'y']);

        $this->load->view('common/header.html');
        $this->load->view('Main/index.phtml', ['parentGoods' => $parentGoods, 'childGoods' => $childGoods]);
        $this->load->view('common/footer.html');
    }

    public function goodsDetail()
    {
        $this->load->model('Goods', 'goods');
        $goods_idx = $this->input->get('goods_idx', 0);
        if (empty($goods_idx)) {
            return false;
        }

        $goods = $this->goods->getChildGoods(['goods_idx' => $goods_idx]);

        $this->load->view('Main/goods-detail.html', [
            'goods' => $goods[0]
        ]);
    }
}
