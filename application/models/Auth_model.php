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

        $this->db->set('edit_dt', date('Y-m-d H:i:s'));
        $this->db->set('token', $params['token']);
        if (!empty($params['refresh_token'])) {
            $this->db->set('refresh_token', $params['refresh_token']);
        }
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
        if (!empty($params['member_sns_idx'])) {
            $bind['member_sns_idx'] = $params['member_sns_idx'];
            $where[] = ' ms.member_sns_idx = ? ';
        }
        if (!empty($params['token'])) {
            $bind['token'] = $params['token'];
            $where[] = ' ms.token = ? ';
        }
        if (!empty($params['refresh_token'])) {
            $bind['refresh_token'] = $params['refresh_token'];
            $where[] = ' ms.refresh_token = ? ';
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
                member_sns ms ON m.member_idx = ms.member_idx
            WHERE
                m.use_fl = \'y\' AND ms.use_fl = \'y\'
                    ' . $whereStr;

        $result = $this->db->query($sql, $bind)->row_array();
        echo $this->db->last_query().'<br><br>';
        echo print_r($result,1).'<br><br>';
        return $result;
    }

    public function insertMemberSns($data = [])
    {
        $data['use_fl'] = 'y';
        $data['reg_dt'] = date("Y-m-d H:i:s");
        return $this->db->insert('member_sns', $data);
    }
}