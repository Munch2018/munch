<?php
/**
 * Created by PhpStorm.
 * User: jungmin
 * Date: 2018-08-19
 * Time: 오후 3:10
 */

class Common_code_service extends MY_Service
{
    public static $subscribe_status = ['active' => '진행', 'cancel' => '취소', 'complete' => '완료', 'pause' => '일시정지'];
    public static $order_status = [
        'pay_pending' => '입금대기',
        'pay_complete' => '결제완료',
        'preparing' => '상품준비중',
        'ready' => '상품준비완료(출고중)',
        'shipping' => '배송중',
        'shipped' => '배송완료',
        'return' => '반품',
        'cancel' => '취소',
        'pay_fail' => '실패'
    ];

    public static $dog_kind = [];
    public static $cat_kind = [];
    public static $character = [];

    public static $cat_breeds = [
        '1' => [
            ['name_extra' => '', 'name' => '기타품종', 'code' => '1'],
            ['name_extra' => '', 'name' => '네바마스커레이드', 'code' => '47'],
            ['name_extra' => '', 'name' => '노르웨이숲', 'code' => '23'],
            ['name_extra' => '', 'name' => '데본렉스', 'code' => '20'],
            ['name_extra' => '', 'name' => '라가머핀', 'code' => '42'],
            ['name_extra' => '', 'name' => '라팜', 'code' => '34'],
            ['name_extra' => '', 'name' => '래그돌', 'code' => '6'],
            ['name_extra' => '', 'name' => '러시안 블루', 'code' => '17'],
        ],
        '2' => [
            ['name_extra' => '', 'name' => '맹크스', 'code' => '24'],
            ['name_extra' => '', 'name' => '먼치킨', 'code' => '35'],
            ['name_extra' => '', 'name' => '메인쿤', 'code' => '5'],
            ['name_extra' => '', 'name' => '발리니즈', 'code' => '27'],
            ['name_extra' => '', 'name' => '버먼', 'code' => '14'],
            ['name_extra' => '', 'name' => '버미즈', 'code' => '10'],
            ['name_extra' => '', 'name' => '벵골', 'code' => '11'],
            ['name_extra' => '', 'name' => '봄베이', 'code' => '25'],
            ['name_extra' => '', 'name' => '브리티시 쇼트헤어', 'code' => '2'],
            ['name_extra' => '', 'name' => '사바나', 'code' => '31'],
            ['name_extra' => '', 'name' => '샤르트뢰', 'code' => '26'],
            ['name_extra' => '', 'name' => '샴', 'code' => '48'],
            ['name_extra' => '', 'name' => '셀러크 렉스', 'code' => '37'],
            ['name_extra' => '', 'name' => '소말리', 'code' => '46'],
            ['name_extra' => '', 'name' => '스노우슈', 'code' => '51'],
            ['name_extra' => '', 'name' => '스코티시 폴드', 'code' => '18'],
            ['name_extra' => '', 'name' => '스핑크스', 'code' => '7'],
            ['name_extra' => '', 'name' => '시베리안 고양이', 'code' => '15'],
            ['name_extra' => '', 'name' => '시암고양이', 'code' => '4'],
            ['name_extra' => '', 'name' => '싱가푸라', 'code' => '40'],
            ['name_extra' => '', 'name' => '아메리칸 밥테일', 'code' => '13'],
            ['name_extra' => '', 'name' => '아메리칸 쇼트헤어', 'code' => '12'],
            ['name_extra' => '', 'name' => '아메리칸 와이어헤어', 'code' => '38'],
            ['name_extra' => '', 'name' => '아메리칸 컬', 'code' => '21'],
            ['name_extra' => '', 'name' => '아비시니안', 'code' => '8'],
            ['name_extra' => '', 'name' => '엑조틱 쇼트헤어', 'code' => '9'],
            ['name_extra' => '', 'name' => '오리엔탈 롱헤어', 'code' => '49'],
            ['name_extra' => '', 'name' => '오리엔탈 쇼트헤어', 'code' => '36'],
            ['name_extra' => '', 'name' => '오시캣', 'code' => '28'],
            ['name_extra' => '', 'name' => '유퍼피언 버미즈', 'code' => '50'],
            ['name_extra' => '', 'name' => '이그조틱', 'code' => '45'],
            ['name_extra' => '', 'name' => '이집션 마우', 'code' => '30'],
        ],
        '3' => [
            ['name_extra' => '', 'name' => '자바니즈', 'code' => '44'],
            ['name_extra' => '', 'name' => '재패니즈 밥테일', 'code' => '16'],
            ['name_extra' => '', 'name' => '코니시 렉스', 'code' => '19'],
            ['name_extra' => '', 'name' => '코랏', 'code' => '33'],
            ['name_extra' => '', 'name' => '코리안 숏헤어', 'code' => '43'],
            ['name_extra' => '', 'name' => '터키시 반', 'code' => '39'],
            ['name_extra' => '', 'name' => '터키시 앙고라', 'code' => '29'],
            ['name_extra' => '', 'name' => '톤키니즈', 'code' => '32'],
            ['name_extra' => '', 'name' => '페르시안', 'code' => '3'],
            ['name_extra' => '', 'name' => '하바나 브라운', 'code' => '41'],
            ['name_extra' => '', 'name' => '히말라얀', 'code' => '22']
        ]
    ];
    public static $dog_breeds = [
        '1' => [
            ['name_extra' => '', 'name' => '고든 세터', 'code' => '21'],
            ['name_extra' => '', 'name' => '그레이 하운드', 'code' => '22'],
            ['name_extra' => '', 'name' => '그레이트 데인', 'code' => '23'],
            ['name_extra' => '', 'name' => '그레이트 스위스 마운틴 도그', 'code' => '24'],
            ['name_extra' => '', 'name' => '그레이트 피레니즈', 'code' => '25'],
            ['name_extra' => '', 'name' => '기슈 이뉴', 'code' => '26'],
            ['name_extra' => '', 'name' => '네오폴리탄 마스티프', 'code' => '27'],
            ['name_extra' => '', 'name' => '노르웨이 엘크하운드', 'code' => '28'],
            ['name_extra' => '', 'name' => '노르위치 테리어', 'code' => '29'],
            ['name_extra' => '', 'name' => '노퍽 테리어', 'code' => '30'],
            ['name_extra' => '', 'name' => '뉴펀들랜드', 'code' => '31'],
            ['name_extra' => '', 'name' => '달마시안', 'code' => '32'],
            ['name_extra' => '', 'name' => '댄지 딘몬트 테리어', 'code' => '33'],
            ['name_extra' => '', 'name' => '도고 아르헨티노', 'code' => '34'],
            ['name_extra' => '', 'name' => '도베르만 핀셔', 'code' => '35'],
            ['name_extra' => '', 'name' => '도사견', 'code' => '36'],
            ['name_extra' => '', 'name' => '딩고', 'code' => '37'],
            ['name_extra' => '', 'name' => '라사압소', 'code' => '38'],
            ['name_extra' => '', 'name' => '레이크랜드 테리어', 'code' => '39'],
            ['name_extra' => '', 'name' => '로디지아 리지백', 'code' => '40'],
            ['name_extra' => '', 'name' => '로트와일러', 'code' => '41'],
            ['name_extra' => '', 'name' => '롱코트치와와', 'code' => '42']
        ],
        '2' => [
            ['name_extra' => '', 'name' => '마스티프', 'code' => '43'],
            ['name_extra' => '', 'name' => '맨체스터 테리어', 'code' => '44'],
            ['name_extra' => '', 'name' => '미니어처 불테리어', 'code' => '45'],
            ['name_extra' => '', 'name' => '미니어처 핀셔', 'code' => '46'],
            ['name_extra' => '', 'name' => '미니핀', 'code' => '47'],
            ['name_extra' => '', 'name' => '믹스', 'code' => '48'],
            ['name_extra' => '', 'name' => '바센지', 'code' => '49'],
            ['name_extra' => '', 'name' => '바셋 하운드', 'code' => '50'],
            ['name_extra' => '', 'name' => '바이마라너', 'code' => '51'],
            ['name_extra' => '', 'name' => '버니즈 마운틴 독', 'code' => '52'],
            ['name_extra' => '', 'name' => '베를링턴 테리어', 'code' => '53'],
            ['name_extra' => '', 'name' => '벨지안 쉽독 그로넨달', 'code' => '54'],
            ['name_extra' => '', 'name' => '벨지안 쉽독 라케노이즈', 'code' => '55'],
            ['name_extra' => '', 'name' => '벨', 'code' => '지안 쉽독 말리노이즈', 'code' => '56'],
            ['name_extra' => '', 'name' => '벨지안 쉽독 터뷰런', 'code' => '57'],
            ['name_extra' => '', 'name' => '보더 콜리', 'code' => '58'],
            ['name_extra' => '', 'name' => '보더 테리어', 'code' => '59'],
            ['name_extra' => '', 'name' => '보르조이', 'code' => '60'],
            ['name_extra' => '', 'name' => '보스턴 테이러', 'code' => '61'],
            ['name_extra' => '', 'name' => '복서', 'code' => '62'],
            ['name_extra' => '', 'name' => '부뤼셀 그리펀', 'code' => '63'],
            ['name_extra' => '', 'name' => '부비베 데 플랑드르', 'code' => '64'],
            ['name_extra' => '', 'name' => '불 마스티프', 'code' => '65'],
            ['name_extra' => '', 'name' => '불 테리어', 'code' => '66'],
            ['name_extra' => '', 'name' => '브리아드', 'code' => '67'],
            ['name_extra' => '', 'name' => '브리타니', 'code' => '68'],
            ['name_extra' => '', 'name' => '블랙 앤 탄 쿤하운드', 'code' => '69'],
            ['name_extra' => '', 'name' => '블러드 하운드', 'code' => '70'],
            ['name_extra' => '', 'name' => '비글', 'code' => '71'],
            ['name_extra' => '', 'name' => '비어디드 콜리', 'code' => '72'],
            ['name_extra' => '', 'name' => '비즐라', 'code' => '73'],
            ['name_extra' => '', 'name' => '사모예드', 'code' => '74'],
            ['name_extra' => '', 'name' => '사우스 러시안 오브차카', 'code' => '75'],
            ['name_extra' => '', 'name' => '살루키', 'code' => '76'],
            ['name_extra' => '', 'name' => '삽살개', 'code' => '77'],
            ['name_extra' => '', 'name' => '샤페이', 'code' => '78'],
            ['name_extra' => '', 'name' => '서섹스 스파니엘', 'code' => '79'],
            ['name_extra' => '', 'name' => '세인트 버나드', 'code' => '80'],
            ['name_extra' => '', 'name' => '셔틀랜드 쉽독', 'code' => '81'],
            ['name_extra' => '', 'name' => '소프트코티드 휘튼 테리어', 'code' => '82'],
            ['name_extra' => '', 'name' => '슈나우저', 'code' => '83'],
            ['name_extra' => '', 'name' => '스무스 폭스테리어', 'code' => '84'],
            ['name_extra' => '', 'name' => '스카이 테리어', 'code' => '85'],
            ['name_extra' => '', 'name' => '스코티쉬 디어하운드', 'code' => '86'],
            ['name_extra' => '', 'name' => '스코티쉬 테리어', 'code' => '87'],
            ['name_extra' => '', 'name' => '스키퍼키', 'code' => '88'],
            ['name_extra' => '', 'name' => '스탠다드 푸들', 'code' => '89'],
            ['name_extra' => '', 'name' => '스테포드셔 불 테리어', 'code' => '90'],
            ['name_extra' => '', 'name' => '스피츠', 'code' => '91'],
            ['name_extra' => '', 'name' => '시코쿠', 'code' => '92'],
            ['name_extra' => '', 'name' => '실리엄 테리어', 'code' => '93'],
            ['name_extra' => '', 'name' => '실키테리어', 'code' => '94'],
            ['name_extra' => '', 'name' => '아메리칼 코카스파니엘', 'code' => '95'],
            ['name_extra' => '', 'name' => '아이리쉬 세터', 'code' => '96'],
            ['name_extra' => '', 'name' => '아이리쉬 울프하운드', 'code' => '97'],
            ['name_extra' => '', 'name' => '아이리쉬 워터 스파니엘', 'code' => '98'],
            ['name_extra' => '', 'name' => '아이리쉬 테리어', 'code' => '99'],
            ['name_extra' => '', 'name' => '아키타', 'code' => '100'],
            ['name_extra' => '', 'name' => '아펜 핀셔', 'code' => '101'],
            ['name_extra' => '', 'name' => '아프간 하운드', 'code' => '102'],
            ['name_extra' => '', 'name' => '알래스칸 맬러뮤트', 'code' => '103'],
            ['name_extra' => '', 'name' => '에어데일 테리어', 'code' => '104'],
            ['name_extra' => '', 'name' => '오스트레일리안 셰퍼드', 'code' => '105'],
            ['name_extra' => '', 'name' => '오스트레일리안 캐틀독', 'code' => '106'],
            ['name_extra' => '', 'name' => '오스트레일리안 테리어', 'code' => '107'],
            ['name_extra' => '', 'name' => '올드 잉글리쉬 쉽독', 'code' => '108'],
            ['name_extra' => '', 'name' => '와이마라너', 'code' => '109'],
            ['name_extra' => '', 'name' => '와이어 폭스테리어', 'code' => '110'],
            ['name_extra' => '', 'name' => '와이어헤어드 포인팅 그리폰', 'code' => '111'],
            ['name_extra' => '', 'name' => '웨스트 하이랜드 화이트 테리어', 'code' => '112'],
            ['name_extra' => '', 'name' => '웰시 스프링거 스파니엘', 'code' => '113'],
            ['name_extra' => '', 'name' => '웰시 테리어', 'code' => '114'],
            ['name_extra' => '', 'name' => '이비전 하운드', 'code' => '115'],
            ['name_extra' => '', 'name' => '이탈리안 그레이하운드', 'code' => '116'],
            ['name_extra' => '', 'name' => '잉글리쉬 세터', 'code' => '117'],
            ['name_extra' => '', 'name' => '잉글리쉬 스트링거 스파니엘', 'code' => '118'],
            ['name_extra' => '', 'name' => '잉글리쉬 코카스파니엘', 'code' => '119'],
            ['name_extra' => '', 'name' => '잉글리쉬 폭스하운드', 'code' => '120'],
        ],
        '3' => [
            ['name_extra' => '', 'name' => '자이언트 슈나우져', 'code' => '121'],
            ['name_extra' => '', 'name' => '재패니즈 스피츠', 'code' => '122'],
            ['name_extra' => '', 'name' => '재패니즈 친', 'code' => '123'],
            ['name_extra' => '', 'name' => '재패니즈 테리어', 'code' => '124'],
            ['name_extra' => '', 'name' => '저먼 세퍼드 도그', 'code' => '126'],
            ['name_extra' => '', 'name' => '저먼 쇼트헤어드 포인터', 'code' => '127'],
            ['name_extra' => '', 'name' => '저먼 와이어헤어드 포인터', 'code' => '128'],
            ['name_extra' => '', 'name' => '차우차우', 'code' => '129'],
            ['name_extra' => '', 'name' => '차이니즈 샤페이', 'code' => '130'],
            ['name_extra' => '', 'name' => '차이니즈 크레스티드', 'code' => '131'],
            ['name_extra' => '', 'name' => '체사피크 베이 리트리버', 'code' => '132'],
            ['name_extra' => '', 'name' => '카바리에 킹 찰스 스파니엘', 'code' => '133'],
            ['name_extra' => '', 'name' => '컬리 코티드 리트리버', 'code' => '134'],
            ['name_extra' => '', 'name' => '케리 블루 테리어', 'code' => '135'],
            ['name_extra' => '', 'name' => '케언 테리어', 'code' => '136'],
            ['name_extra' => '', 'name' => '케이스 혼트', 'code' => '137'],
            ['name_extra' => '', 'name' => '케인 코르소', 'code' => '138'],
            ['name_extra' => '', 'name' => '코몬돌', 'code' => '139'],
            ['name_extra' => '', 'name' => '코카시안 오브차카', 'code' => '140'],
            ['name_extra' => '', 'name' => '코튼 드 툴리어', 'code' => '141'],
            ['name_extra' => '', 'name' => '콜리', 'code' => '142'],
            ['name_extra' => '', 'name' => '쿠바츠', 'code' => '143'],
            ['name_extra' => '', 'name' => '크로아티안 쉽독', 'code' => '144'],
            ['name_extra' => '', 'name' => '클럼버 스파니엘', 'code' => '145'],
            ['name_extra' => '', 'name' => '킹 찰스 스파니엘', 'code' => '146'],
            ['name_extra' => '', 'name' => '토이 맨체스터 테리어', 'code' => '147'],
            ['name_extra' => '', 'name' => '토이 푸들', 'code' => '148'],
            ['name_extra' => '', 'name' => '토종견', 'code' => '149'],
            ['name_extra' => '', 'name' => '티벳탄 마스티프', 'code' => '150'],
            ['name_extra' => '', 'name' => '티벳탄 테리어', 'code' => '151'],
            ['name_extra' => '', 'name' => '퍼그', 'code' => '152'],
            ['name_extra' => '', 'name' => '포인터', 'code' => '153'],
            ['name_extra' => '', 'name' => '풀리', 'code' => '154'],
            ['name_extra' => '', 'name' => '풍산개', 'code' => '155'],
            ['name_extra' => '', 'name' => '플랫 코티드 리트리버', 'code' => '156'],
            ['name_extra' => '', 'name' => '필드 스파니엘', 'code' => '157'],
            ['name_extra' => '', 'name' => '필라 브라질레이로', 'code' => '158'],
            ['name_extra' => '', 'name' => '핏불 테리어', 'code' => '159'],
            ['name_extra' => '', 'name' => '해리어', 'code' => '160'],
            ['name_extra' => '', 'name' => '홋카이도', 'code' => '161'],
            ['name_extra' => '', 'name' => '휘펫', 'code' => '162'],
            ['name_extra' => '', 'name' => '골든 리트리버', 'code' => '2'],
            ['name_extra' => '', 'name' => '기타품종', 'code' => '1'],
            ['name_extra' => '', 'name' => '닥스훈트', 'code' => '3'],
            ['name_extra' => '', 'name' => '래브라도 리트리버', 'code' => '4'],
            ['name_extra' => '', 'name' => '몰티즈', 'code' => '5'],
            ['name_extra' => '', 'name' => '미니어처 슈나우저', 'code' => '6'],
            ['name_extra' => '', 'name' => '불도그', 'code' => '7'],
            ['name_extra' => '', 'name' => '비숑 프리제', 'code' => '8'],
            ['name_extra' => '', 'name' => '시바 이누', 'code' => '21'],
            ['name_extra' => '', 'name' => '시베리안 허스키', 'code' => '9'],
            ['name_extra' => '', 'name' => '시츄', 'code' => '10'],
            ['name_extra' => '', 'name' => '요크셔 테리어', 'code' => '11'],
            ['name_extra' => '', 'name' => '웰시 코기', 'code' => '12'],
            ['name_extra' => '', 'name' => '진돗개', 'code' => '13'],
            ['name_extra' => '', 'name' => '치와와', 'code' => '14'],
            ['name_extra' => '', 'name' => '코커 스패니얼', 'code' => '15'],
            ['name_extra' => '', 'name' => '파피용', 'code' => '16'],
            ['name_extra' => '', 'name' => '페키니즈', 'code' => '17'],
            ['name_extra' => '', 'name' => '포메라니안', 'code' => '18'],
            ['name_extra' => '', 'name' => '푸들', 'code' => '19'],
            ['name_extra' => '', 'name' => '프렌츠 불도그', 'code' => '20']
        ]
    ];

    public function __construct()
    {
        $this->load->model('Common_code', 'code_model');
    }

    public function getCode($name)
    {
        if (in_array($name, ['dog_kind', 'cat_kind', 'character']) && empty(self::${$name})) {
            $this->makeCode();
        }
        return !empty(self::${$name}) ? self::${$name} : [];
    }

    private function makeCode()
    {
        foreach ([1, 2, 3] as $k => $parentCode) {
            $codes = $this->code_model->get_codes($parentCode);
            foreach ($codes as $idx => $code) {
                switch ($parentCode){
                    case 1:
                        self::$dog_kind[trim($code['code'])] = $code['name'];
                        break;
                    case 2:
                        self::$cat_kind[trim($code['code'])] = $code['name'];
                        break;
                    case 3:
                        self::$character[trim($code['code'])] = $code['name'];
                        break;

                }
            }
        }
    }


    public function getBreedsCode($name, $key = '')
    {
        if (!empty(self::${$name})) {
            return !empty($key) ? self::${$name}[$key] : self::${$name};
        } else {
            return [];
        }
    }

    private function makeBreedsCode()
    {
        foreach ([1, 2] as $k => $parentCode) {
            $codes = $this->code_model->get_codes($parentCode);
            foreach ($codes as $idx => $code) {
                if ($parentCode == 1) {
                    self::$dog_breeds[$code['code']]['name'] = $code['name'];
                    self::$dog_breeds[$code['code']]['name_extra'] = $code['name_extra'];
                } else {
                    self::$cat_breeds[$code['code']]['name'] = $code['name'];
                    self::$cat_breeds[$code['code']]['name_extra'] = $code['name_extra'];
                }
            }
        }
    }

}