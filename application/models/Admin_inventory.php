<?php
/**
 * Created by PhpStorm.
 * User: jungmin
 * Date: 2018-08-12
 * Time: 오후 3:34
 */

class Admin_inventory extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getList()
    {
        $this->db->select('goods.goods_idx, goods.title,  goods.use_fl, inventory_history.reg_dt, inventory_history.expiry_dt');
        $this->db->from('inventory_history');
        $this->db->join('goods', 'inventory_history.goods_idx = goods.goods_idx');
        $this->db->where(' DATE(inventory_history.reg_dt) >= DATE(DATE_SUB(NOW(), INTERVAL 1 MONTH))');
        return $this->db->get()->result_array();

        return $this->db->query($query, [])->result_array();
    }
}