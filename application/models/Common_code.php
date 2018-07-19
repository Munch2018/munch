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
        $sql = "SELECT  code, name " . PHP_EOL
            . " FROM code_common " . PHP_EOL
            . " WHERE code_common_group_idx = ? AND use_fl = 'y' ORDER BY code_common_idx ASC";

        return $this->db->query($sql, array($code_common_group_idx))->result_array();
    }

}