<?php
/**
 * Created by PhpStorm.
 * User: jungmin
 * Date: 2018-07-28
 * Time: 오후 10:02
 */

class Card_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getData($member_idx, $customer_uid='')
    {
        $this->db->from('card');
        $this->db->where('member_idx', $member_idx);
        $this->db->where('use_fl', 'y');
        if (!empty($customer_uid)) {
            $this->db->where('customer_uid', $customer_uid);
        }
        return $this->db->get()->row_array();
    }

    public function insert($params)
    {
        $data = $params;
        $data['reg_dt'] = date('Y-m-d H:i:s');
        $data['reg_idx'] = $params['member_idx'];
        $data['use_fl'] = 'y';

        $this->db->set($data);
        return $this->db->insert('card');
    }

    public function delete($member_idx, $customer_uid = '')
    {
        if (empty($member_idx)) {
            return false;
        }
        $this->db->set('use_fl', 'n');
        $this->db->set('del_dt', date('Y-m-d H:i:s'));
        $this->db->set('del_idx', $member_idx, false);
        $this->db->where('member_idx', $member_idx);
        $this->db->where('use_fl', 'y');
        if (!empty($customer_uid)) {
            $this->db->where('customer_uid', $customer_uid);
        }

        return $this->db->update('card');
    }
}