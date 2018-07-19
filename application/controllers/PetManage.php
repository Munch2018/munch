<?php
/**
 * Created by PhpStorm.
 * User: jungmin
 * Date: 2018-07-11
 * Time: 오후 9:13
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class PetManage extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('form');

        $this->checkLogin();

    }

    public function checkLogin()
    {
//        if (empty($this->session->member_idx)) {
//            redirect('/member');
//        }

        return true;
    }

    public function index()
    {
        $this->load->model('Common_code', 'commonCode');
        $data['dog_kind'] = $this->commonCode->get_codes('1');
        $data['cat_kind'] = $this->commonCode->get_codes('2');
        $data['character'] = $this->commonCode->get_codes('3');

        $this->load->view('common/header');
        $this->load->view('PetManage/index', $data);
        $this->load->view('common/footer');
    }

    public function register()
    {
        $params = $this->input->post();

        try {
            list($code, $img_src) = $this->upload($_FILES);

            if (!empty($code)  && !empty($img_src)) {
                $this->load->model('Pet_manage', 'petManage');

                $params['img_src'] = $img_src;
                if ($this->petManage->insert($params)) {
                    alert('우리아이 등록이 완료되었습니다.');
                    redirect('/petManage');
                } else {
                    alert('우리아이 등록이 실패되었습니다.');
                    redirect('/petManage');
                }
            }

        } catch (Exception $e) {

        }
    }

    /**
     * 파일 업로드
     * @param $file
     * @return array
     */
    private function upload($file)
    {
        /**
         * @todo
         */
        $uploads_dir = 'C:\workspace\munch\img\pet-img';
        $allowed_ext = array('jpg', 'jpeg', 'png', 'gif');
        $error = $file['pet-img']['error'];
        $extArr = explode('/', $_FILES['pet-img']['type']);
        $ext = $extArr[count($extArr) - 1];
        $name = date('YmdHis') . $this->session->member_idx . '.' . $ext;


        if ($error != UPLOAD_ERR_OK) {
            switch ($error) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    alert("파일이 너무 큽니다. ($error)");
                    break;
                case UPLOAD_ERR_NO_FILE:
                    alert("파일이 첨부되지 않았습니다. ($error)");
                    break;
                default:
                    alert("파일이 제대로 업로드되지 않았습니다. ($error)");
                    break;
            }
            return ['code' => false];
        }

        // 확장자 확인
        if (!in_array($ext, $allowed_ext)) {
            alert("허용되지 않는 확장자입니다.");
            return ['code' => false];
        }

        // 파일 이동
        if (move_uploaded_file($_FILES['pet-img']['tmp_name'], "$uploads_dir/$name")) {
            return [
                'code' => true,
                'img_src' => $uploads_dir . '/' . $name
            ];
        }
    }

    private function validate($params)
    {
        if (!empty($params['birth_month'])) {
            $params['birth_month'] = sprintf("%02d", $params['birth_month']);
        }
        if (!empty($params['birth_day'])) {
            $params['birth_day'] = sprintf("%02d", $params['birth_day']);
        }

        $insertData = [
            'birth' => implode('-', [$params['birth_year'], $params['birth_month'], $params['birth_day']]),
            'use_fl' => 'y',
            'member_idx' => $this->session->member_idx,
            'reg_idx' => $this->session->member_idx,
            'name' => $params['pet_name'],
            'pet_type' => $params['pet_type'],
            'pet_kind' => $params['kind'],
            'character_type' => $params['character'],
            'detail1' => !empty($params['special'][0]) ? $params['special'][0] : '',
            'detail2' => !empty($params['special'][1]) ? $params['special'][1] : '',
            'detail3' => !empty($params['special'][2]) ? $params['special'][2] : ''
        ];

        return $insertData;
    }

}
