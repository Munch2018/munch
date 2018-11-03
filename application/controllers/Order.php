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


    public function index($subscribe_idx = 0)
    {
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
            $this->order_service->add($params);
        } catch (Exception $e) {
            alert('주문에 실패하였습니다. 재시도해주세요.');
            trigger_error($e->getMessage());
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
            echo print_r($order_info,1);
            exit;
            alert('결제 시도에 실패하였습니다. 결제를 재시도해주세요.');
            redirect('Order/index/' . $subscribe_idx);
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
        $this->load->view('common/header.html');
        $this->load->view('Order/complete.html', $data);
        $this->load->view('common/footer.html');
    }

    public function subscribe_complete()
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
        $this->load->view('Order/subscribe_complete.phtml', $data);
        $this->load->view('common/footer.html');
    }
    public function popupAddress()
    {
        $this->load->model('member_model','member');
        $member_idx = $this->session->userdata('member_idx');
        $data['address_list'] = $this->member->getAddress(['member_idx'=>$member_idx]);

        $data['address_list_json'] = [];
        if (!empty($data['address_list'])) {
            foreach ($data['address_list'] as $k => $val) {
                $data['address_list_json'][$val['address_idx']] = $val;
            }
        }
        $data['address_list_json'] = json_encode($data['address_list_json']);
        $this->load->view('Order/popup-address.phtml', $data);
    }

    public function requestPayment()
    {

    }







    public function getToken()
    {
        /**
         * access token구하기
         */
        $post_data = [
            'imp_key' => self::IMP_REST_KEY,
            'imp_secret' => self::IMP_REST_SECRET
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.iamport.kr/users/getToken');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept: application/json',
            'Content-Type: application/json'
        ));

        $response = curl_exec($ch);
        $responseArr = json_decode($response);
        curl_close($ch);

        return ($responseArr->code === 0 && !empty($responseArr->response->access_token)) ? $responseArr->response->access_token : null;
    }

    public function issueBilling()
    {
        $params = $_POST;
        $params['access_token'] = $this->getToken();
        if (empty($params['access_token'])) {
            echo json_encode(['code' => -1]);
            exit;
        }
        /**
         * 빌링키 구하기
         */
        $post_data = [
            'card_number' => $params['card_number'],
            'expiry' => $params['expiry'],
            'birth' => $params['birth'],
            'pwd_2digit' => $params['pwd_2digit'],
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.iamport.kr/subscribe/customers/' . $params['customer_uid']);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept: application/json',
            'Content-Type: application/json',
            'Authorization: ' . $params['access_token']
        ));

        $response = curl_exec($ch);
        $responseArr = json_decode($response);
        $status_code = curl_getinfo($ch);

        curl_close($ch);

        if ($responseArr->code === 0
            && !empty($responseArr->response->customer_uid)
            && $responseArr->response->customer_uid === $params['customer_uid']) {

            $params['card_code'] = $responseArr->response->card_code;
            $params['card_name'] = $responseArr->response->card_name;
            $responseArr->response->card_last_num = $params['card_last_num'];
            $this->registerCardKey($params);
        }

        echo json_encode($responseArr);
        exit;
    }

    public function registerCardKey($params)
    {

        try {
            return $this->card_model->insert([
                'card_last_num' => $params['card_last_num'],
                'card_name' => $params['card_name'],
                'card_code' => $params['card_code'],
                'customer_uid' => $params['customer_uid'],
                'member_idx' => $this->session->userdata('member_idx')
            ]);
        } catch (Exception $e) {
            echo($e->getMessage());
        }
        return false;
    }

    public function changeCard()
    {
        $params = $_POST;
        if (empty($params['customer_uid'])) {
            echo json_encode(['code' => 'fail']);
            exit;
        }

        if ($this->card_model->delete($params['customer_uid'])) {
            echo json_encode(['code' => 'success']);
            exit;
        }
        echo json_encode(['code' => 'fail']);
        exit;
    }

    public function changeCardHtml()
    {
        echo json_encode(['code' => $this->load->view('Order/card-form.html')]);
    }



}