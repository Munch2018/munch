<?php
/**
 * Created by PhpStorm.
 * User: jungmin
 * Date: 2018-11-15
 * Time: 오전 12:31
 */

class Kakao_service extends MY_Service
{
    const KAKAO_CLIENT_ID = '10937aa222a35af6980c19eb574b9def';
    const KAKAO_CLIENT_RETURN = "http://munchmunch.kr/OAuth/kakao";
    const KAKAO_CLIENT_ME_RETURN = "https://kapi.kakao.com/v2/user/me";

    public function __construct()
    {
        $this->load->service('login_service', '', true);
        $this->load->model('Auth_model', 'auth_model');
        $this->load->model('Member_model', 'member_model');
    }

    public function kakaoLogin()
    {
        $restAPIKey = self::KAKAO_CLIENT_ID; //본인의 REST API KEY를 입력해주세요
        $callbacURI = urlencode(self::KAKAO_CLIENT_RETURN); //본인의 Call Back URL을 입력해주세요
        $kakaoLoginUrl = "https://kauth.kakao.com/oauth/authorize?client_id=" . $restAPIKey . "&redirect_uri=" . $callbacURI . "&response_type=code";

        redirect($kakaoLoginUrl);
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
        echo '<pre>' . var_export($responseArr, 1) . '</pre>';

        $_SESSION['kakao_access_token'] = $responseArr['access_token'];
        $_SESSION['kakao_refresh_token'] = $responseArr['refresh_token'];
        $_SESSION['kakao_refresh_token_expires_in'] = $responseArr['refresh_token_expires_in'];

        //사용자 정보 가저오기
        $USER_API_URL = self::KAKAO_CLIENT_ME_RETURN;
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

            $alreadyData = $this->auth_model->getMemberSns(['email' => $email]);

            //회원정보가 있다면
            if (!empty($alreadyData['member_sns_idx']) && $alreadyData['sns_type'] == 'kakao') {
                if ($alreadyData['refresh_token'] == $responseArr['refresh_token'] || $this->isTest($me_responseArr['kakao_account']['email'])) {
                    if ($this->auth_model->updateToken([
                        'token' => $responseArr['access_token'],
                        'refresh_token' => $responseArr['refresh_token'],
                        'member_sns_idx' => $alreadyData['member_sns_idx'],
                        'refresh_token_expires_dt' => $this->getExpireDate($responseArr['refresh_token_expires_in'])
                    ])) {
                        $this->login_service->login($alreadyData);
                    } else {
                        alert('로그인에 실패하였습니다.', '', 1);
                        return false;
                    }
                } else {
                    alert('소셜로그인에 실패하였습니다.');
                    return false;
                }
            } elseif (!empty($alreadyData)) {
                alert('해당 이메일은 이미 사용되고 있습니다.', '', 1);
                return false;
            } else {
                if (empty($me_responseArr['kakao_account']['email'])) {
                    alert('로그인에 실패하였습니다. 소셜로그인에 이메일을 입력해주시기 바랍니다.', '', 1);
                    return false;
                }

                // properties 항목은 카카오 회원이 설정한 경우만 넘겨 받습니다.
                $email = $me_responseArr['kakao_account']['email']; // 이메일
                $name = $me_responseArr['properties']['nickname']; // 닉네임
                $refresh_token_expires_date = $this->getExpireDate($responseArr['refresh_token_expires_in']);

                // 멤버 DB에 토큰과 회원정보를 넣고 로그인
                if (!$this->login_service->join([
                    'type' => 'kakao',
                    'email' => $email,
                    'name' => !empty($name) ? $name : 'kakao',
                    'token' => $responseArr['access_token'],
                    'refresh_token' => $responseArr['refresh_token'],
                    'refresh_token_expires_dt' => $refresh_token_expires_date
                ])) {
                    alert('로그인에 실패하였습니다.', '', 1);
                    return false;
                }
            }
        } else {
            alert('계정 연동에 실패하였습니다.', '', 1);
            return false;
        }
    }


    public function getExpireDate($hour)
    {
        $refresh_token_expires_date = '0000-00-00';
        $refresh_token_expires_day = min(($hour / 60 / 60 / 24));
        if (!empty($refresh_token_expires_day) && $refresh_token_expires_day > 0) {
            $refresh_token_expires_date = date('Y-m-d',
                strtotime('Y-m-d' . " +" . $refresh_token_expires_day . " day"));
        }

        return $refresh_token_expires_date;
    }

    public function isTest($email)
    {
        return (in_array($email, ['zoselel123@hanmail.net']));
    }
}