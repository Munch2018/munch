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

        $this->load->model('Member_model', 'member');
        $userData = $this->session->get_userdata();

        if (empty($userData['member_idx'])) {
            return false;
        }

        $params['member_idx'] = $userData['member_idx'];

        //중복체크
        $address = $this->member->getAddress($params);
        if (!empty($address)) {
            return $address[0]['address_idx'];
        }

        $params['name'] = empty($params['name']) ? $userData['name'] : $params['name'];
        $params['telphone'] = empty($params['telphone']) ? $userData['telphone'] : $params['telphone'];

        return $this->member->insertAddress($params);
    }
}