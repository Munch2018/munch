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
}