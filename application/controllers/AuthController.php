<?php
/**
 * Created by PhpStorm.
 * User: jungmin
 * Date: 2018-09-25
 * Time: 오후 11:35
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class AuthController extends CI_Controller
{

    public function index()
    {
        echo print_r($_POST, 1);
        echo print_r($_GET, 1);
        exit;
    }



}