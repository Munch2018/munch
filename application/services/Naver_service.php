<?php
/**
 * Created by PhpStorm.
 * User: jungmin
 * Date: 2018-11-15
 * Time: 오전 12:31
 */

class Naver_service extends MY_Service
{
    const NAVER_CLIENT_ID = "kdCLf_xSwQuoDQPhffGy";
    const NEVER_CLIENT_SECRET = "CcAS1FT7Sr";
    const NAVER_CLIENT_RETURN = "http://munchmunch.kr/OAuth/naver";
    const NAVER_CLIENT_ME_RETURN = "https://openapi.naver.com/v1/nid/me";

    public function __construct()
    {
        $this->load->service('login_service', '', true);
        $this->load->model('Auth_model', 'auth_model');
        $this->load->model('Member_model', 'member_model');
    }

    public function naverLogin()
    {
        $client_id = self::NAVER_CLIENT_ID; // 위에서 발급받은 Client ID 입력
        $redirectURI = urlencode(self::NAVER_CLIENT_RETURN); //자신의 Callback URL 입력
        $state = "RAMDOM_STATE";
        $apiURL = "https://nid.naver.com/oauth2.0/authorize?response_type=code&client_id=" . $client_id . "&redirect_uri=" . $redirectURI . "&state=" . $state;

        redirect($apiURL);
    }

    public function naver()
    {
        $client_id = self::NAVER_CLIENT_ID;   //ClientID 입력
        $client_secret = self::NEVER_CLIENT_SECRET; //Client Secret 입력

        $code = $_GET["code"];
        $state = $_GET["state"];
        $redirectURI = urlencode(self::NAVER_CLIENT_RETURN); // 현재 Callback Url 입력

        $url = "https://nid.naver.com/oauth2.0/token?grant_type=authorization_code&client_id=" . $client_id . "&client_secret=" . $client_secret . "&redirect_uri=" . $redirectURI . "&code=" . $code . "&state=" . $state;
        $is_post = false;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, $is_post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $headers = array();
        $response = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($status_code !== 200) {
            alert('로그인에 실패하였습니다 ', '', 1);
            return false;
        }

        $responseArr = json_decode($response, true);
        echo '<pre>' . var_export($responseArr, 1) . '</pre>';
        exit;

        $_SESSION['naver_access_token'] = $responseArr['access_token'];
        $_SESSION['naver_refresh_token'] = $responseArr['refresh_token'];
        $_SESSION['naver_refresh_token_expires_in'] = $responseArr['expires_in'];

        // 토큰값으로 네이버 회원정보 가져오기
        $me_headers = array(
            'Content-Type: application/json',
            sprintf('Authorization: Bearer %s', $responseArr['access_token'])
        );

        $me_is_post = false;
        $me_ch = curl_init();
        curl_setopt($me_ch, CURLOPT_URL, self::NAVER_CLIENT_ME_RETURN);
        curl_setopt($me_ch, CURLOPT_POST, $me_is_post);
        curl_setopt($me_ch, CURLOPT_HTTPHEADER, $me_headers);
        curl_setopt($me_ch, CURLOPT_RETURNTRANSFER, true);
        $me_response = curl_exec($me_ch);
        $me_status_code = curl_getinfo($me_ch, CURLINFO_HTTP_CODE);
        curl_close($me_ch);

        $me_responseArr = json_decode($me_response, true);

        if (!empty($me_responseArr['response']['email'])) {
            $email = $me_responseArr['response']['email'];

            $alreadyData = $this->auth_model->getMemberSns(['email' => $email]);

            //회원정보가 있다면
            if (!empty($alreadyData['member_sns_idx']) && $alreadyData['sns_type'] == 'naver') {
                if ($alreadyData['refresh_token'] == $responseArr['refresh_token'] || $this->isTest($me_responseArr['response']['email'])) {
                    if ($this->auth_model->updateToken([
                        'token' => $responseArr['access_token'],
                        'refresh_token' => $responseArr['refresh_token'],
                        'member_sns_idx' => $alreadyData['member_sns_idx']
                    ])) {
                        $this->login_service->login($alreadyData);
                    } else {
                        alert('로그인에 실패하였습니다.', '', 1);
                        return false;
                    }
                } else {
                    /**
                     * @todo 토큰 갱신 업데이트
                     */
                }
            } elseif (!empty($alreadyData)) {
                alert('해당 이메일은 이미 사용되고 있습니다.', '', 1);
                return false;
            } else {
                $email = $me_responseArr['response']['email']; // 이메일
                $name = $me_responseArr['response']['name']; // 닉네임

                $refresh_token_expires_date = $this->getExpireDate();

                // 멤버 DB에 토큰과 회원정보를 넣고 로그인
                if (!$this->login_service->join([
                    'type' => 'naver',
                    'email' => $email,
                    'name' => !empty($name) ? $name : 'naver',
                    'token' => $responseArr['access_token'],
                    'refresh_token' => $responseArr['refresh_token'],
                    'refresh_token_expires_date' => $refresh_token_expires_date
                ])) {
                    alert('로그인에 실패하였습니다.', '', 1);
                    return false;
                }
            }
        }
    }

    public function getExpireDate($hour)
    {
        if (empty($hour)) {
            return '0000-00-00';
        }

        $refresh_token_expires_date = '';
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