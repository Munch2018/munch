<?php
/**
 * Created by PhpStorm.
 * User: eunju
 * Date: 2018-07-23
 * Time: 오후 11:33
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Review extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->load->view('common/header');
        $this->load->view('review/index');
        $this->load->view('common/footer');
    }


}