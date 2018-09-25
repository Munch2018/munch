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


    public function index(){
        $this->lists();
    }

    public function lists($type = "")
    {
        $data = array();
        $type = $type != "" ? $type : $this->input->get('board_type');

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

        // 조건문
        $where = array();
        $where['where']['use_fl'] = 'y';

        if ($this->input->get('search_value') != "") {
            $where['where_like']['title'] = $this->input->get('search_value');
        }

        $where['where_list'] = array('board_type' => $data['type'], 'code_common_group_idx' => 4);

        $data['list'] = $this->board->getLists($where);
        $data['total_count'] = $this->board->getCount($where);

        $this->load->view('common/header.html');
        $this->load->view('board/lists.html', $data);
        $this->load->view('common/footer.html');
    }

    /**
     * 글을 작성할 때 사용되는 함수
     */

    public function write()
    {
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
     * modify 기능
     */
    public function modify()
    {
        $board_info = $this->board->getBoard(array('where' => array('board_idx' => $this->input->post('board_idx'))));

        if (empty($board_info)) {
            alert("해당되는 게시물을 찾을 수 없습니다.", "/board/lists/");
        } else {
            //ㄱㅔ시판 정보를 찾았을때, 데이터를 모아서 update쳐준다.
            $board_data = array(
                'title'     => $this->input->post('title'),
                'contents'  => $this->input->post('contents'),
            );

            //타입때문에 세분화 해준다.
            if ($this->input->post('board_type') == "refund/change" || $this->input->post('board_type') == 'order/shipping') {
                $type_check = array(
                    'refund/change'     => 'refund',
                    'order/shipping'    => 'order'
                );
                $type = $type_check[$this->input->post('board_type')];
            } else {
                $type = $this->input->post('board_type');
            }

            if ($this->board->doModify($board_data, array('where' => array('board_idx' => $this->input->post('board_idx')))) === false){
                alert("수정 실패하였습니다.");
            } else {
                alert("수정 완료되었습니다.", '/board/lists/'.$type);
            }
        }
    }

    public function delete($board_idx = 0, $type = "")
    {
        if ($board_idx <= 0) {
            alert("해당 게시물을 찾을 수 없습니다.");
        } else {
            $board_data = array(
                'use_fl'    => 'N'
            );

            $return = $this->get_type($type);

            if($this->board->doDelete($board_data, array('where' => array('board_idx' => $board_idx))) === false) {
                alert("글 삭제 실패하였습니다.", '/board/lists/'.$return);
            } else {
                alert("글 삭제 완료하였습니다.", '/board/lists/'.$return);
            }
        }
    }

    public function get_type($board_type = ""){
        //타입때문에 세분화 해준다.

        if ($board_type == "") {
            $board_type = $this->input->post('board_type');
        }

        if ($board_type == "refund/change" || $board_type == 'order/shipping') {
            $type_check = array(
                'refund/change'     => 'refund',
                'order/shipping'    => 'order'
            );
            $type = $type_check[$$board_type];
        } else {
            $type = $board_type;
        }

        return $type;
    }



    /**
     * write_form 글 작성 폼
     */
    public function write_form(){
        $data = array();
        $this->load->view('common/header.html');
        $this->load->view('board/write_form.html', $data);
        $this->load->view('common/footer.html');
    }
    
    /**
     * modify_form 글 수정하는폼
     */
    public function modify_form($board_idx = 0){
        $this->load->model('Common_code');
        $data = array();
        $board_idx = $this->input->post('board_idx') ? $this->input->post('board_idx') : $board_idx;

        $data['board_info'] = $this->board->getBoard(array('where' => array('board_idx' => $board_idx)));

        if (isset($data['board_info']['board_type']) && $data['board_info']['board_type'] != "") {
            foreach (explode(' | ', $data['board_info']['board_type']) as $value) {

                //board_type 에서 카테고리는 무조건 하나라고 생각하자
                $code_info = $this->Common_code_service->getCode(array('where' => array('code_common_idx' => $value, 'use_fl' => 'y', 'code_common_group_idx' => 4)));
                if (!empty($code_info)) {
                    $data['code_info'] = $code_info;
                    break;
                }
            }
        }

        $this->load->view('common/header.html');
        $this->load->view('board/write_form.html', $data);
        $this->load->view('common/footer.html');
    }

}