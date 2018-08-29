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
        $this->load->service('common_code', '', true);

        $this->checkLogin();
    }

    public function checkLogin()
    {
        if (empty($this->session->userdata('member_idx'))) {
            alert('로그인이 필요한 서비스입니다.', '/member');
        }

        return true;
    }

    public function index()
    {
        $this->dashBoard();
    }

    public function dashboard()
    {
        $this->load->model('order_model', 'order');
        $this->load->model('Subscribe_model', 'subscribe');

        $limit =3;
        $page = 0;
        $member_idx = $this->session->userdata('member_idx');
        $data['order_status'] = $this->common_code->getCode('order_status');
        $data['orders'] = $this->order->getOrderData(['member_idx' => $member_idx], $limit, $page);

        $data['subscribe_status'] = $this->common_code->getCode('subscribe_status');
        $data['subscribes'] = $this->subscribe->fetch_subscribe(['member_idx'=>$member_idx], $limit, $page);

        $data['action'] = 'dashboard';

        $this->load->view('common/header.html');
        $this->load->view('Accounts/dashboard.html', $data);
        $this->load->view('common/footer.html');
    }

    public function orders()
    {
        $member_idx = $this->session->userdata('member_idx');
        $this->load->model('order_model', 'order');

        $config = [];
        $config['base_url'] = base_url() . 'Accounts/orders/page/';
        $config['total_rows'] = $this->order->orders_count($member_idx);
        $config['per_page'] = 20;
        $config['uri_segment'] = 3;

        $this->pagination->initialize($config);
        $page = ($this->uri->segment(3)) ? ($this->uri->segment(3)) : 0;

        $data['order_status'] = $this->common_code->getCode('order_status');
        $data['results'] = $this->order->getOrderData(['member_idx'=>$member_idx], $config['per_page'], $page);
        $data['links'] = $this->pagination->create_links();
        $data['action'] = 'orders';

        $this->load->view('common/header.html');
        $this->load->view('Accounts/orders.html', $data);
        $this->load->view('common/footer.html');
    }

    public function subscribe()
    {
        $member_idx = $this->session->userdata('member_idx');
        $this->load->model('Subscribe_model', 'subscribe');

        $config = [];
        $config['base_url'] = base_url() . 'Accounts/subscribe/page/';
        $config['total_rows'] = $this->subscribe->subscribe_count($member_idx);
        $config['per_page'] = 20;
        $config['uri_segment'] = 3;

        $this->pagination->initialize($config);
        $page = ($this->uri->segment(3)) ? ($this->uri->segment(3)) : 0;

        $data['subscribe_status'] = $this->common_code->getCode('subscribe_status');
        $data['results'] = $this->subscribe->fetch_subscribe(['member_idx'=>$member_idx], $config['per_page'], $page);
        $data['links'] = $this->pagination->create_links();
        $data['action'] = 'subscribe';

        $this->load->view('common/header.html');
        $this->load->view('accounts/subscribe.html', $data);
        $this->load->view('common/footer.html');
    }

    public function card()
    {
        $this->load->view('common/header.html');
        $this->load->view('Accounts/card.html', ['action' => 'card']);
        $this->load->view('common/footer.html');
    }

    public function profile()
    {
        $this->load->view('common/header.html');
        $this->load->view('Accounts/profile.html', ['action' => 'profile']);
        $this->load->view('common/footer.html');
    }

    /**
     * 비밀번호 확인
     */
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

    /**
     * 주소변경
     */
    public function changeAddress()
    {
        $params = $_GET;
        $this->load->service('member_service', '', true);
        $this->load->model('Subscribe_model', 'subscribe');

        $address_idx = $this->member_service->addAddress([
            'zipcode' =>$params['zipcode'],
            'addr1st' =>$params['addr1st'],
            'addr2nd' =>$params['addr2nd']
        ]);

        if (empty($address_idx)) {
            echo 'fail';
            return false;
        }

        if ($this->subscribe->updateSubscribeSchedule([
            'address_idx' => $address_idx,
            'subscribe_idx' => $params['subscribe_idx']
        ])) {
            echo 'success';
        } else {
            echo 'fail';
        }
        return;
    }

    public function pauseSubscribe()
    {
        $subscribe_idx = $_GET['subscribe_idx'];
        if (empty($subscribe_idx)) {
            echo 'fail';
            return false;
        }

        $this->load->service('subscribe_service', '', true);

        if ($this->subscribe_service->pause($subscribe_idx)) {
            echo 'success';
            return true;
        } else {
            echo 'fail';
            return false;
        }
    }

    public function cancelSubscribe()
    {
        $subscribe_idx = $_GET['subscribe_idx'];
        if (empty($subscribe_idx)) {
            echo 'fail';
            return false;
        }

        $this->load->service('subscribe_service', '', true);

        if ($this->subscribe_service->cancel($subscribe_idx)) {
            echo 'success';
            return true;
        } else {
            echo 'fail';
            return false;
        }
    }

    public function restartSubscribe()
    {
        $subscribe_idx = $_GET['subscribe_idx'];
        if (empty($subscribe_idx)) {
            echo 'fail';
            return false;
        }

        $this->load->service('subscribe_service', '', true);

        if ($this->subscribe_service->restart($subscribe_idx)) {
            echo 'success';
            return true;
        } else {
            echo 'fail';
            return false;
        }
    }
}