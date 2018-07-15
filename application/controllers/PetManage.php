<?php
/**
 * Created by PhpStorm.
 * User: jungmin
 * Date: 2018-07-11
 * Time: 오후 9:13
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class PetManage extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
    }

    public function index()
    {
        $this->load->model('Common_code', 'commonCode');
        $data['dog_breeds'] = $this->commonCode->get_codes('1');
        $data['cat_breeds'] = $this->commonCode->get_codes('2');
        $data['character'] = $this->commonCode->get_codes('3');

        $this->load->view('common/header');
        $this->load->view('PetManage/index', $data);
        $this->load->view('common/footer');
    }

    public function register()
    {
        $dd = $this->input->post();
        echo print_r($dd, 1);
        exit;
    }
}
