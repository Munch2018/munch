<?php
/**
 * Created by PhpStorm.
 * User: jungmin
 * Date: 2018-08-01
 * Time: 오후 10:19
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Order extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('Card_model', 'card_model');
    }

    private function validateOrder($subscribe_idx)
    {
        $this->load->model('order_model', 'order');

        if ($this->order->isOrder($subscribe_idx)) {
            alert('이미 결제가 진행된 구독정보입니다.');
            redirect('/accounts/');
        }
    }

    private function loginCheck(){
        if (empty($this->session->userdata('member_idx'))) {
            alert('로그인이 필요한 서비스입니다.', '/member/login_form/');
        }
    }

    public function index($subscribe_idx = 0)
    {
        $this->loginCheck();

        if (empty($subscribe_idx)) {
            alert('구독 정보가 전달되지 않았습니다.');
            return false;
        }

        if ($this->validateOrder($subscribe_idx)) {
            return false;
        }

        $this->load->model('Subscribe_model', 'subscribe');
        $this->load->model('Member_model', 'member');
        $member_idx = $this->session->userdata('member_idx');

        $data = [];

        $subscribe_info = $this->subscribe->getSubscribe([
            'subscribe_idx' => $subscribe_idx,
            'member_idx' => $member_idx
        ]);

        if (empty($subscribe_info)) {
            alert('구독정보가 정상적이지 않습니다. 구독을 재요청해주세요.');
            redirect('/subscribe/');
            return false;
        }

        $data['subscribe_info'] = array_shift($subscribe_info);
        $data['address'] = $this->member->getAddress(['member_idx' => $member_idx]);
        $data['card_info'] = $this->card_model->getData($member_idx);

        $this->load->view('common/header.html');
        $this->load->view('Order/index.html', $data);
        $this->load->view('common/footer.html');
    }

    public function add()
    {
        $params = $this->input->post();
        $subscribe_idx = $params['subscribe_idx'];

        if (empty($subscribe_idx)) {
            alert('구독 정보가 전달되지 않았습니다. 재시도해주세요.');
            return false;
        }

        try {
            $this->load->service('order_service', '', true);
            if ($this->order_service->add($params)) {
                alert('결제가 완료되었습니다.', '/order/complete?subscribe_idx=' . $subscribe_idx);
            }
        } catch (Exception $e) {
            alert('주문에 실패하였습니다. 재시도해주세요.');
            log_message('debug',$e->getMessage());
            return false;
        }
        redirect('/order/complete?subscribe_idx=' . $subscribe_idx);
    }

    public function complete()
    {
        $subscribe_idx = $_GET['subscribe_idx'];

        $this->load->model('member_model','member');
        $this->load->model('order_model', 'order');
        $this->load->service('common_code_service', '', true);
        $this->load->model('Subscribe_model', 'subscribe');

        $data = [];
        $member_idx = $this->session->userdata('member_idx');
        $order_info = $this->order->getOrderData([
            'member_idx' => $member_idx,
            'subscribe_idx' => $subscribe_idx
        ]);

        if (empty($order_info)) {
            alert('결제 정보가 없습니다. 결제를 시도해주세요.');
            redirect('/subscribe/index/' . $subscribe_idx);
        }

        $data['order_info'] = !empty($order_info) ? $order_info[0] : [];
        $data['dog_kind'] = $this->common_code_service->getCode('dog_kind');
        $data['cat_kind'] = $this->common_code_service->getCode('cat_kind');
        $data['order_status'] = $this->common_code_service->getCode('order_status');
        $data['subscribe_status'] = $this->common_code_service->getCode('subscribe_status');
        $data['subscribe'] = $this->subscribe->fetch_subscribe(['member_idx'=>$member_idx,'subscribe_idx'=>$subscribe_idx], 1, 0);

        $data['address_info'] = $this->member->getAddress([
            'member_idx' => $member_idx,
            'address_idx' => $data['order_info']['address_idx']
        ])[0];

        $next_subscribe_data = $this->subscribe->pendingNextSubscribeData($subscribe_idx);
        $data['next_subscribe_data'] = !empty($next_subscribe_data) ? $next_subscribe_data[0] : [];

        $this->load->view('common/header.html');
        $this->load->view('Order/complete.html', $data);
        $this->load->view('common/footer.html');
    }

    public function subscriptionComplete()
    {
        $subscribe_idx = $_GET['subscribe_idx'];

        $this->load->model('member_model','member');
        $this->load->model('order_model', 'order');
        $this->load->service('common_code_service', '', true);
        $this->load->model('Subscribe_model', 'subscribe');

        $data = [];
        $member_idx = $this->session->userdata('member_idx');
        $data['order_info_list'] = $this->order->getOrderData([
            'member_idx' => $member_idx,
            'subscribe_idx' => $subscribe_idx
        ]);
        $data['order_info'] = $data['order_info_list'][0];
        $data['dog_kind'] = $this->common_code_service->getCode('dog_kind');
        $data['cat_kind'] = $this->common_code_service->getCode('cat_kind');
        $data['order_status'] = $this->common_code_service->getCode('order_status');
        $data['subscribe_status'] = $this->common_code_service->getCode('subscribe_status');
        $data['subscribe'] = $this->subscribe->fetch_subscribe(['member_idx'=>$member_idx,'subscribe_idx'=>$subscribe_idx], 1, 0);
        $data['subscribe'] = array_shift($data['subscribe']);
        $data['last_pay_subscribe'] = $this->subscribe->getLastPaymentSubscribeSchedule(['subscribe_idx' => $subscribe_idx]);

        $this->load->view('common/header.html');
        $this->load->view('Order/subscription_complete.phtml', $data);
        $this->load->view('common/footer.html');
    }

    public function popupAddress()
    {
        $this->load->model('member_model', 'member');
        $member_idx = $this->session->userdata('member_idx');
        $data['address_list'] = $this->member->getAddress(['member_idx' => $member_idx]);

        $data['address_list_json'] = [];
        if (!empty($data['address_list'])) {
            foreach ($data['address_list'] as $k => $val) {
                $data['address_list_json'][$val['address_idx']] = $val;
            }
        }
        $data['address_list_json'] = json_encode($data['address_list_json']);
        $this->load->view('Order/popup-address.phtml', $data);
    }

    public function issueBilling()
    {
        $this->load->service('IMP_payment_service', '', true);
        $params = $_POST;

        $responseArr = $this->IMP_payment_service->getIssueBilling([
            'customer_uid' => $params['customer_uid'],
            'card_number' => $params['card_number'],
            'expiry' => $params['expiry'],
            'birth' => $params['birth'],
            'pwd_2digit' => $params['pwd_2digit'],
            'card_last_num' =>$params['card_last_num']
        ]);

        echo json_encode($responseArr);
        exit;
    }

    public function changeCard()
    {
        $params = $_POST;
        if (empty($params['customer_uid'])) {
            echo json_encode(['code' => 'fail']);
            exit;
        }

        if ($this->card_model->delete($this->session->userdata('member_idx'), $params['customer_uid'])) {
            echo json_encode(['code' => 'success']);
            exit;
        }
        echo json_encode(['code' => 'fail']);
        exit;
    }

    public function changeCardHtml()
    {
        if (!empty($_GET['page']) && $_GET['page'] == 'account') {
            echo json_encode(['code' => $this->load->view('Accounts/card-form.html')]);
        } else {
            echo json_encode(['code' => $this->load->view('Order/card-form.html')]);
        }
    }
}