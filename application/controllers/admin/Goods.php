<?php
/**
 * Created by PhpStorm.
 * User: jungmin
 * Date: 2018-08-09
 * Time: 오후 9:31
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Goods extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Admin_goods_model', 'model');
    }

    public function index()
    {
        $parentGoods = $this->model->getParentGoods();
        $childGoods = $this->model->getChildGoods();
        $this->load->view('admin/common/header');
        $this->load->view('admin/goods/index', compact('parentGoods', 'childGoods'));
        $this->load->view('admin/common/footer');
    }

    public function addForm()
    {
        $this->load->view('admin/common/header');
        $this->load->view('admin/goods/add-form');
        $this->load->view('admin/common/footer');
    }

    public function add()
    {
        $parentGoods = $this->model->getParentGoods();
        $childGoods = $this->model->getChildGoods();
        $this->load->view('admin/common/header');
        $this->load->view('admin/goods/add', compact('parentGoods', 'childGoods'));
        $this->load->view('admin/common/footer');
    }

    public function editForm($goods_idx = 0)
    {
        if (empty($goods_idx)) {
            alert('상품번호가 전달되지 않았습니다.', '/admin/goods/');
        }

        $this->load->view('admin/common/header');
        $this->load->view('admin/goods/edit-form');
        $this->load->view('admin/common/footer');
    }

    public function edit($goods_idx = 0)
    {
        if (empty($goods_idx)) {
            alert('상품번호가 전달되지 않았습니다.', '/admin/goods/');
        }

        $parentGoods = $this->model->getParentGoods();
        $childGoods = $this->model->getChildGoods();
        $this->load->view('admin/common/header');
        $this->load->view('admin/goods/edit', compact('parentGoods', 'childGoods'));
        $this->load->view('admin/common/footer');
    }

    public function delete($goods_idx = 0)
    {
        if (empty($goods_idx)) {
            alert('상품번호가 전달되지 않았습니다.', '/admin/goods/');
        }

        try {
            $this->model->db->trans_start();

            $this->model->deleteGoods($goods_idx);
            $this->model->deleteGoodsRelation(['parent_idx' => $goods_idx]);
            $this->model->deleteGoodsRelation(['child_idx' => $goods_idx]);

            $this->model->db->trans_complete();
        } catch (Exception $exception) {
            $this->model->db->rollback();
           alert('삭제처리가 실패하였습니다. 잠시후 재시도해주세요.');
        }
        redirect('/admin/goods/');
    }

    public function inventory()
    {
        $this->load->view('admin/common/header');
        $this->load->view('admin/goods/inventory');
        $this->load->view('admin/common/footer');
    }


}

