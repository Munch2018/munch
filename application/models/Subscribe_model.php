<?php
/**
 * Created by PhpStorm.
 * User: kimeu
 * Date: 2018-07-25
 * Time: 오전 12:23
 */

class Subscribe_model extends CI_Model
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

        if (isset($where['group_by']) && !empty($where['group_by'])) {
            $this->db->group_by($where['group_by']);
        }

    }

    /**
     * @param array $where
     * @return mixed
     */
    public function getSubscribe($where = array())
    {
        if (!empty($where)) {
            $this->setWhere($where);
        }

        return $this->db->get('subscribe')->result_array();


    }

    public function subscribe_count($member_idx)
    {
        $this->setWhere(['where' => ['member_idx' => $member_idx]]);
        return $this->db->count_all("subscribe");
    }

    public function fetch_subscribe($member_idx, $limit, $start)
    {
        $this->db->limit($limit, $start);
        $this->setWhere(['where' => ['member_idx' => $member_idx]]);
        $query = $this->db->get('subscribe');

        if ($query->num_rows() > 0) {
            $data = [];
            foreach ($query->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

}