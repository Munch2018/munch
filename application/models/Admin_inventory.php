<?php
/**
 * Created by PhpStorm.
 * User: jungmin
 * Date: 2018-08-12
 * Time: 오후 3:34
 */

class Admin_inventory extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getList()
    {
        $sql = '
        select 
                g.goods_idx, g.title,g.sell_price, g.use_fl, 
                iv.reg_dt,iv.total_count,iv.add_count,
                iv.sub_count,iv.memo, iv.expiry_dt, iv.receiving_dt,
              group_concat(distinct(gi.img_src) SEPARATOR \'|\') as img_src 
        from inventory_history iv
            JOIN goods g on iv.goods_idx = g.goods_idx
            LEFT JOIN goods_img gi on gi.goods_idx = g.goods_idx
        where
          DATE(iv.reg_dt) >= DATE(DATE_SUB(NOW(), INTERVAL 1 MONTH))
          group by inventory_history_idx
          order by iv.inventory_history_idx desc
        ';
        return $this->db->query($sql, [])->result_array();
    }

    public function getTotalCount($goods_idx = 0)
    {
        $this->db->select('goods_idx, sum(add_count)-sum(sub_count) as total_count');

        if (!empty($goods_idx)) {
            $this->db->where('goods_idx', $goods_idx);
        }
        $this->db->group_by('goods_idx');
        return $this->db->get('inventory_history')->result_array();
    }

    public function insert($params)
    {
        $data = $params;
        $data['receiving_dt'] = $params['receiving_dt'] . ' 00:00:00';
        $data['expiry_dt'] = $params['expiry_dt'] . ' 23:59:59';

        $data['reg_idx'] = $this->session->userdata('member_idx');
        $data['reg_dt'] = date('Y-m-d H:i:s');

        $query = $this->db->insert_string('inventory_history', $data);
        echo $query . '<br>';
        return $this->db->query($query);
    }

    public function getGoodsCount($start_date, $end_date)
    {
        $sql = '
        select 
             od.goods_idx, count(*)
             from subscribe_schedule ss
            join `order` o on ss.subscribe_schedule_idx=o.subscribe_schedule_idx
            join order_detail od on o.order_idx=od.order_idx
            join payment p on o.order_idx=p.payment_idx
            where ss.use_fl=\'y\'
            and o.use_fl=\'y\'
            and od.use_fl=\'y\'
            and p.use_fl=\'y\'
            -- and p.status not in (\'pay_fail\', \'pay_pending\')
            and ss.schedule_dt >= ? and ss.schedule_dt <= ?
            group by od.goods_idx
        ';

        return $this->db->query($sql, [$start_date, $end_date])->result_array();
    }
}