<?php
/**
 * Created by PhpStorm.
 * User: eunju
 * Date: 2018-07-11
 * Time: 오후 11:35
 */

class Member_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    private function setWhere($where = array())
    {

        if (isset($where['where']) && !empty($where['where'])) {
            foreach ($where['where'] as $key => $value) {
                $this->db->where($key, $value);
            }
        }

    }

    /*
     * 아이디 기준으로 회원이 있는지 체크
     * */
    public function getMember($where = array())
    {
        if (!empty($where)) {
            $this->setWhere($where);
        }

        return $this->db->get('member')->row_array();


    }

    /**
     * 회원 리스트 가져오기
     */
    public function getMembers($where = array())
    {

        if (!empty($where)) {
            $this->setWhere($where);
        }

        $this->db->select('email, name, telphone, reg_dt, edit_dt, use_fl, (select count(*) as cnt from pet where member_idx = member.member_idx) as pet_is_regist');

        return $this->db->get('member')->result_array();
    }

    /**
     * 회원 total_count 가져오기
     */
    public function getCount($where = [])
    {
        if (!empty($where)) {
            $this->setWhere($where);
        }

//        return $this->db->get('member')->count_all_results();
        $return = $this->db->count_all('member');
//        echo '<pre>';
//        print_r($this->db->last_query());
//        echo '</pre>';
        return $return;
    }


    /**
     * 회원 가입
     */
    public function doRegister($data = array())
    {
        $data['reg_dt'] = date("Y-m-d H:i:s");
        $this->db->trans_begin();
        $this->db->insert('member', $data);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_complete();
        }

        return;
    }

    public function doUpdate($where = array(), $params = array())
    {
        $this->db->trans_begin();
        if (!empty($where)) {
            $this->setWhere($where);
        }

        $this->db->update('member', $params);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_complete();
        }

        return true;
    }

    public function getAddress($member_idx = 0, $address_idx = 0)
    {
        $this->db->select('address_idx, zipcode, addr1st, addr2nd, sort, nation');
        $this->db->from('address');
        $this->db->where('member_idx', $member_idx);
        $this->db->where('use_fl', 'y');
        if(!empty($address_idx)){
            $this->db->where('address_idx',$address_idx);
        }
        $this->db->order_by('sort', 'ASC');

        return $this->db->get()->result_array();
    }

    public function insertAddress($data)
    {
        if (empty($data)) {
            return false;
        }

        $data['use_fl'] = 'y';
        $data['reg_dt'] = date('Y-m-d H:i:s');
        $data['reg_idx'] = $data['member_idx'];

        $this->db->insert('address', $data);
        return $this->db->insert_id();
    }
}