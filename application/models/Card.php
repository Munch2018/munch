<?php
/**
 * Created by PhpStorm.
 * User: jungmin
 * Date: 2018-07-28
 * Time: 오후 10:02
 */

class Card extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getData($member_idx)
    {
        $this->db->from('card');
        $this->db->where('member_idx', $member_idx);
        $this->db->where('use_fl', 'y');
        return $this->db->get();
    }

    public function insert($params)
    {
        $data = $params;
        $data['reg_dt'] = now();
        $data['reg_idx'] = $params['member_idx'];

        $this->db->set($data);
        return $this->db->insert('card');

    }

    public function delete($member_idx)
    {
        $this->db->set('use_fl', 'n', false);
        $this->db->where('member_idx', $member_idx);
        return $this->db->update('card');
    }

}