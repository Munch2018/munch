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

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Member_model', 'member');
    }

    public function index()
    {
        $this->login_form();
    }


    /**
     * 회원가입 폼
     */
    public function join_form()
    {
        $this->load->view('common/header.html');
        $this->load->view('member/join_form.html');
        $this->load->view('common/footer.html');
    }

    public function login_form()
    {
        $this->load->view('common/header.html');
        $this->load->view('member/login_form.html');
        $this->load->view('common/footer.html');
    }

    /**
     * 회원가입
     * password => md5 암호화한다.
     */
    public function join()
    {
        $this->load->model('Member_model', 'member');
//        // email && password 체크

        $validation_data = array(
            'email',
            'password',
            'name'
        );

        foreach ($validation_data as $value) {
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
                    'name' => $this->input->get_post('name', true),
                    'telphone' => $this->input->get_post('telphone', true),
                    'password' => md5(trim($this->input->get_post('password'))),
                    'use_fl' => 'Y'
                );

                $this->member->doRegister($join_data);
            }

            alert("회원가입 완료되었습니다.", '/member/login_form/');
        }
    }

    /**
     * 로그인
     */
    public function login()
    {
        //아이디 체크

        /***
         * 사용할 email, password
         */

        if (trim($this->input->post('email')) == "") {
            alert("아이디를 넣어주세요.");
        }

        if (trim($this->input->post('password')) == "") {
            alert("비밀번호를 넣어주세요.");
        }

        $member_info = $this->member->getMember(array(
            'where' => array(
                'email' => $this->input->post('email'),
                'use_fl' => 'Y'
            )
        ));

        if (empty($member_info)) {
            alert("존재하지 않는 정보입니다.");
        }

        //아이디랑 비밀번호 같은지 체크해보기

        if ($member_info['password'] !== md5(trim($this->input->post('password')))) {
            alert("비밀번호가 맞지 않습니다.");
        }

        // session 으로 저장 하자
        // 원래 session 쓸때 load->libraries('session') 해줘야 하는데
        // autoload.php 에서 세션은 항상 사용한다고 처리함
        // 세션에서 일단 사용할 값은 member_idx , email 로 저장하고 나중에 추가로
        //설정할지말지 선택한다.

        $session_data = array(
            'member_idx' => $member_info['member_idx'],
            'email' => $member_info ['email'],
            'name' => $member_info['name'],
            'telphone' => $member_info['telphone'],
            'is_admin' => $member_info['is_admin']
        );

        if ($member_info['is_admin'] === 1) {
            $session_data['is_admin'] = true;
            $this->load->vars(array('IS_ADMIN', true));
        }

        $this->session->set_userdata($session_data);

//        redirect(SITE_DOMAIN);
        redirect('/');
    }

    /**
     * 로그아웃
     */
    public function logout()
    {
        $session = $this->session->all_userdata();
        foreach ($session as $key => $val) {
            $this->session->unset_userdata($key);
        }

        alert("로그아웃 되었습니다.", '/');
    }


    /**
     *
     * 회원 탈퇴 기능 이지만 user_flg = 'N' 으로 처리한다.
     * use_fl = 'N'
     * 나중에 batch 로 del_dt 3개월 && 1년 삭제 처리되는거 개발 해야함
     */
    public function signout()
    {

        if ($this->session->userdata('email') != "" && $this->session->userdata('member_idx') != "") {
            $this->member->doUpdate(array(
                'where' => array(
                    'member_idx' => $this->session->userdata('member_idx'),
                    'email' => $this->session->userdata('email')
                )
            ), array('use_fl' => 'N', 'del_dt' => date("Y-m-d H:i:s")));

            alert("회원 탈퇴 완료하였습니다.", '/');
            $this->session->unset_userdata('email');
            $this->session->unset_userdata('telphone');
            $this->session->unset_userdata('member_idx');
        } else {

            alert("잘못된 접근입니다.", "/");
        }
    }

    public function findPasswordForm()
    {
        $this->load->view('member/find-password-form.phtml');
    }

    public function findPassword()
    {
        $params = $this->input->post();

        $member_info = $this->member->getMember([
            'where' =>
                ['name' => $params['name'], 'email' => $params['email'], 'use_fl' => 'Y']
        ]);

        if (empty($member_info)) {
            alert('존재하지 않는 이메일입니다.');
            return false;
        }

        $email = $member_info['email'];
        $name = $member_info['name'];
        $updatePwdAuth = $this->_GenerateString(10);

        $modifyData['find_pwd_auth_number'] = md5(trim($updatePwdAuth));

        if (!empty($modifyData)) {
            $modifyData['edit_dt'] = date("Y-m-d H:i:s");
            $modifyData['edit_idx'] = 0;

            $changed = $this->member->doUpdate([
                'where' => [
                    'email' => $email,
                    'name' => $name
                ]
            ], $modifyData);

            if (!empty($changed) && !empty($this->sendEmail($email,$updatePwdAuth ))) {
                alert('인증번호가 이메일로 전송되었습니다.');
            } else {
                alert('이메일 전송에 실패했습니다. 페이지를 새로고침 후 재시도해주세요.');
            }

        } else {
            alert('이메일 전송에 실패했습니다. 페이지를 새로고침 후 재시도해주세요.');
        }
    }

    public function pwdFindAuthForm()
    {
        $params = $_POST;

echo print_r($params,1);
    }

    public function sendEmail($email, $pwd)
    {
        $from = 'munch.hello@gmail.com';
        $msg = "<h3>이메일 인증을 위해 아래 버튼링크를 클릭해주세요.</h3>";

        $msg .= "<form action='http://munchmunch.kr/member/pwdFindAuthForm/' method='post'>";
        $msg .= "<input type='hidden' name='email' value='" . $email . "'>";
        $msg .= "<input type='hidden' name='auth' value='" . $pwd . "'>";
        $msg .= "<button type='submit' value='이메일 인증 하기'>";
        $msg .= "</form>";

        // use wordwrap() if lines are longer than 70 characters
        $msg = wordwrap($msg, 70, "\r\n");

        // Always set content-type when sending HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

        // More headers
        $headers .= 'From: ' . $from . "\r\n";

        // send email
       return mail($email,
            "Munch의 인증번호입니다.",
            $msg,
            $headers);
    }

    public function _GenerateString($length) {
        $characters = "0123456789";
        $characters .= "abcdefghijklmnopqrstuvwxyz";
        $characters .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $string_generated = "";
        $nmr_loops = $length;
        while ($nmr_loops--) {
            $string_generated .= $characters[mt_rand(0, strlen($characters)-1)];
        }
        return $string_generated;
    }

    public function modifyProfile()
    {
        $pwd = $this->input->post('new-pwd');
        $name = $this->input->post('name');
        $telphone = $this->input->post('telphone');

        $member_info = $this->member->getMember([
            'where' =>
                [
                    'email' => $this->session->userdata('email'),
                    'use_fl' => 'Y',
                    'member_idx' => $this->session->userdata('member_idx')
                ]
        ]);

        $modifyData = [];
        $modifySessionData = [];

        if (!empty($pwd) && ($member_info['password'] !== md5(trim($pwd)) || empty($member_info['password']))) {
            $modifyData['password'] = md5(trim($pwd));
        }

        if (!empty($name) && $member_info['name'] !== $name) {
            $modifyData['name'] = $name;
            $modifySessionData['name'] = $name;
        }
        if (!empty($telphone) && ($member_info['telphone'] !== $telphone || empty($member_info['telphone']))) {
            $modifyData['telphone'] = str_replace('-', '', trim($telphone));
            $modifySessionData['telphone'] = $modifyData['telphone'];
        }

        if (!empty($modifyData)) {
            $modifyData['edit_dt'] = date("Y-m-d H:i:s");
            $modifyData['edit_idx'] = $this->session->userdata('mem_idx');

            $changed = $this->member->doUpdate([
                'where' => [
                    'member_idx' => $this->session->userdata('member_idx'),
                    'email' => $this->session->userdata('email')
                ]
            ], $modifyData);

            if (!empty($changed)) {
                if (!empty($modifySessionData)) {
                    $this->session->set_userdata($modifySessionData);
                }

                alert('프로필이 정상적으로 저장되었습니다.');
            } else {
                alert('프로필 변경에 실패하였습니다. 잠시 후 재시도해주세요.');
            }

        } else {
            alert('변경된 내용이 없습니다.');
        }

        redirect('/accounts/profile');
    }

}
