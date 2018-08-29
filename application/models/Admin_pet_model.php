<?php
/**
 * Created by PhpStorm.
 * User: jungmin
 * Date: 2018-08-26
 * Time: 오후 9:37
 */

class Admin_pet_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getList()
    {
        $bind = [];
        $sql = " SELECT 
                m.member_idx, m.name as member_name, m.email, m.telphone,
                p.name, p.pet_kind, p.pet_size, p.pet_type,
                p.detail1, p.detail2, p.detail3
            FROM
                member m
                    JOIN
                pet p ON m.member_idx = p.member_idx
            WHERE
                m.use_fl = 'y' AND p.use_fl = 'y'
                ";

        $result = $this->db->query($sql, $bind)->result_array();
        //   echo $this->db->last_query();
        return $result;
    }
}