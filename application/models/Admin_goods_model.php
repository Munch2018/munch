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

    public function getGoods($params = [])
    {
        $bind = [];
        $where = [];
        $whereStr = '';

        if (!empty($params['use_fl'])) {
            $where[] = ' g.use_fl = ? ';
            $bind['use_fl'] = $params['use_fl'];
        }
        if (!empty($params['goods_idx'])) {
            $where[] = ' g.goods_idx in ? ';
            $bind['goods_idx'] = $params['goods_idx'];
        }

        if (!empty($where)) {
            $whereStr = ' WHERE ' . implode(' and ', $where);
        }

        $sql = 'SELECT g.goods_idx, g.title, g.detail, g.subtitle, g.price, g.sell_price, g.pet_type, g.material, g.ingredients, g.use_fl, g.inventory_count, ' . PHP_EOL
            . '     group_concat(distinct(gi.img_src) SEPARATOR \'|\') as img_src  ' . PHP_EOL
            . ' FROM goods g ' . PHP_EOL
            . '     LEFT JOIN goods_img gi ON g.goods_idx = gi.goods_idx and gi.use_fl="y"' . PHP_EOL
            . $whereStr
            . ' GROUP BY g.goods_idx ';

        return $this->db->query($sql, $bind)->result_array();
    }

    public function getParentGoods($params = [])
    {
        $bind = [];
        $where = [];
        $whereStr = '';

        if (!empty($params['parent_idx'])) {
            $where[] = ' gr.parent_idx = ? ';
            $bind['parent_idx'] = $params['parent_idx'];
        }
        if (!empty($params['goods_idx'])) {
            $where[] = ' g.parent_idx = ? ';
            $bind['goods_idx'] = $params['goods_idx'];
        }
        if (!empty($where)) {
            $whereStr = ' WHERE  gr.use_fl="y" AND ' . implode(' and ', $where);
        }

        $sql = 'SELECT gr.parent_idx, ' . PHP_EOL
            . '     g.title, g.detail, g.subtitle, g.price, g.sell_price, g.pet_type, g.material, g.ingredients, ' . PHP_EOL
            . '     group_concat(distinct(gi.img_src) SEPARATOR \'|\') as img_src  ' . PHP_EOL
            . ' FROM goods_relation gr ' . PHP_EOL
            . '     JOIN goods g ON gr.parent_idx = g.goods_idx ' . PHP_EOL
            . '     LEFT JOIN goods_img gi ON g.goods_idx = gi.goods_idx and gi.use_fl="y" ' . PHP_EOL
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
        if (!empty($params['parent_idx'])) {
            $where[] = ' gr.parent_idx = ? ';
            $bind['parent_idx'] = $params['parent_idx'];
        }
        if (!empty($params['pet_type'])) {
            $where[] = ' g.pet_type = ? ';
            $bind['pet_type'] = $params['pet_type'];
        }
        if (!empty($where)) {
            $whereStr = ' WHERE ' . implode(' and ', $where);
        }

        $sql = 'SELECT gr.parent_idx, gr.child_idx ,  ' . PHP_EOL
            . '     g.goods_idx, g.title, g.detail, g.subtitle, g.price, g.sell_price, g.pet_type, g.material, g.ingredients, ' . PHP_EOL
            . '     group_concat(distinct(gi.img_src) SEPARATOR \'|\') as img_src ' . PHP_EOL
            . ' FROM goods g  ' . PHP_EOL
            . '     LEFT JOIN goods_relation gr ON gr.child_idx = g.goods_idx and gr.use_fl="y" ' . PHP_EOL
            . '     LEFT JOIN goods_img gi ON g.goods_idx = gi.goods_idx and gi.use_fl="y" ' . PHP_EOL
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
            $where = implode(' OR ', $whereConditions);
            $this->db->where($where);
            $this->db->update('goods_relation');
        }
    }

    public function edit($params)
    {
        $this->db->set([
            'title' => $params['title'],
            'subtitle' => $params['subtitle'],
            'price' => $params['price'],
            'sell_price' => $params['sell_price'],
            'pet_type' => $params['pet_type'],
            'detail' => $params['detail'],
            'material' => $params['material'],
            'ingredients' => $params['ingredients'],
            'inventory_count' => $params['inventory_count'],
            'seller_idx' => $params['seller_idx'],
            'use_fl' => $params['use_fl'],
            'edit_dt' => date('Y-m-d H:i:s'),
            'edit_idx' => $this->session->userdata('member_idx')
        ]);

        $this->db->where('goods_idx', $params['goods_idx']);
        $this->db->update('goods');
    }

    public function insert($params)
    {
        $this->db->set([
            'title' => $params['title'],
            'subtitle' => $params['subtitle'],
            'price' => $params['price'],
            'sell_price' => $params['sell_price'],
            'pet_type' => $params['pet_type'],
            'detail' => $params['detail'],
            'material' => $params['material'],
            'ingredients' => $params['ingredients'],
            'inventory_count' => $params['inventory_count'],
            'seller_idx' => $params['seller_idx'],
            'use_fl' => $params['use_fl'],
            'reg_dt' => date('Y-m-d H:i:s'),
            'reg_idx' => $this->session->userdata('member_idx')
        ]);

        $this->db->insert('goods');
        return $this->db->insert_id();
    }

    public function deleteImg($goods_idx)
    {
        $this->db->set('use_fl', 'n');
        $this->db->set('del_dt', date('Y-m-d H:i:s'));
        $this->db->set('del_idx', $this->session->userdata('member_idx'));
        $this->db->where('goods_idx', $goods_idx);
        $this->db->update('goods_img');
    }

    public function addImg($params)
    {
        $data['goods_idx'] = $params['goods_idx'];
        $data['img_src'] = $params['img_src'];
        $data['use_fl'] = $params['use_fl'];
        $data['reg_idx'] = $this->session->userdata('member_idx');
        $data['reg_dt'] = date('Y-m-d H:i:s');

        $query = $this->db->insert_string('goods_img', $data);
        return $this->db->query($query);
    }

    public function insertGoodsRelation($params)
    {
        $data['parent_idx'] = $params['parent_idx'];
        $data['child_idx'] = $params['child_idx'];
        $data['use_fl'] = $params['use_fl'];
        $data['reg_idx'] = $this->session->userdata('member_idx');
        $data['reg_dt'] = date('Y-m-d H:i:s');

        $query = $this->db->insert_string('goods_relation', $data);
        return $this->db->query($query);
    }
}