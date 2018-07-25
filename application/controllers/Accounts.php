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
        $this->dashBoard();
    }

    public function dashboard()
    {
        $this->load->view('common/header');
        $this->load->view('Accounts/dashboard', ['action' => 'dashboard']);
        $this->load->view('common/footer');
    }

    public function orders()
    {
        $this->load->view('common/header');
        $this->load->view('Accounts/orders', ['action' => 'orders']);
        $this->load->view('common/footer');
    }

    public function subscribe()
    {
        $this->load->view('common/header');
        $this->load->view('Accounts/subscribe', ['action' => 'subscribe']);
        $this->load->view('common/footer');
    }

    public function card()
    {
        $this->load->view('common/header');
        $this->load->view('Accounts/card', ['action' => 'card']);
        $this->load->view('common/footer');
    }

    public function profile()
    {
        $this->load->view('common/header');
        $this->load->view('Accounts/profile', ['action' => 'profile']);
        $this->load->view('common/footer');
    }
}
