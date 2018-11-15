<?php
/**
 * Created by PhpStorm.
 * User: jungmin
 * Date: 2018-11-15
 * Time: 오전 1:25
 */

class SnsLogin extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function refreshToken()
    {
        $this->load->model('Auth_model','auth_model');
        $refreshTokenTarget = $this->auth_model->getRefreshTokenTarget();
    }
}