<?php
/**
 * Created by PhpStorm.
 * User: kimeu
 * Date: 2018-07-21
 * Time: 오후 10:15
 */

class Board_model extends CI_Model{
    public function __construct(){
        parent::__construct();
    }

    private function setWhere($where = array()){
        if (isset($where['where']) && !empty($where['where'])) {
            foreach ($where['where'] as $key => $value) {
                $this->db->where($key, $value);
            }
        }

        if (isset($where['where_list']) && $where['where_list'] != "") {
            $this->db->where("board_type regexp ( SELECT code_common_idx from code_common where code_common_group_idx = " . $where['where_list']['code_common_group_idx'] . " and name = '" . $where['where_list']['board_type'] . "' )", null, false );
        }

    }

    public function doRegister($data = array()){
        if (!empty($data)) {

            $data['use_fl'] = 'Y';
            $data['reg_idx'] = $this->session->userdata('member_idx');
            $data['reg_dt'] = date('Y-m-d H:i:s');

            $this->db->trans_begin();
            $this->db->insert('board', $data);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
            } else {
                $this->db->trans_complete();
            }

            return true;
        } else {
            return false;
        }

    }

    public function getLists($where = array()){
        if (!empty($where)) {
            $this->setWhere($where);
        }

        $this->db->select('board_idx, board.title, board.contents, board_type, reg_idx');

        return $this->db->get('board')->result_array();

    }

    public function getCount($where = array()){
        if (!empty($where)) {
            $this->setWhere($where);
        }

        return $this->db->count_all_results('board');
//        $return = $this->db->count_all_results('board');
//        echo '<pre>';
//        print_r($this->db->last_query());
//        echo '</pre>';
//        return $return;
    }

    public function getBoard($where = array()){
        if (!empty($where)) {
            $this->setWhere($where);
        }

        return $this->db->get('board')->row_array();

    }

    public function doReplace($data = array()){
        if (!empty($data)){
            $data['edit_idx'] = $this->session->userdata('member_idx');
            $data['edit_dt'] = date('Y-m-d H:i:s');

            $this->db->trans_begin();

            $this->db->replace('board', $data);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
            } else {
                $this->db->trans_complete();
            }

            return true;
        } else {
            return false;
        }
    }

    public function doModify($data = array(), $where = array())
    {
        if (!empty($where)) {
            $this->setWhere($where);

            $data['edit_idx'] = $this->session->userdata('member_idx');
            $data['edit_dt'] = date('Y-m-d H:i:s');

            $this->db->update('board', $data);
            $this->db->trans_begin();

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
            } else {
                $this->db->trans_complete();
            }

            return true;
        } else {
            return false;
        }
    }

    public function doDelete($data = array(), $where = array())
    {
        if (!empty($where)) {
            $this->setWhere($where);

            $data['del_dt'] = date("Y-m-d H:i:s");
            $data['del_idx'] = $this->session->userdata('member_idx');

            $this->db->update('board', $data);

            $this->db->trans_begin();

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
            } else {
                $this->db->trans_complete();
            }

            return true;
        } else {
            return false;
        }
    }
}