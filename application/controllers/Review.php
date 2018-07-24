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
    }

    public function index()
    {
        // 회원에 해당되는 pet 정보 가져오기
        // order 기준으로 리뷰 써야되서 order 에대한 정보 가져오기
        $this->load->view('common/header');
        $this->load->view('review/index');
        $this->load->view('common/footer');
    }

    /**
     * 리뷰 등록하는
     */
    public function register()
    {

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