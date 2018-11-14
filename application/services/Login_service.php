<?php
/**
 * Created by PhpStorm.
 * User: jungmin
 * Date: 2018-11-15
 * Time: 오전 12:43
 */

class Login_service extends MY_Service
{
    public function __construct()
    {
        $this->load->model('Auth_model', 'auth_model');
        $this->load->model('Member_model', 'member_model');
    }

    public function login($member_info)
    {
        $session_data = array(
            'member_idx' => $member_info['member_idx'],
            'email' => $member_info ['email'],
            'name' => $member_info['name'],
            'telphone' => !empty($member_info['telphone']) ? $member_info['telphone'] : '',
            'is_admin' => !empty($member_info['is_admin']) ? $member_info['is_admin'] : 0
        );

        if (!empty($member_info['is_admin']) && $member_info['is_admin'] === 1) {
            $session_data['is_admin'] = true;
        }

        $this->session->set_userdata($session_data);

        echo "<script type='text/javascript'>  opener.location.href = '/'; ; self.close(); </script>";
        exit;
    }

    public function join($member_info)
    {
        if (empty($member_info['email'])) {
            alert('로그인에 실패하였습니다.', '', 1);
            return false;
        }

        $join_data = array(
            'email' => $member_info['email'],
            'name' => $member_info['name'],
            'telphone' => '',
            'password' => '',
            'use_fl' => 'Y'
        );

        try {
            $this->member_model->db->trans_begin();
            $member_idx = $this->member_model->doRegister($join_data);

            if (!empty($member_idx)) {
                $join_sns_data['member_idx'] = $member_idx;
                $join_sns_data['token'] = $member_info['token'];
                $join_sns_data['refresh_token'] = $member_info['refresh_token'];
                $join_sns_data['type'] = $member_info['type'];
                $join_sns_data['use_fl'] = 'y';

                if ($this->auth_model->insertMemberSns($join_sns_data)) {
                    $join_data['member_idx'] = $member_idx;
                    $this->member_model->db->trans_complete();
                    $this->login($join_data);
                } else {
                    $this->member_model->db->trans_rollback();
                    alert('로그인에 실패하였습니다.', '', 1);
                    return false;
                }
            } else {
                $this->member_model->db->trans_rollback();
            }
        } catch (Exception $e) {
            $this->member_model->db->trans_rollback();
        }

        return false;
    }
}