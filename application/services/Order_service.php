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
        $this->load->service('IMP_payment_service', '', true);
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
        $this->getNextSubscribeSchedule();
        $this->calculatePrice();

        try {
            $this->subscribe->db->trans_begin();

            //주소등록
            $this->setAddress();
            //구독테이블에 주소 업데이트
            $this->updateSubscribeScheduleAddress();

            $this->insertOrder();
            $this->insertOrderDetail();
            //결제 정보 등록
            $payment_idx = $this->insertPayment();

            //결제되는 구독정보에 결제idx 업데이트
            //$this->updateSubscribeSchedule($payment_idx);
            $this->subscribe->db->trans_complete();

            $requestResult = $this->IMP_payment_service->requestPayment($this->orderData + [
                    'customer_uid' => $data['customer_uid'],
                    'merchant_uid' => "pay_monthly_" . $payment_idx,
                    'payment_idx' => $payment_idx
                ]);

            if (empty($requestResult['status']) || $requestResult['status'] !== 'paid') {
                if (!empty($requestResult['fail_reason'])) {
                    alert('결제에 실패하였습니다. 재시도해주세요. (' . $requestResult['fail_reason'] . ')');
                }
                return false;
            }

            $this->registerNextSchedule($data['subscribe_idx']);
            return true;

        } catch (Exception $e) {
            log_message('debug', $e->getMessage());
            $this->subscribe->db->trans_rollback();
            return false;
        }
    }

    private function setAddress()
    {
        $address_idx = !empty($this->data['address_idx']) ? $this->data['address_idx'] : $this->addAddress($this->data);
        if (empty($address_idx)) {
            throw new Exception('insert Address fail');
        }

        return true;
    }

    private function updateSubscribeScheduleAddress()
    {
        if (!$this->subscribe->updateSubscribeSchedule([
            'address_idx' => $this->data['address_idx'],
            'subscribe_idx' => $this->data['subscribe_idx']
        ])) {
            throw new Exception('updateSubscribeSchedule fail');
        } else {
            return true;
        }
    }

    private function insertPayment()
    {
        $payment_idx = $this->payment_service->setPaymentData($this->orderData)->add();

        if (empty($payment_idx)) {
            throw new Exception('insertPayment fail');
        }
        return $payment_idx;
    }

    private function updateSubscribeSchedule($payment_idx)
    {
        //결제되는 구독정보에 결제idx 업데이트
        $updateSubscribeSchedule = $this->subscribe->updatePaymentIdxSubscribeSchedule([
            'sequence' => 0,
            'payment_idx' => $payment_idx,
            'schedule_dt' => date('Y-m-d'),
            'subscribe_idx' => $this->data['subscribe_idx']
        ]);

        if (empty($updateSubscribeSchedule)) {
            throw new Exception('updateSubscribeSchedule fail');
        }

        return $updateSubscribeSchedule;
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
            'nation' => !empty($data['nation']) ? $data['nation'] : '',
            'zipcode' => $data['zipcode'],
            'addr1st' => $data['addr1st'],
            'addr2nd' => $data['addr2nd']
        ]);
    }

    private function insertOrder()
    {
        $data = $this->data;

        $this->deleteOrderDataExists();

        $insert['member_idx'] = $this->member_idx;
        $insert['total_amount'] = $data['total_amount'];
        $insert['last_amount'] = $data['last_amount'];
        $insert['sale_amount'] = $data['sale_amount'];
        $insert['goods_name'] = $this->subscribeData['title'];
        $insert['buyer_name'] = $data['buyer_name'];
        $insert['buyer_phone'] = $data['buyer_phone'];
        //$insert['payment_method'] = 'nice';
        $insert['subscribe_idx'] = $data['subscribe_idx'];
        $insert['subscribe_schedule_idx'] = $data['subscribe_schedule_idx'];
        $insert['memo'] = $data['memo'];
        $insert['buyer_email'] = $data['buyer_email'];

        $this->orderData = $insert;
        $this->orderData['order_idx'] = $this->order->insertOrder($insert);

        if (empty($this->orderData['order_idx'])) {
            throw new Exception('insert order fail');
        }
        return $this->orderData['order_idx'];
    }

    private function deleteOrderDataExists()
    {
        try {
            $findOrderKey = [
                'subscribe_idx' => $this->data['subscribe_idx'],
                'subscribe_schedule_idx' => $this->data['subscribe_schedule_idx']
            ];

            $orders = $this->order->existOrderOfSubscribeIdx($findOrderKey);
            if (empty($orders['order_idx'])) {
                return false;
            }

            $findOrderKey['order_idx'] = explode(',', $orders['order_idx']);
            $this->order->deleteOrder($findOrderKey);
            $this->order->deleteOrderDetail($findOrderKey);
        } catch (Exception $e) {
            throw new Exception('deleteOrderDataExists fail');
        }
        return true;
    }


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
        //log_message('debug', var_export($this->subscribeData,1));

        $is_success = true;
        if (!empty($this->subscribeData)) {
            $childGoods = $this->getChildGoods($this->subscribeData['goods_idx']);
            if (!empty($childGoods)) {
                foreach ($childGoods as $k => $goods) {
                    $insert['member_idx'] = $this->member_idx;
                    $insert['goods_idx'] = $goods['goods_idx'];
                    $insert['goods_name'] = $goods['title'];
                    $insert['order_idx'] = $this->orderData['order_idx'];
                    if (!$this->order->insertOrderDetail($insert)) {
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

        if (empty($is_success)) {
            throw new Exception('insert order_detail fail');
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

    /**
     * 다음 결제 예약 시 주문,결제 데이터 make
     * @param $subscribe_idx
     * @param $nextData
     * @return string
     * @throws Exception
     */
    private function registerNextScheduleData($subscribe_idx, $nextData)
    {
        $lastSubscribeData = $this->subscribe->getLastPaymentSubscribeSchedule($subscribe_idx);
        if (empty($lastSubscribeData['order_idx'])) {
            throw new Exception('registerNextSchedule - lastSubscribeData order_idx empty');
        }

        $defaultData = [
            'member_idx' => $this->member_idx,
            'use_fl' => 'y',
            'reg_dt' => date('Y-m-d H:i:s'),
            'reg_idx' => $this->member_idx
        ];

        $orderData = $this->order->getOnlyOrderData($lastSubscribeData['order_idx']);
        $order_idx = 0;
        if (!empty($orderData)) {
            $order_idx = $this->order->insertOrder($orderData + $defaultData + [
                    'subscribe_schedule_idx' => $nextData['subscribe_schedule_idx']
                ]);
        }

        $orderDetailData = $this->order->getOnlyOrderDetailData($lastSubscribeData['order_idx']);
        $orderDetailData['order_idx'] = $order_idx;
        if (!empty($orderDetailData) && !empty($order_idx)) {
            foreach ($orderDetailData as $key => $value) {
                if (!is_array($value)) {
                    continue;
                }
                $this->order->insertOrderDetail($value + $defaultData);
            }
        } else {
            throw new Exception('registerNextSchedule - insertOrder fail');
        }

        $payment_idx = $this->payment_service->setPaymentData([
            'order_idx' => $order_idx,
            'last_amount' => $orderData['last_amount']
        ])->add();

        $card_info = $this->card_model->getData($this->member_idx);
        if (empty($payment_idx) || empty($card_info['customer_uid'])) {
            throw new Exception('registerNextSchedule - make paymentIdx or customer_uid fail');
        }

        $scheduleData = [
            'customer_uid' => $card_info['customer_uid'],
            'schedules' => [[
                'merchant_uid' => "pay_monthly_" . $payment_idx,
                'schedule_at' => strtotime($nextData['schedule_dt'] . ' 12:00:00'),
                'amount' => (int)$orderData['last_amount'],
                'name' => $orderData['goods_name'],
                'buyer_name' => $orderData['buyer_name'],
                'buyer_tel' => $orderData['buyer_phone'],
                'buyer_email' => $orderData['buyer_email']
            ]]
        ];

        return $this->IMP_payment_service->registerNextSchedule($scheduleData);
    }

    /**
     * 다음결제 예약하기
     * @param $subscribe_idx
     * @return bool
     * @throws Exception
     */
    public function registerNextSchedule($subscribe_idx)
    {
        try {
            $this->subscribe->db->trans_begin();
            $nextData = $this->subscribe->getNextSubscribeScheduleList($subscribe_idx)[0];

            if (empty($nextData) || empty($nextData['subscribe_schedule_idx'])) {
                //구독완료처리
                if ($this->subscribe->updateStatusSubscribe($subscribe_idx, 'complete')) {
                    $this->subscribe->db->trans_complete();
                    return true;
                } else {
                    return false;
                }
            } else {
                $this->registerNextScheduleData($subscribe_idx, $nextData);
                $this->subscribe->db->trans_complete();
            }

        } catch (Exception $e) {
            log_message('error', $e->getMessage());
            $this->subscribe->db->trans_rollback();
            return false;
        }
        return true;
    }
}