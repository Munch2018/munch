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
        // 회원에 해당되는 pet 정보 가져오기
        // order 기준으로 리뷰 써야되서 order 에대한 정보 가져오기
        $this->load->model(
            array(
                'pet_manage',
                'order_model',
                'Goods'
            )
        );
        $data = array();

        $member_idx = $this->session->userdata('member_idx');
        $data['pet_list'] = $this->pet_manage->getPets($member_idx);
        $data['goods_list'] = $this->order_model->getOrders();

        $this->load->view('common/header.html');
        $this->load->view('review/index.html', $data);
        $this->load->view('common/footer.html');
    }

    public function index2()
    {
        $this->lists2();
    }

    public function lists2()
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
        $this->load->view('review/index2.html', $data);
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
        $member_idx = $this->session->userdata('member_idx');
        $data['pet_list'] = $this->pet_manage->getPets($member_idx);

        if (empty($pet_idx)) {
            if
            (!empty($params['pet_idx'])) {
                $pet_idx = $params['pet_idx'];
            } else {
                alert('요청이 정상적이지 않습니다.');
                return false;
            }
        }

        $data['review_list'] = $this->review_model->getList([
            'where' => [
                'member_idx' => $member_idx,
                'pet_idx' => $pet_idx,
                'use_fl' => 'y'
            ]
        ]);
        $data['goods_list'] = $this->order_model->getOrders(['pet_idx' => $pet_idx, 'member_idx' => $member_idx]);
        $data['pet_idx'] = $pet_idx;
        $result['contents'] = $this->load->view('review/goods-list.html', $data);

        return json_encode($result);
    }

    /**
     * 리뷰 등록하는
     */
    public function register()
    {
        /**
         * member_idx / goods_idx / score / like / dislike
         */
$params  =  $this->input->get_post();
print_r($params,1);
exit;
        $member_idx = $this->session->userdata('member_idx');

        if (empty($member_idx)) {
            alert("로그인을 이용해야 사용할 수 있습니다.");
            return false;
        }

        $data = array(
            'member_idx' => $this->session->userdata('member_idx'),
            'goods_idx' => $this->input->post('goods_idx'),
            'pet_idx' => $this->input->post('pet_idx'),
            'order_idx' => $this->input->post('order_idx'),
            'score_level' => $this->input->post('score_level'),
            'like' => $this->input->post('like'),
            'dislike' => $this->input->post('dislike'),
            'comment' => $this->input->post('comment'),
            'use_fl' => 'y',
            'review_idx' => $this->input->post('review_idx'),
        );

        if (!empty($this->input->post('review_idx'))) {
            if ($this->review_model->doUpdate($data) === false) {
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
    }

    public function updateLike()
    {
        if ($this->session->userdata('member_idx') != "") {

        }
    }


    /**
     * 리뷰 점수 평가하기
     */
    public function score ()
    {
        
    }

}