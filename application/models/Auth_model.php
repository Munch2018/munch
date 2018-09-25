<?php
/**
 * Created by PhpStorm.
 * User: jungmin
 * Date: 2018-09-26
 * Time: 오전 1:20
 */

class Auth_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function updateToken($params)
    {
        if (empty($params['token']) || empty($params['member_sns_idx'])) {
            return false;
        }
        $this->db->set('token', $params['token']);
        $this->db->where('member_sns_idx', $params['member_sns_idx']);
        return $this->db->update('member_sns');
    }

    public function getMemberSns($params)
    {
        if (empty($params) || empty($params['email']) || empty($params['type'])) {
            return false;
        }

        $bind = [];
        $where = [];
        $whereStr = '';

        if (!empty($params['email'])) {
            $bind['email'] = $params['email'];
            $where[] = ' m.email = ? ';
        }

        if (!empty($params['type'])) {
            $bind['type'] = $params['type'];
            $where[] = ' ms.type = ? ';
        }

        if (!empty($where)) {
            $whereStr = ' AND ' . implode(' and ', $where);
        }

        $sql = '
            SELECT ms.member_sns_idx, m.member_idx, m.email, m.telphone, m.name, m.is_admin
             FROM
                member m JOIN
                member_sns ms ON m.member_idx = ms.member_sns_idx
            WHERE
                m.use_fl = \'y\' AND ms.use_fl = \'y\'
                    ' . $whereStr;

        return $this->db->query($sql, $bind)->row_array();
    }

    public function insertMemberSns($data = [])
    {
        $data['reg_dt'] = date("Y-m-d H:i:s");
        $this->db->trans_begin();
        $this->db->insert('member_sns', $data);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_complete();
        }

        return;
    }
}