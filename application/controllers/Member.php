<?php
/**
 * Created by PhpStorm.
 * User: eunju
 * Date: 2018-07-01
 * Time: 오후 2:56
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Member extends CI_Controller
{

    public function __construct(){

        parent::__construct();
    }

    public function index(){
        $this->login_form();
    }


    /**
     * 회원가입 폼
     */
    public function join_form(){
        $this->load->view('common/header');
        $this->load->view('member/join_form');
        $this->load->view('common/footer');
    }

    public function login_form(){
        $this->load->view('common/header');
        $this->load->view('member/login_form');
        $this->load->view('common/footer');
    }

    /**
     * 회원가입
     * password => md5 암호화한다.
     */
    public function join(){
        $this->load->model('Member_model', 'member');
//        // email && password 체크
//        if (trim($this->input->get_post('email', true)) != "") {
//            $join_data['email'] = $this->input->get_post('email', true);
//        }
//
//        if (trim($this->input->get_post('password')) != "") {
//            $join_data['password'] = md5(trim($this->input->get_post('password', true)));
//        }
        $validation_data = array(
            'email', 'password', 'name'
        );

        foreach ($validation_data as  $value) {
            $this->form_validation->set_rules($value, $value, 'required');
        }

        if ($this->form_validation->run() == false) {
            alert("필수 데이터를 넣어주세요.");
        } else {
            //중복된 아이디 있는지 체크
            if (!empty($this->member->getMember(array('where' => array('email' => $this->input->get_post('email')))))) {
                // 중복된 아이디 있음
                alert("중복된 아이디가 있습니다.");
            } else {

                $join_data = array(
                    'email' => $this->input->get_post('email', true),
                    'name'  => $this->input->get_post('name', true),
                    'telphone'  => $this->input->get_post('telphone', true),
                    'password'  => md5(trim($this->input->get_post('password'))),
                );

                $this->member->doRegister($join_data);
            }

            alert("회원가입 완료되었습니다.");
        }
    }

    /**
     * 로그인
     */
    public function login()
    {
        //아이디 체크
        $this->load->model('Member_model');
        /***
         * 사용할 email, password
         */
        if (trim($this->input->post('email')) != "") {
            alert("아이디를 넣어주세요.");
        }

        if (trim($this->input->post('password')) != "" ) {
            alert("비밀번호를 넣어주세요.");
        }


    }
}
