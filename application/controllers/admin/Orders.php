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
        $this->load->service('common_code', '', true);

        $this->load->service('admin_service', '', true);
        $this->admin_service->checkAdmin();
    }

    public function index()
    {
        $status = !empty($_POST['status']) ? $_POST['status'] : 'all';
        $data['order_status'] = $this->common_code->getCode('order_status');

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
        $params = $_POST;
        echo print_r($params, 1);
        exit;
        return false;
    }

    public function popupChildGoods($goods_idx = 0)
    {
        $data['goods_idx'] = $goods_idx;
        $data['childGoods'] = $this->goods_model->getChildGoods(['parent_idx' => $goods_idx]);
        $data['allChildGoods'] = $this->goods_model->getChildGoods([]);
        $this->load->view('admin/common/header.html');
        $this->load->view('admin/orders/popup-child-goods.html', $data);
        $this->load->view('admin/common/footer.html');
    }
}