<?php
/**
 * Created by PhpStorm.
 * User: eunju
 * Date: 2018-07-23
 * Time: 오후 11:33
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Review extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('review_model');
    }

    public function index()
    {
        $this->lists();
    }

    public function lists()
    {
        $this->load->model(
            array(
                'pet_manage',
                'order_model',
                'Goods',
                'Review_model'
            )
        );

        $data = array();
        $pet_idx = 0;
        $params = $_GET;

        $member_idx = $this->session->userdata('member_idx');
        $data['pet_list'] = $this->pet_manage->getPets($member_idx);

        if (!empty($params['pet_idx'])) {
            $pet_idx = $params['pet_idx'];
        } elseif (empty($pet_idx) && !empty($data['pet_list'][0])) {
            $pet_idx = $data['pet_list'][0]['pet_idx'];
        }

        $data['goods_list'] = $this->order_model->getOrders(['pet_idx' => $pet_idx, 'member_idx' => $member_idx]);
        $data['pet_idx'] = $pet_idx;
        $data['review_list'] = $this->review_model->getList([
            'where' => [
                'pet_idx' =>$pet_idx,
                'member_idx' => $this->session->userdata('member_idx'),
                'use_fl' => 'y'
            ]
        ]);

        $this->load->view('common/header.html');
        $this->load->view('review/index.html', $data);
        $this->load->view('common/footer.html');
    }

    public function goodsList($pet_idx = 0)
    {
        $this->load->model(
            array(
                'pet_manage',
                'order_model',
                'Goods',
                'review_model'
            )
        );

        $data = array();
        $params = $_GET;
        $pet_idx = !empty($pet_idx) ? $pet_idx :  $params['pet_idx'];
        $member_idx = $this->session->userdata('member_idx');
        $data['pet_list'] = $this->pet_manage->getPets($member_idx, $pet_idx);

        if (empty($pet_idx)) {
            alert('요청이 정상적이지 않습니다.');
            return false;
        }

        $pet_info = $data['pet_list'][0];

        $data['review_list'] = $this->review_model->getList([
            'where' => [
                'member_idx' => $member_idx,
                'pet_idx' => $pet_idx,
                'use_fl' => 'y'
            ]
        ]);
        $data['goods_list'] = $this->order_model->getOrders(['pet_idx' => $pet_idx, 'member_idx' => $member_idx]);
        $data['pet_info'] = $pet_info;
        $result['contents'] = $this->load->view('review/goods-list.html', $data);

        return json_encode($result);
    }

    /**
     * 리뷰 등록하는
     */
    public function register()
    {
        $member_idx = $this->session->userdata('member_idx');

        if (empty($member_idx)) {
            alert("로그인을 이용해야 사용할 수 있습니다.");
            return false;
        }

        $params = $this->input->post();

        $reviewData = $this->review_model->getReview([
            'where' => [
                'member_idx' => $member_idx,
                'pet_idx' => $params['pet_idx'],
                'goods_idx' => $params['goods_idx'],
                'use_fl' => 'y'
            ]
        ]);

        $data = array(
            'member_idx' => $member_idx,
            'goods_idx' => $params['goods_idx'],
            'pet_idx' => $params['pet_idx'],
            'score_level' => $params['score_level'],
            'dislike' => $params['chk_dislike'],
            'like' => $params['chk_like'],
            'comment' => $params['comment'],
            'use_fl' => 'y',
        );

        if (!empty($reviewData)) {
            if ($this->review_model->doUpdate(['score_level' => $params['score_level'], 'dislike' => $params['dislike'], 'comment' => $params['comment']], ['where' => ['review_idx' => $reviewData['review_idx']]]) === false) {
                alert("리뷰 수정에 실패하였습니다.");
            } else {
                alert("리뷰 수정 완료하였습니다.", "/review/");
            }
        } else {
            if ($this->review_model->doRegister($data) === false) {
                alert("리뷰 등록에 실패하였습니다.");
            } else {
                alert("리뷰 등록 완료하였습니다.", "/review/");
            }
        }

        exit;
    }

    /**
     * 또 받고 싶어요, 평점 클릭 시
     */
    public function sendLike()
    {
        $member_idx = $this->session->member_idx;
        if (empty($member_idx)) {
            echo json_encode('logout');
            exit;
        }

        $pet_idx = $this->input->get_post('pet_idx');
        $goods_idx = $this->input->get_post('goods_idx');
        $code = $this->input->get_post('code');
        $value = $this->input->get_post('value');

        $reviewData = $this->review_model->getReview([
            'where' => [
                'member_idx' => $member_idx,
                'pet_idx' => $pet_idx,
                'goods_idx' => $goods_idx,
                'use_fl' => 'y'
            ]
        ]);

        //등록된 리뷰가 없으면 새로 입력
        if (empty($reviewData)) {
            $data = array(
                'member_idx' => $member_idx,
                'goods_idx' => $goods_idx,
                'pet_idx' => $pet_idx,
                'like' => 'y',
                'use_fl' => 'y',
            );

            echo json_encode($this->review_model->doRegister($data) === false ?  'fail' : 'success');
            exit;
        }

        //좋아요, 싫어요, 별점 업데이트
        if (in_array($code, ['like', 'dislike', 'score_level'])) {
            if (in_array($code, ['like', 'dislike'])) {
                $value = !empty($reviewData[$code]) && $reviewData[$code] === 'y' ? 'n' : 'y';
            }

            if ($this->review_model->doUpdate([$code => $value],
                ['where' => ['review_idx' => $reviewData['review_idx'], 'member_idx' => $member_idx]])) {
                echo json_encode('success');
            } else {
                echo json_encode('fail');
            }
        }

        exit;
    }
}