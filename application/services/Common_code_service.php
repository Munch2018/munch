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
            ['name' => '나폴레옹', 'name_extra' => 'Napoleon', 'code' => '2'],
            ['name' => '네벨룽', 'name_extra' => 'Nebelung', 'code' => '3'],
            ['name' => '노르웨이 숲 고양이', 'name_extra' => 'Norwegian Forest Cat', 'code' => '4'],
            ['name' => '데본렉스', 'name_extra' => 'Devon Rex', 'code' => '5'],
            ['name' => '라가머핀', 'name_extra' => 'Ragamuffin', 'code' => '6'],
            ['name' => '라이코이', 'name_extra' => 'Lykoi', 'code' => '7'],
            ['name' => '라팜', 'name_extra' => 'LaPerm', 'code' => '8'],
            ['name' => '랙돌', 'name_extra' => 'Ragdoll', 'code' => '9'],
            ['name' => '러시안 블루', 'name_extra' => 'Russian Blue', 'code' => '10'],
        ],
        '2' => [
            ['name' => '맹크스', 'name_extra' => 'Manx Cat', 'code' => '11'],
            ['name' => '먼치킨', 'name_extra' => 'Munchkin', 'code' => '12'],
            ['name' => '메인쿤', 'name_extra' => 'Maine Coon', 'code' => '13'],
            ['name' => '발리네즈', 'name_extra' => 'Balinese', 'code' => '14'],
            ['name' => '뱅갈', 'name_extra' => 'Bengal', 'code' => '15'],
            ['name' => '버만', 'name_extra' => 'Birman', 'code' => '16'],
            ['name' => '버미즈', 'name_extra' => 'Burmese', 'code' => '17'],
            ['name' => '봄베이', 'name_extra' => 'Bombay Cat', 'code' => '18'],
            ['name' => '브리티시 숏 헤어', 'name_extra' => 'British Shorthair', 'code' => '19'],
            ['name' => '사바나', 'name_extra' => 'Savannah', 'code' => '20'],
            ['name' => '샤트룩스', 'name_extra' => 'Chartreux', 'code' => '21'],
            ['name' => '샴(샤미즈', 'name_extra' => 'Siamese Cat', 'code' => '22'],
            ['name' => '세이셸루아', 'name_extra' => 'Seychellois', 'code' => '23'],
            ['name' => '셀커크 렉스', 'name_extra' => 'Selkirk Rex', 'code' => '24'],
            ['name' => '소말리', 'name_extra' => 'Somali', 'code' => '25'],
            ['name' => '스노우슈', 'name_extra' => 'Snow Shoe', 'code' => '26'],
            ['name' => '스코티시 폴드', 'name_extra' => 'Scottish Fold', 'code' => '27'],
            ['name' => '스쿠컴', 'name_extra' => 'Skookum', 'code' => '28'],
            ['name' => '스핑크스', 'name_extra' => 'Sphynx', 'code' => '29'],
            ['name' => '시베리안 고양이', 'name_extra' => 'Siberian', 'code' => '30'],
            ['name' => '실론', 'name_extra' => 'Ceylon', 'code' => '31'],
            ['name' => '싱가퓨라', 'name_extra' => 'Singapura Cat', 'code' => '32'],
            ['name' => '싸이프러스 아프로디테', 'name_extra' => 'Cyprus Aphrodite', 'code' => '33'],
            ['name' => '아라비안 마우', 'name_extra' => 'Arabian Mau', 'code' => '34'],
            ['name' => '아메리칸 링테일', 'name_extra' => 'American Ringtail', 'code' => '35'],
            ['name' => '아메리칸 밥테일', 'name_extra' => 'American Bobtail', 'code' => '37'],
            ['name' => '아메리칸 숏 헤어', 'name_extra' => 'American Shorthair', 'code' => '38'],
            ['name' => '아메리칸 와이어 헤어', 'name_extra' => 'American Wirehair', 'code' => '39'],
            ['name' => '아메리칸 컬', 'name_extra' => 'American Curl', 'code' => '40'],
            ['name' => '아메리칸 폴리덱틸', 'name_extra' => 'American Polydactyl', 'code' => '36'],
            ['name' => '아비시니안', 'name_extra' => 'Abyssinian', 'code' => '41'],
            ['name' => '오리엔탈', 'name_extra' => 'Oriental', 'code' => '42'],
            ['name' => '오스트레일리안 미스트', 'name_extra' => 'Australian Mist', 'code' => '43'],
            ['name' => '오시캣', 'name_extra' => 'Ocicat', 'code' => '44'],
            ['name' => '오호스 아즐레스', 'name_extra' => 'Ojos Azules', 'code' => '45'],
            ['name' => '우랄렉스', 'name_extra' => 'Ural Rex', 'code' => '46'],
            ['name' => '유러피안 버미즈', 'name_extra' => 'European Burmese', 'code' => '47'],
            ['name' => '유러피안 숏 헤어', 'name_extra' => 'European Shorthair', 'code' => '48'],
            ['name' => '이그저틱', 'name_extra' => 'Exotic', 'code' => '49'],
            ['name' => '이집션 마우', 'name_extra' => 'Egyptian Mau', 'code' => '50'],
        ],
        '3' => [
            ['name' => '자바니즈', 'name_extra' => 'Javanese', 'code' => '51'],
            ['name' => '재패니즈 밥테일', 'name_extra' => 'Japanese Bobtail', 'code' => '52'],
            ['name' => '저먼렉스', 'name_extra' => 'German Rex', 'code' => '53'],
            ['name' => '컬러포인트 숏 헤어', 'name_extra' => 'Colorpoint Shorthair', 'code' => '54'],
            ['name' => '컬러포인트 스팽글드', 'name_extra' => 'Colorpoint Spangled', 'code' => '55'],
            ['name' => '코니시 렉스', 'name_extra' => 'Cornish Rex', 'code' => '56'],
            ['name' => '코랫', 'name_extra' => 'Korat', 'code' => '57'],
            ['name' => '코리안 숏 헤어', 'name_extra' => 'Korean Shorthair', 'code' => '58'],
            ['name' => '쿠리리안 밥테일', 'name_extra' => 'Kurilian Bobtail', 'code' => '59'],
            ['name' => '킴릭', 'name_extra' => 'Cymric', 'code' => '60'],
            ['name' => '터키시 앙고라', 'name_extra' => 'Turkish Angora', 'code' => '62'],
            ['name' => '터키시반', 'name_extra' => 'Turkish Van', 'code' => '61'],
            ['name' => '통키니즈', 'name_extra' => 'Tonkinese', 'code' => '63'],
            ['name' => '페르시안', 'name_extra' => 'Persian Cat', 'code' => '64'],
            ['name' => '픽시밥', 'name_extra' => 'Pixie Bob', 'code' => '65'],
            ['name' => '하바나 브라운', 'name_extra' => 'Havana brown', 'code' => '66'],
            ['name' => '히말라얀', 'name_extra' => 'Himalayan', 'code' => '67']
        ]
    ];
    public static $dog_breeds = [
        '1' => [
            ['name' => '고든 세터', 'name_extra' => 'Gordon Setter', 'code' => '2'],
            ['name' => '골든 리트리버', 'name_extra' => 'Golden Retriever', 'code' => '2'],
            ['name' => '그레이 하운드', 'name_extra' => 'Grey Hound', 'code' => '3'],
            ['name' => '그레이트 데인', 'name_extra' => 'Great Dane', 'code' => '4'],
            ['name' => '그레이트 스위스 마운틴 도그', 'name_extra' => 'Great Swiss Mountain Dog', 'code' => '5'],
            ['name' => '그레이트 피레니즈', 'name_extra' => 'Great Pyrenees', 'code' => '6'],
            ['name' => '글렌 오브 이말 테리어', 'name_extra' => 'Glen of Imaal Terrier', 'code' => '7'],
            ['name' => '기슈 이누', 'name_extra' => 'Kishu Inu', 'code' => '8'],
            ['name' => '네오폴리탄 마스티프', 'name_extra' => 'Neopolitan Mastiff', 'code' => '9'],
            ['name' => '노르웨이안 부훈트', 'name_extra' => 'Norwegian Buhund', 'code' => '10'],
            ['name' => '노르웨이안 엘크하운드', 'name_extra' => 'Norwegian Elkhound', 'code' => '11'],
            ['name' => '노르위치 테리어', 'name_extra' => 'Norwich Terrier', 'code' => '12'],
            ['name' => '노르포크 테리어', 'name_extra' => 'Norfolk Terrier', 'code' => '13'],
            ['name' => '노바 스코셔 덕 톨링 리트리버', 'name_extra' => 'Nova Scotia Duck Tolling Retriever', 'code' => '14'],
            ['name' => '뉴펀들랜드', 'name_extra' => 'Newfoundland', 'code' => '15'],
            ['name' => '닥스훈트', 'name_extra' => 'Dachshund', 'code' => '16'],
            ['name' => '달마시안', 'name_extra' => 'Dalmatian', 'code' => '17'],
            ['name' => '댄디 딘몬트 테리어', 'name_extra' => 'Dandie Dinmont Terrier', 'code' => '18'],
            ['name' => '도고 까나리오', 'name_extra' => 'Dogo Canario', 'code' => '19'],
            ['name' => '도그 드 보르도', 'name_extra' => 'Dogue de Bordeaux', 'code' => '20'],
            ['name' => '도베르만 핀셔', 'name_extra' => 'Dobermann Pinscher', 'code' => '21'],
            ['name' => '라사 압소', 'name_extra' => 'Lhasa Apso', 'code' => '22'],
            ['name' => '라포니안 허더', 'name_extra' => 'Lapponian Herder', 'code' => '23'],
            ['name' => '래브라도 리트리버', 'name_extra' => 'Labrador Retriever', 'code' => '24'],
            ['name' => '레이크랜드 테리어', 'name_extra' => 'Lakeland Terrier', 'code' => '25'],
            ['name' => '로디지안 리즈백', 'name_extra' => 'Rhodesian Ridgeback', 'code' => '26'],
            ['name' => '로첸', 'name_extra' => 'Lowchen', 'code' => '27'],
            ['name' => '롯트와일러', 'name_extra' => 'Rottweiler', 'code' => '28'],
        ],
        '2' => [
            ['name' => '마스티프', 'name_extra' => 'Mastiff', 'code' => '29'],
            ['name' => '맨체스터 테리어', 'name_extra' => 'Manchester Terrier', 'code' => '30'],
            ['name' => '몰트지', 'name_extra' => 'Maltese', 'code' => '31'],
            ['name' => '미니어처 불 테리어', 'name_extra' => 'Miniature Bull Terrier', 'code' => '32'],
            ['name' => '미니어처 슈나우저', 'name_extra' => 'Miniature Schnauzer', 'code' => '33'],
            ['name' => '미니어처 푸들', 'name_extra' => 'Miniature Poodle', 'code' => '34'],
            ['name' => '미니어처 핀셔', 'name_extra' => 'Miniature Pinscher', 'code' => '35'],
            ['name' => '바센지', 'name_extra' => 'Basenji', 'code' => '36'],
            ['name' => '바셋 하운드', 'name_extra' => 'Basset Hound', 'code' => '37'],
            ['name' => '버니즈 마운틴 독', 'name_extra' => 'Bernese Mountain Dog', 'code' => '38'],
            ['name' => '베들링턴 테리어', 'name_extra' => 'Bedlington Terrier', 'code' => '39'],
            ['name' => '벨지안 그리펀', 'name_extra' => 'Belgian Griffon', 'code' => '40'],
            ['name' => '벨지안 셰퍼드 독', 'name_extra' => 'Belgian Shepherd Dog', 'code' => '41'],
            ['name' => '벨지안 쉽도그 그로넨달', 'name_extra' => 'Belgian Sheepdog Groenendael', 'code' => '42'],
            ['name' => '벨지안 쉽도그 라케노이즈', 'name_extra' => 'Belgian Sheepdog Laekenois', 'code' => '43'],
            ['name' => '벨지안 쉽도그 말리노이즈', 'name_extra' => 'Belgian Sheepdog Malinois', 'code' => '44'],
            ['name' => '벨지안 쉽도그 터뷰렌', 'name_extra' => 'Belgian Sheepdong Tervuren', 'code' => '45'],
            ['name' => '보더 콜리', 'name_extra' => 'Border Collie', 'code' => '46'],
            ['name' => '보더 테리어', 'name_extra' => 'Border Terrier', 'code' => '47'],
            ['name' => '보르조이', 'name_extra' => 'Borzoi', 'code' => '48'],
            ['name' => '보스롱', 'name_extra' => 'Beauceron', 'code' => '49'],
            ['name' => '보스턴 테리어', 'name_extra' => 'Boston Terrier', 'code' => '50'],
            ['name' => '복서', 'name_extra' => 'Boxer', 'code' => '51'],
            ['name' => '볼로네즈', 'name_extra' => 'Bolognese', 'code' => '52'],
            ['name' => '부비에 데 플랑드르', 'name_extra' => 'Bouvier Des Flandres', 'code' => '53'],
            ['name' => '불 마스티프', 'name_extra' => 'Bullmastiff', 'code' => '54'],
            ['name' => '불 테리어', 'name_extra' => 'Bull Terrier', 'code' => '55'],
            ['name' => '불도그', 'name_extra' => 'Bulldog', 'code' => '56'],
            ['name' => '브루셀 그리폰', 'name_extra' => 'Brussels Griffon', 'code' => '57'],
            ['name' => '브리아드', 'name_extra' => 'Briard', 'code' => '58'],
            ['name' => '브리타니', 'name_extra' => 'Brittany', 'code' => '59'],
            ['name' => '블랙 러시안 테리어', 'name_extra' => 'Black Russian Terrier', 'code' => '60'],
            ['name' => '블랙 앤드 탄 쿤하운드', 'name_extra' => 'Black and Tan Coonhound', 'code' => '61'],
            ['name' => '블러드 하운드', 'name_extra' => 'Bloodhound', 'code' => '62'],
            ['name' => '비글', 'name_extra' => 'Beagle', 'code' => '63'],
            ['name' => '비숑 프리제', 'name_extra' => 'Bichon Frise', 'code' => '64'],
            ['name' => '비어디드 콜리', 'name_extra' => 'Bearded Collie', 'code' => '65'],
            ['name' => '비즐라', 'name_extra' => 'Vizsla', 'code' => '66'],
            ['name' => '사모예드', 'name_extra' => 'Samoyed', 'code' => '67'],
            ['name' => '사우스 러시안 오브차카', 'name_extra' => 'South Russian Ovtcharka', 'code' => '68'],
            ['name' => '살루키', 'name_extra' => 'Saluki', 'code' => '69'],
            ['name' => '삽살개', 'name_extra' => 'Sapsaree', 'code' => '70'],
            ['name' => '서섹스 스파니엘', 'name_extra' => 'Sussex Spaniel', 'code' => '71'],
            ['name' => '세인트 버나드', 'name_extra' => 'Saint Bernard', 'code' => '72'],
            ['name' => '셰틀랜드 쉽독', 'name_extra' => 'Shetland Sheepdog', 'code' => '73'],
            ['name' => '소프트 코티드 휘튼 테리어', 'name_extra' => 'Soft-Coated Wheaten Terrier', 'code' => '74'],
            ['name' => '수위디쉬 발훈트', 'name_extra' => 'Swedish Vallhund', 'code' => '76'],
            ['name' => '스무드 폭스 테리어', 'name_extra' => 'Smooth Fox Terrier', 'code' => '75'],
            ['name' => '스카이 테리어', 'name_extra' => 'Sky Terrier', 'code' => '77'],
            ['name' => '스코티시 디어하운드', 'name_extra' => 'Scottish Deerhound', 'code' => '78'],
            ['name' => '스코티시 테리어', 'name_extra' => 'Scottish Terrier', 'code' => '79'],
            ['name' => '스키퍼키', 'name_extra' => 'Schipperke', 'code' => '80'],
            ['name' => '스타포드셔 불 테리어', 'name_extra' => 'Staffordshire Bull Terrier', 'code' => '81'],
            ['name' => '스탠더드 슈나우저', 'name_extra' => 'Standard Schnauzer', 'code' => '82'],
            ['name' => '스탠더드 푸들', 'name_extra' => 'Standard Poodle', 'code' => '83'],
            ['name' => '스패니쉬 그레이하운드', 'name_extra' => 'Spanish Greyhound', 'code' => '84'],
            ['name' => '스패니쉬 마스티프', 'name_extra' => 'Spanish Mastiff', 'code' => '85'],
            ['name' => '스피노네 이탈리아노', 'name_extra' => 'Spinone Italiano', 'code' => '86'],
            ['name' => '시바 이누', 'name_extra' => 'Shiba Inu', 'code' => '87'],
            ['name' => '시베리안 허스키', 'name_extra' => 'Siberian Husky', 'code' => '88'],
            ['name' => '시츄', 'name_extra' => 'Shih Tzu', 'code' => '89'],
            ['name' => '실리함 테리어', 'name_extra' => 'Sealyham Terrier', 'code' => '90'],
            ['name' => '실키 테리어', 'name_extra' => 'Silky Terrier', 'code' => '91'],
            ['name' => '아나톨리아 셰퍼드', 'name_extra' => 'Anatolian Shepherd', 'code' => '92'],
            ['name' => '아메리칸 스태퍼드셔 테리어', 'name_extra' => 'American Staffordshire Terrier', 'code' => '93'],
            ['name' => '아메리칸 아키다', 'name_extra' => 'American Akita', 'code' => '94'],
            ['name' => '아메리칸 에스키모 도그', 'name_extra' => 'American Eskimo Dog', 'code' => '95'],
            ['name' => '아메리칸 워터 스패니얼', 'name_extra' => 'American Water Spaniel', 'code' => '96'],
            ['name' => '아메리칸 코카 스파니엘', 'name_extra' => 'American Cocker Spaniel', 'code' => '97'],
            ['name' => '아메리칸 폭스하운드', 'name_extra' => 'American Foxhound', 'code' => '98'],
            ['name' => '아이리쉬 레드 앤드 화이트 세터', 'name_extra' => 'Irish Red and White Setter', 'code' => '104'],
            ['name' => '아이리쉬 세터', 'name_extra' => 'Irish Setter', 'code' => '99'],
            ['name' => '아이리쉬 소프트코티드 휘튼 테리어', 'name_extra' => 'Irish Soft-Coated Wheaten Terrier', 'code' => '100'],
            ['name' => '아이리쉬 울프하운드', 'name_extra' => 'Irish Wolfhound', 'code' => '101'],
            ['name' => '아이리쉬 워터 스파니엘', 'name_extra' => 'Irish Water Spaniel', 'code' => '102'],
            ['name' => '아이리쉬 테리어', 'name_extra' => 'Irish Terrier', 'code' => '103'],
            ['name' => '아키타', 'name_extra' => 'Akita', 'code' => '105'],
            ['name' => '아펜핀셔', 'name_extra' => 'Affenpinscher', 'code' => '106'],
            ['name' => '아프간 하운드', 'name_extra' => 'Afghan Hound', 'code' => '107'],
            ['name' => '알래스칸 맬러뮤트', 'name_extra' => 'Alaskan Malamute', 'code' => '108'],
            ['name' => '에스트렐라 마운틴 독', 'name_extra' => 'Estrela Mountain Dog', 'code' => '109'],
            ['name' => '에어데일 테리어', 'name_extra' => 'Airedale Terrier', 'code' => '110'],
            ['name' => '오스트레일리안 셰퍼드', 'name_extra' => 'Australian Shepherd', 'code' => '111'],
            ['name' => '오스트레일리안 실키 테리어', 'name_extra' => 'Australian silky Terrier', 'code' => '112'],
            ['name' => '오스트레일리안 캐틀 도그', 'name_extra' => 'Australian Cattle Dog', 'code' => '113'],
            ['name' => '오스트레일리안 켈피', 'name_extra' => 'Australian Kelpie', 'code' => '114'],
            ['name' => '오스트레일리안 테리어', 'name_extra' => 'Australian Terrier', 'code' => '115'],
            ['name' => '오터 하운드', 'name_extra' => 'Otter Hound', 'code' => '116'],
            ['name' => '올드 잉글리시 쉽독', 'name_extra' => 'Old English Sheepdog', 'code' => '117'],
            ['name' => '와이마라너', 'name_extra' => 'Weimaraner', 'code' => '118'],
            ['name' => '와이어 폭스 테리어', 'name_extra' => 'Wire Fox Terrier', 'code' => '119'],
            ['name' => '와이어헤어드 포인팅 그리폰', 'name_extra' => 'Wirehaired Pointing Griffon', 'code' => '120'],
            ['name' => '요크셔 테리어', 'name_extra' => 'Yorkshire Terrier', 'code' => '121'],
            ['name' => '웨스트 하이랜드 화이트 테리어', 'name_extra' => 'West Highland White Terrier', 'code' => '122'],
            ['name' => '웰시 스프링어 스파니얼', 'name_extra' => 'Welsh Springer Spaniel', 'code' => '123'],
            ['name' => '웰시 코기', 'name_extra' => 'Welsh Corgi', 'code' => '124'],
            ['name' => '웰시 테리어', 'name_extra' => 'Welsh Terrier', 'code' => '125'],
            ['name' => '이비전 하운드', 'name_extra' => 'Ibizan Hound', 'code' => '126'],
            ['name' => '이탈리안 그레이하운드', 'name_extra' => 'Italian Greyhound', 'code' => '127'],
            ['name' => '잉글리시 세터', 'name_extra' => 'English Setter', 'code' => '128'],
            ['name' => '잉글리시 스프링거 스파니엘', 'name_extra' => 'English Springer Spaniel', 'code' => '129'],
            ['name' => '잉글리시 코카 스파니엘', 'name_extra' => 'English Cocker Spaniel', 'code' => '130'],
            ['name' => '잉글리시 토이 스파니엘', 'name_extra' => 'English Toy Spaniel', 'code' => '131'],
            ['name' => '잉글리시 폭스하운드', 'name_extra' => 'English Foxhound', 'code' => '132'],
        ],
        '3' => [
            ['name' => '자이언트 슈나우저', 'name_extra' => 'Giant Schnauzer', 'code' => '133'],
            ['name' => '잭 러셀 테리어', 'name_extra' => 'Jack Russel Terrier', 'code' => '134'],
            ['name' => '저먼 셰퍼드 도그', 'name_extra' => 'German Shepherd Dog', 'code' => '135'],
            ['name' => '저먼 쇼트헤어드 포인터', 'name_extra' => 'German Shorthaired Pointer', 'code' => '136'],
            ['name' => '저먼 와이어헤어드 포인터', 'name_extra' => 'German Wirehaired Pointer', 'code' => '137'],
            ['name' => '저먼 핀셔', 'name_extra' => 'German Pinscher', 'code' => '138'],
            ['name' => '저먼 헌팅 테리어', 'name_extra' => 'German Hunting Terrier', 'code' => '139'],
            ['name' => '제페니스 스피츠', 'name_extra' => 'Japanese Spitz', 'code' => '140'],
            ['name' => '제페니스 친', 'name_extra' => 'Japanese Chin', 'code' => '141'],
            ['name' => '진돗개', 'name_extra' => 'Jindo Dog', 'code' => '142'],
            ['name' => '차우차우', 'name_extra' => 'Chowchow', 'code' => '143'],
            ['name' => '차이니즈 샤페이', 'name_extra' => 'Chinese Shar-pei', 'code' => '144'],
            ['name' => '차이니즈 크레스티드', 'name_extra' => 'Chinese Crested', 'code' => '145'],
            ['name' => '체사피크 베이 리트리버', 'name_extra' => 'Chesapeake Bay Retriever', 'code' => '146'],
            ['name' => '치와와', 'name_extra' => 'Chihuahua', 'code' => '147'],
            ['name' => '카디건 웰시 코기', 'name_extra' => 'Cardigan Welsh Corgi', 'code' => '148'],
            ['name' => '카바리에 킹 찰스 스파니엘', 'name_extra' => 'Cavalier King Charles Spaniel', 'code' => '149'],
            ['name' => '컬리 코티드 리트리버', 'name_extra' => 'Curly Coated Retriever', 'code' => '150'],
            ['name' => '케리 블루 테리어', 'name_extra' => 'Kerry Blue Terrier', 'code' => '151'],
            ['name' => '케언 테리어', 'name_extra' => 'Cairn Terrier', 'code' => '152'],
            ['name' => '케이넌 도그', 'name_extra' => 'Canaan Dog', 'code' => '153'],
            ['name' => '케이스혼트', 'name_extra' => 'Keeshond', 'code' => '154'],
            ['name' => '코몬돌', 'name_extra' => 'Komondor', 'code' => '155'],
            ['name' => '코카시안 오브차카', 'name_extra' => 'Caucasian Ovtcharka', 'code' => '156'],
            ['name' => '코커 스패니얼', 'name_extra' => 'Cocker Spaniel', 'code' => '157'],
            ['name' => '코튼 드 툴리어', 'name_extra' => 'Coton de Tulear', 'code' => '158'],
            ['name' => '콜리', 'name_extra' => 'Collie', 'code' => '159'],
            ['name' => '쿠바츠', 'name_extra' => 'Kuvasz', 'code' => '160'],
            ['name' => '클럼버 스파니엘', 'name_extra' => 'Clumber Spaniel', 'code' => '161'],
            ['name' => '토이 맨체스터 테리어', 'name_extra' => 'Toy Manchester Terrier', 'code' => '162'],
            ['name' => '토이 폭스 테리어', 'name_extra' => 'Toy Fox Terrier', 'code' => '163'],
            ['name' => '토이 푸들', 'name_extra' => 'Toy Poodle', 'code' => '164'],
            ['name' => '티벳탄 마스티프', 'name_extra' => 'Tibetan Mastiff', 'code' => '165'],
            ['name' => '티벳탄 스파니엘', 'name_extra' => 'Tibetan Spaniel', 'code' => '166'],
            ['name' => '티벳탄 테리어', 'name_extra' => 'Tibetan Terrier', 'code' => '167'],
            ['name' => '파라오 하운드', 'name_extra' => 'Pharaoh Hound', 'code' => '168'],
            ['name' => '파슨 러셀 테리어', 'name_extra' => 'Parson Russell Terrier', 'code' => '169'],
            ['name' => '파피용', 'name_extra' => 'Papillon', 'code' => '170'],
            ['name' => '퍼그', 'name_extra' => 'Pug', 'code' => '171'],
            ['name' => '페키니즈', 'name_extra' => 'Pekingese', 'code' => '172'],
            ['name' => '펨브록 웰시 코기', 'name_extra' => 'Pembroke Welsh Corgi', 'code' => '173'],
            ['name' => '포르투갈 워터 도그', 'name_extra' => 'Portuguese Water Dog', 'code' => '174'],
            ['name' => '포메라니안', 'name_extra' => 'Pomeranian', 'code' => '175'],
            ['name' => '포인터', 'name_extra' => 'Pointer', 'code' => '176'],
            ['name' => '폴리시 롤런드 시프도그', 'name_extra' => 'Polish Lowland Sheepdog', 'code' => '177'],
            ['name' => '푸들', 'name_extra' => 'Poodle', 'code' => '178'],
            ['name' => '푸미', 'name_extra' => 'Pumi', 'code' => '179'],
            ['name' => '풀리', 'name_extra' => 'Puli', 'code' => '180'],
            ['name' => '풍산개', 'name_extra' => 'Poongsan Dog', 'code' => '181'],
            ['name' => '프렌치 불도그', 'name_extra' => 'French Bulldog', 'code' => '182'],
            ['name' => '프티 바세 그리퐁 방데', 'name_extra' => 'Petit Basset Griffon Vendeen', 'code' => '183'],
            ['name' => '플랫 코티드 리트리버', 'name_extra' => 'Flat Coated Retriever', 'code' => '184'],
            ['name' => '플롯 하운드', 'name_extra' => 'Plott Hound', 'code' => '185'],
            ['name' => '피니시 스피츠', 'name_extra' => 'Finnish Spitz', 'code' => '186'],
            ['name' => '피레니안 마스티프', 'name_extra' => 'Pyrenean Mastiff', 'code' => '187'],
            ['name' => '피레니안 쉽독', 'name_extra' => 'Pyrenean Sheepdog', 'code' => '188'],
            ['name' => '피레니언 셰퍼드', 'name_extra' => 'Pyrenean Shepherd', 'code' => '189'],
            ['name' => '필드 스파니엘', 'name_extra' => 'Field Spaniel', 'code' => '190'],
            ['name' => '핏불 테리어', 'name_extra' => 'America Pit Bull Terrier', 'code' => '191'],
            ['name' => '해리어', 'name_extra' => 'Harrier', 'code' => '192'],
            ['name' => '허배너스', 'name_extra' => 'Havanese', 'code' => '193'],
            ['name' => '휘펫', 'name_extra' => 'Whippet', 'code' => '194'],
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
                switch ($parentCode) {
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