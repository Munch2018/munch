<?php
/**
 * Created by PhpStorm.
 * User: jungmin
 * Date: 2018-09-25
 * Time: 오후 11:35
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class OAuth extends CI_Controller
{

    const KAKAO_CLIENT_ID = '10937aa222a35af6980c19eb574b9def';
    const KAKAO_CLIENT_RETURN = "http://munchmunch.kr/OAuth/kakao";


    public function __construct()
    {
        parent::__construct();
        $this->load->model('Auth_model', 'auth_model');
        $this->load->model('Member_model', 'member_model');
    }

    public function index()
    {

    }


    public function snsLogin()
    {
        $sns = $_GET['sns'];

        if (empty($sns)) {
            return false;
        }

        switch ($sns) {
            case 'naver':
                $this->naverLogin();
                break;
            case 'facebook':
                $this->facebookLogin();
                break;
            case 'kakao':
                $this->kakaoLogin();
                break;
        }
    }

    public function naver()
    {
        echo print_r($_POST, 1);
        echo print_r($_GET, 1);
        exit;
    }

    public function naverLogin()
    {
        $client_id = "kdCLf_xSwQuoDQPhffGy"; // 위에서 발급받은 Client ID 입력
        $redirectURI = urlencode("http://munchmunch.kr/OAuth/naver"); //자신의 Callback URL 입력
        $state = "RAMDOM_STATE";
        $apiURL = "https://nid.naver.com/oauth2.0/authorize?response_type=code&client_id=" . $client_id . "&redirect_uri=" . $redirectURI . "&state=" . $state;

        redirect($apiURL);
    }

    public function facebookLogin()
    {
        echo print_r($_POST, 1);
        echo print_r($_GET, 1);
        exit;
    }

    public function kakao()
    {
        if (empty($_GET['code'])) {
            return false;
        }

        //사용자 토큰 받기
        $code = $_GET["code"];
        $params = sprintf('grant_type=authorization_code&client_id=%s&redirect_uri=%s&code=%s', self::KAKAO_CLIENT_ID,
            self::KAKAO_CLIENT_RETURN, $code);

        $TOKEN_API_URL = "https://kauth.kakao.com/oauth/token";
        $opts = array(
            CURLOPT_URL => $TOKEN_API_URL,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSLVERSION => 1, // TLS
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $params,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false
        );

        $curlSession = curl_init();
        curl_setopt_array($curlSession, $opts);
        $accessTokenJson = curl_exec($curlSession);
        curl_close($curlSession);

        $responseArr = json_decode($accessTokenJson, true);
        $_SESSION['kakao_access_token'] = $responseArr['access_token'];
        $_SESSION['kakao_refresh_token'] = $responseArr['refresh_token'];
        $_SESSION['kakao_refresh_token_expires_in'] = $responseArr['refresh_token_expires_in'];

        //사용자 정보 가저오기
        $USER_API_URL = "https://kapi.kakao.com/v2/user/me";
        $opts = array(
            CURLOPT_URL => $USER_API_URL,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSLVERSION => 1,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer " . $responseArr['access_token']
            )
        );

        $curlSession = curl_init();
        curl_setopt_array($curlSession, $opts);
        $accessUserJson = curl_exec($curlSession);
        curl_close($curlSession);

        $me_responseArr = json_decode($accessUserJson, true);

        if (!empty($me_responseArr['kakao_account']['email'])) {
            $email = $me_responseArr['kakao_account']['email'];

            $alreadyData = $this->auth_model->getMemberSns(['type' => 'kakao', 'email' => $email]);
            echo print_r($me_responseArr,1).'<br><br>';
            echo print_r($responseArr,1).'<br><br>';
            echo print_r($alreadyData,1).'<br><br>';

exit;
            //회원정보가 있다면
            if (!empty($alreadyData['member_sns_idx'])) {
                if ($this->auth_model->updateToken([
                    'token' => $responseArr['access_token'],
                    'member_sns_idx' => $alreadyData['member_sns_idx']
                ])) {
                    $this->login($alreadyData);
                } else {
                    alert('로그인에 실패하였습니다.1', '', 1);
                    return false;
                }
            } else {
                // properties 항목은 카카오 회원이 설정한 경우만 넘겨 받습니다.
                $email = $me_responseArr['kakao_account']['email']; // 이메일
                $name = $me_responseArr['properties']['nickname']; // 닉네임

                // 멤버 DB에 토큰과 회원정보를 넣고 로그인
                if (!$this->join([
                    'type' => 'kakao',
                    'email' => $email,
                    'name' => !empty($name) ? $name : 'kakao',
                    'token' => $responseArr['access_token']
                ])) {
                    alert('로그인에 실패하였습니다.3', '', 1);
                    return false;
                }
            }
        }
    }

    public function kakaoLogin()
    {
        $restAPIKey = self::KAKAO_CLIENT_ID; //본인의 REST API KEY를 입력해주세요
        $callbacURI = urlencode(self::KAKAO_CLIENT_RETURN); //본인의 Call Back URL을 입력해주세요
        $kakaoLoginUrl = "https://kauth.kakao.com/oauth/authorize?client_id=" . $restAPIKey . "&redirect_uri=" . $callbacURI . "&response_type=code";

        redirect($kakaoLoginUrl);
    }

    public function login($member_info)
    {
        $session_data = array(
            'member_idx' => $member_info['member_idx'],
            'email' => $member_info ['email'],
            'name' => $member_info['name'],
            'telphone' => !empty($member_info['telphone']) ? $member_info['telphone'] : '',
            'is_admin' => !empty($member_info['is_admin']) ? $member_info['is_admin'] : 0
        );

        if ($member_info['is_admin'] === 1) {
            $session_data['is_admin'] = true;
            $this->load->vars(array('IS_ADMIN', true));
        }

        $this->session->set_userdata($session_data);

        echo "<script type='text/javascript'>  opener.location.href = '/'; ; self.close(); </script>";
        exit;
    }

    public function join($member_info)
    {
        if (empty($member_info['email'])) {
            alert('로그인에 실패하였습니다.2', '', 1);
            return false;
        }

        $join_data = array(
            'email' => $member_info['email'],
            'name' => $member_info['email'],
            'telphone' => '',
            'password' => '',
            'use_fl' => 'Y'
        );


        try {
            $this->member_model->db->trans_begin();
            $member_idx = $this->member_model->doRegister($join_data);

            if (!empty($member_idx)) {
                $join_sns_data['member_idx'] = $member_idx;
                $join_sns_data['token'] = $member_info['token'];
                $join_sns_data['type'] = $member_info['type'];
                $join_sns_data['use_fl'] = 'y';

                if ($this->auth_model->insertMemberSns($join_sns_data)) {
                    $this->login($join_data + $join_sns_data);
                    return true;
                }
            }

            $this->member_model->db->trans_complete();
        } catch (Exception $e) {
            $this->member_model->db->trans_rollback();
        }

        return false;
    }

}