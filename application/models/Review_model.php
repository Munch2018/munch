<?php
/**
 * Created by PhpStorm.
 * User: kimeu
 * Date: 2018-07-29
 * Time: 오후 3:19
 */

class Review_model extends CI_Model{

    public function __construct()
    {
        parent::__construct();
    }

    private function setWhere($where = [])
    {
        if (isset($where['where']) && !empty($where['where'])) {
            foreach ($where['where'] as $key => $value) {
                $this->db->where($key, $value);
            }
        }

        if (isset($where['offset']) && isset($where['limit'])) {
            $this->db->limit($where['limit'] , $where['offset']);
        }
    }

    /**
     * 리뷰 등록하는 모델
     */
    public function doRegister($data = [])
    {
        if (!empty($data)) {
            $data['reg_dt'] = date("Y-m-d H:i:s");
            $data['reg_idx'] = $this->session->userdata('member_idx');

            $this->db->trans_begin();
            $this->db->insert('review', $data);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
            } else {
                $this->db->trans_complete();
            }

            return ;

        } else {
            return false;
        }
    }
    
    /**
     * 리뷰리스트 가져오기
     */

    public function getList($where = [])
    {

        if (!empty($where)) {
            $this->setWhere($where);
        }

        $return = $this->db->get('review')->result_array();
       // echo $this->db->last_query();
        return $return;
    }

    public function getCount($where = [])
    {

        if (!empty($where)) {
            if (isset($where['offset'])) {
                unset($where['offset']);
            }

            if (isset($limit)) {
                unset($where['limit']);
            }

            $this->setWhere($where);
        }

        return $this->db->count_all_results('review');
//        $return = $this->db->count_all_results('review');
//
//        echo '<pre>';
//        print_r($this->db->last_query());
//        echo '</pre>';
//        return $return;

    }

    /*
     * review 하나만 가져오기
     * */
    public function getReview($where = [])
    {   
        if (!empty($where)) {
            $this->setWhere($where);
        }

        $result = $this->db->get('review')->row_array();
        return $result;
    }

    /**
     * review update처리해주기
     * 첫 parameter => set 할 데이터들 넣어준다.
     * 두번ㅉㅐ array 에서는 where 조건들 넣어준다.
     * return boolean
     */

    public function doUpdate($data = [] , $where = [])
    {
        if (!empty($where)) {

            $this->setWhere($where);

            $this->db->trans_begin();

            $this->db->update('review', $data);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                return false;
            } else {
                $this->db->trans_commit();

                return true;
            }
        } else {
            return false;
        }
        return false;
    }


    /**
     * review delete 처리
     * 두번ㅉㅐ array 에서는 where 조건들 넣어준다.
     * return boolean
     */

    public function doDelete($where = [])
    {
        if (!empty($where)) {
            $this->setWhere($where);

            $data['use_fl'] = 'n';

            // edit_dt & delete_dt 다 현재 시간으로 update 해주고
            // edid_idx && delete_idx 세션으로 어드민 아이디로 처리해주기

            $data['edit_dt'] = date("Y-m-d H:i:s");
            $data['del_dt'] = date("Y-m-d H:i:s");

            $data['edit_idx'] = 1;
            $data['del_idx'] = 1;

            $this->db->update('review', $data);

            return true;

        }else {
            return false;
        }

        return false;

    }



}