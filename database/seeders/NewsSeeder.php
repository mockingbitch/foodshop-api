<?php

namespace Database\Seeders;

use App\Models\News;
use Illuminate\Database\Seeder;

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'type' => 'news',
                'category_id' => null,
                'title' => ['en' => 'Welcome to FoodShop', 'vn' => 'Chào mừng đến FoodShop', 'kr' => 'FoodShop에 오신 것을 환영합니다'],
                'content' => [
                    'en' => $this->longContentWelcomeEn(),
                    'vn' => $this->longContentWelcomeVn(),
                    'kr' => $this->longContentWelcomeKr(),
                ],
                'excerpt' => [
                    'en' => '<p>New platform launch – discover restaurants, reviews and menus. Join thousands of food lovers.</p>',
                    'vn' => '<p>Ra mắt nền tảng mới – khám phá nhà hàng, đánh giá và thực đơn. Cùng hàng nghìn người yêu ẩm thực.</p>',
                    'kr' => '<p>새 플랫폼 출시 – 레스토랑, 리뷰, 메뉴를 발견하세요. 수천 명의 음식 애호가와 함께하세요.</p>',
                ],
                'status' => 'published',
                'published_at' => now()->subDays(5),
            ],
            [
                'type' => 'news',
                'category_id' => null,
                'title' => ['en' => 'Best Restaurants 2025', 'vn' => 'Nhà hàng hay nhất 2025', 'kr' => '2025 최고의 레스토랑'],
                'content' => [
                    'en' => $this->longContentBestRestaurantsEn(),
                    'vn' => $this->longContentBestRestaurantsVn(),
                    'kr' => $this->longContentBestRestaurantsKr(),
                ],
                'excerpt' => [
                    'en' => '<p>Top restaurants guide – ranked by reviews, quality and service. Book your table today.</p>',
                    'vn' => '<p>Hướng dẫn nhà hàng hàng đầu – xếp theo đánh giá, chất lượng và dịch vụ. Đặt bàn ngay hôm nay.</p>',
                    'kr' => '<p>최고 레스토랑 가이드 – 리뷰, 품질, 서비스 기준. 오늘 예약하세요.</p>',
                ],
                'status' => 'published',
                'published_at' => now()->subDays(2),
            ],
            [
                'type' => 'news',
                'category_id' => null,
                'title' => ['en' => 'Upcoming Food Festival', 'vn' => 'Lễ hội ẩm thực sắp tới', 'kr' => '다가오는 푸드 페스티벌'],
                'content' => [
                    'en' => $this->longContentFestivalEn(),
                    'vn' => $this->longContentFestivalVn(),
                    'kr' => $this->longContentFestivalKr(),
                ],
                'excerpt' => [
                    'en' => '<p>Food festival announcement – live demos, street food and chef meet & greet. Save the date.</p>',
                    'vn' => '<p>Thông báo lễ hội ẩm thực – trình diễn trực tiếp, ẩm thực đường phố và gặp gỡ đầu bếp. Đánh dấu lịch ngay.</p>',
                    'kr' => '<p>푸드 페스티벌 공지 – 라이브 시연, 스트리트 푸드, 셰프 미팅. 날짜를 저장하세요.</p>',
                ],
                'status' => 'draft',
                'published_at' => null,
            ],
            [
                'type' => 'course',
                'category_id' => null,
                'title' => ['en' => 'Vietnamese Cooking Basics', 'vn' => 'Cơ bản nấu ăn Việt Nam', 'kr' => '베트남 요리 기초'],
                'content' => [
                    'en' => $this->longContentCourseEn(),
                    'vn' => $this->longContentCourseVn(),
                    'kr' => $this->longContentCourseKr(),
                ],
                'excerpt' => [
                    'en' => '<p>4-hour cooking workshop – Pho broth, spring rolls, dipping sauces. Limited to 12 participants.</p>',
                    'vn' => '<p>Workshop nấu ăn 4 giờ – Nước dùng phở, gỏi cuốn, nước chấm. Giới hạn 12 người.</p>',
                    'kr' => '<p>4시간 요리 워크숍 – 팟 국물, 춘권, 소스. 12명 한정.</p>',
                ],
                'course_price' => 50.00,
                'course_duration' => 4,
                'max_participants' => 12,
                'status' => 'published',
                'published_at' => now()->subDay(1),
            ],
            [
                'type' => 'chef',
                'category_id' => null,
                'title' => ['en' => 'Chef Minh Nguyen', 'vn' => 'Đầu bếp Minh Nguyễn', 'kr' => '셰프 민 응우옌'],
                'content' => [
                    'en' => $this->longContentChefEn(),
                    'vn' => $this->longContentChefVn(),
                    'kr' => $this->longContentChefKr(),
                ],
                'excerpt' => [
                    'en' => '<p>Meet our head chef – 15 years of Asian fusion, from Vietnam to France.</p>',
                    'vn' => '<p>Gặp đầu bếp trưởng – 15 năm ẩm thực Á fusion, từ Việt Nam đến Pháp.</p>',
                    'kr' => '<p>헤드 셰프를 만나보세요 – 베트남에서 프랑스까지 15년 아시아 퓨전.</p>',
                ],
                'chef_name' => 'Minh Nguyen',
                'chef_specialty' => 'Asian Fusion',
                'status' => 'published',
                'published_at' => now()->subDays(3),
            ],
        ];

        foreach ($items as $item) {
            News::create(array_merge($item, ['view_count' => 0]));
        }
    }

    private function longContentWelcomeEn(): string
    {
        return <<<'HTML'
<p>We are thrilled to announce the launch of <strong>FoodShop</strong>, a new platform designed to connect restaurants and food lovers in one place. Whether you are looking for the best phở in town, a cozy café, or a fine-dining experience, FoodShop helps you discover, review, and enjoy local cuisine like never before.</p>
<p><img src="https://placehold.co/800x400/f5f5f5/333?text=Welcome+to+FoodShop" alt="FoodShop platform" style="max-width:100%;height:auto;"></p>

<h2>What we offer</h2>
<p>Our platform brings together three core features that make exploring food simple and enjoyable.</p>
<ul>
<li><strong>Restaurant listings</strong> – Browse hundreds of restaurants by location, cuisine type, and rating. Each listing includes photos, opening hours, contact info, and direct links to menus.</li>
<li><strong>Reviews & ratings</strong> – Read honest reviews from real diners and share your own experience. Our 5-star rating system helps you quickly see what others loved about each place.</li>
<li><strong>Menus & dishes</strong> – View full menus, see photos of dishes, filter by dietary preferences (vegetarian, best-seller), and plan your next meal with ease.</li>
</ul>

<h2>Why choose FoodShop?</h2>
<p>In a world where choices are endless, we focus on <em>quality over quantity</em>. Every restaurant on our platform is verified, and we work closely with owners to keep information accurate and up to date. We also support multiple languages and currencies, so whether you are a local or a traveler, you will feel at home.</p>

<h2>Join our community</h2>
<p>Thousands of food lovers have already joined FoodShop. Create an account to save your favourite restaurants, write reviews, and get personalised recommendations. Restaurant owners can register to showcase their business and reach more customers. Together, we are building the future of food discovery.</p>

<blockquote><p>Good food is the foundation of genuine happiness. We are here to help you find it.</p></blockquote>

<p>Thank you for being part of this journey. Explore, taste, and share – and let us know what you think. We would love to hear from you.</p>
HTML;
    }

    private function longContentWelcomeVn(): string
    {
        return <<<'HTML'
<p>Chúng tôi vui mừng ra mắt <strong>FoodShop</strong>, nền tảng mới kết nối nhà hàng và người yêu ẩm thực tại một nơi. Dù bạn đang tìm quán phở ngon nhất, quán cà phê ấm cúng hay trải nghiệm fine-dining, FoodShop giúp bạn khám phá, đánh giá và thưởng thức ẩm thực địa phương chưa từng có.</p>
<p><img src="https://placehold.co/800x400/f5f5f5/333?text=Chao+mung+FoodShop" alt="Nền tảng FoodShop" style="max-width:100%;height:auto;"></p>

<h2>Chúng tôi mang đến gì?</h2>
<p>Nền tảng của chúng tôi gồm ba tính năng chính giúp việc khám phá ẩm thực trở nên đơn giản và thú vị.</p>
<ul>
<li><strong>Danh sách nhà hàng</strong> – Duyệt hàng trăm nhà hàng theo khu vực, loại hình ẩm thực và xếp hạng. Mỗi mục có ảnh, giờ mở cửa, liên hệ và link trực tiếp tới thực đơn.</li>
<li><strong>Đánh giá & xếp hạng</strong> – Đọc đánh giá thật từ thực khách và chia sẻ trải nghiệm của bạn. Hệ thống xếp hạng 5 sao giúp bạn nhanh chóng biết điều mọi người thích ở mỗi địa điểm.</li>
<li><strong>Thực đơn & món ăn</strong> – Xem thực đơn đầy đủ, ảnh món ăn, lọc theo chế độ ăn (chay, bán chạy) và lên kế hoạch bữa ăn tiếp theo dễ dàng.</li>
</ul>

<h2>Tại sao chọn FoodShop?</h2>
<p>Trong thế giới lựa chọn vô tận, chúng tôi chú trọng <em>chất lượng hơn số lượng</em>. Mọi nhà hàng trên nền tảng đều được xác minh, và chúng tôi làm việc chặt chẽ với chủ quán để thông tin chính xác và cập nhật. Chúng tôi cũng hỗ trợ đa ngôn ngữ và đa tiền tệ, nên dù bạn là người địa phương hay du khách, bạn đều cảm thấy như ở nhà.</p>

<h2>Tham gia cộng đồng</h2>
<p>Hàng nghìn người yêu ẩm thực đã tham gia FoodShop. Tạo tài khoản để lưu nhà hàng yêu thích, viết đánh giá và nhận gợi ý cá nhân hóa. Chủ nhà hàng có thể đăng ký để quảng bá và tiếp cận thêm khách hàng. Cùng nhau, chúng ta xây dựng tương lai của việc khám phá ẩm thực.</p>

<blockquote><p>Đồ ăn ngon là nền tảng của hạnh phúc thật sự. Chúng tôi ở đây để giúp bạn tìm thấy nó.</p></blockquote>

<p>Cảm ơn bạn đã đồng hành. Khám phá, nếm thử và chia sẻ – và cho chúng tôi biết ý kiến của bạn. Chúng tôi rất muốn lắng nghe.</p>
HTML;
    }

    private function longContentWelcomeKr(): string
    {
        return <<<'HTML'
<p><strong>FoodShop</strong>의 출시를 알리게 되어 기쁩니다. 레스토랑과 음식 애호가를 한곳에서 연결하는 새 플랫폼입니다. 동네 최고의 쌀국수, 아늑한 카페, 파인다이닝을 찾고 계시든, FoodShop으로 로컬 요리를 발견하고, 리뷰하고, 즐기세요.</p>
<p><img src="https://placehold.co/800x400/f5f5f5/333?text=FoodShop" alt="FoodShop 플랫폼" style="max-width:100%;height:auto;"></p>

<h2>제공 서비스</h2>
<p>세 가지 핵심 기능으로 음식 탐험을 쉽고 즐겁게 만듭니다.</p>
<ul>
<li><strong>레스토랑 목록</strong> – 지역, 요리 종류, 평점별로 수백 개의 레스토랑을 둘러보세요. 사진, 영업 시간, 연락처, 메뉴 링크를 제공합니다.</li>
<li><strong>리뷰 및 평점</strong> – 실제 이용 후기를 읽고 여러분의 경험을 공유하세요. 5점 만점 평점으로 다른 사람들이 좋아하는 점을 빠르게 확인할 수 있습니다.</li>
<li><strong>메뉴 및 요리</strong> – 전체 메뉴, 요리 사진, 채식·베스트셀러 필터로 다음 식사를 쉽게 계획하세요.</li>
</ul>

<h2>왜 FoodShop인가?</h2>
<p>선택이 넘치는 세상에서 우리는 <em>양보다 질</em>을 중시합니다. 플랫폼의 모든 레스토랑은 검증되었으며, 정보의 정확성과 최신화를 위해 사장님과 긴밀히 협력합니다. 다국어와 다통화를 지원해 현지인이든 여행자든 편안하게 이용할 수 있습니다.</p>

<h2>커뮤니티에 참여하세요</h2>
<p>이미 수천 명의 음식 애호가가 FoodShop에 합류했습니다. 계정을 만들어 좋아하는 레스토랑을 저장하고, 리뷰를 작성하고, 맞춤 추천을 받아보세요. 레스토랑 사장님은 등록해 비즈니스를 소개하고 더 많은 고객에게 다가갈 수 있습니다. 함께 음식 발견의 미래를 만들어 갑시다.</p>

<blockquote><p>좋은 음식은 진정한 행복의 기반입니다. 저희는 여러분이 그것을 찾도록 돕습니다.</p></blockquote>

<p>이 여정에 함께해 주셔서 감사합니다. 탐험하고, 맛보고, 공유해 주세요. 의견을 들려주시면 감사하겠습니다.</p>
HTML;
    }

    private function longContentBestRestaurantsEn(): string
    {
        return <<<'HTML'
<p>Finding the best restaurants in your city can feel overwhelming. With so many options, how do you know where to spend your time and money? Our team has analysed thousands of reviews, visited dozens of venues, and talked to chefs and owners to bring you this definitive guide to the <strong>Best Restaurants 2025</strong>.</p>
<p><img src="https://placehold.co/800x450/e8f5e9/2e7d32?text=Best+Restaurants+2025" alt="Best Restaurants 2025" style="max-width:100%;height:auto;"></p>

<h2>How we rank</h2>
<p>Our list is not paid or sponsored. We use a transparent methodology based on three pillars:</p>
<ol>
<li><strong>Customer reviews</strong> – We aggregate and weight ratings from verified diners. Consistency and recency matter: a place with hundreds of 4.5-star reviews ranks higher than one with a few perfect scores.</li>
<li><strong>Food quality</strong> – Our editors and local experts taste dishes anonymously. We look at ingredients, technique, presentation, and value for money.</li>
<li><strong>Service & ambience</strong> – A great meal is more than the plate. We consider staff friendliness, waiting times, cleanliness, and the overall vibe of the place.</li>
</ol>

<h2>Top 5 this month</h2>
<p>Here are the restaurants that have impressed us most in the past month. Book early – they fill up fast.</p>
<ul>
<li><strong>Golden Dragon</strong> – Asian fusion at its best. Try the phở reimagined and the Korean-style short ribs. Perfect for groups.</li>
<li><strong>Pho Paradise</strong> – Authentic Vietnamese. The broth is simmered for 12 hours. A must-visit for phở lovers.</li>
<li><strong>Seoul Kitchen</strong> – Korean BBQ with a modern twist. High-quality meat, generous banchan, and a lively atmosphere.</li>
<li><strong>Lotus Garden</strong> – Vegetarian-friendly with a focus on fresh, local produce. The tofu dishes are outstanding.</li>
<li><strong>Street Food Hub</strong> – A curated selection of street food from across Asia. Great for a casual lunch or dinner.</li>
</ul>

<h2>Tips for diners</h2>
<p>Reservations are recommended for the top spots, especially on weekends. Many restaurants offer set menus or chef’s specials – ask your server. And don’t forget to leave a review after your visit; your feedback helps other food lovers and supports the restaurants you enjoy.</p>

<p>We update this list monthly. Come back next month to see who made the cut. Happy eating!</p>
HTML;
    }

    private function longContentBestRestaurantsVn(): string
    {
        return <<<'HTML'
<p>Tìm nhà hàng hay nhất trong thành phố đôi khi khiến ta bối rối. Quá nhiều lựa chọn, làm sao biết nên dành thời gian và tiền bạc ở đâu? Đội ngũ chúng tôi đã phân tích hàng nghìn đánh giá, ghé thăm hàng chục địa điểm và trò chuyện với đầu bếp cùng chủ quán để mang tới bạn bản hướng dẫn <strong>Nhà hàng hay nhất 2025</strong>.</p>
<p><img src="https://placehold.co/800x450/e8f5e9/2e7d32?text=Nha+hang+hay+nhat+2025" alt="Nhà hàng hay nhất 2025" style="max-width:100%;height:auto;"></p>

<h2>Cách chúng tôi xếp hạng</h2>
<p>Danh sách của chúng tôi không được trả phí hay tài trợ. Chúng tôi dùng phương pháp minh bạch dựa trên ba trụ cột:</p>
<ol>
<li><strong>Đánh giá khách hàng</strong> – Chúng tôi tổng hợp và gia trọng xếp hạng từ thực khách đã xác minh. Tính nhất quán và độ mới quan trọng: nơi có hàng trăm đánh giá 4.5 sao được xếp cao hơn nơi chỉ có vài điểm hoàn hảo.</li>
<li><strong>Chất lượng món ăn</strong> – Biên tập viên và chuyên gia địa phương nếm món ẩn danh. Chúng tôi xem xét nguyên liệu, kỹ thuật, trình bày và giá trị đồng tiền.</li>
<li><strong>Phục vụ & không gian</strong> – Bữa ăn tuyệt vời không chỉ nằm trên đĩa. Chúng tôi xem xét sự thân thiện của nhân viên, thời gian chờ, vệ sinh và không khí tổng thể.</li>
</ol>

<h2>Top 5 tháng này</h2>
<p>Đây là những nhà hàng gây ấn tượng nhất trong tháng qua. Hãy đặt bàn sớm – họ thường kín chỗ.</p>
<ul>
<li><strong>Golden Dragon</strong> – Ẩm thực Á fusion đỉnh cao. Thử món phở cách tân và sườn non kiểu Hàn. Phù hợp cho nhóm đông.</li>
<li><strong>Pho Paradise</strong> – Việt Nam đích thực. Nước dùng ninh 12 giờ. Điểm đến bắt buộc cho người yêu phở.</li>
<li><strong>Seoul Kitchen</strong> – BBQ Hàn Quốc phiên bản hiện đại. Thịt chất lượng cao, banchan hào phóng, không khí sôi động.</li>
<li><strong>Lotus Garden</strong> – Thân thiện người ăn chay, chú trọng nguyên liệu tươi địa phương. Các món đậu hũ xuất sắc.</li>
<li><strong>Street Food Hub</strong> – Tuyển chọn ẩm thực đường phố từ khắp châu Á. Lý tưởng cho bữa trưa hoặc tối thoải mái.</li>
</ul>

<h2>Gợi ý cho thực khách</h2>
<p>Nên đặt bàn trước với các địa điểm top, đặc biệt cuối tuần. Nhiều nhà hàng có set menu hoặc đặc sản đầu bếp – hãy hỏi nhân viên. Và đừng quên để lại đánh giá sau khi ghé thăm; phản hồi của bạn giúp người yêu ẩm thực khác và ủng hộ những nhà hàng bạn thích.</p>

<p>Chúng tôi cập nhật danh sách hàng tháng. Hẹn gặp lại tháng sau để xem ai lọt top. Chúc ngon miệng!</p>
HTML;
    }

    private function longContentBestRestaurantsKr(): string
    {
        return <<<'HTML'
<p>도시에서 최고의 레스토랑을 찾는 일은 부담스러울 수 있습니다. 선택이 너무 많은데, 시간과 돈을 어디에 쓸지 어떻게 알 수 있을까요? 저희 팀은 수천 개의 리뷰를 분석하고, 수십 개의 매장을 방문하며, 셰프와 사장님과 이야기해 <strong>2025 최고의 레스토랑</strong> 가이드를 만들었습니다.</p>
<p><img src="https://placehold.co/800x450/e8f5e9/2e7d32?text=2025+Best+Restaurants" alt="2025 최고의 레스토랑" style="max-width:100%;height:auto;"></p>

<h2>순위 기준</h2>
<p>이 목록은 유료·광고가 아닙니다. 세 가지 기준에 따른 투명한 방법론을 사용합니다.</p>
<ol>
<li><strong>고객 리뷰</strong> – 검증된 이용자의 평점을 종합하고 가중치를 둡니다. 일관성과 최신성이 중요합니다.</li>
<li><strong>음식 품질</strong> – 편집진과 현지 전문가가 익명으로 시식합니다. 재료, 기법, 플레이팅, 가성비를 봅니다.</li>
<li><strong>서비스 & 분위기</strong> – 좋은 식사는 접시 그 이상입니다. 직원 친절, 대기 시간, 청결, 전체 분위기를 고려합니다.</li>
</ol>

<h2>이번 달 Top 5</h2>
<p>지난달 가장 인상 깊었던 레스토랑입니다. 인기 매장은 빨리 예약하세요.</p>
<ul>
<li><strong>Golden Dragon</strong> – 아시아 퓨전. 리이매지닝 팟과 한국식 숏립을 추천. 단체 모임에 좋습니다.</li>
<li><strong>Pho Paradise</strong> – 정통 베트남. 국물은 12시간 우려냅니다. 팟 애호가 필수.</li>
<li><strong>Seoul Kitchen</strong> – 모던 한국 BBQ. 고급 고기, 풍성한 반찬, 활기찬 분위기.</li>
<li><strong>Lotus Garden</strong> – 채식 친화적, 신선한 로컬 재료. 두부 요리가 훌륭합니다.</li>
<li><strong>Street Food Hub</strong> – 아시아 스트리트 푸드 큐레이션. 캐주얼 런치·디너에 좋습니다.</li>
</ul>

<h2>이용 팁</h2>
<p>Top 매장은 주말 특히 예약을 권합니다. 세트 메뉴나 셰프 스페셜이 있는지 서버에게 문의하세요. 방문 후 리뷰를 남겨 주시면 다른 음식 애호가와 좋아하는 레스토랑을 돕게 됩니다.</p>

<p>이 목록은 매월 업데이트됩니다. 다음 달에 다시 만나요. 맛있게 드세요!</p>
HTML;
    }

    private function longContentFestivalEn(): string
    {
        return <<<'HTML'
<p>Mark your calendars: the <strong>FoodShop Annual Food Festival</strong> is coming next month. For one weekend, the city’s best chefs, street food vendors, and food lovers will gather in a single venue for cooking demos, tastings, and unforgettable meals.</p>
<p><img src="https://placehold.co/800x400/fff3e0/ef6c00?text=Food+Festival+2025" alt="Food Festival" style="max-width:100%;height:auto;"></p>

<h2>What to expect</h2>
<p>The festival is designed for everyone – families, couples, and solo explorers. You will find:</p>
<ul>
<li><strong>Live cooking demos</strong> – Watch top chefs prepare signature dishes from start to finish. Learn tips and tricks you can use at home. Sessions run every two hours on both days.</li>
<li><strong>Street food from 20+ vendors</strong> – From Vietnamese bánh mì to Korean tteokbokki, Japanese takoyaki to Thai mango sticky rice. One ticket gives you access to all stalls; pay per dish at each.</li>
<li><strong>Chef meet & greet</strong> – Get your cookbook signed, ask questions, and take photos with the chefs you admire. Schedule will be posted one week before the event.</li>
<li><strong>Kids’ corner</strong> – Cookie decorating, simple cooking activities, and healthy snack stations so the little ones stay happy too.</li>
</ul>

<h2>Practical info</h2>
<p><strong>Dates:</strong> To be announced (next month). <strong>Venue:</strong> Central Park Convention Hall. <strong>Opening hours:</strong> 10:00 – 20:00 both days. <strong>Tickets:</strong> Early bird available soon; follow us on social media for the link.</strong></p>

<p>We are still finalising the full programme and vendor list. More details – including dietary options and accessibility – will be published in the coming weeks. Stay tuned!</p>
HTML;
    }

    private function longContentFestivalVn(): string
    {
        return <<<'HTML'
<p>Đánh dấu lịch ngay: <strong>Lễ hội Ẩm thực Thường niên FoodShop</strong> sẽ diễn ra vào tháng tới. Trong một cuối tuần, những đầu bếp, gian hàng ẩm thực đường phố và người yêu ẩm thực hay nhất thành phố sẽ tụ họp tại một địa điểm cho các buổi trình diễn nấu ăn, nếm thử và bữa ăn khó quên.</p>
<p><img src="https://placehold.co/800x400/fff3e0/ef6c00?text=Le+hoi+Am+thuc+2025" alt="Lễ hội ẩm thực" style="max-width:100%;height:auto;"></p>

<h2>Bạn sẽ thấy gì?</h2>
<p>Lễ hội dành cho mọi người – gia đình, cặp đôi và người khám phá một mình. Bạn sẽ có:</p>
<ul>
<li><strong>Trình diễn nấu ăn trực tiếp</strong> – Xem đầu bếp hàng đầu chế biến món đặc trưng từ đầu đến cuối. Học mẹo và kỹ thuật áp dụng tại nhà. Mỗi buổi cách nhau hai giờ trong cả hai ngày.</li>
<li><strong>Ẩm thực đường phố từ 20+ gian hàng</strong> – Từ bánh mì Việt Nam đến tteokbokki Hàn Quốc, takoyaki Nhật đến xôi xoài Thái. Một vé cho phép vào tất cả gian hàng; thanh toán theo món tại mỗi gian.</li>
<li><strong>Gặp gỡ đầu bếp</strong> – Ký sách nấu ăn, đặt câu hỏi và chụp ảnh với đầu bếp bạn ngưỡng mộ. Lịch trình sẽ đăng một tuần trước sự kiện.</li>
<li><strong>Góc trẻ em</strong> – Trang trí bánh quy, hoạt động nấu ăn đơn giản và trạm ăn vặt lành mạnh để các bé cũng vui.</li>
</ul>

<h2>Thông tin thực tế</h2>
<p><strong>Ngày:</strong> Sẽ công bố (tháng tới). <strong>Địa điểm:</strong> Hội trường Công viên Trung tâm. <strong>Giờ mở cửa:</strong> 10:00 – 20:00 cả hai ngày. <strong>Vé:</strong> Ưu đãi sớm sắp mở; theo dõi chúng tôi trên mạng xã hội để nhận link.</p>

<p>Chúng tôi đang hoàn thiện chương trình và danh sách gian hàng. Chi tiết thêm – gồm lựa chọn ăn kiêng và tiếp cận – sẽ được đăng trong vài tuần tới. Hãy theo dõi!</p>
HTML;
    }

    private function longContentFestivalKr(): string
    {
        return <<<'HTML'
<p>일정을 표시하세요: <strong>FoodShop 연례 푸드 페스티벌</strong>이 다음 달에 열립니다. 한 주말 동안 도시 최고의 셰프, 스트리트 푸드 노점, 음식 애호가가 한곳에 모여 요리 시연, 시식, 잊지 못할 식사를 합니다.</p>
<p><img src="https://placehold.co/800x400/fff3e0/ef6c00?text=Food+Festival" alt="푸드 페스티벌" style="max-width:100%;height:auto;"></p>

<h2>무엇을 기대할 수 있나요?</h2>
<p>페스티벌은 가족, 커플, 혼자 탐험하는 분 모두를 위한 것입니다.</p>
<ul>
<li><strong>라이브 요리 시연</strong> – 탑 셰프가 시그니처 요리를 처음부터 끝까지 만드는 것을 보세요. 집에서 쓸 수 있는 팁을 배웁니다. 양일 2시간마다 진행됩니다.</li>
<li><strong>20개 이상 노점의 스트리트 푸드</strong> – 베트남 반미, 한국 떡볶이, 일본 타코야키, 태국 망고 찹쌀까지. 한 장의 티켓으로 모든 스탤 이용, 각 스탤에서 음식별 결제.</li>
<li><strong>셰프 미팅</strong> – 요리책 사인, 질문, 좋아하는 셰프와 사진 촬영. 일정은 행사 1주 전 공지됩니다.</li>
<li><strong>키즈 코너</strong> – 쿠키 데코, 간단한 요리 활동, 건강한 간식 스테이션으로 아이들도 즐겁게.</li>
</ul>

<h2>안내</h2>
<p><strong>일시:</strong> 다음 달 공지. <strong>장소:</strong> 센트럴 파크 컨벤션 홀. <strong>운영 시간:</strong> 양일 10:00–20:00. <strong>티켓:</strong> 얼리버드 곧 오픈; 링크는 SNS에서 확인하세요.</p>

<p>전체 프로그램과 노점 목록을 최종 확정 중입니다. 식이 옵션, 접근성 등 자세한 내용은 곧 공개됩니다. 기대해 주세요!</p>
HTML;
    }

    private function longContentCourseEn(): string
    {
        return <<<'HTML'
<p>Vietnamese cuisine is beloved around the world for its fresh herbs, balanced flavours, and comforting bowls of phở. In this <strong>4-hour hands-on workshop</strong>, you will learn the fundamentals of Vietnamese cooking from our experienced instructors. No prior experience required – just bring your curiosity and appetite.</p>
<p><img src="https://placehold.co/800x450/e3f2fd/1565c0?text=Vietnamese+Cooking+Workshop" alt="Vietnamese Cooking Workshop" style="max-width:100%;height:auto;"></p>

<h2>What you will learn</h2>
<p>The workshop is divided into three main modules.</p>
<ul>
<li><strong>Pho broth from scratch</strong> – The soul of Vietnam’s most famous dish. You will learn how to select bones and spices, char ginger and onion, and simmer the broth to a clear, aromatic perfection. We will also cover the importance of timing and how to store broth for later use.</li>
<li><strong>Spring rolls (fresh & fried)</strong> – Fresh gỏi cuốn with shrimp and herbs, and crispy chả giò. You will practice rolling techniques, making the perfect rice paper wrap, and frying to golden crispness without burning.</li>
<li><strong>Dipping sauces & herbs</strong> – Nước chấm (fish sauce dressing), peanut sauce, and the essential herb plate. We will discuss where to find ingredients locally and how to substitute if needed.</li>
</ul>

<h2>What’s included</h2>
<p>All ingredients, recipe booklet, and a shared meal at the end where you taste everything you made. You will also take home a container of broth and a few spring rolls. <strong>Duration:</strong> 4 hours with a short break. <strong>Max participants:</strong> 12 (small groups for hands-on attention). <strong>Price:</strong> As listed; booking in advance recommended.</p>

<p>Whether you want to impress friends at a dinner party or simply cook better at home, this workshop will give you the skills and confidence to bring Vietnamese flavours to your table.</p>
HTML;
    }

    private function longContentCourseVn(): string
    {
        return <<<'HTML'
<p>Ẩm thực Việt Nam được yêu thích khắp thế giới nhờ rau thơm tươi, hương vị cân bằng và những tô phở ấm lòng. Trong <strong>workshop thực hành 4 giờ</strong> này, bạn sẽ học những điều cơ bản về nấu ăn Việt Nam từ giáo viên giàu kinh nghiệm. Không cần kinh nghiệm trước – chỉ cần sự tò mò và thèm ăn.</p>
<p><img src="https://placehold.co/800x450/e3f2fd/1565c0?text=Workshop+Nau+an+Viet+Nam" alt="Workshop nấu ăn Việt Nam" style="max-width:100%;height:auto;"></p>

<h2>Nội dung học</h2>
<p>Workshop gồm ba module chính.</p>
<ul>
<li><strong>Nước dùng phở từ đầu</strong> – Linh hồn của món Việt nổi tiếng nhất. Bạn sẽ học chọn xương và gia vị, đốt gừng hành và ninh nước dùng trong vắt, thơm. Chúng ta cũng nói về tầm quan trọng của thời gian và cách bảo quản nước dùng.</li>
<li><strong>Gỏi cuốn & chả giò</strong> – Gỏi cuốn tươi với tôm và rau thơm, và chả giò giòn. Bạn sẽ thực hành kỹ thuật cuốn, gói bánh tráng hoàn hảo và chiên vàng giòn không cháy.</li>
<li><strong>Nước chấm và rau thơm</strong> – Nước chấm (nước mắm pha), sốt đậu phộng và đĩa rau thơm không thể thiếu. Chúng ta sẽ bàn nơi mua nguyên liệu địa phương và cách thay thế nếu cần.</li>
</ul>

<h2>Bao gồm</h2>
<p>Toàn bộ nguyên liệu, sổ tay công thức và bữa ăn chung cuối giờ để nếm mọi thứ bạn làm. Bạn cũng mang về nhà một hộp nước dùng và vài cuốn gỏi/chả giò. <strong>Thời lượng:</strong> 4 giờ có giải lao ngắn. <strong>Số người tối đa:</strong> 12 (nhóm nhỏ để hướng dẫn sát). <strong>Giá:</strong> Theo bảng; nên đặt trước.</p>

<p>Dù bạn muốn gây ấn tượng bạn bè tại bữa tiệc hay đơn giản là nấu ngon hơn ở nhà, workshop này sẽ cho bạn kỹ năng và tự tin để mang hương vị Việt lên bàn ăn.</p>
HTML;
    }

    private function longContentCourseKr(): string
    {
        return <<<'HTML'
<p>베트남 요리는 신선한 허브, 균형 잡힌 맛, 편안한 팟 한 그릇으로 전 세계에서 사랑받습니다. 이 <strong>4시간 실습 워크숍</strong>에서 경험 많은 강사에게 베트남 요리 기초를 배웁니다. 사전 경험 불필요 – 호기심과 식욕만 가져오세요.</p>
<p><img src="https://placehold.co/800x450/e3f2fd/1565c0?text=Vietnamese+Cooking" alt="베트남 요리 워크숍" style="max-width:100%;height:auto;"></p>

<h2>학습 내용</h2>
<p>워크숍은 세 가지 모듈로 구성됩니다.</p>
<ul>
<li><strong>팟 국물 처음부터</strong> – 베트남 대표 요리의 영혼. 뼈와 향신료 고르기, 생강·양파 태우기, 맑고 향기로운 국물 우려내기. 시간 조절의 중요성과 국물 보관법도 다룹니다.</li>
<li><strong>춘권 (생 & 튀김)</strong> – 새우와 허브가 들어간 생춘권, 바삭한 볶음춘권. 말기 기술, 완벽한 쌀페이퍼 감기, 타지 않고 바삭하게 튀기기 연습.</li>
<li><strong>소스와 허브</strong> – 눅챰(피시소스 드레싱), 땅콩 소스, 필수 허브 플레이트. 현지에서 재료 구하는 법, 대체 재료도 설명합니다.</li>
</ul>

<h2>포함 사항</h2>
<p>모든 재료, 레시피 북, 마지막에 만든 요리를 함께 맛보는 식사. 국물 한 용기와 춘권 몇 개를 집으로 가져갑니다. <strong>소요 시간:</strong> 4시간 (짧은 휴식 포함). <strong>최대 인원:</strong> 12명 (소그룹 실습). <strong>가격:</strong> 표시대로; 사전 예약 권장.</p>

<p>친구들을 저녁 파티에서 놀라게 하고 싶든, 집에서 더 잘 요리하고 싶든, 이 워크숍이 베트남 맛을 식탁에 올리는 기술과 자신감을 줄 것입니다.</p>
HTML;
    }

    private function longContentChefEn(): string
    {
        return <<<'HTML'
<p><strong>Chef Minh Nguyen</strong> is our head chef and the creative force behind some of the most talked-about dishes in the city. With 15 years of experience spanning Vietnam, Japan, and France, he has developed a unique style that honours tradition while embracing innovation. We sat down with him to learn about his journey, his philosophy, and what inspires him in the kitchen.</p>
<p><img src="https://placehold.co/800x450/fce4ec/c2185b?text=Chef+Minh+Nguyen" alt="Chef Minh Nguyen" style="max-width:100%;height:auto;"></p>

<h2>Background</h2>
<p>Minh was born in Hanoi and grew up watching his grandmother cook. “She never used a recipe,” he says. “Everything was by feel – a pinch of this, a splash of that. That’s where I learned that cooking is about intuition as much as technique.” He left Vietnam at 18 to study in Japan, where he trained in washoku and learned discipline and precision. Later, he moved to France and worked in a Michelin-starred kitchen in Lyon. “France taught me about structure and sauce. Japan taught me about balance and seasonality. Vietnam gave me soul.”</p>

<h2>Signature style</h2>
<p>Today, Minh’s cooking is best described as <em>Asian fusion with a Vietnamese heart</em>. He reimagines classics – phở, bánh mì, gỏi cuốn – with techniques and ingredients from his travels. His phở might feature a clarified broth and a hint of dashi; his spring rolls might be served with a miso-based dip. “I don’t want to shock people. I want to surprise them in a way that feels familiar and new at the same time.”</p>

<h2>Specialties</h2>
<ul>
<li><strong>Pho reimagined</strong> – Clear, intense broth; house-made noodles; optional add-ons like slow-cooked brisket or herb oil. A dish that looks simple but takes two days to prepare.</li>
<li><strong>Vietnamese–French fusion</strong> – Crêpes with fillings inspired by bánh xèo; pâté and pickles that nod to bánh mì; desserts that blend tropical fruit with French pastry.</li>
<li><strong>Street food elevated</strong> – Bún chả, bánh cuốn, and bò lá lốt reimagined with premium ingredients and refined presentation, without losing the casual, joyful spirit of the original.</li>
</ul>

<blockquote><p>“Good food should make you feel something. Nostalgia, joy, curiosity – that’s what I’m after.”</p></blockquote>

<p>When he’s not in the kitchen, Minh enjoys hiking and reading about food history. He hopes to open a cooking school one day to pass on what he has learned. For now, you can taste his food at our restaurant and meet him at special events and festivals.</p>
HTML;
    }

    private function longContentChefVn(): string
    {
        return <<<'HTML'
<p><strong>Đầu bếp Minh Nguyễn</strong> là bếp trưởng của chúng tôi và là lực sáng tạo đằng sau những món ăn được bàn tán nhiều nhất trong thành phố. Với 15 năm kinh nghiệm trải dài Việt Nam, Nhật Bản và Pháp, anh đã xây dựng phong cách riêng tôn trọng truyền thống nhưng vẫn đón nhận đổi mới. Chúng tôi đã trò chuyện với anh về hành trình, triết lý và điều gì truyền cảm hứng cho anh trong bếp.</p>
<p><img src="https://placehold.co/800x450/fce4ec/c2185b?text=Dau+bep+Minh+Nguyen" alt="Đầu bếp Minh Nguyễn" style="max-width:100%;height:auto;"></p>

<h2>Xuất thân</h2>
<p>Minh sinh ra ở Hà Nội và lớn lên khi xem bà nấu ăn. “Bà chưa bao giờ dùng công thức,” anh nói. “Mọi thứ đều theo cảm nhận – một nhúm này, một chút kia. Đó là nơi tôi học rằng nấu ăn là trực giác cũng như kỹ thuật.” Anh rời Việt Nam năm 18 tuổi để học ở Nhật Bản, nơi anh tập washoku và học kỷ luật cùng sự chính xác. Sau đó anh chuyển sang Pháp và làm việc trong bếp một nhà hàng Michelin tại Lyon. “Pháp dạy tôi về cấu trúc và nước sốt. Nhật dạy tôi về cân bằng và tính thời vụ. Việt Nam cho tôi linh hồn.”</p>

<h2>Phong cách đặc trưng</h2>
<p>Ngày nay, ẩm thực của Minh được mô tả tốt nhất là <em>fusion Á với trái tim Việt</em>. Anh cách tân các món kinh điển – phở, bánh mì, gỏi cuốn – bằng kỹ thuật và nguyên liệu từ những chuyến đi. Phở của anh có thể có nước dùng trong vắt và chút dashi; gỏi cuốn có thể ăn kèm nước chấm gốc miso. “Tôi không muốn làm người ta sốc. Tôi muốn gây bất ngờ theo cách vừa quen vừa mới.”</p>

<h2>Chuyên môn</h2>
<ul>
<li><strong>Phở cách tân</strong> – Nước dùng trong, đậm đà; bánh phở nhà làm; topping tùy chọn như gầu ninh nhừ hay dầu rau thơm. Món trông đơn giản nhưng mất hai ngày chuẩn bị.</li>
<li><strong>Fusion Việt – Pháp</strong> – Bánh xèo kiểu crêpe; pâté và đồ chua gợi bánh mì; món tráng miệng kết hợp trái cây nhiệt đới với bánh ngọt Pháp.</li>
<li><strong>Ẩm thực đường phố nâng tầm</strong> – Bún chả, bánh cuốn, bò lá lốt cách tân với nguyên liệu cao cấp và trình bày tinh tế, nhưng vẫn giữ tinh thần thoải mái, vui tươi của bản gốc.</li>
</ul>

<blockquote><p>“Đồ ăn ngon phải khiến bạn cảm thấy điều gì đó. Hoài niệm, niềm vui, tò mò – đó là điều tôi hướng tới.”</p></blockquote>

<p>Khi không ở trong bếp, Minh thích đi bộ đường dài và đọc sách lịch sử ẩm thực. Anh hy vọng một ngày mở trường dạy nấu ăn để truyền lại những gì đã học. Hiện tại bạn có thể thưởng thức món ăn của anh tại nhà hàng chúng tôi và gặp anh tại các sự kiện và lễ hội đặc biệt.</p>
HTML;
    }

    private function longContentChefKr(): string
    {
        return <<<'HTML'
<p><strong>셰프 민 응우옌</strong>은 저희 헤드 셰프이자 도시에서 가장 화제가 되는 요리 뒤의 창작력입니다. 베트남, 일본, 프랑스에 걸친 15년 경력으로 전통을 존중하면서 혁신을 받아들이는 독특한 스타일을 만들었습니다. 그의 여정, 철학, 주방에서의 영감에 대해 이야기를 나눴습니다.</p>
<p><img src="https://placehold.co/800x450/fce4ec/c2185b?text=Chef+Minh+Nguyen" alt="셰프 민 응우옌" style="max-width:100%;height:auto;"></p>

<h2>배경</h2>
<p>민은 하노이에서 태어나 할머니가 요리하는 것을 보며 자랐습니다. “할머니는 레시피를 쓰지 않으셨어요. 모든 게 감이었죠 – 이것 한 꼬집, 저것 한 스푼. 요리는 기법만큼 직관이라는 걸 그때 배웠어요.” 18세에 베트남을 떠나 일본에서 유학하며 와쇼쿠를 배우고 절제와 정확함을 익혔습니다. 이후 프랑스로 건너가 리옹의 미슐랭 스타 부엌에서 일했습니다. “프랑스는 구조와 소스를, 일본은 균형과 계절성을, 베트남은 영혼을 줬어요.”</p>

<h2>시그니처 스타일</h2>
<p>오늘날 민의 요리는 <em>베트남의 마음을 담은 아시아 퓨전</em>으로 설명됩니다. 그는 팟, 반미, 고이쿠온 같은 클래식을 여행에서 얻은 기법과 재료로 재해석합니다. 그의 팟은 맑은 국물에 다시 맛을 더할 수 있고, 춘권은 미소 베이스 딥과 함께 나올 수 있습니다. “사람들을 충격에 빠뜨리려는 게 아니에요. 친숙하면서 새롭게 느껴지는 방식으로 놀라게 하고 싶어요.”</p>

<h2>전문</h2>
<ul>
<li><strong>리이매지닝 팟</strong> – 맑고 진한 국물, 집에서 만든 면, 슬로우 쿡 브리스킷이나 허브 오일 같은 토핑. 단순해 보이지만 이틀 걸리는 요리.</li>
<li><strong>베트남–프랑스 퓨전</strong> – 반세오에서 영감한 크레페, 반미를 연상시키는 파테와 피클, 열대 과일과 프랑스 페이스트리의 디저트.</li>
<li><strong>스트리트 푸드 업그레이드</strong> – 분차, 반쿠온, 보라롯을 고급 재료와 정제된 플레이팅으로 재해석하되, 원본의 캐주얼하고 즐거운 정신은 유지.</li>
</ul>

<blockquote><p>“좋은 음식은 무언가를 느끼게 해야 해요. 향수, 기쁨, 호기심 – 그게 제가 추구하는 거예요.”</p></blockquote>

<p>주방에 있지 않을 때 민은 하이킹과 음식 역사 책 읽기를 즐깁니다. 배운 것을 전하기 위해 언젠가 요리 학교를 열 희망입니다. 지금은 저희 레스토랑에서 그의 요리를 맛보고, 특별 이벤트와 페스티벌에서 그를 만나실 수 있습니다.</p>
HTML;
    }
}
