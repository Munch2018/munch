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
                 m.name, o.order_idx, g.title, 
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
}