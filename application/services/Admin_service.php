<?php
/**
 * Created by PhpStorm.
 * User: jungmin
 * Date: 2018-09-22
 * Time: 오후 11:45
 */


class Admin_service extends MY_Service
{
    public function checkAdmin()
    {
        if (empty($this->session->userdata('member_idx'))) {
            alert('로그인을 해주세요.');
            redirect('/member/login_form/');
            return false;
        }

        if (empty($this->session->userdata('is_admin'))) {
            alert('관리자만 접속이 가능합니다.');
            redirect('/');
            return false;
        }
    }

}