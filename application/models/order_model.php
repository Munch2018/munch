<?php
/**
 * Created by PhpStorm.
 * User: kimeu
 * Date: 2018-07-25
 * Time: 오전 12:23
 */

class Order_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    private function setWhere($where = array())
    {
        if (isset($where['where']) && !empty($where['where'])) {
            foreach ($where['where'] as $key => $value) {
                $this->db->where($key, $value);
            }
        }

        if (isset($where['group_by']) && !empty($where['group_by'])) {
            $this->db->group_by($where['group_by']);
        }

    }

    /**
     * @param array $where
     * 단품 주문건에 대해서 가져오기
     * join order_detail
     */
    public function getOrders($where = array())
    {
        if (!empty($where)) {
            $this->setWhere($where);
        }
        /*
        goods_name,
        title, sell_price, pet_type,
        */
        $this->db->join('order_detail', 'order.order_idx = order_detail.order_idx', 'left');
        $this->db->join('goods', 'goods.goods_idx = order_detail.goods_idx', 'left');
        $this->db->join('goods_img', 'goods.goods_idx = goods_img.goods_idx');
        return $this->db->get('order')->result_array();
//        $return = $this->db->get('order')->result_array();
//        echo "<pre>";
//        print_r($this->db->last_query());
//        echo "</pre>";
//        return $return;

    }


    public function orders_count($member_idx)
    {
        $this->setWhere(['where'=>['member_idx' => $member_idx]]);
        return $this->db->count_all("order");
    }

    public function fetch_orders($member_idx, $limit, $start)
    {
        $this->db->limit($limit, $start);
        $this->setWhere(['where'=>['member_idx' => $member_idx]]);
        $query = $this->db->get('order');

        if ($query->num_rows() > 0) {
            $data = [];
            foreach ($query->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

}