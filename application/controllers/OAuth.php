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
        $this->load->model('Auth_model', 'auth_model');
        $this->load->model('Member_model', 'member_model');

        $this->load->service('naver_service', '', true);
        $this->load->service('facebook_service', '', true);
        $this->load->service('kakao_service', '', true);
    }

    public function snsLogin()
    {
        $sns = $_GET['sns'];

        if (empty($sns)) {
            return false;
        }

        switch ($sns) {
            case 'naver':
                $this->naver_service->naverLogin();
                break;
            case 'facebook':
                $this->facebook_service->facebookLogin();
                break;
            case 'kakao':
                $this->kakao_service->kakaoLogin();
                break;
        }
    }

    public function naver()
    {
        $this->naver_service->naver();
    }

    public function kakao()
    {
        $this->kakao_service->kakao();
    }

    public function facebook()
    {
        $this->facebook_service->facebook();
    }
}