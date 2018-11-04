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

    public function updatePaymentResult($params)
    {
        if ( empty($params['payment_idx'])) {
            return false;
        }

        $this->db->where('payment_idx', $params['payment_idx']);
        if (!empty($params['tid'])) {
            $data['tid'] = $params['tid'];
        }
        if (!empty($params['pg_provider'])) {
            $data['pg_provider'] = $params['pg_provider'];
        }
        if (!empty($params['memo'])) {
            $data['memo'] = $params['memo'];
        }
        $data['status'] = $params['status'];
        $data['edit_dt'] = date("Y-m-d H:i:s");
        $data['edit_idx'] = $this->session->userdata('member_idx');

        return $this->db->update('payment', $data);
    }

}