<?php
/**
 * Created by PhpStorm.
 * User: kimeu
 * Date: 2018-07-25
 * Time: 오전 12:23
 */

class Subscribe_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getSubscribeGoodsPrice()
    {
        $this->db->where('use_fl', 'y');
        return $this->db->get('subscribe_price')->result_array();
    }

    public function getSubscribe($params = [])
    {
        $whereStr = '';
        $where= [];
        $bind = [];

        if (!empty($params['member_idx'])) {
            $where[] = 's.member_idx = ?';
            $bind['member_idx'] = $params['member_idx'];
        }
        if(!empty($params['subscribe_idx'])) {
            $where[] = 's.subscribe_idx = ?';
            $bind['subscribe_idx'] = $params['subscribe_idx'];
        }
        if(!empty($params['use_fl'])) {
            $where[] = 's.use_fl = ?';
            $bind['use_fl'] = $params['use_fl'];
        }
        if (!empty($where)) {
            $whereStr = 'WHERE ' . implode(' and ', $where);
        }

        $sql = '
        SELECT 
            s.subscribe_idx, s.subscribe_month, s.goods_idx, s.buy_count,
            g.title, s.start_date,
            sp.sell_price, sp.price,
            p.pet_kind, p.pet_size
        FROM
            subscribe s 
            JOIN pet p ON s.pet_idx = p.pet_idx
            JOIN goods g ON s.goods_idx = g.goods_idx AND g.use_fl = \'y\'
            JOIN subscribe_price sp ON s.subscribe_month = sp.month_count AND sp.use_fl = \'y\'
        '.$whereStr;

       $result = $this->db->query($sql, $bind)->result_array();
       //echo $this->db->last_query();
       return $result ;
    }

    /**
     * 미결제 다음 구독 스케쥴
     * @param $subscribe_idx
     * @param int $limit
     * @return mixed
     */
    public function getNextSubscribeScheduleList($subscribe_idx, $limit = 1)
    {
        $sql = '
            SELECT 
                ss.schedule_dt, ss.subscribe_schedule_idx, ss.sequence
            FROM
                subscribe_schedule ss
                    LEFT JOIN
                `order` o ON ss.subscribe_schedule_idx = o.subscribe_schedule_idx
                    AND o.use_fl = \'y\'
                    LEFT JOIN
                payment p ON o.order_idx = p.order_idx
                    AND p.use_fl = \'y\'
            WHERE
                ss.subscribe_idx = ?
                  AND ((o.order_idx IS NULL
                    AND p.payment_idx IS NULL) OR p.status =\'pay_fail\')
                    AND ss.schedule_dt >= CURRENT_DATE 
            ORDER BY ss.schedule_dt ASC, ss.sequence ASC
            LIMIT '.$limit;

        $result = $this->db->query($sql, [$subscribe_idx])->result_array();
        return $result;
    }

    /**
     * 예약된 결제 포함 다음결제정보
     * @param $subscribe_idx
     * @return mixed
     */
    public function pendingNextSubscribeData($subscribe_idx)
    {
        $sql = '
        SELECT 
             subscribe_schedule.schedule_dt, subscribe_schedule.subscribe_schedule_idx, subscribe_schedule.sequence
        FROM
            subscribe_schedule
                LEFT JOIN
            `order` o ON subscribe_schedule.subscribe_schedule_idx = o.subscribe_schedule_idx
                AND o.use_fl = \'y\'
                LEFT JOIN
            payment ON o.order_idx = payment.order_idx
                AND payment.use_fl = \'y\'
        WHERE
            ((payment.payment_idx IS NULL
                AND o.order_idx IS NULL)
                OR payment.status IN (\'pay_fail\' , \'pay_pending\'))
                AND subscribe_schedule.use_fl = \'y\'
                AND subscribe_schedule.subscribe_idx = ?
                         ';


        $result = $this->db->query($sql, [$subscribe_idx])->result_array();
        return $result;
    }

    public function subscribe_count($params)
    {
        $this->db->select(' subscribe.subscribe_idx,
                        subscribe.subscribe_month,
                        subscribe.start_date,
                        subscribe.end_date,
                        subscribe.goods_idx,
                        subscribe.buy_count,
                        subscribe.status,
                        goods.title,
                        (subscribe_price.sell_price * subscribe_price.month_count) AS total_amount,
                        pet.name,
                        ( select min(subscribe_schedule.schedule_dt)  from subscribe_schedule
                            LEFT join payment on subscribe_schedule.payment_idx = payment.payment_idx and payment.use_fl=\'y\' and payment.status in (\'\',\'pay_pending\')
                            where subscribe_schedule.use_fl=\'y\' and subscribe_schedule.subscribe_idx=subscribe.subscribe_idx) 
                         as schedule_dt
                        ');

        $this->db->from('subscribe');
        $this->db->join('pet','subscribe.pet_idx = pet.pet_idx');
        $this->db->join('goods','subscribe.goods_idx = goods.goods_idx ');
        $this->db->join('subscribe_price','subscribe.subscribe_month = subscribe_price.month_count AND subscribe_price.use_fl = \'y\'');

        $this->db->where('subscribe.member_idx', $params['member_idx']);

        if (!empty($params['subscribe_idx'])) {
            $this->db->where('subscribe.subscribe_idx', $params['subscribe_idx']);
        }

        $this->db->order_by('subscribe.subscribe_idx', 'DESC');
        $result = $this->db->get()->result_array();
        return count($result);
    }

    public function fetch_subscribe($params, $limit, $start)
    {
        $this->db->select(' subscribe.subscribe_idx,
                        subscribe.subscribe_month,
                        subscribe.start_date,
                        subscribe.end_date,
                        subscribe.goods_idx,
                        subscribe.buy_count,
                        subscribe.status,
                        goods.title,
                        (subscribe_price.sell_price * subscribe_price.month_count) AS total_amount,
                        pet.name,
                        pet.pet_idx,
                        ( SELECT  MIN(subscribe_schedule.schedule_dt)
                        FROM subscribe_schedule
                                LEFT JOIN `order` o ON subscribe_schedule.subscribe_schedule_idx = o.subscribe_schedule_idx AND o.use_fl = \'y\'
                                LEFT JOIN payment ON o.order_idx = payment.order_idx AND payment.use_fl = \'y\'
                        WHERE
                            ((payment.payment_idx IS NULL AND o.order_idx IS NULL) OR payment.status IN (\'pay_fail\' , \'pay_pending\'))
                                AND subscribe_schedule.use_fl = \'y\' AND subscribe_schedule.subscribe_idx = subscribe.subscribe_idx) AS schedule_dt
                        ');

        $this->db->from('subscribe');
        $this->db->join('pet','subscribe.pet_idx = pet.pet_idx');
        $this->db->join('goods','subscribe.goods_idx = goods.goods_idx ');
        $this->db->join('subscribe_price','subscribe.subscribe_month = subscribe_price.month_count AND subscribe_price.use_fl = \'y\'');

        $this->db->limit($limit, $start);
        $this->db->where('subscribe.member_idx', $params['member_idx']);

        if (!empty($params['subscribe_idx'])) {
            $this->db->where('subscribe.subscribe_idx', $params['subscribe_idx']);
        }

        $this->db->order_by('subscribe.subscribe_idx', 'DESC');
        $result = $this->db->get()->result_array();
        //echo $this->db->last_query();
        return $result;
    }

    public function insertSubscribe($params)
    {
        $this->db->set([
            'member_idx' => $params['member_idx'],
            'pet_idx' => $params['pet_idx'],
            'subscribe_month' => $params['period'],
            'goods_idx' => $params['goods_idx'],
            'buy_count' => $params['buy_count'],
            'status' => 'active',
            'start_date' => date('Y-m-d'),
            'end_date' => date('Y-m-d', strtotime("+" . $params['period'] . " month")),
            'use_fl' => 'y',
            'reg_dt' => date('Y-m-d H:i:s'),
            'reg_idx' => $params['member_idx'],
        ]);

        $this->db->insert('subscribe');
        return $this->db->insert_id();
    }

    public function insertSubscribeDetail($params)
    {
        $this->db->set([
            'subscribe_idx' => $params['subscribe_idx'],
            'sequence' => $params['sequence'],
            'schedule_dt' => $params['schedule_dt'],
            'use_fl' => 'y',
            'reg_dt' => date('Y-m-d H:i:s'),
            'reg_idx' => $params['member_idx'],
        ]);

        return $this->db->insert('subscribe_schedule');
    }

    public function getGoodsToBuy($pet_idx = 0)
    {
        if (empty($pet_idx)) {
            return false;
        }

        $sql = '
             SELECT 
                g.goods_idx
            FROM
                pet p
                    JOIN
                goods g ON p.pet_type = g.pet_type
                    AND g.main_display = \'y\'
                    AND g.use_fl = \'y\'
                    RIGHT JOIN
                goods_relation gr ON gr.parent_idx = g.goods_idx
                    AND gr.use_fl = \'y\'
            WHERE
                p.pet_idx = ?
            GROUP BY g.goods_idx
        ';

        $bind['pet_idx'] = $pet_idx;
        return $this->db->query($sql, $bind)->result_array();
    }

    public function updateStatusSubscribe($subscribe_idx, $status = '')
    {
        if (empty($status) || !in_array($status, ['active', 'pause', 'cancel', 'complete'])) {
            return false;
        }

        $data = [
            'status' => $status,
            'edit_dt' => date('Y-m-d H:i:s'),
            'edit_idx' => $this->session->userdata('member_idx')
        ];
        $this->db->where('subscribe_idx', $subscribe_idx);
        return $this->db->update('subscribe', $data);
    }

    /**
     * 결제 완료되지않은 구독스케쥴의 주소 변경
     * @param array $params
     * @return bool
     */
    public function updateSubscribeSchedule($params = [])
    {
        if (empty($params) || (empty($params['subscribe_idx']) && empty($params['subscribe_schedule_idx']))) {
            return false;
        }

        $data = [
            'edit_dt' => date('Y-m-d H:i:s'),
            'edit_idx' => $this->session->userdata('member_idx')
        ];

        if (!empty($params['address_idx'])) {
            $data['address_idx'] = $params['address_idx'];
        }
        if (!empty($params['schedule_dt'])) {
            $data['schedule_dt'] = $params['schedule_dt'];
        }

        $this->db->where('use_fl', 'y');
        $this->db->where('payment_idx', null);

        if (!empty($params['subscribe_idx'])) {
            $this->db->where('subscribe_idx', $params['subscribe_idx']);
        }
        if (!empty($params['subscribe_schedule_idx'])) {
            $this->db->where('subscribe_schedule_idx', $params['subscribe_schedule_idx']);
        }
        $result = $this->db->update('subscribe_schedule', $data);
        //echo $this->db->last_query().'<br><br>';
        return $result;
    }


    /**
     * 결제시도된 구독 스케쥴 payment_idx 업데이트
     * @param array $params
     * @return bool
     */
    public function updatePaymentIdxSubscribeSchedule($params = [])
    {
        if (empty($params) || empty($params['subscribe_idx']) || empty($params['payment_idx'])) {
            return false;
        }

        $data = [
            'edit_dt' => date('Y-m-d H:i:s'),
            'edit_idx' => $this->session->userdata('member_idx')
        ];

        $data['payment_idx'] = $params['payment_idx'];

        $this->db->where('schedule_dt', ($params['schedule_dt']) ? $params['subscribe_idx'] : date('Y-m-d'));
        $this->db->where('subscribe_idx', $params['subscribe_idx']);
        $this->db->where('use_fl', 'y');
        $this->db->where('payment_idx', null);

        return $this->db->update('subscribe_schedule', $data);
    }


    /**
     *  마지막으로 결제된  구독 스케쥴
     * @param $subscribe_idx
     * @param string $type
     * @return mixed
     */
    public function getLastPaymentSubscribeSchedule($subscribe_idx, $type = '')
    {
        $pay_status_where = '';
        if ($type != 'all') {
            $pay_status_where = " AND p.status NOT IN ('' , 'pay_fail', 'pay_pending')";
        }

        $sql = '
           SELECT 
                ss.schedule_dt, ss.subscribe_schedule_idx, ss.sequence, o.order_idx, o.member_idx
            FROM
                subscribe_schedule ss
                    JOIN
                `order` o ON ss.subscribe_schedule_idx = o.subscribe_schedule_idx
                    AND o.use_fl = \'y\'
                    JOIN
                payment p ON o.order_idx = p.order_idx
                AND p.use_fl = \'y\' ' . $pay_status_where;
           $sql .= ' WHERE
                ss.subscribe_idx = ?
                    AND o.order_idx > 0
                    AND p.payment_idx > 0
            ORDER BY ss.schedule_dt DESC , ss.sequence DESC
            LIMIT 1';


        $result = $this->db->query($sql, [$subscribe_idx])->row_array();
        return  $result ;
    }
}