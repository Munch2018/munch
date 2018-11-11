<?php
/**
 * Created by PhpStorm.
 * User: jungmin
 * Date: 2018-11-11
 * Time: 오후 4:56
 */

class Pet extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }


    public function reg()
    {
        exit;
        $params =
            array(
                array('name' => '나폴레옹', 'name_extra' => 'Napoleon'),
                array('name' => '네벨룽', 'name_extra' => 'Nebelung'),
                array('name' => '노르웨이 숲 고양이', 'name_extra' => 'Norwegian Forest Cat'),
                array('name' => '데본렉스', 'name_extra' => 'Devon Rex'),
                array('name' => '라가머핀', 'name_extra' => 'Ragamuffin'),
                array('name' => '라이코이', 'name_extra' => 'Lykoi'),
                array('name' => '라팜', 'name_extra' => 'LaPerm'),
                array('name' => '랙돌', 'name_extra' => 'Ragdoll'),
                array('name' => '러시안 블루', 'name_extra' => 'Russian Blue'),
                array('name' => '맹크스', 'name_extra' => 'Manx Cat'),
                array('name' => '먼치킨', 'name_extra' => 'Munchkin'),
                array('name' => '메인쿤', 'name_extra' => 'Maine Coon'),
                array('name' => '발리네즈', 'name_extra' => 'Balinese'),
                array('name' => '뱅갈', 'name_extra' => 'Bengal'),
                array('name' => '버만', 'name_extra' => 'Birman'),
                array('name' => '버미즈', 'name_extra' => 'Burmese'),
                array('name' => '봄베이', 'name_extra' => 'Bombay Cat'),
                array('name' => '브리티시 숏 헤어', 'name_extra' => 'British Shorthair'),
                array('name' => '사바나', 'name_extra' => 'Savannah'),
                array('name' => '샤트룩스', 'name_extra' => 'Chartreux'),
                array('name' => '샴(샤미즈', 'name_extra' => 'Siamese Cat'),
                array('name' => '세이셸루아', 'name_extra' => 'Seychellois'),
                array('name' => '셀커크 렉스', 'name_extra' => 'Selkirk Rex'),
                array('name' => '소말리', 'name_extra' => 'Somali'),
                array('name' => '스노우슈', 'name_extra' => 'Snow Shoe'),
                array('name' => '스코티시 폴드', 'name_extra' => 'Scottish Fold'),
                array('name' => '스쿠컴', 'name_extra' => 'Skookum'),
                array('name' => '스핑크스', 'name_extra' => 'Sphynx'),
                array('name' => '시베리안 고양이', 'name_extra' => 'Siberian'),
                array('name' => '실론', 'name_extra' => 'Ceylon'),
                array('name' => '싱가퓨라', 'name_extra' => 'Singapura Cat'),
                array('name' => '싸이프러스 아프로디테', 'name_extra' => 'Cyprus Aphrodite'),
                array('name' => '아라비안 마우', 'name_extra' => 'Arabian Mau'),
                array('name' => '아메리칸 링테일', 'name_extra' => 'American Ringtail'),
                array('name' => '아메리칸 폴리덱틸', 'name_extra' => 'American Polydactyl'),
                array('name' => '아메리칸 밥테일', 'name_extra' => 'American Bobtail'),
                array('name' => '아메리칸 숏 헤어', 'name_extra' => 'American Shorthair'),
                array('name' => '아메리칸 와이어 헤어', 'name_extra' => 'American Wirehair'),
                array('name' => '아메리칸 컬', 'name_extra' => 'American Curl'),
                array('name' => '아비시니안', 'name_extra' => 'Abyssinian'),
                array('name' => '오리엔탈', 'name_extra' => 'Oriental'),
                array('name' => '오스트레일리안 미스트', 'name_extra' => 'Australian Mist'),
                array('name' => '오시캣', 'name_extra' => 'Ocicat'),
                array('name' => '오호스 아즐레스', 'name_extra' => 'Ojos Azules'),
                array('name' => '우랄렉스', 'name_extra' => 'Ural Rex'),
                array('name' => '유러피안 버미즈', 'name_extra' => 'European Burmese'),
                array('name' => '유러피안 숏 헤어', 'name_extra' => 'European Shorthair'),
                array('name' => '이그저틱', 'name_extra' => 'Exotic'),
                array('name' => '이집션 마우', 'name_extra' => 'Egyptian Mau'),
                array('name' => '자바니즈', 'name_extra' => 'Javanese'),
                array('name' => '재패니즈 밥테일', 'name_extra' => 'Japanese Bobtail'),
                array('name' => '저먼렉스', 'name_extra' => 'German Rex'),
                array('name' => '컬러포인트 숏 헤어', 'name_extra' => 'Colorpoint Shorthair'),
                array('name' => '컬러포인트 스팽글드', 'name_extra' => 'Colorpoint Spangled'),
                array('name' => '코니시 렉스', 'name_extra' => 'Cornish Rex'),
                array('name' => '코랫', 'name_extra' => 'Korat'),
                array('name' => '코리안 숏 헤어', 'name_extra' => 'Korean Shorthair'),
                array('name' => '쿠리리안 밥테일', 'name_extra' => 'Kurilian Bobtail'),
                array('name' => '킴릭', 'name_extra' => 'Cymric'),
                array('name' => '터키시반', 'name_extra' => 'Turkish Van'),
                array('name' => '터키시 앙고라', 'name_extra' => 'Turkish Angora'),
                array('name' => '통키니즈', 'name_extra' => 'Tonkinese'),
                array('name' => '페르시안', 'name_extra' => 'Persian Cat'),
                array('name' => '픽시밥', 'name_extra' => 'Pixie Bob'),
                array('name' => '하바나 브라운', 'name_extra' => 'Havana brown'),
                array('name' => '히말라얀', 'name_extra' => 'Himalayan')
            );

        $code = '2';
        $this->load->model('Common_code', 'model');
        foreach ($params as $ket => $data) {
            $data['code_common_group_idx'] = '2';
            $data['code'] = (string)$code++;
            $data['use_fl'] = 'y';
            $this->model->insert($data);
        }
    }
}