<?php
/**
 * Created by PhpStorm.
 * User: jungmin
 * Date: 2018-08-23
 * Time: 오후 11:05
 */

class Payment_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function insertPayment($data)
    {
        $data['reg_dt'] = date('Y-m-d H:i:s');
        $data['use_fl'] = 'y';
        $data['reg_idx'] = $data['member_idx'];

        $query = $this->db->insert_string('payment', $data);
        $this->db->query($query);
        return $this->db->insert_id();
    }

}