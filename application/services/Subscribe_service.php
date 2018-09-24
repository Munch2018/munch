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

            for ($sequence = 0; $sequence < $params['period']; $sequence++) {
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

        $member_idx  = $this->session->userdata('member_idx');
        $data = $this->subscribe_model->getSubscribe([
            'subscribe_idx' => $subscribe_idx,
            'use_fl' => 'y',
            'status' => 'pause',
            'member_idx' => $member_idx
        ]);

        if (empty($data)) {
            return false;
        }

        //구독은 재시작,
        if ($this->subscribe_model->updateStatusSubscribe($subscribe_idx, 'active')) {

            //구독 스케쥴은 재조정 - 다시시작할 스케쥴 회차날짜가 지난 경우 재조정
            $nextSchedule = $this->subscribe_model->getNextSubscribeScheduleList($subscribe_idx, 12);
            if (empty($nextSchedule[0]['schedule_dt'])) {
                alert('구독 재시작이 정상적이지 않습니다. 재시도해주세요.');
                return false;
            }

            if (!empty($nextSchedule[0]['schedule_dt']) && $nextSchedule[0]['schedule_dt'] <= date('Y-m-d')) {
                if ($nextSchedule) {
                    return $this->reSchedule($data[0]['start_date'], $nextSchedule);
                }
            }
        }
    }

    private function reSchedule($start_date, $nextSchedule)
    {
        $startSequence = $nextSchedule[0]['sequence'];
        $lastSequence = $nextSchedule[count($nextSchedule) - 1]['sequence'];
        $newScheduleDt = date('Y-m-d', strtotime($start_date . " +" . $startSequence . " month"));
        if ($newScheduleDt <= date('Y-m-d')) {
            $startSequence++;
            $lastSequence++;
        }

        $idx = 0;
        for ($sequence = $startSequence; $sequence <= $lastSequence; $sequence++) {
            $subscribe_schedule_idx = $nextSchedule[$idx]['subscribe_schedule_idx'];
            $newScheduleDt = date('Y-m-d', strtotime($start_date . " +" . $sequence . " month"));
            $this->subscribe_model->updateSubscribeSchedule([
                'schedule_dt' => $newScheduleDt,
                'subscribe_schedule_idx' => $subscribe_schedule_idx
            ]);

            $idx++;
        }
    }
}
