<?php
/**
 * Created by PhpStorm.
 * User: jungmin
 * Date: 2018-09-24
 * Time: 오후 9:03
 */


class Inventory extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function setWeekInventory()
    {

        $this->load->model('Admin_inventory', 'model');
        $data = $this->model->getGoodsCount();


    }
}
