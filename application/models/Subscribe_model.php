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

    public function getAddress($member_idx = 0)
    {
        $this->db->select('zipcode, addr1st, addr2nd, sort, nation');
        $this->db->from('address');
        $this->db->where('member_idx', $member_idx);
        $this->db->order_by('sort', 'ASC');
      //  return $this->db->query($this->db->get())->result
        return $this->db->get()->result_array();
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
        if(!empty($where)){
            $whereStr = 'WHERE '.implode(' and ',$where);
        }

        $sql = '
        SELECT 
            s.subscribe_idx, s.subscribe_month, s.goods_idx, s.buy_count,
            g.title,
            sp.sell_price,
            p.pet_kind, p.pet_size
        FROM
            subscribe s 
            JOIN pet p ON s.pet_idx = p.pet_idx
            JOIN goods g ON s.goods_idx = g.goods_idx AND g.use_fl = \'y\'
            JOIN subscribe_price sp ON s.subscribe_month = sp.month_count AND sp.use_fl = \'y\'
        '.$whereStr;

       return $this->db->query($sql, $bind)->result_array();
    }

    public function subscribe_count($member_idx)
    {
        $this->db->where('member_idx', $member_idx);
        return $this->db->count_all("subscribe");
    }

    public function fetch_subscribe($member_idx, $limit, $start)
    {
        $this->db->select(' subscribe.subscribe_idx,
                        subscribe.subscribe_month,
                        subscribe.goods_idx,
                        subscribe.buy_count,
                        subscribe.status,
                        goods.title,
                        (subscribe_price.sell_price * subscribe_price.month_count) AS total_amount,
                        pet.name,
                        ( select min(subscribe_detail.schedule_dt)  from subscribe_detail
                            LEFT join payment on subscribe_detail.payment_idx = payment.payment_idx and payment.use_fl=\'y\' and payment.status !=\'complet\'
                            where subscribe_detail.use_fl=\'y\' and subscribe_detail.subscribe_idx=subscribe.subscribe_idx) 
                         as schedule_dt
                        ');

        $this->db->from('subscribe');
        $this->db->join('pet','subscribe.pet_idx = pet.pet_idx');
        $this->db->join('goods','subscribe.goods_idx = goods.goods_idx ');
        $this->db->join('subscribe_price','subscribe.subscribe_month = subscribe_price.month_count AND subscribe_price.use_fl = \'y\'');


        $this->db->limit($limit, $start);
        $this->db->where('subscribe.member_idx', $member_idx);
        return  $this->db->get()->result_array();

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
            'sequence'=>$params['sequence'],
            'use_fl' => 'y',
            'reg_dt' => date('Y-m-d H:i:s'),
            'reg_idx' => $params['member_idx'],
        ]);

        return $this->db->insert('subscribe_detail');
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


}