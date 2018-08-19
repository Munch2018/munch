<?php
/**
 * Created by PhpStorm.
 * User: jungmin
 * Date: 2018-07-19
 * Time: 오후 10:54
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class About extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

    }

    public function index()
    {
        $this->load->view('common/header.html');
        $this->load->view('About/index.html');
        $this->load->view('common/footer.html');
    }
}