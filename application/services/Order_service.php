<?php

/**
 * Created by PhpStorm.
 * User: jungmin
 * Date: 2018-08-21
 * Time: 오후 10:17
 */
class Order_service extends MY_Service
{
    private $orderData = [];
    private $orderDetailData = [];
    private $subscribeData = [];
    private $data = [];
    private $member_idx = 0;

    public function __construct()
    {
        $this->member_idx = $this->session->userdata('member_idx');
        if (empty($this->member_idx)) {
            alert('로그인이 필요합니다. ', '/member/login_form/');
            return false;
        }

        $this->load->service('member_service', '', true);
        $this->load->service('payment_service', '', true);
        $this->load->model('Subscribe_model', 'subscribe');
        $this->load->model('order_model', 'order');
        $this->load->model('goods', 'goods');
    }

    public function checkDuplication($subscribe_idx)
    {
        return $this->order->isOrder($subscribe_idx);
    }

    public function add($data)
    {
        if ($this->checkDuplication($data['subscribe_idx'])) {
            alert('이미 주문건이 존재합니다.', '/order/index/' . $data['subscribe_idx']);
            return false;
        }

        $this->data = $data;
        $this->orderData = [];
        $this->orderDetailData = [];
        $this->getSubscribeData($data['subscribe_idx']);
        $this->calculatePrice();

        try {
            $this->subscribe->db->trans_begin();

            //주소등록
            $address_idx = !empty($data['address_idx']) ? $data['address_idx'] : $this->addAddress($data);

            $this->subscribe->updateSubscribeSchedule([
                'address_idx' => $address_idx,
                'subscribe_idx' => $data['subscribe_idx']
            ]);

            if (!$this->insertOrder() || $this->insertOrderDetail()) {
                $this->subscribe->db->trans_rollback();
                return false;
            }

            $payment_idx = $this->payment_service->setPaymentData($this->orderData)->add();

            $this->subscribe->updatePaymentIdxSubscribeSchedule([
                'sequence' => 0,
                'payment_idx' => $payment_idx,
                'schedule_dt' => date('Y-m-d'),
                'subscribe_idx' => $data['subscribe_idx']
            ]);

            $this->subscribe->db->trans_commit();
            return true;
        } catch (Exception $e) {
            $this->subscribe->db->trans_rollback();
            return false;
        }

        exit;
    }

    private function getChildGoods($goods_idx)
    {
        if (empty($goods_idx)) {
            return [];
        }

        return $childGoods = $this->goods->getChildGoods(['parent_idx' => $goods_idx, 'use_fl' => 'y']);
    }

    private function addAddress($data)
    {
        return $this->member_service->addAddress([
            'nation' => $data['nation'],
            'zipcode' => $data['zipcode'],
            'addr1st' => $data['addr1st'],
            'addr2nd' => $data['addr2nd']
        ]);
    }

    private function insertOrder()
    {
        $data = $this->data;

        $insert['member_idx'] = $this->member_idx;
        $insert['total_amount'] = $data['total_amount'];
        $insert['last_amount'] = $data['last_amount'];
        $insert['sale_amount'] = $data['sale_amount'];
        $insert['goods_name'] = $this->subscribeData['title'];
        $insert['buyer_name'] = $data['buyer_name'];
        $insert['buyer_phone'] = $data['buyer_phone'];
        $insert['payment_method'] = 'nicepay';
        $insert['subscribe_idx'] = $data['subscribe_idx'];
        $insert['subscribe_schedule_idx'] = $this->getNextSubscribeSchedule();
        $insert['memo'] = $data['memo'];
        $insert['buyer_email'] = $data['buyer_email'];

        $this->orderData = $insert;
        $this->orderData['order_idx'] = $this->order->insertOrder($insert);

        return $this->orderData['order_idx'];
    }

    /**
     * @todo
     * 임시!
     */
    private function insertOrderDetail()
    {
//        if (!empty($this->subscribeDetailData)) {
//            foreach ($this->subscribeDetailData as $key => $detail) {
//                $insert['member_idx'] = $this->member_idx;
//                $insert['goods_idx'] = $detail['goods_idx'];
//                $insert['subscribe_detail_idx'] = $detail['subscribe_detail_idx'];
//                $insert['order_idx'] =  $this->orderData['order_idx'];
//                $this->order->insertOrderDetail($insert);
//            }
//        }
        log_message('debug', var_export($this->subscribeData,1));

        $is_success = true;
        if (!empty($this->subscribeData)) {
            $childGoods = $this->getChildGoods($this->subscribeData['goods_idx']);
            if (!empty($childGoods)) {
                foreach ($childGoods as $k => $goods) {
                    $insert['member_idx'] = $this->member_idx;
                    $insert['goods_idx'] = $goods['goods_idx'];
                    $insert['goods_name'] = $goods['title'];
                    $insert['order_idx'] = $this->orderData['order_idx'];
                    if(!$this->order->insertOrderDetail($insert)){
                        $is_success = false;
                    }
                }
            } else {
                $insert['member_idx'] = $this->member_idx;
                $insert['goods_idx'] = $this->subscribeData['goods_idx'];
                $insert['goods_name'] = $this->orderData['goods_name'];
                $insert['order_idx'] = $this->orderData['order_idx'];
                $is_success = $this->order->insertOrderDetail($insert);
            }
        }
        return $is_success;
    }

    private function getSubscribeData()
    {
        $subscribes = $this->subscribe->getSubscribe([
            'subscribe_idx' => $this->data['subscribe_idx'],
            'member_idx' => $this->member_idx,
            'use_fl' => 'y'
        ]);

        $this->subscribeData = $subscribes[0];
    }

    private function getNextSubscribeSchedule()
    {
        return $this->data['subscribe_schedule_idx'] = $this->subscribe->getNextSubscribeScheduleList($this->data['subscribe_idx'])[0]['subscribe_schedule_idx'];
    }

    private function calculatePrice()
    {
        $this->data['total_amount'] = (int)$this->subscribeData['price'] * (int)$this->subscribeData['buy_count'];
        $this->data['last_amount'] = (int)$this->subscribeData['sell_price'] * (int)$this->subscribeData['buy_count'];
        $this->data['sale_amount'] = $this->data['total_amount'] - $this->data['last_amount'];
    }
}