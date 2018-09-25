<?php
/**
 * Created by PhpStorm.
 * User: jungmin
 * Date: 2018-08-25
 * Time: 오후 7:19
 */

class Orders extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
        $this->load->model('admin_order_model', 'model');
        $this->load->model('admin_goods_model', 'goods_model');
        $this->load->service('common_code_service', '', true);

        $this->load->service('admin_service', '', true);
        $this->admin_service->checkAdmin();
    }

    public function index()
    {
        $status = !empty($_GET['status']) ? $_GET['status'] : 'all';
        $data['order_status'] = $this->common_code_service->getCode('order_status');

        if (!empty($status) && !empty($data['order_status'][$status])) {
            $data['orders'] = $this->model->getOrders(['status' => $status]);
        } else {
            $data['orders'] = $this->model->getOrders();
        }

        $data['status'] = $status;
        $this->load->view('admin/common/header.html');
        $this->load->view('admin/orders/list.html', $data);
        $this->load->view('admin/common/footer.html');
    }

    /**
     * 주문건 상태 변경
     * @return bool
     */
    public function changeStatus()
    {
        $params = $this->input->post();
        $order_status = $this->common_code_service->getCode('order_status');

        if (empty($params['change_status'])) {
            alert('변경할 상태를 선택해주세요.');
            return false;
        }

        if (empty($params['payment_idx'])) {
            alert('변경할 주문을 선택해주세요.');
            return false;
        }

        if (empty($order_status[$params['change_status']])) {
            alert('변경할 상태가 유효하지 않습니다. 재선택해주세요.');
            return false;
        }

        if ($this->model->updatePaymentStatus([
            'payment_idx' => $params['payment_idx'],
            'status' => $params['change_status']
        ])) {
            alert('주문상태를 정상적으로 변경하였습니다.');
        } else {
            alert('주문상태 변경에 실패하였습니다.');
            return false;
        }

    }

    public function popupChangeChildGoods($order_idx = 0)
    {
        if (empty($order_idx)) {
            alert('주문번호가 넘어오지 않았습니다.');
            return false;
        }

        $data['order_idx'] = $order_idx;
        $data['currentOrderDetail'] = $this->model->getOrder(['order_idx' => $order_idx]);
        $data['allChildGoods'] = $this->goods_model->getChildGoods([]);

        $this->load->view('admin/orders/popup-change-child-goods.phtml', $data);
    }

    public function changeChildGoods()
    {
        $params = $this->input->post();

        if (empty($params) || empty($params['new_goods_idx']) || empty($params['order_idx'])) {
            return false;
        }
        $newOrderDetailGoods = $params['new_goods_idx'];
        $currentOrderDetailGoods = $this->model->getOrderDetailGoods(['order_idx' => $params['order_idx']]);
        $currentOrderDetailGoods = explode(',', $currentOrderDetailGoods[0]['goods_idx']);
        $deleteGoodsIdx = [];
        $insertGoodsIdx = [];

        foreach ($currentOrderDetailGoods as $k => $goods_idx) {
            if (!in_array($goods_idx, $newOrderDetailGoods)) {
                $deleteGoodsIdx[] = $goods_idx;
            }
        }
        foreach ($newOrderDetailGoods as $k => $goods_idx) {
            if (!in_array($goods_idx, $currentOrderDetailGoods)) {
                $insertGoodsIdx[] = $goods_idx;
            }
        }

        try {
            $this->model->db->trans_begin();

            //삭제 구성상품
            if (!empty($deleteGoodsIdx)) {
                $this->model->deleteOrderDetail(['order_idx' => $params['order_idx'], 'goods_idx' => $deleteGoodsIdx]);
            }

            //구성상품 인서트
            if (!empty($insertGoodsIdx)) {
                $goodsData = $this->goods_model->getChildGoods(['goods_idx' => $insertGoodsIdx, 'use_fl' => 'y']);
                $currentOrderDetail = $this->model->getOrder(['order_idx' => $params['order_idx']]);
                $insertData['member_idx'] = $currentOrderDetail[0]['member_idx'];

                if (!empty($goodsData)) {
                    foreach ($goodsData as $k => $goods) {
                        $insertData['goods_idx'] = $goods['goods_idx'];
                        $insertData['goods_name'] = $goods['title'];
                        $this->model->insertOrderDetail($insertData + ['order_idx' => $params['order_idx']]);
                    }
                }
            }

            $this->model->db->trans_complete();
            alert('정상적으로 상품을 변경하였습니다.','',1);
            return true;
        } catch (Exception $e) {
            $this->model->db->rollback();
            alert('상품 변경에 실패하였습니다.');
            return false;
        }
    }
}