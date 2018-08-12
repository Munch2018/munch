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
            alert('상품을 선택해주세요','/admin/inventory/selectGoods');
            return false;
        }

        $childGoods = $this->goods->getGoods(['goods_idx' => $goods_idx]);
        $this->load->view('admin/common/header');
        $this->load->view('admin/inventory/add-form', compact('childGoods'));
        $this->load->view('admin/common/footer');
    }

    public function add()
    {
        $params = $this->input->post();

        if (empty($params['goods_idx']) || count(array_filter($params['goods_idx'])) == 0) {
            alert('재고 정보가 없습니다. 다시 입력해주세요.', '/admin/inventory/selectGoods');
        }

        try {
            $this->inventory->db->trans_begin();

            for ($cnt = 0; $cnt < count($params['goods_idx']); $cnt++) {
                if (empty($params['goods_idx'][$cnt]) ||
                    $params['add_count'][$cnt] < 0
                ) {
                    return false;
                }
                $before_data = $this->inventory->getTotalCount($params['goods_idx'][$cnt]);

                $before_data = array_shift($before_data);
                if (!empty($params['add_count'][$cnt])) {
                    $before_data['total_count'] = $before_data['total_count'] + $params['add_count'][$cnt];
                }
                if (!empty($params['sub_count'][$cnt])) {
                    $before_data['total_count'] = $before_data['total_count'] - $params['sub_count'][$cnt];
                }

                $this->inventory->insert([
                    'goods_idx' => $params['goods_idx'][$cnt],
                    'add_count' => (int)$params['add_count'][$cnt],
                    'sub_count' => (int)$params['sub_count'][$cnt],
                    'total_count' => (int)$before_data['total_count'],
                    'receiving_dt' => $params['receiving_dt'][$cnt],
                    'expiry_dt' => $params['expiry_dt'][$cnt],
                    'memo' => $params['memo'][$cnt]
                ]);
            }

            $this->inventory->db->trans_complete();
        } catch (Exception $e) {
            $this->inventory->db->trans_rollback();
            alert('재고 등록에 실패하였습니다');
            return false;
        }
        alert('재고 등록을 완료하였습니다', '/admin/inventory/');
    }

    public function selectGoods()
    {
        $childGoods = $this->goods->getChildGoods();
        $this->load->view('admin/common/header');
        $this->load->view('admin/inventory/select-goods', compact('childGoods'));
        $this->load->view('admin/common/footer');
    }


}