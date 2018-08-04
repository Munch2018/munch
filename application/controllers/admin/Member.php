<?php
/**
 * Created by PhpStorm.
 * User: kimeu
 * Date: 2018-08-04
 * Time: 오후 4:14
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Member extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->lists();
    }

    public function lists()
    {

        $this->load->view('admin/common/header');
        $this->load->view('admin/common/footer');
    }
}