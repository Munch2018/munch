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
        $this->load->model('Board_model', 'board');
    }

//    public function qna($type = "")
//    {
//        $data = array();
//
//        if ($type == "") {
//            $data['type'] = "product";
//        }
//
//        $this->load->view('common/header');
//        $this->load->view('board/qna', $data);
//        $this->load->view('common/footer');
//    }

    public function lists($type = "")
    {
        $data = array();

        if ($type == "refund" || $type == "order") {
            $type_check = array(
                'refund'    => 'refund/change',
                'order'     => 'order/shipping',
            );
            $data['type'] = $type_check[$type];
        } else {
            if ($type == "") {
                $data['type'] = "product";
            } else {
                $data['type'] = $type;
            }
        }

        $data['list'] = $this->board->getLists(array('where_list' => array('board_type' => $data['type'], 'code_common_group_idx' => 4)));
        $data['total_count'] = $this->board->getCount(array('where_list' => array('board_type' => $data['type'], 'code_common_group_idx' => 4)));

        $this->load->view('common/header');
        $this->load->view('board/lists', $data);
        $this->load->view('common/footer');
    }

    /**
     * 글을 작성할 때 사용되는 함수
     */

    public function write(){

        $data = array(
            'title'         => $this->input->post('title'),
            'board_type'    => $this->input->post('board_type'),
            'contents'      => $this->input->post('contents'),

        );

        if ($this->board->doRegister($data) === false){
            alert("글 작성에 실패하였습니다.", '/board/lists');
        } else {
            alert("글 작성에 성공하였습니다.", "/board/lists");
        }

    }



    /**
     * write_form 글 작성 폼
     */
    public function write_form(){
        $data = array();
        $this->load->view('common/header');
        $this->load->view('board/write_form', $data);
        $this->load->view('common/footer');
    }
    
    /**
     * modify_form 글 수정하는폼
     */
    public function modify_form($board_idx = 0){
        $this->load->model('Common_code');
        $data = array();
        $board_idx = $this->input->post('board_idx') ? $this->input->post('board_idx') : $board_idx;

        $data['board_info'] = $this->board->getBoard(array('where' => array('board_idx' => $board_idx)));

        echo "<pre>";
        print_r($data);
        echo "</pre>";

        //board_type code_commondetail 연결
//76 | 81

        if (isset($data['board_info']['board_type']) && $data['board_info']['board_type'] != "") {
            foreach (explode(' | ', $data['board_info']['board_type']) as $value) {

                $data_info = $this->Common_code->get_codes($value);
                echo '<pre>';
                print_r($data_info); echo '</pre>'; exit;
            }
        }

        $this->load->view('common/header');
        $this->load->view('board/write_form', $data);
        $this->load->view('common/footer');
    }

}