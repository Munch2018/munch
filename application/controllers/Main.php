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
        $this->load->model('Subscribe_model', 'subscribe');
        $this->load->model('Goods', 'goods');
        $this->load->model('Goods', 'goods');

        $data['parentGoods'] = $this->goods->getParentGoods();
        $data['childGoods'] = $this->goods->getChildGoods(['goods_use_fl' => 'y', 'package_fl'=>'n', 'use_fl'=>'y']);
        $data['goodsPrice'] = $this->subscribe->getSubscribeGoodsPrice(['month_count' => 12]);
        $this->load->view('common/header.html');
        $this->load->view('Main/index.phtml', $data);
        $this->load->view('common/footer.html');
    }

    public function goodsDetail()
    {
        $this->load->model('Goods', 'goods');
        $goods_idx = $this->input->get('goods_idx', 0);
        if (empty($goods_idx)) {
            return false;
        }

        $goods = $this->goods->getChildGoods(['goods_idx' => $goods_idx, 'goods_use_fl' => 'y', 'use_fl' => 'y']);

        $this->load->view('Main/goods-detail.html', [
            'goods' => $goods[0]
        ]);
    }
}
