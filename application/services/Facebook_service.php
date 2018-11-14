<?php
/**
 * Created by PhpStorm.
 * User: jungmin
 * Date: 2018-11-15
 * Time: 오전 12:32
 */

class Facebook_service extends MY_Service
{
    const FB_APP_ID = '272310950292907';
    const FB_SECRET_KEY = '275e5c46d4a3bfffe4a65fd1629eba62';

    public function __construct()
    {
        $this->load->service('login_service','',true);
        $this->load->model('Auth_model', 'auth_model');
        $this->load->model('Member_model', 'member_model');
    }

    public function facebookLogin()
    {
        $params = [
            'app_id' => self::FB_APP_ID, // Replace {app-id} with your app id
            'app_secret' => self::FB_SECRET_KEY,
            'default_graph_version' => 'v2.8',
            'permissions' => [
                'email'
            ]
        ];

        $loginUrl = redirect('https://example.com/fb-callback.php?'.http_build_query($params));

        echo '<a href="' . htmlspecialchars($loginUrl) . '">Log in with Facebook!</a>';
    }


    public function facebook()
    {

    }

    public function cancelFacebook()
    {

    }

    public function deleteFacebook()
    {

    }



    public function getExpireDate($hour)
    {
        if (empty($hour)) {
            return '0000-00-00';
        }

        $refresh_token_expires_date = '';
        $refresh_token_expires_day = min(($hour / 60 / 60 / 24));
        if (!empty($refresh_token_expires_day) && $refresh_token_expires_day > 0) {
            $refresh_token_expires_date = date('Y-m-d', strtotime('Y-m-d' . " +" . $refresh_token_expires_day . " day"));
        }

        return $refresh_token_expires_date;
    }

    public function isTest($email)
    {
        return (in_array($email, ['zoselel123@hanmail.net']));
    }

}