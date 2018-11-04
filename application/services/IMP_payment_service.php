<?php
/**
 * Created by PhpStorm.
 * User: jungmin
 * Date: 2018-11-04
 * Time: 오전 3:35
 */

class IMP_payment_service extends MY_Service
{
  
    public function __construct()
    {
        $this->load->model('payment_model', 'payment_model');
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

    /**
     * 빌링키 구하기
     * @param $params
     * @return bool|mixed
     */
    public function getIssueBilling($params)
    {
        $access_token = $this->getToken();
        if (empty($access_token)) {
            return false;
        }

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
            'Authorization: ' . $access_token
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

            if ($this->registerCardKey($params)) {
                return $responseArr;
            }
        }

        return false;
    }


    public function registerCardKey($params)
    {
        $this->load->model('Card_model', 'card_model');

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

    public function requestPayment($params)
    {
        $access_token = $this->getToken();
        if (empty($access_token)) {
            return false;
        }

        $post_data = [
            'customer_uid' => $params['customer_uid'],
            'merchant_uid' => $params['merchant_uid'], // 새로 생성한 결제(재결제)용 주문 번호
            'amount' => $params['last_amount'],
            'name' => $params['goods_name']
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.iamport.kr/subscribe/payments/again');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: ' . $access_token
        ));

        $response = curl_exec($ch);
        $responseArr = json_decode($response);

        $status_code = curl_getinfo($ch);

        curl_close($ch);

        $result = [];
        $result['code'] = $responseArr->code;
        $result['message'] = $responseArr->message;
        $result['status'] = $responseArr->response->status;
        $result['fail_reason'] = $responseArr->response->fail_reason;

        if ($responseArr->code === 0 && $responseArr->response->status === 'paid'
            && $responseArr->response->amount == $params['last_amount']) {
            $result['updateResult'] = $this->updatePaymentResult($params + [
                    'status' => 'pay_complete',
                    'payment_idx' => $params['payment_idx'],
                    'tid' => $responseArr->response->pg_tid,
                    'pg_provider' => $responseArr->response->pg_provider,
                ]);

            $this->registerNextSchedule($params);

        } else {
            $result['updateResult']  = $this->updatePaymentResult([
                'status' => 'pay_fail',
                'payment_idx' => $params['payment_idx'],
                'pg_provider' => $responseArr->response->pg_provider,
                'memo' => $responseArr->response->fail_reason
            ]);
        }

        return $result;
    }

    public function updatePaymentResult($params)
    {
        if (empty($params['payment_idx'])) {
            return false;
        }

        try {
            $params['member_idx']= $this->session->userdata('member_idx');

            return $this->payment_model->updatePaymentResult($params);
        } catch (Exception $e) {
            echo($e->getMessage());
        }
        return false;
    }

    public function payGateLog($params)
    {

    }

    public function registerNextSchedule($params)
    {
        $access_token = $this->getToken();
        if (empty($access_token)) {
            return false;
        }

        $post_data = [
            'customer_uid' => $params['customer_uid'],
            'schedules' => $params['schedules'], // 새로 생성한 결제(재결제)용 주문 번호
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.iamport.kr/subscribe/payments/schedule`');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: ' . $access_token
        ));

        $response = curl_exec($ch);
        $responseArr = json_decode($response);


        echo var_export($responseArr,1);
        exit;
        $status_code = curl_getinfo($ch);
        curl_close($ch);

        //성공

    }
}