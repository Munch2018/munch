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

    public function insertOrder($data)
    {
        $data['reg_dt'] = date('Y-m-d H:i:s');
        $data['use_fl'] = 'y';
        $data['member_idx'] = $this->session->userdata('member_idx');
        $data['reg_idx'] = $data['member_idx'];

        $query = $this->db->insert_string('order', $data);
        $this->db->query($query);
        return $this->db->insert_id();
    }

    public function insertOrderDetail($data)
    {
        $data['reg_dt'] = date('Y-m-d H:i:s');
        $data['use_fl'] = 'y';
        $data['member_idx'] = $this->session->userdata('member_idx');
        $data['reg_idx'] = $data['member_idx'];

        $query = $this->db->insert_string('order_detail', $data);
        return $this->db->query($query);
    }

    /**
     * @param $params
     * @return mixed
     */
    public function getOrders($params = [])
    {
        $bind = [];
        $where = [];
        $whereStr = '';

        $bind['member_idx'] = $this->session->userdata('member_idx');

        if (!empty($params['pet_idx'])) {
            $bind['pet_idx'] = $params['pet_idx'];
            $where[] = ' pet.pet_idx=? ';
        }
        if (!empty($where)) {
            $whereStr = ' and ' . implode(' and ', $where);
        }


        $sql = '
            SELECT 
                pet.pet_idx, g.goods_idx, g.title,g.title as goods_name, g.sell_price, 
                group_concat(distinct(gi.img_src) SEPARATOR \'|\') as  goods_img
            FROM
                pet pet
                    JOIN subscribe s ON pet.pet_idx = s.pet_idx
                    JOIN `order` o ON s.subscribe_idx = o.subscribe_idx
                    JOIN order_detail od ON o.order_idx = od.order_idx AND s.subscribe_idx = o.subscribe_idx
                    JOIN goods g ON od.goods_idx = g.goods_idx AND g.use_fl = \'y\' AND g.package_fl != \'y\'
                    JOIN goods_img gi ON g.goods_idx = gi.goods_idx AND gi.use_fl = \'y\'
                    JOIN payment p ON o.order_idx = p.order_idx
            WHERE
                pet.use_fl = \'y\' AND s.use_fl = \'y\'
                    AND o.use_fl = \'y\'
                    AND od.use_fl = \'y\'
                    AND o.member_idx = ? '
            . $whereStr
            . ' GROUP BY g.goods_idx ';

        $result = $this->db->query($sql, $bind)->result_array();
        //echo $this->db->last_query();
        return $result;

    }


    public function orders_count($member_idx)
    {
        $this->db->where('member_idx',$member_idx);
        return $this->db->count_all("order");
    }

    public function fetch_orders($member_idx, $limit, $start)
    {
        $this->db->limit($limit, $start);
        $this->db->where('member_idx',$member_idx);
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


    /**
     * 결제 시도건이있었는지
     * @param $subscribe_idx
     * @return mixed
     */
    public function isOrder($subscribe_idx)
    {
        $sql = " SELECT count(s.subscribe_idx) as cnt
            FROM
                subscribe s
                    JOIN subscribe_schedule ss ON s.subscribe_idx = ss.subscribe_idx
                    JOIN `order` o ON ss.subscribe_schedule_idx = o.subscribe_schedule_idx
                    JOIN order_detail od ON o.order_idx = od.order_idx
                    JOIN payment p ON o.order_idx = p.order_idx
                    JOIN goods g ON g.goods_idx = od.goods_idx
            WHERE 
                s.use_fl = 'y' AND ss.use_fl = 'y'
                    AND o.use_fl = 'y' AND p.use_fl = 'y'
                    AND od.use_fl = 'y'
                    AND g.use_fl = 'y'
                    AND s.member_idx = ?
                    AND s.subscribe_idx = ?
        ";

        $bind['member_idx'] =  $this->session->userdata('member_idx');
        $bind['subscribe_idx'] = $subscribe_idx;

        $result = $this->db->query($sql, $bind)->result();
        return $result[0]->cnt > 0;
    }

    public function getOrderData($params, $limit = 20, $start = 0)
    {
        if (empty($params['member_idx'])) {
            return [];
        }

        $bind = [];
        $where = [];
        $whereStr = '';
        $bind['use_fl'] = 'y';
        $bind['member_idx'] = $params['member_idx'];

        if (!empty($params['subscribe_idx'])) {
            $bind['subscribe_idx'] = $params['subscribe_idx'];
            $where[] = ' s.subscribe_idx = ? ';
        }

        if (!empty($where)) {
            $whereStr = ' AND '.implode(' and ', $where);
        }

        $sql = " SELECT  o.order_idx, o.reg_dt, o.total_amount, o.last_amount, 
                          g.title, p.reg_dt, p.status, 
                          s.subscribe_month, pet.name, pet.pet_size,
                          group_concat(od.goods_name) as detail_goods_names ,
                          GROUP_CONCAT(DISTINCT (ss.address_idx)) AS address_idx
            FROM
                subscribe s
                    JOIN subscribe_schedule ss ON s.subscribe_idx = ss.subscribe_idx
                    JOIN `order` o ON s.subscribe_idx = o.subscribe_idx
                    JOIN order_detail od ON o.order_idx = od.order_idx 
                    JOIN payment p ON o.order_idx = p.order_idx
                    JOIN goods g ON g.goods_idx = od.goods_idx
                    JOIN pet pet ON s.pet_idx = pet.pet_idx AND pet.use_fl='y'
            WHERE 
                    s.use_fl = ?
                    AND o.use_fl = 'y' AND od.use_fl = 'y' AND p.use_fl = 'y'
                    AND g.use_fl = 'y'
                    AND s.member_idx = ?
                    " . $whereStr
            . " limit " . $limit . " offset " . $start;

        return $this->db->query($sql, $bind)->result_array();
    }
}