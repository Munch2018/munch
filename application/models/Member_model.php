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
//        //member_idx없이 조회 -> sns 고객 제외
//        if (empty($where['member_idx'])) {
//            return $this->getMemberNotSns($where);
//        }

        if (!empty($where)) {
            $this->setWhere($where);
        }

        return $this->db->get('member')->row_array();
    }

    /**
     * sns로그인이 아닌 일반회원
     * @param $where
     * @return mixed
     */
    public function getMemberNotSns($where)
    {
        $this->db->select('member.*');
        $this->db->from('member');
        $this->db->join('member_sns', 'member.member_idx = member_sns.member_idx', 'LEFT');
        $this->db->where('member_sns.member_sns_idx is null');

        if (!empty($where['where']['use_fl'])) {
            $this->db->where('member.use_fl', $where['where']['use_fl']);
        }
        if (!empty($where['where']['email'])) {
            $this->db->where('member.email', $where['where']['email']);
        }

        return $this->db->get()->row_array();
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
        $this->db->order_by('member_idx','DESC');
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

        $return = $this->db->count_all_results('member');

        return $return;
    }


    /**
     * 회원 가입
     */
    public function doRegister($data = array())
    {
        $data['reg_dt'] = date("Y-m-d H:i:s");

        $this->db->insert('member', $data);
        return $this->db->insert_id();
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

    public function getAddress($params)
    {
        if (empty($params['member_idx'])) {
            return false;
        }

        $member_idx = $params['member_idx'];

        $this->db->select('address_idx, zipcode, addr1st, addr2nd, sort, nation, name, telphone');
        $this->db->from('address');
        $this->db->where('member_idx', $member_idx);
        $this->db->where('use_fl', 'y');
        if (!empty($params['address_idx'])) {
            $this->db->where('address_idx', $params['address_idx']);
        }
        if (!empty($params['zipcode'])) {
            $this->db->where('zipcode', $params['zipcode']);
        }
        if (!empty($params['addr1st'])) {
            $this->db->where('addr1st', $params['addr1st']);
        }
        if (!empty($params['addr2nd'])) {
            $this->db->where('addr2nd', $params['addr2nd']);
        }
        $this->db->order_by('sort', 'ASC');
        $this->db->order_by('address_idx', 'DESC');

        $result = $this->db->get()->result_array();
        return $result;
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