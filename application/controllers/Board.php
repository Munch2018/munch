<?php
/**
 * Created by PhpStorm.
 * User: eunju
 * Date: 2018-07-15
 * Time: 오후 8:25
 */

class Board extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function qna($type = "")
    {
        $this->load->view('common/header');

        if ($type == "") {
            $data['type'] = "product";
        }

        $this->load->view('board/qna', $data);
        $this->load->view('common/footer');
    }

}