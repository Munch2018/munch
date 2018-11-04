<?php
/**
 * Created by PhpStorm.
 * User: eunju
 * Date: 2018-07-23
 * Time: 오후 11:33
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Review extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('review_model');

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
        if ($this->getPetCount() === 0) {
            redirect('/petManage/');
            return false;
        }

        $this->lists();
    }

    public function getPetCount()
    {
        $member_idx = $this->session->userdata('member_idx');
        $this->load->model('Pet_manage', 'petmanage');
        $pets = $this->petmanage->getPets($member_idx);

        return count($pets);
    }

    public function lists()
    {
        $this->load->model(
            array(
                'pet_manage',
                'order_model',
                'Goods',
                'Review_model'
            )
        );

        $data = array();
        $pet_idx = 0;
        $params = $_GET;

        $member_idx = $this->session->userdata('member_idx');
        $data['pet_list'] = $this->pet_manage->getPets($member_idx);

        if (!empty($params['pet_idx'])) {
            $pet_idx = $params['pet_idx'];
        } elseif (empty($pet_idx) && !empty($data['pet_list'][0])) {
            $pet_idx = $data['pet_list'][0]['pet_idx'];
        }

        $data['goods_list'] = $this->order_model->getOrders(['pet_idx' => $pet_idx, 'member_idx' => $member_idx]);
        $data['pet_idx'] = $pet_idx;
        $data['review_list'] = $this->review_model->getList([
            'where' => [
                'pet_idx' => $pet_idx,
                'member_idx' => $this->session->userdata('member_idx'),
                'use_fl' => 'y'
            ]
        ]);

        $this->load->view('common/header.html');
        $this->load->view('review/index.phtml', $data);
        $this->load->view('common/footer.html');
    }

    public function goodsList($pet_idx = 0)
    {
        $this->load->model(
            array(
                'pet_manage',
                'order_model',
                'Goods',
                'review_model'
            )
        );

        $data = array();
        $params = $_GET;
        $pet_idx = !empty($pet_idx) ? $pet_idx : $params['pet_idx'];
        $member_idx = $this->session->userdata('member_idx');
        $data['pet_list'] = $this->pet_manage->getPets($member_idx, $pet_idx);

        if (empty($pet_idx)) {
            alert('요청이 정상적이지 않습니다.');
            return false;
        }

        $pet_info = $data['pet_list'][0];

        $data['review_list'] = $this->review_model->getList([
            'where' => [
                'member_idx' => $member_idx,
                'pet_idx' => $pet_idx,
                'use_fl' => 'y'
            ]
        ]);
        $data['goods_list'] = $this->order_model->getOrders(['pet_idx' => $pet_idx, 'member_idx' => $member_idx]);
        $data['pet_info'] = $pet_info;
        $result['contents'] = $this->load->view('review/goods-list.html', $data);

        return json_encode($result);
    }

    /**
     * 리뷰 등록하는
     */
    public function register()
    {
        $member_idx = $this->session->userdata('member_idx');
        $params = $this->input->post();

        if (empty($member_idx)) {
            alert("로그아웃 되었습니다. 로그인을 해주세요.");
            return false;
        }

        if (empty($params['pet_idx']) || empty($params['goods_idx'])) {
            alert("정상적인 요청이 아닙니다. 페이지를 새로고침 후 재시도해주세요.");
            return false;
        }

        $reviewData = $this->review_model->getReview([
            'where' => [
                'member_idx' => $member_idx,
                'pet_idx' => $params['pet_idx'],
                'goods_idx' => $params['goods_idx'],
                'use_fl' => 'y'
            ]
        ]);

        $data = array(
            'member_idx' => $member_idx,
            'goods_idx' => $params['goods_idx'],
            'pet_idx' => $params['pet_idx'],
            'score_level' => !empty($params['score_level']) ? $params['score_level'] : 0,
            'dislike' => !empty($params['chk_dislike']) ? $params['chk_dislike'] : 'n',
            'like' => !empty($params['chk_like']) ? $params['chk_like'] : 'n',
            'comment' => !empty($params['comment']) ? $params['comment'] : '',
            'use_fl' => 'y',
        );

        if (!empty($reviewData)) {
            if ($this->review_model->doUpdate([
                    'score_level' => !empty($params['score_level']) ? $params['score_level'] : 0,
                    'dislike' => !empty($params['chk_dislike']) ? $params['chk_dislike'] : 'n',
                    'comment' => !empty($params['comment']) ? $params['comment'] : '',
                ], ['where' => ['review_idx' => $reviewData['review_idx']]]) === false) {
                alert("리뷰 수정에 실패하였습니다.");
            } else {
                alert("리뷰 수정 완료하였습니다.", "/review/");
            }
        } else {
            if ($this->review_model->doRegister($data) === false) {
                alert("리뷰 등록에 실패하였습니다.");
            } else {
                alert("리뷰 등록 완료하였습니다.", "/review/");
            }
        }

        exit;
    }

    /**
     * 또 받고 싶어요, 평점 클릭 시
     */
    public function sendLike()
    {
        $member_idx = $this->session->member_idx;
        if (empty($member_idx)) {
            echo json_encode('logout');
            exit;
        }

        $pet_idx = $this->input->get_post('pet_idx');
        $goods_idx = $this->input->get_post('goods_idx');
        $code = $this->input->get_post('code');
        $value = $this->input->get_post('value');

        $reviewData = $this->review_model->getReview([
            'where' => [
                'member_idx' => $member_idx,
                'pet_idx' => $pet_idx,
                'goods_idx' => $goods_idx,
                'use_fl' => 'y'
            ]
        ]);

        //등록된 리뷰가 없으면 새로 입력
        if (empty($reviewData)) {
            $data = array(
                'member_idx' => $member_idx,
                'goods_idx' => $goods_idx,
                'pet_idx' => $pet_idx,
                'use_fl' => 'y',
            );
            if (in_array($code, ['like', 'dislike', 'score_level'])) {
                if (in_array($code, ['like', 'dislike'])) {
                    $value = !empty($reviewData[$code]) && $reviewData[$code] === 'y' ? 'n' : 'y';
                }
            }
            echo json_encode($this->review_model->doRegister($data+[$code => $value]) === false ? 'fail' : 'success');
            exit;
        }

        //좋아요, 싫어요, 별점 업데이트
        if (in_array($code, ['like', 'dislike', 'score_level'])) {
            if (in_array($code, ['like', 'dislike'])) {
                $value = !empty($reviewData[$code]) && $reviewData[$code] === 'y' ? 'n' : 'y';
            }

            if ($this->review_model->doUpdate([$code => $value],
                ['where' => ['review_idx' => $reviewData['review_idx'], 'member_idx' => $member_idx]])) {
                echo json_encode('success');
            } else {
                echo json_encode('fail');
            }
        }

        exit;
    }


    public function reviewLayer()
    {
        $params = $_GET;

        $member_idx = $this->session->userdata('member_idx');
        $this->load->model('Pet_manage', 'petmanage');

        $data = [];
        $data['pet'] = $this->petmanage->getPets($member_idx, $params['pet_idx']);
        $data['pet'] = array_shift($data['pet']);

        $data['review_data'] = $this->review_model->getReview([
            'where' => [
                'member_idx' => $member_idx,
                'pet_idx' => $params['pet_idx'],
                'goods_idx' => $params['goods_idx'],
                'use_fl' => 'y'
            ]
        ]);

        $contents = $this->load->view('review/review_layer.html', $data);
        echo json_encode($contents);
    }
}