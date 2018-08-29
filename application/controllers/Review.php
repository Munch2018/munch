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

    /**
     * 리뷰 등록하는
     */
    public function register()
    {
        /**
         * member_idx / goods_idx / score / like / dislike
         */

        if ($this->session->userdata('member_idx') != "") {
            $data = array(
                'member_idx'    => $this->session->userdata('member_idx'),
                'goods_idx'     => $this->input->post('goods_idx'),
                'order_idx'     => $this->input->post('order_idx'),
                'score_level'   => $this->input->post('rating-input-1'),
                'comment'       => $this->input->post('comment'),
                'use_fl'        => 'y',
            );

            if ($this->review_model->doRegister($data) === false ) {
                alert("리뷰 등록에 실패하였습니다.");
            } else {
                alert("리뷰 등록 완료하였습니다.", "/review/");
            }

        } else {
            alert("로그인을 이용해야 사용할 수 있습니다.");
        }
    }

    /**
     * 리뷰 수정
     */
    public function update()
    {

    }

    /**
     * 리뷰 점수 평가하기
     */
    public function score ()
    {
        
    }

}