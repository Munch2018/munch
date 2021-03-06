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
        $this->load->model('Pet_manage', 'petmanage');
        $this->load->service('Common_code_service', 'code_service');
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
        if (!$this->canPetRegister()) {
            alert('우리아이 정보등록은 최대 5개까지 등록이 가능합니다.');
            return false;
        }

        $data['dog_kind'] = $this->code_service->getCode('dog_kind');
        $data['cat_kind'] =  $this->code_service->getCode('cat_kind');
        $data['character'] = $this->code_service->getCode('character');

        $this->load->view('common/header.html');
        $this->load->view('PetManage/index.html', $data);
        $this->load->view('common/footer.html');
    }

    public function edit($pet_idx)
    {
        if (empty($pet_idx)) {
            redirect('/review');
        }

        $this->load->model('Pet_manage', 'model');

        $data['pet_kind']['dog'] = $this->code_service->getCode('dog_kind');
        $data['pet_kind']['cat'] =  $this->code_service->getCode('cat_kind');
        $data['character'] = $this->code_service->getCode('character');

        $data['pet_info'] = $this->model->getPets($this->session->userdata('member_idx'), $pet_idx);
        $data['pet_info'] = array_shift($data['pet_info']);
        if (!empty($data['pet_info']['birth'])) {
            list($data['pet_info']['birth_year'], $data['pet_info']['birth_month'], $data['pet_info']['birth_day']) = explode('-',
                $data['pet_info']['birth']);
        }
        if (!empty($data['pet_info']['character_type'])) {
            $data['pet_info']['character_array'] = explode('|', $data['pet_info']['character_type']);
        }
        if (!empty($data['pet_info']['pet_kind'])) {
            $data['pet_info']['pet_kind_array'] = explode('|', $data['pet_info']['pet_kind']);
        }
        $data['form_type'] = 'edit';
        $this->load->view('common/header.html');
        $this->load->view('PetManage/edit.html', $data);
        $this->load->view('common/footer.html');
    }

    public function canPetRegister()
    {
        $member_idx = $this->session->userdata('member_idx');

        $pets = $this->petmanage->getPets($member_idx);

        return (count($pets) < self::REGISTER_MAX_CNT);
    }

    public function deletePet()
    {
        $params = $this->input->post();

        if (empty($params['pet_idx'])) {
            alert('펫에 대한 정보가 정확하지 않습니다.');
            return false;
        }

        $this->load->model('Pet_manage', 'petManage');
        if ($this->petManage->update([
            'use_fl' => 'n',
            'pet_idx' => $params['pet_idx'],
            'member_idx' => $this->session->userdata('member_idx')
        ])) {
            alert('삭제되었습니다.', '/review');
        } else {
            alert('삭제에 실패하였습니다.');
            return false;
        }
    }

    public function register()
    {
        $params = $this->input->post();

        try {
            if (!empty($_FILES['pet_img']['name'])) {
                $upload_result = $this->upload($_FILES);
                $params['img_src'] = str_replace('/var/www/html/branches/munch', '',
                    $upload_result['upload_data']['full_path']);
            }

            if (!empty($upload_result['error'])) {
                alert('파일 업로드에 실패하였습니다. 재시도해주세요.');
                return false;
            } else {
                $this->load->model('Pet_manage', 'petManage');
                //업데이트
                if (!empty($params['pet_idx'])) {
                    if ($this->petManage->update(['pet_idx' => $params['pet_idx']] + $this->validate($params))) {
                        alert('우리아이 수정이 완료되었습니다.', '/review');
                    } else {
                        alert('우리아이 수정이 실패되었습니다.');
                        return false;
                    }
                } else {
                    if ($this->petManage->insert($this->validate($params))) {
                        alert('우리아이 등록이 완료되었습니다.', '/review');
                    } else {
                        alert('우리아이 등록이 실패되었습니다.');
                        return false;
                    }
                }
            }
        } catch (Exception $e) {
            alert('우리아이 등록/수정이 실패되었습니다.');
            return false;
        }
    }

    public function upload()
    {
        $folderName = date('Ym');
        $uploadDir = '/var/www/html/branches/munch/img/pet-img/' . $folderName;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
            chmod($uploadDir, 0777);
        }

        $config['upload_path'] = $uploadDir;
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['encrypt_name'] = TRUE;
//        $config['max_size'] = 500;
//        $config['max_width'] = 1024;
//        $config['max_height'] = 768;

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('pet_img')) {
            $error = array('error' => $this->upload->display_errors());
            return $error;
        } else {
            $data = array('upload_data' => $this->upload->data());
            return $data;
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
            'name' => !empty($params['pet_name']) ? $params['pet_name'] : '',
            'pet_type' => !empty($params['pet_type']) ? $params['pet_type'] : '',
            'pet_kind' => !empty($params['kind']) ? $params['kind'] : '',
            'gender' => !empty($params['gender']) ? $params['gender'] : '',
            'character_type' => !empty($params['character']) ? $params['character'] : '',
            'detail1' => !empty($params['special'][0]) ? $params['special'][0] : '',
            'detail2' => !empty($params['special'][1]) ? $params['special'][1] : '',
            'detail3' => !empty($params['special'][2]) ? $params['special'][2] : ''
        ];

        if (!empty($params['img_src'])) {
            $insertData['img_src'] = $params['img_src'];
        }

        return $insertData;
    }


    public function getBreedsCode()
    {
        $params = $_GET;
        $pet_type = !empty($params['pet_type']) ? $params['pet_type'] : 'dog';
        $option =  !empty($params['option']) ? $params['option'] : '';
        $all_codes = [];

        switch ($pet_type) {
            case 'dog' :
                $all_codes = $this->code_service->getBreedsCode('dog_breeds', $option);
                break;
            case 'cat' :
                $all_codes = $this->code_service->getBreedsCode('cat_breeds',$option);
                break;
        }

        if (empty($all_codes)) {
            echo json_encode('');
            exit;
        }

        $html = '';
        if (empty($option)) {
            foreach ($all_codes as $option_div => $codes) {
                foreach ($codes as $val) {
                    $html .= '<span class="list" data-value="' . $val['code'] . '"><span class="name">' . $val['name'] . '</span><span class="extra">' . $val['name_extra'] . '</span></span>';
                }
            }
        } else {
            foreach ($all_codes as $code => $val) {
                $html .= '<span class="list" data-value="' . $val['code'] . '"><span class="name">' . $val['name'] . '</span><span class="extra">' . $val['name_extra'] . '</span></span>';
            }
        }

        echo json_encode($html);
        exit;
    }
}