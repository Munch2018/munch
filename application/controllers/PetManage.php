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
    const REGISTER_MAX_CNT = 5;

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('form');

        $this->checkLogin();

    }

    public function checkLogin()
    {
        if (empty($this->session->userdata('member_idx'))) {
            alert('로그인이 필요한 서비스입니다.', '/member');
        }

        return true;
    }

    public function index()
    {
        $this->load->model('Common_code', 'commonCode');
        $data['dog_kind'] = $this->commonCode->get_codes('1');
        $data['cat_kind'] = $this->commonCode->get_codes('2');
        $data['character'] = $this->commonCode->get_codes('3');

        $this->load->view('common/header.html');
        $this->load->view('PetManage/index.html', $data);
        $this->load->view('common/footer.html');
    }

    /**
     * 파일 유무
     * @param $file
     * @return bool
     */
    private function isFile($file)
    {
        if ($file['pet-img']['error'] == 4) {
            return false;
        }

        return true;
    }

    public function canPetRegister()
    {
        $this->load->model('Pet_manage', 'petmanage');
        $member_idx = $this->session->userdata('member_idx');

        $pets = $this->petmanage->getPets($member_idx);

        return (count($pets) < self::REGISTER_MAX_CNT);
    }

    public function register()
    {
        if (!$this->canPetRegister()) {
            alert('우리아이 정보등록은 최대 5개까지 등록이 가능합니다.', '/petManage');
        }

        $params = $this->input->post();

        try {
            $params['img_src'] = '';
            $code = true;

            if ($this->isFile($_FILES)) {
                $result = $this->upload($_FILES);
                $code = $result['code'];
                $params['img_src'] = $result['img_src'];
            }

            if (!empty($code)) {
                $this->load->model('Pet_manage', 'petManage');

                if ($this->petManage->insert($this->validate($params))) {
                    alert('우리아이 등록이 완료되었습니다.', '/petManage');
                } else {
                    alert('우리아이 등록이 실패되었습니다.', '/petManage');
                }
            }
        } catch (Exception $e) {
            alert('우리아이 등록이 실패되었습니다.', '/petManage');
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
        $extArr = explode('.', $_FILES['pet-img']['name']);
        $ext = $extArr[count($extArr) - 1];
        $name = date('YmdHis') . '_' . $this->session->userdata('member_idx') . '.' . $ext;

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
            return ['code' => false, 'img_src' => ''];
        }

        // 확장자 확인
        if (!in_array($ext, $allowed_ext)) {
            alert("허용되지 않는 확장자입니다.");
            return ['code' => false, 'img_src' => ''];
        }

        // 파일 이동
        if (!move_uploaded_file($_FILES['pet-img']['tmp_name'], "$uploads_dir/$name")) {
            return [
                'code' => false,
                'img_src' => ''
            ];
        }
        return [
            'code' => true,
            'img_src' => $uploads_dir . '/' . $name
        ];
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
            'name' => $params['pet_name'],
            'pet_type' => $params['pet_type'],
            'pet_kind' => $params['kind'],
            'pet_size' => $params['size'],
            'character_type' => $params['character'],
            'detail1' => !empty($params['special'][0]) ? $params['special'][0] : '',
            'detail2' => !empty($params['special'][1]) ? $params['special'][1] : '',
            'detail3' => !empty($params['special'][2]) ? $params['special'][2] : '',
            'img_src' => $params['img_src']
        ];

        return $insertData;
    }
}