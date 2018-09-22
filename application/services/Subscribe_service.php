<?php
/**
 * Created by PhpStorm.
 * User: jungmin
 * Date: 2018-08-19
 * Time: 오후 3:51
 */

class Subscribe_service extends MY_Service
{
    public function __construct()
    {
        $this->load->model('Subscribe_model', 'subscribe_model');
    }

    public function insert($params)
    {
        $subscribe_idx = 0;
        $goods = $this->subscribe_model->getGoodsToBuy($params['pet_idx']);

        try {
            $this->subscribe_model->db->trans_begin();

            $member_idx = $this->session->userdata('member_idx');
            $subscribe_idx = $this->subscribe_model->insertSubscribe([
                'pet_idx' => $params['pet_idx'],
                'period' => $params['period'],
                'member_idx' => $member_idx,
                'goods_idx' => $goods[0]['goods_idx'],
                'buy_count' => 1
            ]);

            for ($sequence = 0; $sequence <= $params['period']; $sequence++) {
                $this->subscribe_model->insertSubscribeDetail([
                    'subscribe_idx' => $subscribe_idx,
                    'member_idx' => $member_idx,
                    'sequence' => $sequence,
                    'schedule_dt' => date('Y-m-d', strtotime("+" . $sequence . " month")),
                ]);
            }

            $this->subscribe_model->db->trans_complete();
        } catch (Exception $e) {
            $this->subscribe_model->db->trans_rollback();
            return false;
        }

        return $subscribe_idx;
    }

    public function pause($subscribe_idx)
    {
        if (empty($subscribe_idx)) {
            return false;
        }

        $data = $this->subscribe_model->getSubscribe([
            'subscribe_idx' => $subscribe_idx,
            'use_fl' => 'y',
            'member_idx' => $this->session->userdata('member_idx')
        ]);

        if (empty($data)) {
            return false;
        }

        return ($this->subscribe_model->updateStatusSubscribe($subscribe_idx,'pause'));
    }

    public function cancel($subscribe_idx)
    {
        if (empty($subscribe_idx)) {
            return false;
        }

        $data = $this->subscribe_model->getSubscribe([
            'subscribe_idx' => $subscribe_idx,
            'use_fl' => 'y',
            'member_idx' => $this->session->userdata('member_idx')
        ]);

        if (empty($data)) {
            return false;
        }

        return ($this->subscribe_model->updateStatusSubscribe($subscribe_idx, 'cancel'));
    }

    public function restart($subscribe_idx)
    {
        if (empty($subscribe_idx)) {
            return false;
        }

        $data = $this->subscribe_model->getSubscribe([
            'subscribe_idx' => $subscribe_idx,
            'use_fl' => 'y',
            'status' => 'pause',
            'member_idx' => $this->session->userdata('member_idx')
        ]);

        if (empty($data)) {
            return false;
        }

        return ($this->subscribe_model->updateStatusSubscribe($subscribe_idx, 'active'));
    }

    public function complete()
    {

    }

    public function changeAddressForPendingSubscribe()
    {

    }

}
