<?php
/**
 * Created by PhpStorm.
 * User: jungmin
 * Date: 2018-08-25
 * Time: 오후 7:20
 */

class Admin_order_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getOrders($params = [], $limit = 20, $start = 0)
    {
        $bind = [];
        $where = [];
        $whereStr = '';

        if (!empty($params['status'])) {
            $where['status'] = ' p.status = ? ';
            $bind[] = $params['status'];
        }

        if (!empty($where)) {
            $whereStr = ' AND ' . implode(' AND ', $where);
        }

        $sql = " SELECT
                 m.name, p.payment_idx, o.order_idx, g.title, o.shipping_idx,
                 p.reg_dt, p.status, o.total_amount, 
                 s.subscribe_month, s.goods_idx,
                 pet.name as pet_name
            FROM
                subscribe s
                    JOIN subscribe_schedule ss ON s.subscribe_idx = ss.subscribe_idx
                    JOIN `order` o ON ss.subscribe_schedule_idx = o.subscribe_schedule_idx
                    LEFT JOIN payment p ON o.order_idx = p.order_idx
                    JOIN goods g ON g.goods_idx = s.goods_idx
                    JOIN pet pet ON s.pet_idx = pet.pet_idx
                    JOIN member m ON m.member_idx = o.member_idx
            WHERE 
                    s.use_fl = 'y' AND o.use_fl = 'y' AND p.use_fl = 'y' AND g.use_fl = 'y' "
            . $whereStr
            . "      limit " . $limit . " offset " . $start;

        $result = $this->db->query($sql, $bind)->result_array();
        //   echo $this->db->last_query();
        return $result;
    }

    public function updatePaymentStatus($params)
    {
        if (empty($params['status']) || empty($params['payment_idx'])) {
            return false;
        }

        if (!is_array($params['payment_idx'])) {
            $params['payment_idx'] = (array)$params['payment_idx'];
        }

        $this->db->where_in('payment_idx', $params['payment_idx']);

        $data['status'] = $params['status'];
        $data['edit_dt'] = date("Y-m-d H:i:s");
        $data['edit_idx'] = $this->session->userdata('member_idx');

        return $this->db->update('payment', $data);
    }

    public function getOrder($params = [])
    {
        $bind = [];
        $where = [];
        $whereStr = '';

        if (!empty($params['order_idx'])) {
            $where['order_idx'] = ' o.order_idx = ? ';
            $bind[] = $params['order_idx'];
        }

        if (!empty($where)) {
            $whereStr = ' AND ' . implode(' AND ', $where);
        }

        $sql = "
        SELECT 
            o.order_idx, o.member_idx, p.payment_idx, g.goods_idx, g.title, g.price
        FROM
            `order` o
            JOIN `payment` p ON o.order_idx = p.order_idx
            JOIN order_detail od ON o.order_idx = od.order_idx
            JOIN goods g ON od.goods_idx = g.goods_idx AND g.package_fl != 'y'
        WHERE
            o.use_fl = 'y' AND od.use_fl = 'y'
        " . $whereStr;

        $result = $this->db->query($sql, $bind)->result_array();
        //echo $this->db->last_query();
        return $result;
    }

    /**
     * 주문의 구성상품만 가져오고 싶은 경우
     * @param array $params
     * @return mixed
     */
    public function getOrderDetailGoods($params = [])
    {
        $bind = [];
        $where = [];
        $whereStr = '';

        if (!empty($params['order_idx'])) {
            $where['order_idx'] = ' o.order_idx = ? ';
            $bind[] = $params['order_idx'];
        }

        if (!empty($where)) {
            $whereStr = ' AND ' . implode(' AND ', $where);
        }

        $sql = "
        SELECT 
            group_concat(g.goods_idx) as goods_idx
        FROM
            `order` o
            JOIN `payment` p ON o.order_idx = p.order_idx
            JOIN order_detail od ON o.order_idx = od.order_idx
            JOIN goods g ON od.goods_idx = g.goods_idx AND g.package_fl != 'y'
        WHERE
            o.use_fl = 'y' AND od.use_fl = 'y'
        " . $whereStr;

        $result = $this->db->query($sql, $bind)->result_array();
        //echo $this->db->last_query();
        return $result;
    }

    public function deleteOrderDetail($params)
    {
        if (empty($params['order_idx']) || empty($params['goods_idx'])) {
            return false;
        }

        if (!is_array($params['goods_idx'])) {
            $params['goods_idx'] = (array)$params['goods_idx'];
        }
        $this->db->set('use_fl', 'n');
        $this->db->set('del_dt', date('Y-m-d H:i:s'));
        $this->db->set('del_idx', $this->session->userdata('member_idx'));

        $this->db->where('order_idx', $params['order_idx']);
        $this->db->where('use_fl', 'y');
        $this->db->where_in('goods_idx', $params['goods_idx']);

        $return = $this->db->update('order_detail');
        return $return;
    }

    public function insertOrderDetail($data)
    {
        $data['reg_dt'] = date('Y-m-d H:i:s');
        $data['use_fl'] = 'y';
        $data['member_idx'] = $this->session->userdata('member_idx');
        $data['reg_idx'] = $data['member_idx'];

        $query = $this->db->insert_string('order_detail', $data);
        //echo '<br><br>'.$this->db->last_query().'<br><br>';
        return $this->db->query($query);
    }
}