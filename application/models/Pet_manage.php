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


    function getPets($member_idx)
    {
        $this->db->select('pet_idx, name, birth, img_src, pet_type, pet_kind, character_type,detail1,detail2,detail3');
        $this->db->from('pet');
        $this->db->where('mem_idx', $member_idx);
        $this->db->where('use_fl', 'y');

        return $this->db->get();
    }

    function insert($params)
    {
        $data = $params;
        $data['reg_dt'] = now();
        $data['reg_idx'] = $params['member_idx'];

        return $this->db->insert_string('pet', $data);
    }

    function update($params)
    {
        $data = $params;
        $data['edit_dt'] = now();
        $data['edit_idx'] = $params['member_idx'];

        uset($data['pet_idx']);
        uset($data['member_idx']);

        $this->db->where('pet_idx', $params['pet_idx']);
        $this->db->where('member_idx', $params['member_idx']);

        return $this->db->update('pet', $data);
    }


}