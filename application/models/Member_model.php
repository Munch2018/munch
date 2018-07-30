<?php
/**
 * Created by PhpStorm.
 * User: eunju
 * Date: 2018-07-11
 * Time: 오후 11:35
 */

class Member_model extends CI_Model{

    public function __construct(){
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
}