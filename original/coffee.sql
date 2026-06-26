-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- ホスト: 127.0.0.1
-- 生成日時: 2026-06-26 02:21:16
-- サーバのバージョン： 10.4.32-MariaDB
-- PHP のバージョン: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- データベース: `coffee`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `anything`
--

CREATE TABLE `anything` (
  `id` int(11) NOT NULL,
  `content` text NOT NULL,
  `flag` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `anything`
--

INSERT INTO `anything` (`id`, `content`, `flag`) VALUES
(1, 'スペシャルブレンドは酸味が少なくて飲みやすくておすすめ', 1),
(2, 'もう寝なさい', 0),
(3, '休息は大事', 0),
(5, '最近のお気に入りはマンデリン\r\n', 1),
(7, 'アイスとコーヒーの組み合わせって最強だと思う。', 1),
(8, '暑い時はイタリアンローストに氷をたくさん入れて飲むとすっきりする', 1);

-- --------------------------------------------------------

--
-- テーブルの構造 `badges`
--

CREATE TABLE `badges` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `badges`
--

INSERT INTO `badges` (`id`, `name`, `image`) VALUES
(1, 'コーヒー好き', 'material/badges/easy.png'),
(2, 'コーヒー愛好家', 'material/badges/normal.png'),
(3, 'コーヒーソムリエ', 'material/badges/hard.png'),
(4, '中毒患者', 'material/badges/easy_ex.png'),
(5, '重症患者', 'material/badges/normal_ex.png'),
(6, '末期患者', 'material/badges/hard_ex.png'),
(7, '被害者', 'material/badges/salmon.png');

-- --------------------------------------------------------

--
-- テーブルの構造 `beans`
--

CREATE TABLE `beans` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `bitter` tinyint(4) DEFAULT NULL,
  `acidity` tinyint(4) DEFAULT NULL,
  `body` tinyint(4) DEFAULT NULL,
  `roast` varchar(20) DEFAULT NULL,
  `info` varchar(300) DEFAULT NULL,
  `ruby` varchar(255) DEFAULT NULL,
  `flag` tinyint(4) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `shop_url` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `beans`
--

INSERT INTO `beans` (`id`, `name`, `bitter`, `acidity`, `body`, `roast`, `info`, `ruby`, `flag`, `image`, `shop_url`) VALUES
(1, 'マイルドカルディ', 3, 3, 3, '中煎り', 'ブラジル本来のやさしい甘さ。\r\nやわらかな口あたりと長く続く甘い余韻が特徴。\r\nバランスのとれた中煎りのブレンドコーヒー。\r\nブラックでもミルクや砂糖を加えても楽しめます。', 'ブラジルほんらいのやさしいあまさ。 やわらかなくちあたりとながくつづくあまいよいんがとくちょう。 バランスのとれたなかいりのブレンドコーヒー。 ブラックでもミルクやさとうをくわえてもたのしめます。', 1, 'https://www.kaldi.co.jp/ec/img/050/4515996019050_M_1m.jpg', 'https://www.kaldi.co.jp/ec/pro/disp/1/4515996019050'),
(2, 'スペシャルブレンド', 4, 2, 4, '中煎り', '苦味や複雑さが加わり、深みのあるリッチ感を楽しめる。\r\nブラックでは、コクを感じつつもすっきりとした後味がある。\r\nミルクや砂糖を合わせると程よい苦味と、ミルク感を楽しめる。', 'にがみやふくざつさがくわわり、ふかみのあるリッチかんをたのしめる。 ブラックでは、コクをかんじつつもすっきりとしたあとあじがある。 ミルクやさとうをあわせるとほどよいにがみと、ミルクかんをたのしめる。', 0, 'https://www.kaldi.co.jp/ec/img/045/4515996015045_M_1m.jpg', 'https://www.kaldi.co.jp/ec/pro/disp/1/4515996015045'),
(4, 'リッチブレンド', 5, 1, 5, '深煎り', '重厚感のある強い苦味が特徴のブレンド。\r\n奥深い味わいと長く続く余韻が楽しめる。', 'じゅうこうかんのあるつよいにがみがとくちょうのブレンド。おくぶかいあじわいとながくつづくよいんがたのしめる。', 0, 'https://ec.jal.co.jp/img/0089/goods/L/0089-4515996015083.jpg', 'https://www.kaldi.co.jp/ec/pro/disp/1/4515996015083'),
(5, 'イタリアンロースト', 5, 1, 3, '深煎り', 'アイスでもホットでも飲みごたえがある。\r\n濃厚な苦味と甘さが特徴。\r\nミルクや砂糖との相性も抜群。', 'アイスでもホットでものみごたえがある。のうこうなにがみとあまさがとくちょう。ミルクやさとうとのあいしょうもばつぐん。', 0, 'https://cdn.askul.co.jp/img/product/3L2/P962858_3L2.jpg', 'https://www.kaldi.co.jp/ec/pro/disp/1/4515996019074'),
(17, 'キリマンジャロ', 1, 5, 3, '浅煎り', '甘酸っぱい果実を思わせる。\r\n上質で切れの良い酸味が特徴。', 'あまずっぱいかじつをおもわせる。じょうしつできれのよいさんみがとくちょう。', 0, 'https://www.kaldi.co.jp/ec/img/120/4515996015120_M_1m.jpg', 'https://www.kaldi.co.jp/ec/pro/disp/1/4515996015120'),
(18, 'マンデリン', 4, 2, 4, '中煎り', '野性的で力強いボディ（コク）。 引き締まった酸味による深みが特徴。', 'やせいてきでちからづよいボディ（コク）。 ひきしまったさんみによるふかみがとくちょう。\r\n\r\n', 0, 'https://www.kaldi.co.jp/ec/img/137/4515996015137_M_1m.jpg', 'https://www.kaldi.co.jp/ec/pro/disp/1/4515996015137'),
(19, 'マンデリンフレンチ', 5, 1, 4, '深煎り', '野性味あふれる香りと強いボディ。\r\n深煎りにすることでさらに個性を引き出したコーヒー。', 'やせいみあふれるかおりとつよいボディ。ふかいりにすることでさらにこせいをひき出したコーヒー。', 0, 'https://www.kaldi.co.jp/ec/img/205/4515996015205_M_1m.jpg', 'https://www.kaldi.co.jp/ec/pro/disp/1/4515996015205'),
(20, 'モカフレンチ', 4, 2, 4, '深煎り', '豊かで甘い香りが特徴。\r\n透明感のある苦みと余韻が続く。', 'ゆたかであまいかおりがとくちょう。とうめいかんのあるにがみとよいんがつづく。', 0, 'https://www.kaldi.co.jp/ec/img/783/4515996016783_M_1m.jpg', 'https://www.kaldi.co.jp/ec/pro/disp/1/4515996016783'),
(21, 'ブルーマウンテンブレンド', 3, 3, 3, '中煎り', 'ブルーマウンテンをブレンドした堂々たる味わい。\r\n香りと甘み、苦みと酸味が織りなす秀逸なバランス。', 'ブルーマウンテンをブレンドしたどうどうたるあじわい。かおりとあまみ、にがみとさんみがおりなすしゅういつなバランス。', 0, 'https://www.kaldi.co.jp/ec/img/038/4515996015038_M_1m.jpg', 'https://www.kaldi.co.jp/ec/pro/disp/1/4515996015038'),
(22, 'モカブレンド', 2, 4, 3, '中煎り', '華やかな香りに、柔らかなコクをバランスよくブレンド。\r\nワインのような豊潤さが特徴。', 'はなやかなかおりに、やわらかなコクをバランスよくブレンド。 ワインのようなほうじゅんさがとくちょう。', 0, 'https://www.kaldi.co.jp/ec/img/052/4515996015052_M_1m.jpg', 'https://www.kaldi.co.jp/ec/pro/disp/1/4515996015052'),
(23, 'モーニングブレンド', 4, 2, 3, '深煎り', '切れのある苦みとすっきりとした後味。\r\n目覚めの一杯におすすめの飲みやすいブレンド。', 'きれのあるにがみとすっきりとしたあとあじ。めざめのいっぱいにおすすめののみやすいブレンド。', 0, 'https://www.kaldi.co.jp/ec/img/076/4515996015076_M_2m.jpg', 'https://www.kaldi.co.jp/ec/pro/disp/1/4515996015076'),
(24, 'エスプレッソブレンド', 5, 1, 5, '深煎り', '強いボディと長く続く芳醇なアロマが特徴。\r\n器具を選ばない、確かな味わいと香り。', 'つよいボディとながくつづくほうじゅんなアロマがとくちょう。きぐをえらばない、たしかなあじわいとかおり。', 0, 'https://www.kaldi.co.jp/ec/img/090/4515996015090_M_1m.jpg', 'https://www.kaldi.co.jp/ec/pro/disp/1/4515996015090'),
(25, 'グアテマラ', 1, 5, 2, '浅煎り', 'フローラルな香りと爽やかな酸味が特徴。\r\nミルク砂糖と合わせれば、甘味とコクが際立つ。', 'フローラルなかおりとさわやかなさんみがとくちょう。ミルクざとうとあわせれば、あまみとコクがきわだつ。', 0, 'https://www.kaldi.co.jp/ec/img/175/4515996015175_M_1m.jpg', 'https://www.kaldi.co.jp/ec/pro/disp/1/4515996015175'),
(26, 'コロンビア', 2, 3, 5, '浅煎り', '甘い香りとしっかりした酸味とボディ。\r\nマイルドコーヒーの代名詞。', 'あまいかおりとしっかりしたさんみとボディ。 マイルドコーヒーのだいめいし。', 0, 'https://www.kaldi.co.jp/ec/img/144/4515996015144_M_1m.jpg', 'https://www.kaldi.co.jp/ec/pro/disp/1/4515996015144'),
(27, 'プレミアムブレンド', 3, 3, 3, '中煎り', '有機栽培コーヒー生豆を贅沢に使用。\r\n3つの産地の味・香りを楽しめる。', 'ゆうきさいばいコーヒーきまめをぜいたくにしよう。 3つのさんちのあじ・かおりをたのしめる。', 0, 'https://www.kaldi.co.jp/ec/img/554/4515996016554_M_1m.jpg', 'https://www.kaldi.co.jp/ec/pro/disp/1/4515996016554'),
(28, 'バードフレンドリーブレンド', 3, 3, 3, '浅煎り', 'バードフレンドリー認証コーヒーのみでブレンド。\r\n柔らかな口当たりと甘い余韻が特徴のコーヒー。', 'バードフレンドリーにんしょうコーヒーのみでブレンド。やわらかなくちあたりとあまいよいんがとくちょうのコーヒー。', 0, 'https://www.kaldi.co.jp/ec/img/622/4515996016622_M_3m.jpg', 'https://www.kaldi.co.jp/ec/pro/disp/1/4515996016622'),
(29, 'グアテマラフレンチ', 5, 1, 5, '深煎り', 'タイプの違うグアテマラのブレンド。\r\nストロベリーをかむような濃厚な香りと甘み。', 'タイプのちがうグアテマラのブレンド。ストロベリーをかむようなのうこうなかおりとあまみ。', 0, 'https://www.kaldi.co.jp/ec/img/868/4515996016868_M_1m.jpg', 'https://www.kaldi.co.jp/ec/pro/disp/1/4515996016868'),
(30, 'ブラジル', 3, 3, 3, '中煎り', 'ソフトでバランスが良い。\r\nカカオを思わせる甘い香りが特徴。', 'ソフトでバランスがよい。カカオをおもわせるあまいかおりがとくちょう。', 0, 'https://www.kaldi.co.jp/ec/img/168/4515996015168_M_1m.jpg', 'https://www.kaldi.co.jp/ec/pro/disp/1/4515996015168'),
(31, 'ツッカーノブルボン', 3, 3, 4, '中煎り', '熟した実だけを収穫、手間をかけて乾燥。\r\n深く甘いふくよかな香りが楽しめる。', 'じゅくしたみだけをしゅうかく、てまをかけてかんそう。ふかくあまいふくよかなかおりがたのしめる。', 0, 'https://www.kaldi.co.jp/ec/img/212/4515996015212_M_1m.jpg', 'https://www.kaldi.co.jp/ec/pro/disp/1/4515996015212'),
(32, 'アメリカンブレンド', 2, 4, 1, '中煎り', '飲み飽きないやさしい味わいのブレンド。\r\nほんのり感じる甘みと軽い口当たりが特徴。', 'のみあきないやさしいあじわいのブレンド。ほんのりかんじるあまみとかるいくちあたりがとくちょう。', 0, 'https://www.kaldi.co.jp/ec/img/106/4515996015106_M_1m.jpg', 'https://www.kaldi.co.jp/ec/pro/disp/1/4515996015106');

-- --------------------------------------------------------

--
-- テーブルの構造 `board`
--

CREATE TABLE `board` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `badge_id` tinyint(4) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `board`
--

INSERT INTO `board` (`id`, `name`, `message`, `badge_id`, `created_at`) VALUES
(1, 'テストアカウント', 'Hello World', NULL, '2026-05-27 09:50:38'),
(2, 'テストアカウント', 'ミスったかもしれない。', NULL, '2026-05-27 10:08:00'),
(3, 'テストアカウント', '顏ない', NULL, '2026-05-27 10:22:15'),
(4, 'テストアカウント', 'あれ？成功してる\r\nなぜに？？？？？\r\n', NULL, '2026-05-27 10:23:03'),
(6, 'テストアカウント', '疲れた\r\nマジ無理\r\n', NULL, '2026-06-02 16:24:57'),
(7, 'テストアカウント', '最近のお気に入りはマンデリン\r\n', NULL, '2026-06-04 01:49:25'),
(8, 'テストアカウント', 'アイスとコーヒーの組み合わせって最強だと思う。', NULL, '2026-06-04 01:50:10'),
(9, 'テストアカウント', '寝起きの頭にイタリアンローストでカフェインを投入', NULL, '2026-06-10 19:47:28'),
(10, 'テストアカウント', '意味わかんない\r\n', NULL, '2026-06-10 19:56:23'),
(11, 'テストアカウント', 'できた？', 7, '2026-06-10 20:15:22'),
(12, 'テストアカウント', 'テステス\r\n', 7, '2026-06-10 20:22:59'),
(13, 'テストアカウント', 'っできてるが..CSSおかしすぎるな。\r\n', 7, '2026-06-10 20:27:48'),
(14, 'テストアカウント', 'これでどうだ', 7, '2026-06-10 20:28:57'),
(15, 'テストアカウント', 'さらに検証', 7, '2026-06-10 20:31:47'),
(16, 'テストアカウント', 'たぶんできてる？', 7, '2026-06-10 20:32:01'),
(17, 'テストアカウント', 'どう？', 7, '2026-06-10 20:51:27'),
(18, 'テストアカウント', 'tesuto', 7, '2026-06-11 07:01:13'),
(19, 'テストアカウント', '暑い時はイタリアンローストに氷をたくさん入れて飲むとすっきりする', 7, '2026-06-11 07:14:52');

-- --------------------------------------------------------

--
-- テーブルの構造 `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `count_s` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `count_s`) VALUES
(2, 'テストアカウント', '$2y$10$KHYht.ofHg65EysLKSTzCOFouPUAirUpc.aCYXveVaMDvuuX8agaK', 14),
(3, 'あ', '$2y$10$zo7vHbIf2kILUEigMx3XNeEfTz1zCnaUEhn2mk6cmz/vX/9YBynVW', NULL);

-- --------------------------------------------------------

--
-- テーブルの構造 `user_badges`
--

CREATE TABLE `user_badges` (
  `id` int(11) NOT NULL,
  `users_id` int(11) DEFAULT NULL,
  `badges_id` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `user_badges`
--

INSERT INTO `user_badges` (`id`, `users_id`, `badges_id`) VALUES
(2, 2, 7);

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `anything`
--
ALTER TABLE `anything`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `badges`
--
ALTER TABLE `badges`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `beans`
--
ALTER TABLE `beans`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `board`
--
ALTER TABLE `board`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- テーブルのインデックス `user_badges`
--
ALTER TABLE `user_badges`
  ADD PRIMARY KEY (`id`);

--
-- ダンプしたテーブルの AUTO_INCREMENT
--

--
-- テーブルの AUTO_INCREMENT `anything`
--
ALTER TABLE `anything`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- テーブルの AUTO_INCREMENT `badges`
--
ALTER TABLE `badges`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- テーブルの AUTO_INCREMENT `beans`
--
ALTER TABLE `beans`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- テーブルの AUTO_INCREMENT `board`
--
ALTER TABLE `board`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- テーブルの AUTO_INCREMENT `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- テーブルの AUTO_INCREMENT `user_badges`
--
ALTER TABLE `user_badges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
