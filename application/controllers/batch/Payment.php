<?php
/**
 * 예약결제 등록페이지
 *
 * Created by PhpStorm.
 * User: jungmin
 * Date: 2018-09-24
 * Time: 오후 9:04
 */


class Payment extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 오늘 예약일인 구독 예약결제 등록
     * @param string $schedule_date
     */
    public function registerSchedulePayment($schedule_date = '')
    {
        $this->load->service('order_service', '', true);
        $this->load->model('/batch/Batch_subscribe_model', 'batch_subscribe_model');

        if (empty($schedule_date)) {
            $schedule_date = date('Y-m-d');
        }

        $activeSubscribeList = $this->batch_subscribe_model->getActiveSubscribe($schedule_date);
        if (count($activeSubscribeList) == 0) {
            exit;
        }

        $count = 0;
        foreach ($activeSubscribeList as $row => $data) {
            echo $data['subscribe_idx'] . '\n';
            $count += $this->order_service->registerNextSchedule($data['subscribe_idx'], $schedule_date);
        }

        echo '예약결제 전체 건수 : ' . number_format(count($activeSubscribeList)) . '\n\n';
        echo '예약결제 등록 성공 건수 : ' . number_format($count) . '\n\n';
        exit;
    }
}