<?php
/**
 * Created by PhpStorm.
 * User: jungmin
 * Date: 2018-07-17
 * Time: 오후 9:33
 */


class Pet_manage extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    function getPets($member_idx, $pet_idx = 0)
    {
        $this->db->select('pet_idx, name, gender, birth, img_src, pet_type, pet_kind, character_type,detail1,detail2,detail3');
        $this->db->from('pet');
        $this->db->where('member_idx', $member_idx);
        if(!empty($pet_idx)){
            $this->db->where('pet_idx', $pet_idx);
        }
        $this->db->where('use_fl', 'y');

       $query = $this->db->get();
       return $query->result_array();
    }

    function insert($params)
    {
        $data = $params;

        $data['reg_dt'] = date('Y-m-d H:i:s');
        $data['member_idx'] = $this->session->userdata('member_idx');
        $data['reg_idx'] = $data['member_idx'];

        $query = $this->db->insert_string('pet', $data);
        return $this->db->query($query);
    }

    function update($params)
    {
        $data = $params;
        $data['edit_dt'] = date('Y-m-d H:i:s');
        $data['edit_idx'] = $this->session->userdata('member_idx');

        unset($data['pet_idx']);
        unset($data['member_idx']);

        $this->db->set($data);
        $this->db->where('pet_idx', $params['pet_idx']);
        $this->db->where('member_idx', $data['edit_idx']);

        return $this->db->update('pet');
    }
}