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

    public function __construct()
    {
        parent::__construct();
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
        echo print_r($_POST, 1);
        echo print_r($_GET, 1);
        exit;
    }

    public function kakaoLogin()
    {
        $restAPIKey = "10937aa222a35af6980c19eb574b9def"; //본인의 REST API KEY를 입력해주세요
        $callbacURI = urlencode("http://munchmunch.kr/OAuth/kakao"); //본인의 Call Back URL을 입력해주세요
        $kakaoLoginUrl = "https://kauth.kakao.com/oauth/authorize?client_id=" . $restAPIKey . "&redirect_uri=" . $callbacURI . "&response_type=code";

        redirect($kakaoLoginUrl);
    }

}