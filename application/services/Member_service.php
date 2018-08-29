<?php
/**
 * Created by PhpStorm.
 * User: jungmin
 * Date: 2018-08-21
 * Time: 오후 8:01
 */

class Member_service extends MY_Service
{
    public function __construct()
    {
    }

    /**
     * 주소 추가
     * @param $params
     * @return bool
     */
    public function addAddress($params)
    {
        if (empty(array_filter($params))) {
            return false;
        }

        $member_idx = $this->session->userdata('member_idx');
        if (empty($member_idx)) {
            return false;
        }

        $params['member_idx'] = $member_idx;

        $this->load->model('Member_model', 'member');

        return $this->member->insertAddress($params);
    }
}