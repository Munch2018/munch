<?php
/**
 * Created by PhpStorm.
 * User: jungmin
 * Date: 2018-07-21
 * Time: 오후 3:50
 */

class Goods extends CI_Model
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
            . '     group_concat(distinct(gi.img_src) SEPARATOR \'|\') as img_src ' . PHP_EOL
            . ' FROM goods_relation gr ' . PHP_EOL
            . '     JOIN goods g ON gr.parent_idx = g.goods_idx ' . PHP_EOL
            . '     LEFT JOIN goods_img gi ON g.goods_idx = gi.goods_idx and gi.use_fl=\'y\' ' . PHP_EOL
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
        if (!empty($params['use_fl'])) {
            $where[] = ' gr.use_fl = ? ';
            $bind['use_fl'] = $params['use_fl'];
        }
        if (!empty($params['goods_use_fl'])) {
            $where[] = ' g.use_fl = ? ';
            $bind['goods_use_fl'] = $params['goods_use_fl'];
        }
        if (!empty($params['img_use_fl'])) {
            $where[] = ' gi.use_fl = ? ';
            $bind['img_use_fl'] = $params['img_use_fl'];
        }
        if (!empty($params['package_fl'])) {
            $where[] = ' g.package_fl = ? ';
            $bind['package_fl'] = $params['package_fl'];
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

}
