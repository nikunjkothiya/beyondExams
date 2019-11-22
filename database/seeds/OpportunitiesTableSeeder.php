<?php

use Illuminate\Database\Seeder;
use App\Opportunity;
use App\OpportunityTranslation;

class OpportunitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $opportunities = array(
        		'id' => 2779,
        		'deadline' => '2019-10-15',
        		'image' => 'https://www.noticebard.com/wp-content/uploads/2019/08/wfi.png',
        		'link' => 'https://www.noticebard.com/call-for-applications-international-fellowship-world-forestry-center/',
        		'fund_type_id' => 3,
                'slug' => 'international-fellowship-world-forestry-center-823nf892fv2ncicw',
        		'opportunity_location_id' => 1,
        		'bn' => [
        			'title' => 'আন্তর্জাতিক ফেলোশিপ বিশ্বের বনজ কেন্দ্র',
        			'description' => 'wfc আবিষ্কারের জাদুঘর 1971 সালে খোলা হয় স্থানীয় এবং বিশ্ব বন ও টেকসই বনায়ন সম্পর্কে সাধারণ জনগণের শিক্ষিত। Magness স্মারক বৃক্ষ খামার, আমাদের প্রধান বিক্ষোভের বন শেরউড, অরেগন কাছাকাছি অবস্থিত, উপলব্ধ করা হয় একটি হাত অন পরিবেশগত শেখার বহিরঙ্গন পদ্ধতির।',
        		],
        		'de' => [
        			'title' => "internationale Gemeinschaft Welt Forstzentrum",
        			'description' => "WFC Entdeckung Museum wurde 1971 eröffnet die allgemeine Öffentlichkeit über lokale und globale Wälder und eine nachhaltige Forstwirtschaft zu erziehen. magness Denkmal Baumfarm, unser premier Demonstration Wald in der Nähe von Sherwood, Oregon, bietet ein Hands-on-Außen Ansatz für Umwelt Lernen.",
        		],
        		'en' => [
        			'title' => "International Fellowship World Forestry Center",
        			'description' => "WFC’s Discovery Museum was opened in 1971 to educate the general public about local and global forests and sustainable forestry. Magness Memorial Tree Farm, our premier demonstration forest located near Sherwood, Oregon, offers a hands-on outdoor approach to environmental learning.",
        		],
        		'es' => [
        			'title' => "beca internacional en centros forestal mundial",
        			'description' => "museo descubrimiento del Congreso, que se abrió en 1971 para educar al público en general acerca de los bosques locales y globales y la silvicultura sostenible. granja de árboles monumento Magness, nuestro bosque demostración Premier encuentra cerca de Sherwood, Oregon, ofrece un enfoque práctico al aire libre para el aprendizaje del medio ambiente.",
        		],
        		'fr' => [
        			'title' => "Fellowship Centre international forestier mondial",
        			'description' => "Le musée de la découverte de WFC a ouvert ses portes en 1971 pour sensibiliser le grand public sur les forêts locales et mondiales et de la foresterie durable. Magness ferme arbre commémoratif, notre forêt de démonstration de premier plan situé près sherwood, oregon, offre une approche pratique en plein air à l'apprentissage de l'environnement.",
        		],
        		'hi' => [
        			'title' => "अंतरराष्ट्रीय फैलोशिप दुनिया वानिकी केंद्र",
        			'description' => "WFC की खोज संग्रहालय 1971 में खोला गया था स्थानीय और वैश्विक जंगलों और स्थायी वानिकी के बारे में आम जनता को शिक्षित करने के। Magness स्मारक पेड़ खेत, हमारे प्रमुख प्रदर्शन वन शेरवुड, ओरेगन निकट स्थित है, प्रदान करता है एक हाथ पर्यावरण सीखने के लिए घर के बाहर दृष्टिकोण।",
        		],
        		'id' => [
        			'title' => "persekutuan internasional pusat kehutanan dunia",
        			'description' => "Museum Penemuan WFC ini dibuka pada tahun 1971 untuk mendidik masyarakat umum tentang hutan lokal dan global dan kehutanan yang berkelanjutan. Magness pertanian pohon memorial, hutan demonstrasi utama kami terletak dekat sherwood, oregon, menawarkan tangan-pendekatan di luar ruangan untuk belajar lingkungan.",
        		],
        		'it' => [
        			'title' => "borsa di studio internazionale centro forestale mondo",
        			'description' => "museo scoperta di WFC è stato aperto nel 1971 per educare il pubblico sui foreste locali e globali e silvicoltura sostenibile. Magness albero memoriale azienda, la nostra foresta premier dimostrazione trova vicino Sherwood, Oregon, offre un approccio pratico esterna all'apprendimento ambientale.",
        		],
        		'ja' => [
        			'title' => "国際フェローシップ世界の林業センター",
        			'description' => "WFCの発見博物館は、ローカルおよびグローバルな森林と持続可能な林業に関する一般大衆を教育するために1971年にオープンしました。 magnessメモリアルツリーファーム、シャーウッドの近くに私たちの最高のデモンストレーション林、オレゴン州は、ハンズオン環境学習への屋外のアプローチを提供しています。",
        		],
        		'km' => [
        			'title' => "កណ្តាលនៅលើពិភពលោកព្រៃឈើប្រកបអន្ដរជាតិ",
        			'description' => "សារមន្ទីររកឃើញ WFC ត្រូវបានបើកក្នុងឆ្នាំ 1971 ដើម្បីអប់រំសាធារណជនទូទៅអំពីព្រៃក្នុងតំបន់និងពិភពលោកនិងព្រៃឈើប្រកបដោយនិរន្តរភាព។ កសិដ្ឋានដើមឈើជាទីរំលឹក magness, នាយករដ្ឋមន្ត្រីរបស់យើងព្រៃបាតុកម្ម Sherwood ទីតាំងស្ថិតនៅក្បែរ, រដ្ឋ Oregon បានផ្តល់នូវដៃនៅលើវិធីសាស្រ្តខាងក្រៅដើម្បីរៀនបរិស្ថាន។",
        		],
        		'ko' => [
        			'title' => "국제 친교 세계 임업 센터",
        			'description' => "WFC의 발견 박물관은 로컬 및 글로벌 숲과 지속 가능한 산림에 대한 일반 대중을 교육하기 위해 1971 년에 문을 열었습니다. magness 기념 나무 농장은 우리의 최고의 데모 숲 셔우드, 오레곤 근처에 위치하고, 제공하는 실습 환경 학습에 야외 접근.",
        		],
        		'lo' => [
        			'title' => "fellowship ລະຫວ່າງປະເທດສູນກາງປ່າໄມ້ໂລກ",
        			'description' => "ພິພິທະພັນການຄົ້ນພົບ WFC ຂອງຖືກເປີດຂຶ້ນໃນປີ 1971 ເພື່ອສຶກສາອົບຮົມປະຊາຊົນທົ່ວໄປກ່ຽວກັບປ່າໄມ້ໃນທ້ອງຖິ່ນແລະລະດັບໂລກແລະປ່າໄມ້ແບບຍືນຍົງ. Magness ກະສິກໍາອະນຸສອນຕົ້ນໄມ້, ປ່າໄມ້ສາທິດຊັ້ນນໍາຂອງພວກເຮົາຕັ້ງຢູ່ໃກ້ກັບປະເທດຂອງ Sherwood, oregon, ສະຫນອງການມືກ່ຽວກັບວິທີກາງແຈ້ງເພື່ອການຮຽນຮູ້ສິ່ງແວດລ້ອມ.",
        		],
        		'ms' => [
        			'title' => "persekutuan antarabangsa pusat perhutanan dunia",
        			'description' => "Muzium Penemuan WFC telah dibuka pada tahun 1971 untuk mendidik orang awam mengenai hutan tempatan dan global dan perhutanan mampan. magness ladang pokok peringatan, hutan demonstrasi utama kami terletak berhampiran sherwood, oregon, menawarkan hands-on pendekatan luar pembelajaran alam sekitar.",
        		],
        		'my' => [
        			'title' => "အပြည်ပြည်ဆိုင်ရာမိတ်သဟာယဖွဲ့သည်ကမ္ဘာပေါ်တွင်သစ်တောစင်တာ",
        			'description' => "wfc ရဲ့ရှာဖွေတွေ့ရှိမှုပြတိုက်ပြည်တွင်းနှင့်ကမ္ဘာလုံးဆိုင်ရာသစ်တောများနှင့်ရေရှည်တည်တံ့သစ်တောနှင့် ပတ်သက်. အများပြည်သူပညာပေးရန် 1971 ခုနှစ်တွင်ဖွင့်လှစ်ခဲ့ခြင်းဖြစ်သည်။ magness အောက်မေ့ဖွယ်သစ်ပင်လယ်ယာ, sherwood အနီးတွင်တည်ရှိသောကျွန်တော်တို့ရဲ့ဝန်ကြီးချုပ်သရုပ်ပြသစ်တော, Oregon, တစ်ဦးလက်ပေါ်သဘာဝပတ်ဝန်းကျင်သင်ယူမှုမှပြင်ပချဉ်းကပ်ကမ်းလှမ်း။",
        		],
        		'ne' => [
        			'title' => "अन्तर्राष्ट्रिय संघ विश्व वानिकी केन्द्र",
        			'description' => "WFC गरेको खोज संग्रहालय स्थानीय र वैश्विक वन र दिगो वानिकी बारेमा सामान्य सार्वजनिक शिक्षित गर्न 1971 मा खोलिएको थियो। magness स्मारक रूख खेत, हाम्रो प्रिमियर प्रदर्शन वन Sherwood, ओरेगन नजिकै स्थित प्रदान गर्दछ एक हात-मा पर्यावरण सिक्ने गर्न आउटडोर दृष्टिकोण।",
        		],
        		'ro' => [
        			'title' => "bursa internațională centru mondial forestiere",
        			'description' => "muzeu descoperirea WFC a fost deschis în 1971 pentru a educa publicul larg cu privire la pădurile locale și globale și forestiere durabile. Magness ferma copac memorial, pădure noastră demonstrație Premier situat în apropiere de Sherwood, Oregon, oferă un hands-on abordare a procesului de învățare în aer liber de mediu.",
        		],
        		'ru' => [
        			'title' => "Международное содружество мировых лесной центр",
        			'description' => "Открытие музея ВЛК был открыт в 1971 году для обучения широкой общественности о местных и глобальных лесов и устойчивого развития лесного хозяйства. Magness памятного дерево фермы, наш главная демонстрация леса недалеко от Sherwood, Орегон, предлагает практические открытый подход к экологическому обучению.",
        		],
        		'si' => [
        			'title' => "ජාත්යන්තර සහභාගිකම ලෝක වන මධ්යස්ථානය",
        			'description' => "wfc ගවේෂණය කෞතුකාගාරය දේශීය හා ගෝලීය වනාන්තර සහ තිරසාර වන ගැන පොදු මහජනතාව දැනුවත් කිරීමට 1971 දී විවෘත කරන ලදී. magness අනුස්මරණ ගස් වගාවේ ෂර්වුඩ් අසල පිහිටා අපගේ ප්රමුඛතම විරෝධතාව වනාන්තර, ඔරිගන්, එය අත්-මත පාරිසරික ඉගෙනුම් කිරීමට එළිමහන් ප්රවේශය සපයයි.",
        		],
        		'ta' => [
        			'title' => "சர்வதேச கூட்டுறவு உலக வனவியல் மையம்",
        			'description' => "WFC கண்டுபிடிக்கும்வரை அருங்காட்சியகத்தில் 1971 இல் திறக்கப்பட்டது உள்ளூர் மற்றும் உலக காடுகள் மற்றும் நிலையான வனவியல் பற்றி பொது கல்வி இருந்தது. magness நினைவு மரம் விவசாய, எங்கள் முதன்மையான ஆர்ப்பாட்டம் காட்டில் ஷெர்வுட், ஓரிகன் அருகே அமைந்துள்ள வழங்குகிறது ஒரு நேரடி சுற்றுச்சூழல் கற்றல் வெளிப்புற அணுகுமுறை.",
        		],
        		'th' => [
        			'title' => "การคบหาระหว่างประเทศศูนย์ป่าไม้โลก",
        			'description' => "พิพิธภัณฑ์การค้นพบ WFC ถูกเปิดในปี 1971 เพื่อให้ความรู้กับประชาชนทั่วไปเกี่ยวกับการป่าไม้ในท้องถิ่นและทั่วโลกและการป่าไม้อย่างยั่งยืน Magness ฟาร์มที่ระลึกต้นไม้ป่าสาธิตชั้นนำของเราตั้งอยู่ใกล้กับเชอร์วู้ด, โอเรกอนมีมือบนวิธีกลางแจ้งเพื่อการเรียนรู้ด้านสิ่งแวดล้อม",
        		],
        		'tl' => [
        			'title' => "internasyonal na pakikisama mundo panggugubat center",
        			'description' => "ni WFC discovery museum ay binuksan noong 1971 upang turuan ang pangkalahatang publiko tungkol sa mga lokal at global na kagubatan at sustainable panggugubat. Magness memorial puno sakahan, ang aming mga premier demonstration kagubatan na matatagpuan malapit sa sherwood, oregon, nag-aalok ng isang hands-on panlabas na diskarte sa kapaligiran pag-aaral.",
        		],
        		'vi' => [
        			'title' => "trung tâm lâm nghiệp thế giới học bổng quốc tế",
        			'description' => "Bảo tàng khám phá WFC đã được khai trương vào năm 1971 để giáo dục công chúng về rừng địa phương và toàn cầu và lâm nghiệp bền vững. Magness trang trại cây lưu niệm, rừng trình diễn hàng đầu của chúng tôi nằm gần sherwood, oregon, cung cấp một thực hành phương pháp ngoài trời để học tập về môi trường.",
        		],
        		'zh' => [
        			'title' => "國際獎學金世界林業中心",
        			'description' => "WFC發現博物館於1971年開業，教導當地和全球森林和林業可持續廣大市民。麥格尼斯紀念樹的農場，我們的首要示範林靠近舍伍德，俄勒岡州，提供一個動手的戶外方法來應付環境學習。",
        		],
        );

       	$r = Opportunity::create($opportunities);
       	$r->tags()->sync([1,14,19]); 
        $r->eligible_regions()->sync([243]);

       	/*--------------------------------------------------------------------------------*/

       	$opportunities = array(
        		'id' => 2780,
        		'deadline' => '2019-09-15',
        		'image' => 'http://competitions.archi/wp-content/uploads/2019/08/city-of-dreams.jpg',
        		'link' => 'http://competitions.archi/competition/city-of-dreams-pavilion-2020-call-for-proposals/',
        		'fund_type_id' => 3,
                'slug' => 'city-of-dreams-pavilion-2020-call-for-proposals-182bnc89bvxc2e3',
        		'opportunity_location_id' => 1,
        		'bn' => [
        			'title' => "স্বপ্ন পটমণ্ডপ 2020 শহরে: প্রস্তাব জন্য কল",
        			'description' => "বিশ্বের বর্তমান অবস্থা এমন হল যে উভয় অর্থনৈতিক ও প্রাকৃতিক সম্পদ সীমিত এবং অসম বিতরণ করা হয়। চিন্তা একটি নতুন উপায় সমস্যার যে বিশ্বের মুখোমুখি সমাধান করা প্রয়োজন। অবশ্যম্ভাবীরূপে, ফলে মৌলিক চর্চা এটির বর্তমান অবস্থায় বিশ্বের চালিত আছে পরিবর্তন হতে হবে। এক জায়গায় সক্রিয় করতে এবং এই পরিবর্তনগুলি উদ্যত শুরু স্থাপত্য এবং নকশা সম্প্রদায়, যেখানে জলবায়ু কর্ম দিকে আন্দোলন শক্তি মান যে ভবিষ্যতে কার্বন নিরপেক্ষ পরিবেশ বানাও হতে ব্যবহারের উৎসাহিত করেছে মধ্যে।",
        		],
        		'de' => [
        			'title' => "Stadt der Träume Pavillon 2020: zur Einreichung von Vorschlägen nennen",
        			'description' => "der aktuelle Zustand der Welt ist so, dass sowohl wirtschaftliche als auch die natürlichen Ressourcen begrenzt sind und ungleich verteilt. eine neue Art des Denkens ist notwendig, um die Probleme zu lösen, die die Welt steht. unvermeidlich, wird das Ergebnis eine Änderung der grundlegenden Praktiken, die die Welt zu seinem aktuellen Zustand getrieben haben. ein Ort zu starten, um diese Änderungen zu aktivieren und Energie ist innerhalb der Architektur und Design-Community, wo die Bewegung in Richtung auf der Bewältigung des Klimawandels hat die Verwendung von Energiestandards gefördert, die zu einem zukünftigen klimaneutral baulichen Umfeld führen.",
        		],
        		'en' => [
        			'title' => "City of Dreams Pavilion 2020: Call for Proposals",
        			'description' => "The current state of the world is such that both economic and natural resources are limited and unequally distributed. A new way of thinking is necessary to solve the problems that the world faces. Inevitably, the result will be a change in the basic practices that have driven the world to its current state. One place to start to activate and energize these changes is within the architecture and design community, where the movement toward climate action has encouraged the use of energy standards that lead to a future carbon neutral built environment.",
        		],
        		'es' => [
        			'title' => "ciudad de los sueños pabellón de 2020: convocatoria de propuestas",
        			'description' => "el estado actual del mundo es tal que tanto los recursos económicos y naturales son limitados y distribuidos de forma desigual. una nueva forma de pensar es necesaria para resolver los problemas que enfrenta el mundo. Inevitablemente, el resultado será un cambio en las prácticas básicas que han llevado al mundo a su estado actual. un lugar para empezar a activar y energizar estos cambios está dentro de la arquitectura y el diseño de la comunidad, donde el movimiento hacia la acción climática ha fomentado el uso de estándares de energía que conducen a un entorno construido de carbono neutral futuro.",
        		],
        		'fr' => [
        			'title' => "ville de rêves pavillon 2020: appel à propositions",
        			'description' => "l'état actuel du monde est telle que les ressources économiques et naturelles sont limitées et inégalement réparties. une nouvelle façon de penser est nécessaire pour résoudre les problèmes auxquels le monde est confronté. inévitablement, le résultat sera un changement dans les pratiques de base qui ont poussé le monde à son état actuel. un endroit pour commencer à activer et dynamiser ces changements au sein de la communauté de l'architecture et du design, où le mouvement vers l'action climatique a encouragé l'utilisation des normes énergétiques qui mènent à un futur environnement bâti neutre en carbone.",
        		],
        		'hi' => [
        			'title' => "सपनों मंडप 2020 के शहर: प्रस्तावों के लिए कॉल",
        			'description' => "दुनिया की वर्तमान स्थिति ऐसी है कि दोनों आर्थिक और प्राकृतिक संसाधनों सीमित है और असमान वितरित कर रहे हैं। सोच का एक नया तरीका समस्याओं का सामना करना पड़ता है कि दुनिया को हल करने के लिए आवश्यक है। अनिवार्य रूप से, परिणाम बुनियादी प्रथाओं कि अपनी वर्तमान स्थिति के लिए दुनिया को प्रेरित किया में एक परिवर्तन किया जाएगा। एक ही स्थान पर सक्रिय करने और इन परिवर्तनों को उत्साहित करने के लिए शुरू करने के लिए वास्तुकला और डिजाइन समुदाय है, जहां जलवायु कार्रवाई की ओर आंदोलन ऊर्जा मानकों है कि भविष्य कार्बन न्यूट्रल निर्माण परिवेश नेतृत्व के उपयोग को प्रोत्साहित किया है के भीतर है।",
        		],
        		'id' => [
        			'title' => "kota impian paviliun 2020: panggilan untuk proposal",
        			'description' => "keadaan saat ini dunia adalah seperti bahwa baik sumber daya ekonomi dan alam terbatas dan tidak merata. cara berpikir baru yang diperlukan untuk memecahkan masalah yang dihadapi dunia. pasti, hasilnya akan perubahan dalam praktek dasar yang telah mendorong dunia untuk kondisi saat ini. satu tempat untuk memulai untuk mengaktifkan dan energi perubahan ini adalah dalam arsitektur dan desain masyarakat, di mana gerakan menuju aksi iklim telah mendorong penggunaan standar energi yang mengarah ke karbon lingkungan buatan netral masa depan.",
        		],
        		'it' => [
        			'title' => "città dei sogni padiglione 2020: invito a presentare proposte",
        			'description' => "lo stato attuale del mondo è tale che sia le risorse economiche e naturali sono limitate e inegualmente distribuita. un nuovo modo di pensare è necessario per risolvere i problemi che il mondo deve affrontare. inevitabilmente, il risultato sarà un cambiamento nelle pratiche di base che hanno guidato il mondo per il suo stato attuale. Un punto di partenza per attivare ed energizzare questi cambiamenti è all'interno della comunità dell'architettura e del design, dove il movimento verso l'azione clima ha favorito l'uso di standard energetici che portano ad un futuro ambiente carbon neutral costruito.",
        		],
        		'ja' => [
        			'title' => "夢のパビリオン2020年の都市：提案を求めます",
        			'description' => "世界の現在の状態は、両方の経済的、天然資源が限られており、不均等に分布するようなものです。新しい考え方は、世界が直面している問題を解決する必要があります。必然的に、結果は現在の状態に世界を牽引してきた基本的な慣行の変化となります。これらの変更を有効にし、通電する開始する一つの場所は、気候行動に向けた動きは、今後のカーボンニュートラル建築環境につながるエネルギー基準の使用を奨励してきた建築とデザインのコミュニティ内です。",
        		],
        		'km' => [
        			'title' => "ទីក្រុងនៃក្តីសុបិន្តពន្លាឆ្នាំ 2020: អំពាវនាវឱ្យសំណើ",
        			'description' => "ស្ថានភាពបច្ចុប្បន្ននៃពិភពលោកនេះគឺមានដូចថាទាំងធម្មជាតិនិងធនធានសេដ្ឋកិច្ចនិងការចែកចាយនៅមានកម្រិតមិនស្មើគ្នា។ ជាវិធីថ្មីនៃការគិតគឺជាការចាំបាច់ដើម្បីដោះស្រាយបញ្ហាដែលពិភពលោកជួប។ ចៀសមិនរួច, លទ្ធផលនឹងមិនប្រែប្រួលនៅក្នុងការអនុវត្តន៍មូលដ្ឋានដែលបានជំរុញពិភពលោកឱ្យស្ថានភាពបច្ចុប្បន្នរបស់ខ្លួន។ កន្លែងមួយទៅកន្លែងចាប់ផ្តើមដើម្បីធ្វើឱ្យការផ្លាស់ប្តូរទាំងនេះនិងជួយស្ថាបត្យកម្មនិងការគឺនៅក្នុងការរចនាសហគមន៍ដែលជាកន្លែងដែលចលនាឆ្ពោះទៅរកសកម្មភាពអាកាសធាតុដែលបានលើកទឹកចិត្តនៃការប្រើប្រាស់ស្តង់ដារថាមពលដែលនាំឱ្យមានបរិយាកាសអព្យាក្រឹតកាបូននាពេលអនាគតសាងសង់នេះ។",
        		],
        		'ko' => [
        			'title' => "꿈 파빌리온 2020 도시 : 제안을 요청",
        			'description' => "세계의 현재 상태는 경제적 천연 자원이 제한적이고 불균등하게 분포하도록한다. 새로운 사고 방식으로는 세계가 직면하고있는 문제를 해결하는 것이 필요하다. 불가피하게, 결과는 현재 상태로 세상을 구동 한 기본적인 관행의 변화 일 것이다. 활성화하고 이러한 변화에 활력을 불어 시작하는 한 곳은 기후 행동을 향한 움직임이 미래의 탄소 중립 건축 환경을 초래할 에너지 표준의 사용을 장려 한 건축과 디자인 커뮤니티에 있습니다.",
        		],
        		'lo' => [
        			'title' => "ນະຄອນຂອງຕູນ pavilion 2020: ໂທຫາສໍາລັບບົດສະເຫນີ",
        			'description' => "ສະຖານະປັດຈຸບັນຂອງໂລກແມ່ນວ່າຊັບພະຍາກອນດ້ານເສດຖະກິດແລະທໍາມະຊາດມີຈໍາກັດແລະສະເຫມີກັນ. ວິທີການໃຫມ່ຂອງການຄິດເປັນສິ່ງຈໍາເປັນເພື່ອແກ້ໄຂບັນຫາທີ່ໂລກປະເຊີນຫນ້າກັບ. inevitably, ຜົນໄດ້ຮັບຈະມີການປ່ຽນແປງໃນການປະຕິບັດຂັ້ນພື້ນຖານທີ່ໄດ້ຂັບໄລ່ໂລກໃນການຂອງລັດໃນປະຈຸບັນຂອງຕົນ. ສະຖານທີ່ຫນຶ່ງເພື່ອເລີ່ມຕົ້ນການເພື່ອກະຕຸ້ນແລະກະຕຸ້ນການປ່ຽນແປງເຫຼົ່ານີ້ຢູ່ພາຍໃນສະຖາປັດຕະແລະການອອກແບບຊຸມຊົນ, ບ່ອນທີ່ການເຄື່ອນໄຫວໄປສູ່ການປະຕິບັດສະພາບອາກາດທີ່ໄດ້ຊຸກຍູ້ໃຫ້ນໍາໃຊ້ມາດຕະຖານພະລັງງານທີ່ນໍາໄປສູ່ການເປັນກາກບອນສະພາບແວດລ້ອມໃນອະນາຄົດ built ກາງໄດ້.",
        		],
        		'ms' => [
        			'title' => "bandar impian pavilion 2020: panggilan bagi cadangan",
        			'description' => "keadaan semasa dunia itu dalam keadaan bahawa kedua-dua sumber ekonomi dan semula jadi adalah terhad dan dibahagikan tidak sama rata. cara berfikir yang baru adalah perlu untuk menyelesaikan masalah-masalah yang dihadapi dunia. tidak dapat tidak, hasilnya akan menjadi perubahan dalam amalan asas yang telah memacu dunia untuk keadaan semasa. satu tempat ke tempat mula mengaktifkan dan memberi tenaga kepada perubahan ini terletak dalam seni bina dan reka bentuk masyarakat, di mana pergerakan ke arah tindakan iklim telah menggalakkan penggunaan standard tenaga yang membawa kepada karbon alam bina neutral hadapan.",
        		],
        		'my' => [
        			'title' => "အိပ်မက်တဲ 2020 ၏မြို့: အဆိုပြုချက်အဘို့ပဌနာ",
        			'description' => "ကမ္ဘာ၏လက်ရှိပြည်နယ်နှစ်ခုလုံးကိုစီးပွားရေးနှင့်သဘာဝသယံဇာတကန့်သတ်ဖြစ်ကြပြီးမမျှတစွာဖြန့်ဝေကြောင်းထိုကဲ့သို့သောဖြစ်ပါတယ်။ စဉ်းစားတွေးခေါ်နေတဲ့နည်းလမ်းသစ်သည်ကမ္ဘာ့ရင်ဆိုင်နေရသောပြဿနာများကိုဖြေရှင်းနိုင်ရန်လိုအပ်ပေသည်။ မလွှဲမရှောင်, ရလဒ်က၎င်း၏လက်ရှိပြည်နယ်မှကမ္ဘာကြီးကိုမောင်းနှင်ကြသောအခြေခံအလေ့အကျင့်အတွက်အပြောင်းအလဲတစ်ခုဖြစ်လိမ့်မည်။ ဤပြောင်းလဲသက်ဝင်စေနှင့်ဝါဒီများကိုစွမ်းအားမှစတင်နိုင်ရန်တစ်နေရာတည်းရာသီဥတုအရေးယူဆီသို့လှုပ်ရှားမှုအနာဂတ်ကာဗွန်ကြားနေ built ပတ်ဝန်းကျင်ဖို့ဦးဆောင်လမ်းပြကြောင်းစွမ်းအင်စံချိန်စံညွှန်းများအသုံးပြုခြင်းအားပေးခဲ့ရှိရာဗိသုကာနှင့်ဒီဇိုင်းအသိုင်းအဝိုင်းအတွင်းဖြစ်ပါတယ်။",
        		],
        		'ne' => [
        			'title' => "सपना मंडप 2020 को शहर: प्रस्ताव लागि कल",
        			'description' => "संसारको वर्तमान राज्य दुवै आर्थिक र प्राकृतिक स्रोतहरू सीमित र unequally वितरण छन् कि यस्तो छ। सोचाइ एक नयाँ तरिका दुनिया सामना कि समस्या समाधान गर्न आवश्यक छ। अनिवार्य, परिणाम आफ्नो वर्तमान अवस्थामा विश्व संचालित छन् भन्ने आधारभूत अभ्यासहरू मा परिवर्तन हुनेछ। सक्रिय र यी परिवर्तनहरू energize गर्न सुरु गर्न एक ठाउँमा आर्किटेक्चर र डिजाइन समुदाय, जहाँ जलवायु कार्य तिर आन्दोलन भावी कार्बन तटस्थ निर्मित वातावरण नेतृत्व भन्ने ऊर्जा स्तर को प्रयोग प्रोत्साहन गरेको छ भित्र छ।",
        		],
        		'ro' => [
        			'title' => "orașul viselor pavilionului 2020: cerere de propuneri",
        			'description' => "starea actuală a lumii este de așa natură încât atât resursele economice și naturale sunt limitate și distribuite inegal. un nou mod de gândire este necesară pentru a rezolva problemele cu care se confruntă lumea. în mod inevitabil, rezultatul va fi o schimbare în practicile de bază care au condus lumea la starea sa actuală. un singur loc pentru a începe să activeze și energiza aceste schimbări este în cadrul comunității de arhitectură și design, în cazul în care mișcarea spre acțiune climatică a încurajat utilizarea unor standarde energetice care duc la un viitor mediu construit neutru de carbon.",
        		],
        		'ru' => [
        			'title' => "город мечта павильона 2020: призыв к предложениям",
        			'description' => "текущее состояние мира таково, что как экономические, так и природные ресурсы ограничены и распределены неравномерно. новый способ мышления необходимо решить проблемы, с которыми сталкивается мир. неизбежно, то результат будет изменением основных методов, которые привели мир к его текущему состоянию. одно место, чтобы начать, чтобы активировать и активизировать эти изменения в архитектуре и дизайн сообщества, где движение к действию климата поощряло использование энергетических стандартов, которые приводят к будущему углерода нейтральной застроенной среде.",
        		],
        		'si' => [
        			'title' => "සිහින මණ්ඩපය 2020 නගරය: යෝජනා ඉල්ලා",
        			'description' => "ලෝකයේ දැනට පවතින රාජ්ය දෙකම ආර්ථික හා ස්වභාවික සම්පත් සීමිත බව හා අසාමාන්ය ලෙස බෙදා ආකාරයේ වේ. චින්තනය නව ක්රමය ලෝකයේ මුහුන දෙන බව ප්රශ්න විසඳා ගැනීමට අවශ්ය වේ. අනිවාර්යයෙන් ම, ප්රතිඵලය, එහි වර්තමාන තත්ත්වය ගැන ලෝකය ධාවනය කර ඇති මූලික පිළිවෙත් වෙනස් වනු ඇත. මෙම වෙනස්කම් ක්රියාත්මක සහ ශක්තිමත් කිරීමට ආරම්භ කිරීමට එක් එක් ස්ථානය දේශගුණික ක්රියාමාර්ගයක් දෙසට ව්යාපාරය අනාගත කාබන් හරිත ඉදි පරිසරය සඳහා ඒ ශක්තිය ප්රමිතීන් භාවිතය දිරිමත් කර තිබෙනවා එහිදී ගෘහ නිර්මාණ ශිල්පය හා මෝස්තර නිර්මාණ ප්රජාව, තුළ ය.",
        		],
        		'ta' => [
        			'title' => "கனவுகள் பெவிலியன் 2020 நகரத்தில்: முன்மொழிவுகள் அழைப்பு",
        			'description' => "தற்போதைய உலகின் மாநில இருவரும் பொருளாதார மற்றும் இயற்கை வளங்கள் வரையறுக்கப்பட்ட மற்றும் சமத்துவமின்றி விநியோகிக்கப்படுகிறது இருப்பதோடு அதன். சிந்தனை ஒரு புதிய வழி உலகிற்கு ஏற்படுகின்ற பிரச்சினைகளை தீர்க்க வேண்டும். தவிர்க்க முடியாமல், விளைவாக அதன் தற்போதைய மாநில உலக தூண்டப்படுகின்றனர் என்று அடிப்படை நடைமுறைகளில் மாற்றம் இருக்கும். செயல்படுத்த இந்த மாற்றங்களின் உற்சாகப்படுத்தவதற்கான தொடங்க ஒரே இடத்தில் காலநிலை நடவடிக்கை நோக்கி இயக்கம் ஒரு எதிர்கால கார்பன் நடுநிலை கட்டப்பட்ட சூழலுக்கு வழிவகுக்கும் என்று ஆற்றல் தரத்தை ஊக்குவித்தது எங்கே கட்டிடக்கலை மற்றும் வடிவமைப்பு சமுதாயம் என்பது இந்நகரில் அமைந்துள்ளது.",
        		],
        		'th' => [
        			'title' => "เมืองในฝันของศาลา 2020: โทรสำหรับข้อเสนอ",
        			'description' => "สถานะปัจจุบันของโลกเป็นเช่นนั้นทรัพยากรทั้งทางเศรษฐกิจและธรรมชาติมีจำนวน จำกัด และจัดจำหน่ายอย่างไม่มีที่เปรียบ วิธีการใหม่ของการคิดเป็นสิ่งที่จำเป็นในการแก้ปัญหาที่โลกใบหน้า หลีกเลี่ยงไม่ได้ผลที่จะมีการเปลี่ยนแปลงในการปฏิบัติพื้นฐานที่มีการขับเคลื่อนโลกให้กับรัฐในปัจจุบัน หนึ่งในสถานที่ที่จะเริ่มต้นเพื่อเปิดใช้งานและพลังการเปลี่ยนแปลงเหล่านี้อยู่ภายในสถาปัตยกรรมและการออกแบบชุมชนที่การเคลื่อนไหวต่อการกระทำของสภาพภูมิอากาศได้สนับสนุนให้ใช้มาตรฐานการใช้พลังงานที่นำไปสู่การสร้างสภาพแวดล้อมคาร์บอนในอนาคตที่เป็นกลาง",
        		],
        		'tl' => [
        			'title' => "lungsod ng mga pangarap pavilion 2020: tawag para sa mga panukala",
        			'description' => "ang kasalukuyang estado ng mundo ay tulad na ang parehong pang-ekonomiya at likas na yaman ay limitado at hindi pantay ipinamamahagi. isang bagong paraan ng pag-iisip ay kinakailangan upang malutas ang mga problema na ang mundo ay nakaharap. hindi maaaring hindi, ang resulta ay magiging isang pagbabago sa ang pangunahing kasanayan na naghimok ng mundo sa kanyang kasalukuyang estado. isang lugar upang magsimula upang i-activate at pasiglahin ang mga pagbabagong ito ay sa loob ng arkitektura at disenyo ng komunidad, kung saan ang kilusan papunta sa klima aksyon ay hinihikayat ang paggamit ng mga pamantayan ng enerhiya na humantong sa isang hinaharap na carbon neutral built kapaligiran.",
        		],
        		'vi' => [
        			'title' => "thành phố của những giấc mơ Pavilion 2020: kêu gọi đề xuất",
        			'description' => "tình trạng hiện thời của thế giới là như vậy mà cả hai nguồn lực kinh tế và thiên nhiên có giới hạn và phân bố không đều. một cách suy nghĩ mới là cần thiết để giải quyết những vấn đề mà thế giới đang phải đối mặt. chắc chắn, kết quả sẽ là một sự thay đổi trong hoạt động cơ bản đã thúc đẩy thế giới đến tình trạng hiện thời của nó. một nơi để bắt đầu kích hoạt và năng lượng cho những thay đổi này nằm trong kiến ​​trúc và thiết kế cộng đồng, nơi phong trào hướng tới hành động khí hậu đã khuyến khích việc sử dụng các tiêu chuẩn năng lượng dẫn đến một carbon môi trường xây dựng trung lập trong tương lai.",
        		],
        		'zh' => [
        			'title' => "城市夢想館2020：徵集",
        			'description' => "世界的現狀是這樣的，經濟和自然資源是有限的，分佈不均勻。一種新的思維方式是必要的，以解決世界面臨的問題。不可避免的，其結果將是在推動了世界到其當前狀態的基本做法的改變。一個地方開始啟動和激發這些變化是在建築和設計社區，在這裡對氣候行動運動鼓勵使用能效標準，導致未來的碳中和建築環境中。",
        		],
        );

       	$r = Opportunity::create($opportunities);
       	$r->tags()->sync([1,11,18]);
        $r->eligible_regions()->sync([243]);

       	/*--------------------------------------------------------------------------------*/

       	$opportunities = array(
        		'id' => 2789,
        		'deadline' => '2020-12-25',
        		'image' => 'https://d8it4huxumps7.cloudfront.net/uploads/images/150x150/5d56543de4bf0_moon_trip1.jpg',
        		'link' => 'https://competitions.uni.xyz/moontrip',
        		'fund_type_id' => 3,
                'slug' => 'moon-trip-inspiring-humanity-to-explore-beyond-earth-1jf83bvcdu3fgduh',
        		'opportunity_location_id' => 243,
        		'bn' => [
        			'title' => "চাঁদ ট্রিপ - পৃথিবী অতিক্রম অন্বেষণ করতে মানবতা দীপক",
        			'description' => "moontrip স্বপ্ন শুধুমাত্র বেশি 18.300 মানুষ 2016 সালে নাসার নভোচারী বর্গ, যা গতিতে 2012 সালে অ্যাপ্লিকেশন প্রায় ট্রিপল হয় কম 14 দাগ জন্য আবেদন সঙ্গে সময়ের ছড়ানোর হয়েছে, প্রত্যেক পরিবারের একটি হবে যখন একটি সময় হতে পারে মহাকাশচারী। তবে, অনেক একটি স্থান মিশন পিছনে যায় এটা মহাকাশচারী ছাড়া ঘটতে না। বিভিন্ন প্রকৌশলী, প্রযুক্তিবিদ প্রশিক্ষণ পেশাদার, বিশেষজ্ঞ, বিজ্ঞানী ও ডিজাইনার এই মিশনের সম্ভব যখন লাইন পিছনে হচ্ছে। চ্যালেঞ্জ moontrip, বিভিন্ন মানুষ এবং স্থান অন্বেষণ অন্যান্য উন্নয়নের ভূমিকা নিয়ে তরুণ প্রজন্মের আলোকিত করার জন্য একটি স্পেস সেন্টারের ডিজাইন করতে হয়।",
        		],
        		'de' => [
        			'title' => "Mondfahrt - inspirierende Menschheit jenseits der Erde zu erforschen",
        			'description' => "der moontrip Traum hat mit mehr als 18.300 Menschen die Anwendung im Jahr 2016 für weniger als 14 Punkte in der NASA Astronautenklasse im Laufe der Zeit nur eskaliert, die im Jahr 2012 in einem solchen Tempo fast verdreifachen von Anwendungen ist, könnte es eine Zeit sein, wenn jeder Haushalt ein hätte Astronaut. jedoch geht viel hinter einer Weltraummission, um es abgesehen von den Astronauten passieren. mehrere Ingenieure, Techniker, ausgebildete Fachkräfte, Spezialisten, Wissenschaftler und Designer machen diese Missionen möglich, während hinter den Linien zu sein. Herausforderung ist es, ein Raumfahrtzentrum zu entwerfen junge Generation über die moontrip, die Rolle der verschiedenen Menschen und anderer Fortschritte in dem Raum Erkundungen zu erleuchten.",
        		],
        		'en' => [
        			'title' => "Moon Trip - Inspiring Humanity to Explore Beyond Earth",
        			'description' => "The moontrip dream has only escalated over time with more than 18,300 people applying for fewer than 14 spots in NASA's astronaut class in 2016, which is nearly triple of applications in 2012. At such pace, there might be a time when every household would have an astronaut. However, a lot goes behind a space mission to make it happen apart from the astronauts. Several engineers, technicians, trained professionals, specialists, scientists and designers make these missions possible while being behind the lines. Challenge is to design a space center to enlighten young generation about the moontrip, the role of different people and other advancements in space explorations.",
        		],
        		'es' => [
        			'title' => "viaje de luna de - inspirar a la humanidad a explorar más allá de la tierra",
        			'description' => "el sueño moontrip sólo ha aumentado con el tiempo, con más de 18.300 las personas que solicitan menos de 14 puntos en la clase de astronautas de la NASA en 2016, que es casi el triple de las aplicaciones en 2012. En tal ritmo, puede haber un momento en que todos los hogares tendría una astronauta. Sin embargo, una gran cantidad va detrás de una misión espacial para que esto ocurra, aparte de los astronautas. varios ingenieros, técnicos, profesionales capacitados, especialistas, científicos y diseñadores hacen posible estas misiones, mientras que estar detrás de las líneas. reto es diseñar un centro espacial de iluminar a la generación joven de la moontrip, el papel de las diferentes personas y otros avances en las exploraciones espaciales.",
        		],
        		'fr' => [
        			'title' => "voyage de lune - l'humanité inspirer à explorer au-delà de la terre",
        			'description' => "le rêve de moontrip a seulement augmenté au fil du temps, avec plus de 18.300 personnes qui demandent moins de 14 points dans la classe des astronautes de la Nasa en 2016, ce qui est près du triple des applications en 2012. à ce rythme, il pourrait y avoir un moment où chaque ménage aurait un astronaute. Cependant, beaucoup passe derrière une mission spatiale pour y arriver en dehors des astronautes. plusieurs ingénieurs, techniciens, professionnels qualifiés, des spécialistes, des scientifiques et les concepteurs font de ces missions possibles tout en étant derrière les lignes. défi consiste à concevoir un centre spatial pour éclairer la jeune génération sur le moontrip, le rôle des différentes personnes et d'autres progrès dans les explorations spatiales.",
        		],
        		'hi' => [
        			'title' => "चंद्रमा यात्रा - पृथ्वी से परे पता लगाने के लिए मानवता को प्रेरित",
        			'description' => "moontrip सपना केवल एक से अधिक 18,300 लोगों को 2016 में नासा के अंतरिक्ष यात्री वर्ग, जो इस प्रकार गति से 2012 में आवेदनों की लगभग तीन गुना है में कम से कम 14 स्थानों के लिए आवेदन करने के साथ समय के साथ बढ़ता है, हर घर के एक होता है जब वहाँ एक समय हो सकता है अंतरिक्ष यात्री। हालांकि, एक बहुत एक अंतरिक्ष मिशन के पीछे चला जाता है कि यह अंतरिक्ष यात्रियों से अलग ऐसा करने के लिए। कई इंजीनियरों, तकनीशियनों, प्रशिक्षित पेशेवरों, विशेषज्ञों, वैज्ञानिकों और डिजाइनरों इन मिशन को संभव बनाने, जबकि रेखा के पीछे जा रहा है। चुनौती moontrip, विभिन्न लोगों और अंतरिक्ष अन्वेषण में अन्य प्रगति की भूमिका के बारे युवा पीढ़ी को जागरूक करने के लिए एक अंतरिक्ष केंद्र डिजाइन करने के लिए है।",
        		],
        		'id' => [
        			'title' => "Perjalanan bulan - inspirasi manusia untuk menjelajahi luar bumi",
        			'description' => "mimpi moontrip hanya telah meningkat dari waktu ke waktu dengan lebih dari 18.300 orang melamar kurang dari 14 tempat di kelas astronot nasa di 2016, yang hampir tiga kali lipat dari aplikasi pada tahun 2012. pada kecepatan seperti itu, mungkin ada saat ketika setiap rumah tangga akan memiliki astronaut. Namun, banyak terjadi di balik misi ruang untuk mewujudkannya terpisah dari astronot. beberapa insinyur, teknisi, profesional terlatih, spesialis, ilmuwan dan desainer membuat misi ini mungkin sementara berada di belakang garis. tantangan adalah untuk merancang sebuah pusat ruang untuk mencerahkan generasi muda tentang moontrip, peran orang yang berbeda dan kemajuan lainnya dalam eksplorasi ruang angkasa.",
        		],
        		'it' => [
        			'title' => "viaggio di luna - ispirando umanità da esplorare oltre la Terra",
        			'description' => "il sogno moontrip è aumentata solo nel tempo con più di 18.300 persone che chiedono meno di 14 punti in classe di astronauti della NASA nel 2016, che è quasi il triplo di applicazioni nel 2012. a tale ritmo, ci potrebbe essere un momento in cui ogni famiglia avrebbe un astronauta. Tuttavia, un sacco va dietro una missione spaziale per farlo accadere a parte gli astronauti. diversi ingegneri, tecnici, professionisti esperti, specialisti, scienziati e progettisti rendono possibile queste missioni, pur essendo dietro le linee. sfida è quella di progettare un centro spaziale per illuminare le giovani generazioni sulla moontrip, il ruolo delle diverse persone e di altri progressi in esplorazioni spaziali.",
        		],
        		'ja' => [
        			'title' => "月の旅行 - 地球を超えて探求する人類を鼓舞",
        			'description' => "moontripの夢は唯一、このようなペースで、2012年にアプリケーションのほぼ3倍である2016年、NASAの宇宙飛行士クラス、中に14個の未満のスポットを申請する以上18,300人で時間をかけてエスカレートしている、すべての家庭を持っているだろう時間があるかもしれません宇宙飛行士。しかし、多くは、それが宇宙飛行士から離れて実現するために宇宙ミッションの後ろになります。ラインの後ろにありながら、いくつかのエンジニア、技術者、訓練を受けた専門家、専門家、科学者やデザイナーは、これらのミッションを可能にします。挑戦はmoontrip、異なる人々と宇宙開発における他の進歩の役割について、若い世代を啓発するために宇宙センターを設計することです。",
        		],
        		'km' => [
        			'title' => "ធ្វើដំណើរព្រះច័ន្ទ - ការបំផុសទឹកចិត្តមនុស្សជាតិដើម្បីស្វែងរកពីផែនដី",
        			'description' => "ក្តីសុបិន្ត moontrip នេះបានកើនឡើងតែនៅលើពេលវេលាជាមួយនឹងការច្រើនជាង 18.300 នាក់ដែលបានដាក់ពាក្យសុំមានចំនួនតិចជាង 14 ចំណុចនៅក្នុងថ្នាក់អវកាសរបស់ NASA នៅឆ្នាំ 2016 ដែលជាជិតបីដងនៃកម្មវិធីក្នុងឆ្នាំ 2012 ក្នុងល្បឿនដូចនោះវាអាចនឹងមានពេលមួយដែលគ្រប់ផ្ទះនឹងមាន អវកាស។ ទោះជាយ៉ាងណា, ជាច្រើនបានទៅនៅពីក្រោយបេសកកម្មអវកាសមួយដើម្បីធ្វើឱ្យវាកើតឡើងក្រៅពីអវកាសនេះ។ ជាច្រើនវិស្វករបច្ចេកទេស, អ្នកជំនាញបណ្តុះបណ្តា, ជំនាញ, វិទ្យាសាស្រ្តនិងបេសកកម្មទាំងនេះរចនាធ្វើឱ្យអាចធ្វើទៅបានខណៈពេលដែលត្រូវនៅពីក្រោយបន្ទាត់។ បញ្ហាប្រឈមគឺដើម្បីរៀបចំមជ្ឈមណ្ឌលអវកាសមួយដើម្បីបំភ្លឺដល់យុវជនជំនាន់ក្រោយអំពី moontrip តួនាទីរបស់មនុស្សផ្សេងគ្នានិងការរីកចំរើនក្នុងការរុករកមានទំហំផ្សេងទៀតនេះ។",
        		],
        		'ko' => [
        			'title' => "달 여행 - 지구를 넘어 탐험 인류를 감동",
        			'description' => "moontrip 꿈은 모든 가정이있을 것입니다 때 시간이있을 수 있습니다 이상 1만8천3백명 같은 속도로 2012 년 응용 프로그램의 거의 세 배 2016 항공 우주국 (NASA)의 우주 비행사 클래스에 미만 14 점 신청으로 시간이 지남에 에스컬레이션있다 우주 비행사. 그러나, 많은은 우주 비행사에서 떨어져 일어날 수 있도록하기 위해 우주 임무 뒤에 간다. 라인 뒤에하면서 여러 엔지니어, 기술자, 숙련 된 전문가, 전문가, 과학자와 디자이너는 이러한 임무가 가능합니다. 문제는 moontrip, 다른 사람과 공간 탐험에서 다른 발전의 역할에 대해 젊은 세대를 계몽하기 위해 우주 센터를 설계하는 것입니다.",
        		],
        		'lo' => [
        			'title' => "ການເດີນທາງເດືອນ - ແຮງບັນດານໃຈຂອງມະນຸດທີ່ຈະສໍາຫລວດນອກເຫນືອແຜ່ນດິນໂລກ",
        			'description' => "ຝັນ moontrip ໄດ້ເພີ່ມຂຶ້ນພຽງແຕ່ໃນໄລຍະທີ່ໃຊ້ເວລາມີຫຼາຍກ່ວາ 18,300 ປະຊາຊົນຍື່ນຄໍາຮ້ອງຂໍສໍາລັບການຫນ້ອຍກ່ວາ 14 ຈຸດໃນລະດັບນັກອາວະກາດ nasa ຂອງໃນປີ 2016, ຊຶ່ງເປັນເກືອບ triple ຂອງຄໍາຮ້ອງສະຫມັກໃນປີ 2012 ທີ່ສາມາດຄວບຄຸມດັ່ງກ່າວ, ອາດຈະມີເວລາໃນເວລາທີ່ທຸກຄອບຄົວຈະມີ ນັກອາວະກາດ. ຢ່າງໃດກໍຕາມ, ຫຼາຍໄປຫລັງພາລະກິດສະຖານທີ່ຈະເຮັດໃຫ້ມັນເກີດຂຶ້ນນອກຈາກນັກອາວະກາດ. ຫຼາຍວິສະວະກອນ, ນັກວິຊາການ, ຜູ້ຊ່ຽວຊານດ້ານການຝຶກອົບຮົມ, ຜູ້ຊ່ຽວຊານ, ວິທະຍາສາດແລະນັກອອກແບບເຮັດໃຫ້ຄະນະເຫຼົ່ານີ້ທີ່ເປັນໄປໄດ້ໃນຂະນະທີ່ເປັນທາງຫລັງຂອງສາຍການ. ສິ່ງທີ່ທ້າທາຍແມ່ນການອອກແບບສູນອະວະກາດເພື່ອໃຫ້ຄວາມຮູ້ແກ່ຊາວຫນຸ່ມລຸ່ນໃຫມ່ກ່ຽວກັບ moontrip ໄດ້, ພາລະບົດບາດຂອງປະຊາຊົນທີ່ແຕກຕ່າງກັນແລະຄວາມກ້າວຫນ້າອື່ນໆໃນການສໍາຫຼວດພື້ນທີ່ໄດ້.",
        		],
        		'ms' => [
        			'title' => "percutian bulan - inspirasi umat manusia untuk menjelajah di bumi",
        			'description' => "impian moontrip telah hanya meningkat dari masa ke masa dengan lebih daripada 18,300 orang yang memohon kurang daripada 14 tempat di dalam kelas angkasawan NASA pada 2016, iaitu hampir tiga kali ganda daripada permohonan pada tahun 2012. pada kadar itu, tidak mungkin masa yang apabila setiap rumah akan mempunyai angkasawan. Walau bagaimanapun, banyak yang pergi di belakang misi ruang untuk menjadikannya satu kenyataan selain daripada angkasawan. beberapa jurutera, juruteknik, profesional terlatih, pakar, ahli-ahli sains dan pereka membuat misi ini mungkin semasa berada di belakang garisan. cabaran adalah untuk mereka bentuk pusat angkasa untuk menyedarkan generasi muda tentang moontrip, peranan orang yang berlainan dan kemajuan lain dalam penerokaan angkasa.",
        		],
        		'my' => [
        			'title' => "လခရီးစဉ် - မြေကြီးတပြင်ထက် ကျော်လွန်. စူးစမ်းဖို့လူသားမျိုးနွယ်အပေါ်ဖှယျ",
        			'description' => "အဆိုပါ moontrip အိပ်မက်သာထိုကဲ့သို့သောအရှိန်အဟုန်မှာ 2012 ခုနှစ် applications များနီးပါးသုံးဆဖြစ်သည့် 2016 ခုနှစ်တွင် NASA ရဲ့အာကာသယာဉ်မှူးလူတန်းစားအတွက်နည်းပါးလာထက် 14 အစက်အပြောက်လျှောက်ထားထက်ပို 18,300 လူတို့နှင့်အတူအချိန်ကြာလာတာနဲ့အမျှတိုက်ပွဲတွေအရှိန်မြင့်လာခဲ့သည်ဟုတိုင်းအိမ်သူအိမ်သားတစ်ဦးရှိသည်မယ်လို့တဲ့အခါအချိန်ရှိစေခြင်းငှါ, အာကာသယာဉ်မှူး။ သို့သော်အများကြီးကအာကာသယာဉ်မှူးထံမှဆိတ်ကွယ်ရာဖြစ်ပျက်စေတဲ့အာကာသမစ်ရှင်နောက်ကွယ်မှတတ်၏။ အဆိုပါလိုင်းများနောက်ကွယ်မှဖြစ်ခြင်းစဉ်အင်ဂျင်နီယာများ, ပညာရှင်များ, လေ့ကျင့်သင်ကြားကျွမ်းကျင်ပညာရှင်များ, အထူးကု, သိပ္ပံပညာရှင်များနှင့်ဒီဇိုင်နာများအများအပြားကဤမစ်ရှင်ဖြစ်နိုင်သောပါစေ။ စိန်ခေါ်မှု, အ moontrip အကြောင်းကိုအာကာသစူးစမ်းအတွက်ကွဲပြားခြားနားသောလူဦးနှင့်အခြားတိုးတက်မှု၏အခန်းကဏ္ဍကိုငယ်ရွယ်မျိုးဆက်များ၏တစ်ဦးအာကာသစင်တာဒီဇိုင်းရန်ဖြစ်ပါသည်။",
        		],
        		'ne' => [
        			'title' => "चन्द्र यात्रा - पृथ्वी परे अन्वेषण गर्न मानवता प्रेरक",
        			'description' => "को moontrip सपना मात्र हरेक घरेलू एक हुनेछ गर्दा त्यहाँ एक समय हुन सक्छ, 18.300 भन्दा बढी मानिसहरू 2012. मा आवेदन लगभग तीन जो यस्तो गति मा 2016 मा नासा गरेको अन्तरिक्ष यात्री वर्ग, मा 14 भन्दा कम स्थलहरू लागि आवेदन संग समय escalated छ अन्तरिक्ष यात्री। तथापि, धेरै यो अन्तरिक्ष यात्री अलग्गै हुन बनाउन एक ठाउँ मिशन पछि जान्छ। लाइनहरु पछि हुनुको गर्दा धेरै ईन्जिनियरहरु, टेक्नीसियन, प्रशिक्षित पेशेवरों, विशेषज्ञहरु, वैज्ञानिकहरू र डिजाइनर यी मिशन सम्भव बनाउन। चुनौती को moontrip, विभिन्न व्यक्ति र ठाउँ अन्वेषणहरू अन्य प्रगति को भूमिका बारे जवान पुस्ता ज्योति एक ठाउँ केन्द्र डिजाइन छ।",
        		],
        		'ro' => [
        			'title' => "excursie luna - inspira omenirea pentru a explora dincolo de pământ",
        			'description' => "visul moontrip a escaladat în timp, cu mai mult de 18.300 de persoane care solicită mai puțin de 14 puncte din clasa astronaut NASA în 2016, care este aproape triplu de aplicații în 2012. la astfel de ritm, ar putea exista un moment în care fiecare gospodărie ar avea un astronaut. Cu toate acestea, o mulțime merge în spatele o misiune de spațiu pentru a face acest lucru în afară de astronauți. mai mulți ingineri, tehnicieni, specialiști instruiți, specialiști, oameni de știință și designeri face aceste misiuni posibile fiind în același timp în spatele liniilor. provocarea este de a proiecta un centru de spațiu pentru a ilumina tinerei generatii despre moontrip, rolul diferitelor persoane și alte progrese în explorări spațiale.",
        		],
        		'ru' => [
        			'title' => "луна поездка - вдохновляющее человечество, чтобы исследовать пределы земли",
        			'description' => "мечта moontrip только обострилась в течение долгого времени с более чем 18,300 людей применения для менее чем 14 мест в астронавта классе НАСА в 2016 году, что почти в три раза приложений в 2012 году в таком темпе, что может быть время, когда каждая семья будет иметь космонавт. Однако, много идет позади космического полета, чтобы это произошло, кроме космонавтов. несколько инженеров, техники, квалифицированные специалисты, специалисты, ученые и конструкторы делают эти миссии, находясь в тыле. Задача состоит в том, чтобы создать космический центр, чтобы просветить молодое поколение о moontrip, роли различных людей и других достижений в области космических исследований.",
        		],
        		'si' => [
        			'title' => "සඳ ගමන - පොළොව ඔබ්බට ගවේෂණය කිරීමට මනුෂ්යත්වයට උපදවන",
        			'description' => "මෙම moontrip සිහිනය එවැනි වේගයකින් 2012 දී අයදුම්පත් ආසන්න ත්රිත්ව වන 2016 දී නාසා ආයතනය විසින් ගගනගාමී පන්තිය, ලප 14 ට වඩා අඩු සඳහා අයදුම් 18.300 කට වැඩි පිරිසක් සමග කාලය පුරා තීව්ර වී තිබේ පමණක් සෑම නිවසකටම සතුව ලැබෙන කාලයක් තිබිය හැකි අඡටාකාශගාමියන් විය. කෙසේ වෙතත්, ගොඩක් එය ගඟනගාමීන් අමතරව සිදු කිරීමට අවකාශයක් මෙහෙයුම පිටුපස යයි. රේඛා පිටුපස අතර ඉංජිනේරුවන්, තාක්ෂණ නිලධාරීන්, පුහුණු වෘත්තිකයන්, විශේෂඥයින්, විද්යාඥයන් හා නිර්මාණකරුවන් කිහිප මෙම මෙහෙයුම්වල කල හැක. අභියෝගය අවකාශය ගවේෂණ විවිධ මිනිසුන් හා අනෙකුත් ප්රගමනයක් ඇති භූමිකාව moontrip ගැන තරුණ පරම්පරාව දැනුවත් කිරීම සඳහා අවකාශය මධ්යස්ථානය නිර්මාණය කිරීමයි.",
        		],
        		'ta' => [
        			'title' => "சந்திரன் பயணம் - பூமியில் அப்பால் ஆராய மனித ஈர்க்கப்பட்டு",
        			'description' => "moontrip கனவு மட்டுமே காலப்போக்கில் ஒவ்வொரு வீட்டு ஒரு வேண்டும் போது ஒரு முறை அங்கு இருக்கலாம், அத்தகைய வேகத்தில் 2012 இல் பயன்பாடுகள் கிட்டத்தட்ட மூன்று இது 2016 இல் நாசாவின் விண்வெளி வகுப்புகளில் 14 க்கும் குறைவான புள்ளிகள் க்கான 18.300 க்கும் மேற்பட்ட மக்கள் விண்ணப்பிக்கும் அதிகரித்தது வருகிறது விண்வெளி. எனினும், நிறைய அது விண்வெளி தவிர நடக்கும் செய்ய ஒரு விண்வெளி ஆய்வுப் பின்னால் செல்கிறது. வரிகளை பின்னால் இருப்பது அதேசமயம் சில பொறியாளர்கள், தொழில் நுட்ப வல்லுனர்கள், பயிற்சி பெற்ற நிபுணர்கள், சிறப்பு, விஞ்ஞானிகள் மற்றும் வடிவமைப்பாளர்கள் இந்த பயணங்கள் சாத்தியமாக்கும். சவால் moontrip, பல்வேறு மக்கள் மற்றும் விண்வெளி கண்டுபிடிப்புகள் மற்ற மேம்பாடுகள் பங்கு பற்றி இளம் தலைமுறை தெளிவுபடுத்துங்கள் ஒரு விண்வெளி மையத்தில் வடிவமைக்க வேண்டும்.",
        		],
        		'th' => [
        			'title' => "การเดินทางของดวงจันทร์ - แรงบันดาลใจในการสำรวจมนุษยชาติเกินแผ่นดิน",
        			'description' => "ฝัน moontrip ได้เพิ่มขึ้นเพียงช่วงเวลาที่มีมากกว่า 18,300 คนที่ใช้สำหรับน้อยกว่า 14 จุดในชั้นเรียนนักบินอวกาศของนาซ่าในปี 2016 ซึ่งเป็นเกือบสามของการใช้งานในปี 2012 ที่ก้าวดังกล่าวอาจจะมีช่วงเวลาที่ทุกครัวเรือนจะมี มนุษย์อวกาศ แต่มากไปอยู่เบื้องหลังภารกิจพื้นที่ที่จะทำให้มันเกิดขึ้นนอกเหนือจากอวกาศ หลายวิศวกรช่างเทคนิคผู้เชี่ยวชาญด้านการฝึกอบรมผู้เชี่ยวชาญนักวิทยาศาสตร์และนักออกแบบทำให้ภารกิจเหล่านี้เป็นไปได้ในขณะที่เป็นเส้นหลัง ความท้าทายคือการออกแบบศูนย์พื้นที่ที่จะสอนคนรุ่นใหม่เกี่ยวกับ moontrip บทบาทของคนที่แตกต่างกันและความก้าวหน้าอื่น ๆ ในการสำรวจพื้นที่",
        		],
        		'tl' => [
        			'title' => "moon trip - kagila sangkatauhan upang galugarin lampas sa earth",
        			'description' => "ang moontrip panaginip ay tanging tumataas sa paglipas ng panahon na may higit sa 18,300 mga tao-apply para sa mas kaunti sa 14 spot sa astronaut klase ni nasa sa 2016, na kung saan ay halos triple ng mga aplikasyon sa 2012. sa naturang makasabay, maaaring may isang panahon kapag ang bawat sambahayan ay may isang astronaut. gayunpaman, isang pulutong na napupunta sa likod ng isang space misyon upang gawin itong mangyari bukod sa mga astronaut. ang ilang mga inhinyero, technicians, sinanay na mga propesyonal, mga espesyalista, mga siyentipiko at designer gumawa ng mga misyon maaari habang pagiging sa likod ng mga linya. hamon ay upang mag-disenyo ng isang puwang center upang maliwanagan batang henerasyon tungkol sa moontrip, ang papel na ginagampanan ng iba't ibang mga tao at iba pang mga advancements sa pag-explore space.",
        		],
        		'vi' => [
        			'title' => "chuyến du lịch trăng - truyền cảm hứng cho nhân loại để khám phá ngoài trái đất",
        			'description' => "giấc mơ moontrip đã chỉ leo thang theo thời gian với hơn 18.300 người nộp đơn xin ít hơn 14 điểm trong lớp học du hành vũ trụ của NASA vào năm 2016, đó là gần gấp ba lần các ứng dụng vào năm 2012. theo tốc độ như vậy, có thể có một thời gian khi mỗi hộ gia đình sẽ có một phi hành gia. Tuy nhiên, rất nhiều đi đằng sau một sứ mệnh không gian để làm cho nó xảy ra ngoài các phi hành gia. một số kỹ sư, kỹ thuật viên, chuyên gia đào tạo, các chuyên gia, nhà khoa học và các nhà thiết kế thực hiện những nhiệm vụ có thể trong khi đứng đằng sau các dòng. Thách thức là để thiết kế một trung tâm không gian để soi sáng cho thế hệ trẻ về moontrip, vai trò của những người khác nhau và tiến bộ khác trong cuộc thám hiểm không gian.",
        		],
        		'zh' => [
        			'title' => "月球之旅 - 鼓舞人類探索地球以外",
        			'description' => "該moontrip夢想才會上報隨著時間的推移有超過18300人，在2016年美國航空航天局的宇航員類，這是近三倍的申請於2012年在這樣的速度申請少於14點，有可能是一個時間，當每家每戶將有宇航員。然而，大量銷往後面的太空任務，使其從宇航員除了發生。幾位工程師，技術人員，訓練有素的專業人員，專家，科學家和設計師使這些任務可能，同時背後的紋路。挑戰在於設計一個航天中心開導有關moontrip，不同的人，在太空探索其他進步的作用年輕一代。",
        		],
        );

       	$r = Opportunity::create($opportunities);
       	$r->tags()->sync([2,11,12,13,14,15,16,17,18,19,20]);
        $r->eligible_regions()->sync([243]);

        /*--------------------------------------------------------------------------------*/

        $opportunities = array(
                'id' => 2792,
                'deadline' => '2022-02-12',
                'image' => 'https://d8it4huxumps7.cloudfront.net/uploads/images/150x150/5c626936d0e46_Brentwood-Open-Learning-College.png',
                'link' => 'https://dare2compete.com/o/bolc-distance-learning-scholarships-brentwood-open-learning-college-80847',
                'fund_type_id' => 3,
                'slug' => 'bolc-distance-learning-scholarships-brentwood-open-learning-college-009ehf93ubcdyu9f',
                'opportunity_location_id' => 243,
                'bn' => [
                    'title' => "bolc দূরশিক্ষণ বৃত্তি",
                    'description' => "সারা পৃথিবী শিক্ষার্থীদের জন্য আরো সাশ্রয়ী মূল্যের শেখার তুলতে পরিকল্পিত - Brentwood খোলা লার্নিং কলেজ নতুন bolc দূরশিক্ষণ বৃত্তি প্রকল্প ঘোষণা করে হয়। এই একচেটিয়া বৃত্তি চাহিদা এবং আবেদনকারীর যোগ্যতা অনুযায়ী, £ থেকে 100 এবং £ 460 (অবশ্যই ফি পর্যন্ত 80%) পরিসীমা।",
                ],
                'de' => [
                    'title' => "BOLC Fernunterricht Stipendien",
                    'description' => "brent offenes Lernen College freut sich, die neue BOLC Fernstudium Stipendienprogramms bekannt zu geben - entworfen, um mehr Geld für Studenten auf der ganzen Welt zu machen das Lernen. Diese exklusiven Stipendien reichen von £ 100 und £ 460 (bis zu 80% der Kursgebühr), in Übereinstimmung mit den Bedürfnissen und Förderfähigkeit des Antragstellers.",
                ],
                'en' => [
                    'title' => "BOLC Distance Learning Scholarships",
                    'description' => "Brentwood Open Learning College is pleased to announce the new BOLC Distance Learning Scholarship scheme – designed to make learning more affordable for students all over the world. These exclusive scholarships range from £100 and £460 (Up to 80% of the course fee), in accordance with the needs and eligibility of the applicant.",
                ],
                'es' => [
                    'title' => "beca internacional en centros forestal mundial",
                    'description' => "museo descubrimiento del Congreso, que se abrió en 1971 para educar al público en general acerca de los bosques locales y globales y la silvicultura sostenible. granja de árboles monumento Magness, nuestro bosque demostración Premier encuentra cerca de Sherwood, Oregon, ofrece un enfoque práctico al aire libre para el aprendizaje del medio ambiente.",
                ],
                'fr' => [
                    'title' => "bourses d'apprentissage à distance BOLC",
                    'description' => "brentwood collège d'apprentissage ouvert est heureux d'annoncer le nouveau programme de bourses d'apprentissage à distance BOLC - conçu pour rendre l'apprentissage plus abordable pour les étudiants du monde entier. ces bourses exclusives vont de 100 £ et 460 £ (jusqu'à 80% des frais de cours), conformément aux besoins et à l'admissibilité du demandeur.",
                ],
                'hi' => [
                    'title' => "bolc दूरस्थ शिक्षा छात्रवृत्ति",
                    'description' => "दुनिया भर में छात्रों के लिए और अधिक किफायती सीखने बनाने के लिए डिजाइन - Brentwood खुला सीखने कॉलेज नई bolc दूरस्थ शिक्षा छात्रवृत्ति योजना घोषणा करते हुए खुशी है। इन विशेष छात्रवृत्ति की जरूरत है और आवेदक की पात्रता के अनुसार, £ 100 और £ 460 (पाठ्यक्रम शुल्क के 80% तक) सीमा होती है।",
                ],
                'id' => [
                    'title' => "beasiswa pembelajaran jarak jauh bolc",
                    'description' => "brentwood pembelajaran terbuka perguruan tinggi dengan bangga mengumumkan jarak bolc skema pembelajaran beasiswa baru - yang dirancang untuk membuat belajar lebih terjangkau bagi siswa di seluruh dunia. ini beasiswa eksklusif berkisar dari £ 100 dan £ 460 (hingga 80% dari biaya kursus), sesuai dengan kebutuhan dan kelayakan dari pemohon.",
                ],
                'it' => [
                    'title' => "BOLC borse di studio di formazione a distanza",
                    'description' => "Brentwood apprendimento aperto college è lieta di annunciare la nuova distanza BOLC schema di apprendimento di borse di studio - ha progettato per rendere l'apprendimento più accessibile per gli studenti di tutto il mondo. queste borse di studio esclusive vanno da £ 100 e £ 460 (fino al 80% del costo del corso), in conformità con le esigenze e l'ammissibilità del richiedente.",
                ],
                'ja' => [
                    'title' => "bolc遠隔学習奨学金",
                    'description' => "世界中の学生のための学習がより手頃な価格にするために設計された - ブレントウッドオープン学習カレッジは、新しいbolc距離学習奨学金制度を発表しています。これらの排他的な奨学金は、申請者のニーズ及び資格に応じて、£100と£460（コース料金の80％まで）の範囲です。",
                ],
                'km' => [
                    'title' => "អាហារូបករណ៍សិក្សាពីចម្ងាយ bolc",
                    'description' => "Brentwood មហាវិទ្យាល័យបើកចំហរៀនគឺសេចក្តីរីករាយសូមប្រកាសគម្រោងចម្ងាយ bolc ថ្មីអាហារូបករណ៍រៀន - បានរចនាឡើងដើម្បីធ្វើឱ្យការរៀនបន្ថែមទៀតដែលមានតំលៃសមរម្យសម្រាប់សិស្សនិស្សិតទាំងអស់នៅលើពិភពលោក។ អាហារូបករណ៍ផ្តាច់មុខទាំងនេះមានចាប់ពី£ 100 និង£ 460 (រហូតដល់ទៅ 80% នៃថ្លៃពិតណាស់) ដែលស្របតាមតម្រូវការនិងសិទ្ធិទទួលបានរបស់បេក្ខជន។",
                ],
                'ko' => [
                    'title' => "BOLC 원격 교육 장학금",
                    'description' => "전 세계 학생들에게보다 저렴한 학습하도록 설계 - 브렌트 우드 열린 학습 대학은 새로운 BOLC 거리 학습 장학금 제도를 발표하게 된 것을 기쁘게 생각합니다. 이러한 독점 장학금 신청자의 요구와 자격에 따라,에서 £ 100 £ 460 (수강료의 최대 80 %)까지 다양합니다.",
                ],
                'lo' => [
                    'title' => "bolc ທຶນການສຶກສາຮຽນຮູ້ໄລຍະທາງ",
                    'description' => "Brentwood ວິທະຍາໄລການຮຽນຮູ້ເປີດມີຄວາມຍິນດີທີ່ຈະປະກາດ bolc ໄລຍະທຶນການສຶກສາຮຽນຮູ້ການໃຫມ່ - ອອກແບບມາເພື່ອເຮັດໃຫ້ການຮຽນສາມາດໃຫ້ໄດ້ສໍາລັບນັກສຶກສາທັງຫມົດໃນທົ່ວໂລກ. ທຶນການສຶກສາພິເສດເຫຼົ່ານີ້ໄດ້ສະຈາກ£ 100 ແລະ£ 460 (ສູງເຖິງ 80% ຂອງຄ່າທໍານຽມວິຊາການ), ໃນສອດຄ່ອງກັບຄວາມຕ້ອງການແລະສິດຂອງຜູ້ສະຫມັກ.",
                ],
                'ms' => [
                    'title' => "bolc biasiswa pembelajaran jarak jauh",
                    'description' => "brentwood kolej pembelajaran terbuka dengan sukacitanya mengumumkan skim pembelajaran biasiswa jarak bolc baru - yang direka untuk menjadikan pembelajaran lebih murah untuk pelajar di seluruh dunia. ini biasiswa eksklusif berkisar dari £ 100 dan £ 460 (sehingga 80% daripada yuran kursus), selaras dengan keperluan dan kelayakan pemohon.",
                ],
                'my' => [
                    'title' => "bolc အကွာအဝေးသင်ယူမှုပညာသင်ဆု",
                    'description' => "လောကီနိုင်ငံအရပ်ရပ်ရှိသမျှကျော်ကျောင်းသားများအတွက်သင်ယူပိုပြီးတတ်နိုင်စေရန်ဒီဇိုင်း - brentwood ပွင့်လင်းသင်ယူမှုကောလိပ်သစ်ကို bolc အကွာအဝေးသင်ယူမှုပညာသင်ဆုအစီအစဉ်ကိုကြေညာဖို့နှစ်သက်သည်။ ထိုအသီးသန့်ပညာသင်ဆုလျှောက်ထား၏လိုအပ်ချက်များကိုဖြည့်နှင့်ရထိုက်ခွင့်များနှင့်အညီ, £ 100 နဲ့£ 460 (သင်တန်းကြေး၏အထိ 80%) ကနေအထိ။",
                ],
                'ne' => [
                    'title' => "bolc दूरी सिक्ने छात्रवृत्ति",
                    'description' => "सबै दुनिया भर विद्यार्थीहरूको लागि थप किफायती सिक्ने बनाउन डिजाइन - Brentwood खुला सिक्ने कलेज नयाँ bolc दूरी सिक्ने छात्रवृत्ति योजना घोषणा गर्न खुसी हुनुहुन्छ। यी विशेष छात्रवृत्ति अनुसार आवश्यकता र आवेदक को योग्यता संग देखि £ 100 र £ 460 (पाठ्यक्रम शुल्क को माथि 80%) को दायरामा।",
                ],
                'ro' => [
                    'title' => "bolc burse de învățare la distanță",
                    'description' => "colegiu de învățare deschis Brentwood are plăcerea să anunțe noul program de burse de învățare la distanță bolc - conceput pentru a face procesul de învățare mai accesibile pentru studenți din întreaga lume. aceste burse exclusive variază de la £ 100 si £ 460 (până la 80% din taxa de curs), în conformitate cu nevoile și eligibilitatea solicitantului.",
                ],
                'ru' => [
                    'title' => "bolc стипендии дистанционного обучения",
                    'description' => "Brentwood колледж открытого обучения рад объявить о выпуске новой bolc дистанционного обучения стипендиальной схемы - разработано, чтобы сделать обучение более доступным для студентов во всем мире. эти эксклюзивные стипендии варьируются от £ 100 и £ 460 (до 80% от стоимости курса), в соответствии с потребностями и правами заявителя.",
                ],
                'si' => [
                    'title' => "bolc දුරස්ථ අධ්යාපන ශිෂ්යත්ව",
                    'description' => "ලොව පුරා සිසුන් සඳහා ඉගෙන වඩා දැරිය හැකි පත් කිරීම සඳහා සැලැසුම් කරන - brentwood විවෘත ඉගෙනුම් විද්යාලයේ නව bolc දුරස්ථ අධ්යාපන ශිෂ්යත්ව ක්රමයක් නිවේදනය පිළිබඳව අපි සතුටු වෙනවා. මෙම සුවිශේෂී ශිෂ්යත්ව ඉල්ලුම්කරුෙග් අවශ්යතා හා සුදුසුකම් අනුව, £ 100 හා £ 460 (පාඨමාලා ගාස්තු සියයට 80% ක් පමණ) සිට ක්රියාත්මක වේ.",
                ],
                'ta' => [
                    'title' => "bolc தொலைதூர கல்வி உதவித் தொகை",
                    'description' => "உலகம் முழுவதும் மாணவர்கள் வாங்கக்கூடிய விலையில் கற்றல் செய்ய வடிவமைக்கப்பட்டுள்ளது - Brentwood திறந்த கற்றல் கல்லூரி புதிய bolc தொலைதூர கல்வி உதவித்தொகை திட்டம் அறிவிக்க மகிழ்ச்சி. இந்த பிரத்தியேக உதவித்தொகைகளையோ, இருந்து £ 100 மற்றும் £ 460 (நிச்சயமாக கட்டணம் வரை 80%) வரை விண்ணப்பதாரரின் தேவைகள் மற்றும் தகுதி ஏற்ப.",
                ],
                'th' => [
                    'title' => "bolc ทุนการศึกษาการเรียนทางไกล",
                    'description' => "เบรนท์วิทยาลัยการเรียนรู้ที่เปิดกว้างมีความยินดีที่จะประกาศ bolc ระยะทุนการศึกษาการเรียนรู้โครงการใหม่ - ออกแบบมาเพื่อทำให้การเรียนรู้ที่เหมาะสมมากขึ้นสำหรับนักเรียนทุกคนทั่วโลก ทุนการศึกษาพิเศษเหล่านี้มีตั้งแต่£ 100 และ£ 460 (ไม่เกิน 80% ของค่าเรียน) ให้สอดคล้องกับความต้องการและคุณสมบัติของผู้สมัคร",
                ],
                'tl' => [
                    'title' => "bolc distance pag-aaral ng scholarship",
                    'description' => "brentwood bukas na pag-aaral sa kolehiyo ay i-anunsyo ang bagong bolc distance pag-aaral ng scholarship scheme - na dinisenyo upang gumawa ng pag-aaral ng higit pang mga abot-kayang para sa mga estudyante sa buong mundo. mga eksklusibong mga scholarship saklaw mula £ 100 at £ 460 (hanggang sa 80% ng kurso fee), alinsunod sa mga pangangailangan at pagiging karapat-dapat ng aplikante.",
                ],
                'vi' => [
                    'title' => "bolc học bổng đào tạo từ xa",
                    'description' => "brentwood học tập mở đại học là vui mừng thông báo khoảng cách bolc học bổng học tập chương trình mới - được thiết kế để làm cho việc học chi phí hợp lý dành cho sinh viên trên toàn thế giới. những học bổng độc quyền dao động từ £ 100 đến £ 460 (lên đến 80% học phí), phù hợp với nhu cầu và đủ điều kiện của người nộp đơn.",
                ],
                'zh' => [
                    'title' => "BOLC遠程教育獎學金",
                    'description' => "布倫特伍德開放學習學院很高興地宣布新BOLC遠程教育獎學金計劃 - 旨在使學習的學生遍布世界各地更多的實惠。這些獨特的獎學金範圍從£100和£460（的過程費用高達80％），按照與申請人的需要和資格。",
                ],
        );

        $r = Opportunity::create($opportunities);
        $r->tags()->sync([3,11,12,13,14,15,16,17,18,19,20]);
        $r->eligible_regions()->sync([243]);

        /*--------------------------------------------------------------------------------*/

        $opportunities = array(
                'id' => 2940,
                'deadline' => '2020-05-31',
                'image' => 'https://opportunitydesk.org/wp-content/uploads/2019/08/Engage-Art-Contest-in-North-America-2019-2020.jpg',
                'link' => 'https://engageart.submittable.com/submit/',
                'fund_type_id' => 3,
                'slug' => 'engage-art-contest-in-north-america-023089fwbjhciu91',
                'opportunity_location_id' => 243,
                'bn' => [
                    'title' => "এন্ট্রি জন্য কল: উত্তর আমেরিকা 2019/2020 সালে শিল্প প্রতিযোগিতা নিয়োজিত ($ 100,000 মোট নগদ পুরস্কার)",
                    'description' => "এন্ট্রি উত্তর আমেরিকা 2019/2020 নিয়োজিত শিল্প প্রতিযোগিতার জন্য আমন্ত্রণ জানানো হয়েছে। ব্যস্ত শিল্প প্রতিযোগিতার মূল চাক্ষুষ শিল্প, সঙ্গীত ভিডিও, চলচ্চিত্র ও পারফর্মিং আর্টস একটি juried প্রতিযোগিতা। 10-20 ও সংশ্লিষ্ট আয়াত: প্রতিযোগিতার থিম আধ্যাত্মিক ইফিষীয় 6 বর্ণিত যুদ্ধ হয়। আপনি ইফিষীয় উত্তরণ বা অন্য কোন কিতাব কোন অংশ জ্বালান আপনার আর্টওয়ার্ক, যতদিন আপনি এটা কোনো না কোনোভাবে যে উত্তরণ ফিরে কহা করতে পারেন হতে পারে। সেখানে যুদ্ধ চিত্রাবলী ব্যবহার করার কোনও প্রয়োজন নেই। ব্যস্ত শিল্প প্রতিযোগিতার যে উভয় লেজার শ্রেনীর এবং অনন্ত শাস্ত্রীয় থিম নিয়ে শৈল্পিক সৃজনশীলতার গভীর শিরা খনি করতে চায়। আধ্যাত্মিক যুদ্ধ উপস্থিত মুহূর্ত পর্যন্ত সামনে রেকর্ড ইতিহাস থেকে raged হয়েছে। মানুষের ভাল এবং মন্দ মধ্যে এই সংঘর্ষ একটি গুরুত্বপূর্ণ এবং কখনও কখনও অনিচ্ছাকৃত অংশ খেলা এবং খেলার। আল্লাহর বাণী আমাদের উভয় অপরাধ এবং আমাদের প্রতিরক্ষা সমালোচনামূলক তথ্য, একটি প্লেবুক, অনুপ্রেরণা, দিক, আশ্রয়স্থল, এবং দুর্গ প্রদানের এই প্রয়াসে আমাদের সাহায্য বোঝানো হয়। সব ব্যাকগ্রাউন্ড থেকে শিল্পী সহস্রাব্দ ধরে খ্রিস্টান ধর্মগ্রন্থ থেকে অনুপ্রেরণা গ্রহণ করেছে, এবং এই প্রতিযোগিতা যে কেউ আধ্যাত্মিক যুদ্ধ প্রায় থিম ব্যস্ত কলা ব্যবহার করতে আগ্রহী এর জন্য।",
                ],
                'de' => [
                    'title' => "BOLC Fernunterricht Stipendien",
                    'description' => "brent offenes Lernen College freut sich, die neue BOLC Fernstudium Stipendienprogramms bekannt zu geben - entworfen, um mehr Geld für Studenten auf der ganzen Welt zu machen das Lernen. Diese exklusiven Stipendien reichen von £ 100 und £ 460 (bis zu 80% der Kursgebühr), in Übereinstimmung mit den Bedürfnissen und Förderfähigkeit des Antragstellers.",
                ],
                'en' => [
                    'title' => "BOLC Distance Learning Scholarships",
                    'description' => "Brentwood Open Learning College is pleased to announce the new BOLC Distance Learning Scholarship scheme – designed to make learning more affordable for students all over the world. These exclusive scholarships range from £100 and £460 (Up to 80% of the course fee), in accordance with the needs and eligibility of the applicant.",
                ],
                'es' => [
                    'title' => "beca internacional en centros forestal mundial",
                    'description' => "museo descubrimiento del Congreso, que se abrió en 1971 para educar al público en general acerca de los bosques locales y globales y la silvicultura sostenible. granja de árboles monumento Magness, nuestro bosque demostración Premier encuentra cerca de Sherwood, Oregon, ofrece un enfoque práctico al aire libre para el aprendizaje del medio ambiente.",
                ],
                'fr' => [
                    'title' => "bourses d'apprentissage à distance BOLC",
                    'description' => "brentwood collège d'apprentissage ouvert est heureux d'annoncer le nouveau programme de bourses d'apprentissage à distance BOLC - conçu pour rendre l'apprentissage plus abordable pour les étudiants du monde entier. ces bourses exclusives vont de 100 £ et 460 £ (jusqu'à 80% des frais de cours), conformément aux besoins et à l'admissibilité du demandeur.",
                ],
                'hi' => [
                    'title' => "bolc दूरस्थ शिक्षा छात्रवृत्ति",
                    'description' => "दुनिया भर में छात्रों के लिए और अधिक किफायती सीखने बनाने के लिए डिजाइन - Brentwood खुला सीखने कॉलेज नई bolc दूरस्थ शिक्षा छात्रवृत्ति योजना घोषणा करते हुए खुशी है। इन विशेष छात्रवृत्ति की जरूरत है और आवेदक की पात्रता के अनुसार, £ 100 और £ 460 (पाठ्यक्रम शुल्क के 80% तक) सीमा होती है।",
                ],
                'id' => [
                    'title' => "beasiswa pembelajaran jarak jauh bolc",
                    'description' => "brentwood pembelajaran terbuka perguruan tinggi dengan bangga mengumumkan jarak bolc skema pembelajaran beasiswa baru - yang dirancang untuk membuat belajar lebih terjangkau bagi siswa di seluruh dunia. ini beasiswa eksklusif berkisar dari £ 100 dan £ 460 (hingga 80% dari biaya kursus), sesuai dengan kebutuhan dan kelayakan dari pemohon.",
                ],
                'it' => [
                    'title' => "BOLC borse di studio di formazione a distanza",
                    'description' => "Brentwood apprendimento aperto college è lieta di annunciare la nuova distanza BOLC schema di apprendimento di borse di studio - ha progettato per rendere l'apprendimento più accessibile per gli studenti di tutto il mondo. queste borse di studio esclusive vanno da £ 100 e £ 460 (fino al 80% del costo del corso), in conformità con le esigenze e l'ammissibilità del richiedente.",
                ],
                'ja' => [
                    'title' => "bolc遠隔学習奨学金",
                    'description' => "世界中の学生のための学習がより手頃な価格にするために設計された - ブレントウッドオープン学習カレッジは、新しいbolc距離学習奨学金制度を発表しています。これらの排他的な奨学金は、申請者のニーズ及び資格に応じて、£100と£460（コース料金の80％まで）の範囲です。",
                ],
                'km' => [
                    'title' => "អាហារូបករណ៍សិក្សាពីចម្ងាយ bolc",
                    'description' => "Brentwood មហាវិទ្យាល័យបើកចំហរៀនគឺសេចក្តីរីករាយសូមប្រកាសគម្រោងចម្ងាយ bolc ថ្មីអាហារូបករណ៍រៀន - បានរចនាឡើងដើម្បីធ្វើឱ្យការរៀនបន្ថែមទៀតដែលមានតំលៃសមរម្យសម្រាប់សិស្សនិស្សិតទាំងអស់នៅលើពិភពលោក។ អាហារូបករណ៍ផ្តាច់មុខទាំងនេះមានចាប់ពី£ 100 និង£ 460 (រហូតដល់ទៅ 80% នៃថ្លៃពិតណាស់) ដែលស្របតាមតម្រូវការនិងសិទ្ធិទទួលបានរបស់បេក្ខជន។",
                ],
                'ko' => [
                    'title' => "BOLC 원격 교육 장학금",
                    'description' => "전 세계 학생들에게보다 저렴한 학습하도록 설계 - 브렌트 우드 열린 학습 대학은 새로운 BOLC 거리 학습 장학금 제도를 발표하게 된 것을 기쁘게 생각합니다. 이러한 독점 장학금 신청자의 요구와 자격에 따라,에서 £ 100 £ 460 (수강료의 최대 80 %)까지 다양합니다.",
                ],
                'lo' => [
                    'title' => "bolc ທຶນການສຶກສາຮຽນຮູ້ໄລຍະທາງ",
                    'description' => "Brentwood ວິທະຍາໄລການຮຽນຮູ້ເປີດມີຄວາມຍິນດີທີ່ຈະປະກາດ bolc ໄລຍະທຶນການສຶກສາຮຽນຮູ້ການໃຫມ່ - ອອກແບບມາເພື່ອເຮັດໃຫ້ການຮຽນສາມາດໃຫ້ໄດ້ສໍາລັບນັກສຶກສາທັງຫມົດໃນທົ່ວໂລກ. ທຶນການສຶກສາພິເສດເຫຼົ່ານີ້ໄດ້ສະຈາກ£ 100 ແລະ£ 460 (ສູງເຖິງ 80% ຂອງຄ່າທໍານຽມວິຊາການ), ໃນສອດຄ່ອງກັບຄວາມຕ້ອງການແລະສິດຂອງຜູ້ສະຫມັກ.",
                ],
                'ms' => [
                    'title' => "bolc biasiswa pembelajaran jarak jauh",
                    'description' => "brentwood kolej pembelajaran terbuka dengan sukacitanya mengumumkan skim pembelajaran biasiswa jarak bolc baru - yang direka untuk menjadikan pembelajaran lebih murah untuk pelajar di seluruh dunia. ini biasiswa eksklusif berkisar dari £ 100 dan £ 460 (sehingga 80% daripada yuran kursus), selaras dengan keperluan dan kelayakan pemohon.",
                ],
                'my' => [
                    'title' => "bolc အကွာအဝေးသင်ယူမှုပညာသင်ဆု",
                    'description' => "လောကီနိုင်ငံအရပ်ရပ်ရှိသမျှကျော်ကျောင်းသားများအတွက်သင်ယူပိုပြီးတတ်နိုင်စေရန်ဒီဇိုင်း - brentwood ပွင့်လင်းသင်ယူမှုကောလိပ်သစ်ကို bolc အကွာအဝေးသင်ယူမှုပညာသင်ဆုအစီအစဉ်ကိုကြေညာဖို့နှစ်သက်သည်။ ထိုအသီးသန့်ပညာသင်ဆုလျှောက်ထား၏လိုအပ်ချက်များကိုဖြည့်နှင့်ရထိုက်ခွင့်များနှင့်အညီ, £ 100 နဲ့£ 460 (သင်တန်းကြေး၏အထိ 80%) ကနေအထိ။",
                ],
                'ne' => [
                    'title' => "bolc दूरी सिक्ने छात्रवृत्ति",
                    'description' => "सबै दुनिया भर विद्यार्थीहरूको लागि थप किफायती सिक्ने बनाउन डिजाइन - Brentwood खुला सिक्ने कलेज नयाँ bolc दूरी सिक्ने छात्रवृत्ति योजना घोषणा गर्न खुसी हुनुहुन्छ। यी विशेष छात्रवृत्ति अनुसार आवश्यकता र आवेदक को योग्यता संग देखि £ 100 र £ 460 (पाठ्यक्रम शुल्क को माथि 80%) को दायरामा।",
                ],
                'ro' => [
                    'title' => "bolc burse de învățare la distanță",
                    'description' => "colegiu de învățare deschis Brentwood are plăcerea să anunțe noul program de burse de învățare la distanță bolc - conceput pentru a face procesul de învățare mai accesibile pentru studenți din întreaga lume. aceste burse exclusive variază de la £ 100 si £ 460 (până la 80% din taxa de curs), în conformitate cu nevoile și eligibilitatea solicitantului.",
                ],
                'ru' => [
                    'title' => "bolc стипендии дистанционного обучения",
                    'description' => "Brentwood колледж открытого обучения рад объявить о выпуске новой bolc дистанционного обучения стипендиальной схемы - разработано, чтобы сделать обучение более доступным для студентов во всем мире. эти эксклюзивные стипендии варьируются от £ 100 и £ 460 (до 80% от стоимости курса), в соответствии с потребностями и правами заявителя.",
                ],
                'si' => [
                    'title' => "bolc දුරස්ථ අධ්යාපන ශිෂ්යත්ව",
                    'description' => "ලොව පුරා සිසුන් සඳහා ඉගෙන වඩා දැරිය හැකි පත් කිරීම සඳහා සැලැසුම් කරන - brentwood විවෘත ඉගෙනුම් විද්යාලයේ නව bolc දුරස්ථ අධ්යාපන ශිෂ්යත්ව ක්රමයක් නිවේදනය පිළිබඳව අපි සතුටු වෙනවා. මෙම සුවිශේෂී ශිෂ්යත්ව ඉල්ලුම්කරුෙග් අවශ්යතා හා සුදුසුකම් අනුව, £ 100 හා £ 460 (පාඨමාලා ගාස්තු සියයට 80% ක් පමණ) සිට ක්රියාත්මක වේ.",
                ],
                'ta' => [
                    'title' => "bolc தொலைதூர கல்வி உதவித் தொகை",
                    'description' => "உலகம் முழுவதும் மாணவர்கள் வாங்கக்கூடிய விலையில் கற்றல் செய்ய வடிவமைக்கப்பட்டுள்ளது - Brentwood திறந்த கற்றல் கல்லூரி புதிய bolc தொலைதூர கல்வி உதவித்தொகை திட்டம் அறிவிக்க மகிழ்ச்சி. இந்த பிரத்தியேக உதவித்தொகைகளையோ, இருந்து £ 100 மற்றும் £ 460 (நிச்சயமாக கட்டணம் வரை 80%) வரை விண்ணப்பதாரரின் தேவைகள் மற்றும் தகுதி ஏற்ப.",
                ],
                'th' => [
                    'title' => "bolc ทุนการศึกษาการเรียนทางไกล",
                    'description' => "เบรนท์วิทยาลัยการเรียนรู้ที่เปิดกว้างมีความยินดีที่จะประกาศ bolc ระยะทุนการศึกษาการเรียนรู้โครงการใหม่ - ออกแบบมาเพื่อทำให้การเรียนรู้ที่เหมาะสมมากขึ้นสำหรับนักเรียนทุกคนทั่วโลก ทุนการศึกษาพิเศษเหล่านี้มีตั้งแต่£ 100 และ£ 460 (ไม่เกิน 80% ของค่าเรียน) ให้สอดคล้องกับความต้องการและคุณสมบัติของผู้สมัคร",
                ],
                'tl' => [
                    'title' => "bolc distance pag-aaral ng scholarship",
                    'description' => "brentwood bukas na pag-aaral sa kolehiyo ay i-anunsyo ang bagong bolc distance pag-aaral ng scholarship scheme - na dinisenyo upang gumawa ng pag-aaral ng higit pang mga abot-kayang para sa mga estudyante sa buong mundo. mga eksklusibong mga scholarship saklaw mula £ 100 at £ 460 (hanggang sa 80% ng kurso fee), alinsunod sa mga pangangailangan at pagiging karapat-dapat ng aplikante.",
                ],
                'vi' => [
                    'title' => "bolc học bổng đào tạo từ xa",
                    'description' => "brentwood học tập mở đại học là vui mừng thông báo khoảng cách bolc học bổng học tập chương trình mới - được thiết kế để làm cho việc học chi phí hợp lý dành cho sinh viên trên toàn thế giới. những học bổng độc quyền dao động từ £ 100 đến £ 460 (lên đến 80% học phí), phù hợp với nhu cầu và đủ điều kiện của người nộp đơn.",
                ],
                'zh' => [
                    'title' => "BOLC遠程教育獎學金",
                    'description' => "布倫特伍德開放學習學院很高興地宣布新BOLC遠程教育獎學金計劃 - 旨在使學習的學生遍布世界各地更多的實惠。這些獨特的獎學金範圍從£100和£460（的過程費用高達80％），按照與申請人的需要和資格。",
                ],
        );

        $r = Opportunity::create($opportunities);
        $r->tags()->sync([2,11,12,13,14,15,16,17,18,19,20]);
        $r->eligible_regions()->sync([1,2,140]);
    }
}
