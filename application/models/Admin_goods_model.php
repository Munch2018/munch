<?php
/**
 * Created by PhpStorm.
 * User: jungmin
 * Date: 2018-08-09
 * Time: 오후 9:32
 */

class Admin_goods_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getParentGoods()
    {
        $bind = [];
        $where = [];
        $whereStr = '';

        if (!empty($params['child_idx'])) {
            $where[] = ' gr.child_idx = ? ';
            $bind['child_idx'] = $params['child_idx'];
        }

        if (!empty($where)) {
            $whereStr = ' WHERE ' . implode(' and ', $where);
        }

        $sql = 'SELECT gr.parent_idx, ' . PHP_EOL
            . '     g.title, g.detail, g.subtitle, g.price, g.sell_price, g.pet_type, g.material, g.ingredients, ' . PHP_EOL
            . '     gi.img_src ' . PHP_EOL
            . ' FROM goods_relation gr ' . PHP_EOL
            . '     JOIN goods g ON gr.parent_idx = g.goods_idx ' . PHP_EOL
            . '     LEFT JOIN goods_img gi ON g.goods_idx = gi.goods_idx ' . PHP_EOL
            . $whereStr
            . ' GROUP BY g.goods_idx ';

        return $this->db->query($sql, $bind)->result_array();
    }

    public function getChildGoods($params = [])
    {
        $bind = [];
        $where = [];
        $whereStr = '';

        if (!empty($params['goods_idx'])) {
            $where[] = ' g.goods_idx = ? ';
            $bind['goods_idx'] = $params['goods_idx'];
        }

        if (!empty($where)) {
            $whereStr = ' WHERE ' . implode(' and ', $where);
        }

        $sql = 'SELECT gr.parent_idx, ' . PHP_EOL
            . '     g.goods_idx, g.title, g.detail, g.subtitle, g.price, g.sell_price, g.pet_type, g.material, g.ingredients, ' . PHP_EOL
            . '     group_concat(gi.img_src SEPARATOR \'|\') as img_src ' . PHP_EOL
            . ' FROM goods_relation gr ' . PHP_EOL
            . '     JOIN goods g ON gr.child_idx = g.goods_idx ' . PHP_EOL
            . '     LEFT JOIN goods_img gi ON g.goods_idx = gi.goods_idx ' . PHP_EOL
            . $whereStr
            . ' GROUP BY g.goods_idx ' . PHP_EOL
            . ' ORDER BY g.goods_idx ';

        return $this->db->query($sql, $bind)->result_array();
    }

    public function deleteGoods($goods_idx)
    {
        $this->db->set('use_fl', 'n');
        $this->db->set('del_dt', date('Y-m-d H:i:s'));
        $this->db->set('del_idx', $this->session->userdata('member_idx'));

        $this->db->where('goods_idx', $goods_idx);
        $this->db->update('goods');
    }

    public function deleteGoodsRelation($params)
    {
        $this->db->set('use_fl', 'n');
        $this->db->set('del_dt', date('Y-m-d H:i:s'));
        $this->db->set('del_idx', $this->session->userdata('member_idx'));

        if (!empty($params['parent_idx'])) {
            $whereConditions[] = ' parent_idx= ' . $params['parent_idx'];
        }
        if (!empty($params['child_idx'])) {
            $whereConditions[] = ' child_idx= ' . $params['child_idx'];
        }

        if (!empty($whereConditions)) {
            $where =implode(' OR ', $whereConditions);
            $this->db->where($where);
            $this->db->update('goods_relation');
        }
    }
}