<?php
/**
 * Created by PhpStorm.
 * User: jungmin
 * Date: 2018-08-09
 * Time: 오후 9:31
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Goods extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
        $this->load->model('Admin_goods_model', 'model');

        $this->load->service('admin_service', '', true);
        $this->admin_service->checkAdmin();
    }

    public function index($type = 'parent')
    {
        if ($type === 'parent') {
            $this->parentGoodsList();
        } else {
            $this->childGoodsList();
        }
    }

    public function parentGoodsList()
    {
        $parentGoods = $this->model->getParentGoods();
        $childGoods = $this->model->getChildGoods();

        $this->load->view('admin/common/header.html');
        $this->load->view('admin/goods/parent-goods-list.html', compact('parentGoods', 'childGoods'));
        $this->load->view('admin/common/footer.html');
    }

    public function childGoodsList()
    {
        $childGoods = $this->model->getChildGoods();

        $this->load->view('admin/common/header.html');
        $this->load->view('admin/goods/child-goods-list.html', compact('childGoods'));
        $this->load->view('admin/common/footer.html');
    }

    public function addForm()
    {
        $this->load->view('admin/common/header.html');
        $this->load->view('admin/goods/add-form.html');
        $this->load->view('admin/common/footer.html');
    }

    public function add()
    {
        $params = $this->input->post();

        try {
            $this->model->db->trans_begin();

            $goods_idx = $this->model->insert($params);

            if (!empty($params['child_goods_idx'])) {
                foreach (explode(',', $params['child_goods_idx']) as $k => $child_idx) {
                    $this->model->insertGoodsRelation([
                        'parent_idx' => $goods_idx,
                        'child_idx' => $child_idx,
                        'use_fl' => 'y'
                    ]);
                }
            }

            if (!empty($_FILES['pet_img']['name'])) {
                $upload_result = $this->upload();

                if (!empty($upload_result['error'])) {
                    $this->model->db->trans_rollback();
                    alert('이미지 업로드에 실패하였습니다. 재시도해주세요.\n' . $upload_result['error'],
                        '/admin/goods/editForm/' . $goods_idx);
                    return false;
                } else {
                    $this->model->addImg([
                        'goods_idx' => $goods_idx,
                        'img_src' => str_replace('/var/www/html/branches/munch', '',
                            $upload_result['upload_data']['full_path']),
                        'use_fl' => 'y'
                    ]);
                }
            }

            $this->model->db->trans_complete();
        } catch (Exception $exception) {
            $this->model->db->rollback();
            alert('상품 수정이 실패하였습니다. 잠시후 재시도해주세요.');
        }

        redirect('/admin/goods/');
    }

    public function editForm($goods_idx = 0)
    {
        if (empty($goods_idx)) {
            alert('상품번호가 전달되지 않았습니다.', '/admin/goods/');
        }

        $goods = $this->model->getGoods(['goods_idx' => $goods_idx]);
        $goods = array_shift($goods);
        $childGoods = $this->model->getChildGoods(['parent_idx' => $goods_idx, 'use_fl']);

        $this->load->view('admin/common/header.html');
        $this->load->view('admin/goods/edit-form.html', compact('goods', 'childGoods'));
        $this->load->view('admin/common/footer.html');
    }

    public function edit()
    {
        $params = $this->input->post();
        $goods_idx = $params['goods_idx'];

        if (empty($goods_idx)) {
            alert('상품번호가 전달되지 않았습니다.', '/admin/goods/');
        }

        try {
            $this->model->db->trans_begin();

            $this->model->edit($params);
            $this->model->deleteGoodsRelation(['parent_idx' => $goods_idx]);
            if (!empty($params['child_goods_idx'])) {
                foreach (explode(',', $params['child_goods_idx']) as $k => $child_idx) {
                    $this->model->insertGoodsRelation([
                        'parent_idx' => $goods_idx,
                        'child_idx' => $child_idx,
                        'use_fl' => 'y'
                    ]);
                }
            }

            if (!empty($_FILES['pet_img']['name'])) {
                $upload_result = $this->upload();

                if (!empty($upload_result['error'])) {
                    $this->model->db->trans_rollback();
                    alert('이미지 업로드에 실패하였습니다. 재시도해주세요.\n' . $upload_result['error'],
                        '/admin/goods/editForm/' . $goods_idx);
                    return false;
                } else {
                    $this->model->deleteImg($goods_idx);
                    $this->model->addImg([
                        'goods_idx' => $goods_idx,
                        'img_src' => $upload_result['upload_data']['full_path'],
                        'use_fl' => 'y'
                    ]);
                }
            }


            $this->model->db->trans_complete();
        } catch (Exception $exception) {
            alert('상품 수정이 실패하였습니다. 잠시후 재시도해주세요.');
        }

        redirect('/admin/goods/');
    }

    public function upload()
    {
        $config['upload_path'] = './img/goods-img';
        $config['allowed_types'] = 'gif|jpg|png';
        $config['encrypt_name'] = TRUE;
        //$config['file_name'] = time();
//        $config['max_size'] = 500;
//        $config['max_width'] = 1024;
//        $config['max_height'] = 768;

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('pet_img')) {
            $error = array('error' => $this->upload->display_errors());
            return $error;
        } else {
            $data = array('upload_data' => $this->upload->data());
            return $data;
        }
    }

    public function delete($goods_idx = 0)
    {
        if (empty($goods_idx)) {
            alert('상품번호가 전달되지 않았습니다.', '/admin/goods/');
        }

        try {
            $this->model->db->trans_begin();

            $this->model->deleteGoods($goods_idx);
            $this->model->deleteGoodsRelation(['parent_idx' => $goods_idx]);
            $this->model->deleteGoodsRelation(['child_idx' => $goods_idx]);

            $this->model->db->trans_complete();
        } catch (Exception $exception) {
            $this->model->db->trans_rollback();
            alert('삭제처리가 실패하였습니다. 잠시후 재시도해주세요.');
        }
        redirect('/admin/goods/');
    }

    public function popupChildGoods($pet_type = '')
    {
        $childGoods = $this->model->getChildGoods(['pet_type' => $pet_type]);
        $this->load->view('admin/common/header.html');
        $this->load->view('admin/goods/popup-child-goods.html', compact('childGoods'));
        $this->load->view('admin/common/footer.html');
    }

}

