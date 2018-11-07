<?php
/**
 * Created by PhpStorm.
 * User: jungmin
 * Date: 2018-11-07
 * Time: 오후 9:34
 */

class Batch_subscribe_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }


    public function getActiveSubscribe($date = '')
    {
        $sql = '
            SELECT 
                o.order_idx, o.last_amount, o.goods_name, o.subscribe_idx,  o.payment_method, o.subscribe_schedule_idx,
                p.payment_idx, p.pay_method, p.status, p.use_fl
            FROM
                member m
                    JOIN subscribe s ON m.member_idx = s.member_idx
                    JOIN subscribe_schedule ss ON s.subscribe_idx = ss.subscribe_idx
                    JOIN `order` o ON s.subscribe_idx = o.subscribe_idx  AND ss.subscribe_schedule_idx = o.subscribe_schedule_idx
                    JOIN payment p ON o.order_idx = p.order_idx
            WHERE
                m.use_fl = \'y\' AND s.use_fl = \'y\'
                    AND ss.use_fl = \'y\'
                    AND o.use_fl = \'y\'
                    AND p.use_fl = \'y\'
                    AND o.order_idx IS NOT NULL
                    AND p.payment_idx IS NOT NULL
                    AND s.status = \'active\'
                    AND ss.schedule_dt < ?
                    AND s.end_date >= ?
                    AND s.subscribe_idx IN 
                    (
                       SELECT  ss2.subscribe_idx FROM subscribe_schedule ss2
                                LEFT JOIN `order` o2 ON ss2.subscribe_schedule_idx = o2.subscribe_schedule_idx
                                LEFT JOIN payment p2 ON o2.order_idx = p2.order_idx
                        WHERE
                            ss2.use_fl = \'y\' AND 
                            ((o2.order_idx IS NULL AND p2.payment_idx IS NULL) or 
                            o2.use_fl=\'n\' AND p2.use_fl = \'n\'
                            ) AND ss2.schedule_dt = ? AND ss2.subscribe_idx = s.subscribe_idx
                    )
                    LIMIT 100	        
        ';

        if (empty($date)) {
            $date = date('Y-m-d');
        }

        return $this->db->query($sql, [$date,$date,$date])->result_array();
    }

    /**
     * @deprecated
     * @param $subscribe_idx
     * @param $subscribe_schedule_idx
     * @param string $schedule_dt
     * @return bool
     */
    public function existsPayment($subscribe_idx, $subscribe_schedule_idx, $schedule_dt='')
    {
        $sql = " 
             SELECT 
                COUNT(s.subscribe_idx) AS cnt
            FROM
                subscribe s
                    JOIN
                subscribe_schedule ss ON s.subscribe_idx = ss.subscribe_idx
                    JOIN
                `order` o ON ss.subscribe_schedule_idx = o.subscribe_schedule_idx
                    JOIN
                payment p ON o.order_idx = p.order_idx
            WHERE
                s.use_fl = 'y' AND ss.use_fl = 'y'
                    AND o.use_fl = 'y'
                    AND p.use_fl = 'y'
                    AND s.subscribe_idx = ?
                    AND ss.subscribe_schedule_idx = ?
                    
                    AND p.status <> 'pay_fail';
        ";

        $bind['subscribe_idx'] = $subscribe_idx;
        $bind['subscribe_schedule_idx'] = $subscribe_schedule_idx;


        $result = $this->db->query($sql, $bind)->result();
        return $result[0]->cnt > 0;
    }
}