<?php
/**
 * Created by PhpStorm.
 * User: jungmin
 * Date: 2018-07-14
 * Time: 오후 5:45
 */


class Common_code extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param $code_common_group_idx
     * @return mixed
     */
    public function get_codes($code_common_group_idx)
    {
        $sql = "SELECT  code, name, name_extra " . PHP_EOL
            . " FROM code_common " . PHP_EOL
            . " WHERE code_common_group_idx = ? AND use_fl = 'y' ORDER BY code_common_idx ASC";

        return $this->db->query($sql, array($code_common_group_idx))->result_array();
    }

    /**
     * LIST 나 getData 같은거 가지고올때 where절 공통으로 사용하기위해 
     * 공통으로뺌
    */
    private function setWhere($where = array())
    {
        if (isset($where['where']) && !empty($where['where'])) {
            foreach ($where['where'] as $key => $value) {
                $this->db->where($key, $value);
            }
        }
    }

    /**
     * @param array $where
     * @return mixed
     */
    public function getCode($where = array())
    {
        if (!empty($where)) {
            $this->setWhere($where);
        }

        return $this->db->get('code_common')->row_array();
    }

    function insert($params)
    {
        $data = $params;
        $data['reg_dt'] = date('Y-m-d H:i:s');

        $query = $this->db->insert_string('code_common', $data);
        return $this->db->query($query);
    }
}