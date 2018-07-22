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
        $childGoods = $this->goods->getChildGoods();

        $this->load->view('common/header');
        $this->load->view('Main/index', ['parentGoods' => $parentGoods, 'childGoods' => $childGoods]);
        $this->load->view('common/footer');
    }

    public function goodsDetail()
    {
        $this->load->model('Goods', 'goods');
        $goods_idx = $this->input->get('goods_idx', 0);
        if (empty($goods_idx)) {
            return false;
        }

        $goods = $this->goods->getChildGoods(['goods_idx' => $goods_idx]);

        $this->load->view('Main/goods-detail', [
            'goods' => $goods[0]
        ]);
    }
}
