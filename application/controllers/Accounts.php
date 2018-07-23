<?php
/**
 * Created by PhpStorm.
 * User: jungmin
 * Date: 2018-07-23
 * Time: 오전 12:45
 */

class Accounts extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->load->view('common/header');
   //     $this->load->view('common/left-menu');
        $this->load->view('Accounts/index');
        $this->load->view('common/footer');
    }
}
