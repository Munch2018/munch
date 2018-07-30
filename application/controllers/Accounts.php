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
        $this->load->helper('url');
        $this->load->library('pagination');
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
        $member_idx  = $this->session->userdata('member_idx');
        $this->load->model('order_model', 'order');

        $config = [];
        $config['base_url'] = base_url().'Accounts/orders';
        $config['total_rows'] = $this->order->orders_count($member_idx);
        $config['per_page'] = 5;
        $config['uri_segment'] = 3;

        $this->pagination->initialize($config);
        $page = ($this->uri->segment(3)) ? ($this->uri->segment(3)) : 0;

        $data['results'] = $this->order->fetch_orders($member_idx, $config['per_page'], $page);
        $data['links'] = $this->pagination->create_links();
        $data['action'] = 'orders';


        $this->load->view('common/header');
        $this->load->view('Accounts/orders', $data);
        $this->load->view('common/footer');
    }

    public function subscribe()
    {
        $member_idx  = $this->session->userdata('member_idx');
        $this->load->model('Subscribe_model', 'subscribe');

        $config = [];
        $config['base_url'] = base_url().'Accounts/subscribe';
        $config['total_rows'] = $this->subscribe->subscribe_count($member_idx);
        $config['per_page'] = 5;
        $config['uri_segment'] = 3;

        $this->pagination->initialize($config);
        $page = ($this->uri->segment(3)) ? ($this->uri->segment(3)) : 0;

        $data['results'] = $this->subscribe->fetch_subscribe($member_idx, $config['per_page'], $page);
        $data['links'] = $this->pagination->create_links();
        $data['action'] = 'subscribe';



        $this->load->view('common/header');
        $this->load->view('Accounts/subscribe', $data);
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

    public function confirmPassword()
    {
        $pwd = $this->input->get_post('pwd');
        $this->load->model('Member_model', 'member');
        $memberData = $this->member->getMember(['where' => ['member_idx' => $this->session->userdata('member_idx')]]);

        if (md5(trim($pwd)) == $memberData['password']) {
            echo json_encode(['code' => 'success']);
            exit;
        }

        echo json_encode(['code' => 'fail']);
    }
}
