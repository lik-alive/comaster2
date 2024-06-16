-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Хост: localhost:3306
-- Время создания: Июн 16 2024 г., 20:00
-- Версия сервера: 8.0.37-0ubuntu0.20.04.3
-- Версия PHP: 7.4.3-4ubuntu2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `co2db`
--

-- --------------------------------------------------------

--
-- Структура таблицы `wp_ab_articles`
--

CREATE TABLE `wp_ab_articles` (
  `ID_Article` smallint NOT NULL,
  `ID_Issue` tinyint NOT NULL,
  `ID_Section` tinyint NOT NULL,
  `SeqNumber` tinyint DEFAULT NULL,
  `Title` varchar(1023) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Authors` varchar(1023) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Affiliation` varchar(2047) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `PageCount` tinyint DEFAULT NULL,
  `ID_CorAuthor` smallint NOT NULL,
  `RemDate` date DEFAULT NULL,
  `FinalVerdictDate` date DEFAULT NULL,
  `HasPriority` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N',
  `Comments` varchar(1023) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Триггеры `wp_ab_articles`
--
DELIMITER $$
CREATE TRIGGER `SeqNoSetTrigger` BEFORE UPDATE ON `wp_ab_articles` FOR EACH ROW BEGIN	
	IF NEW.ID_Issue<=3 THEN
    	SET NEW.SeqNumber = NULL;
    ELSEIF OLD.ID_Issue<>NEW.ID_Issue OR OLD.ID_Section<>NEW.ID_Section THEN
    	SET @max = (SELECT MAX(a.SeqNumber) FROM wp_ab_articles a WHERE a.ID_Issue=NEW.ID_Issue AND a.ID_Section=NEW.ID_Section);
        IF @max IS NULL THEN
        	SET NEW.SeqNumber = 1;
        ELSE
        	SET NEW.SeqNumber = @max+1;
        END IF;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Структура таблицы `wp_ab_chats`
--

CREATE TABLE `wp_ab_chats` (
  `ID_Chat` smallint NOT NULL,
  `ID_Article` smallint NOT NULL,
  `ID_User` bigint UNSIGNED NOT NULL,
  `DateTime` datetime NOT NULL,
  `Message` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `wp_ab_experts`
--

CREATE TABLE `wp_ab_experts` (
  `ID_Expert` smallint NOT NULL,
  `Name` varchar(127) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `CallName` varchar(127) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Mail` varchar(127) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `IsActive` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Y',
  `Language` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'R',
  `Interests` varchar(2047) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Comments` varchar(1023) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Position` varchar(1023) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Phone` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `wp_ab_issues`
--

CREATE TABLE `wp_ab_issues` (
  `ID_Issue` tinyint NOT NULL,
  `Title` varchar(31) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `IsActive` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Y'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `wp_ab_letters`
--

CREATE TABLE `wp_ab_letters` (
  `ID_Letter` tinyint NOT NULL,
  `Type` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Language` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'R',
  `Title` varchar(127) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Text` varchar(4095) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `wp_ab_reviews`
--

CREATE TABLE `wp_ab_reviews` (
  `ID_Review` smallint NOT NULL,
  `ID_Article` smallint NOT NULL,
  `ID_Expert` smallint NOT NULL,
  `RevNo` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ToExpDate` date DEFAULT NULL,
  `FromExpDate` date DEFAULT NULL,
  `ID_Verdict` tinyint DEFAULT NULL,
  `Quality` tinyint DEFAULT NULL,
  `ToAuthDate` date DEFAULT NULL,
  `FromAuthDate` date DEFAULT NULL,
  `RemDate` date DEFAULT NULL,
  `Comments` varchar(1023) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Триггеры `wp_ab_reviews`
--
DELIMITER $$
CREATE TRIGGER `ReviewInsertTrigger` BEFORE INSERT ON `wp_ab_reviews` FOR EACH ROW BEGIN
	SET @ida=NEW.ID_Article;
    SET @ide=NEW.ID_Expert;
	SET @num2=(SELECT COUNT(*) FROM wp_ab_reviews r WHERE r.ID_Article=@ida and r.ID_Expert=@ide);
    IF @num2=0 THEN
    	SET @num1=(SELECT COUNT(DISTINCT r.ID_Expert) FROM wp_ab_reviews r WHERE r.ID_Article=@ida)+1;
    	SET @num2=1;
    ELSE
    	SET @num1=(SELECT r.RevNo FROM wp_ab_reviews r WHERE r.ID_Article=@ida and r.ID_Expert=@ide LIMIT 1);
        SET @num1=SUBSTRING(@num1, 1, LOCATE('.', @num1)-1);
        SET @num2=@num2+1;
    END IF;
    	
    SET NEW.RevNo=CONCAT(@num1,'.',@num2);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Структура таблицы `wp_ab_sections`
--

CREATE TABLE `wp_ab_sections` (
  `ID_Section` tinyint NOT NULL,
  `Title` varchar(127) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ShortTitle` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ID_Editor` smallint DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `wp_ab_verdicts`
--

CREATE TABLE `wp_ab_verdicts` (
  `ID_Verdict` tinyint NOT NULL,
  `Title` varchar(31) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `wp_ab_verdicts`
--

INSERT INTO `wp_ab_verdicts` (`ID_Verdict`, `Title`) VALUES
(1, 'добро'),
(2, 'подправить'),
(3, 'переделать'),
(4, 'отклонить'),
(5, 'отказался'),
(6, 'снят');

-- --------------------------------------------------------

--
-- Структура таблицы `wp_ab_versions`
--

CREATE TABLE `wp_ab_versions` (
  `ID_Version` smallint NOT NULL,
  `ID_Article` smallint NOT NULL,
  `VerNo` tinyint NOT NULL DEFAULT '1',
  `RecvDate` date NOT NULL,
  `TechComments` varchar(2047) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ToAuthDate` date DEFAULT NULL,
  `Comments` varchar(1023) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Триггеры `wp_ab_versions`
--
DELIMITER $$
CREATE TRIGGER `VerNoTrigger` BEFORE INSERT ON `wp_ab_versions` FOR EACH ROW BEGIN
	SET @ida=NEW.ID_Article;
	SET @num=(SELECT COUNT(*) FROM wp_ab_versions v WHERE v.ID_Article=@ida);
    SET NEW.VerNo=@num+1;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Структура таблицы `wp_aryo_activity_log`
--

CREATE TABLE `wp_aryo_activity_log` (
  `histid` int NOT NULL,
  `user_caps` varchar(70) NOT NULL DEFAULT 'guest',
  `action` varchar(255) NOT NULL,
  `object_type` varchar(255) NOT NULL,
  `object_subtype` varchar(255) NOT NULL DEFAULT '',
  `object_name` varchar(255) NOT NULL,
  `object_id` int NOT NULL DEFAULT '0',
  `user_id` int NOT NULL DEFAULT '0',
  `hist_ip` varchar(55) NOT NULL DEFAULT '127.0.0.1',
  `hist_time` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `wp_commentmeta`
--

CREATE TABLE `wp_commentmeta` (
  `meta_id` bigint UNSIGNED NOT NULL,
  `comment_id` bigint UNSIGNED NOT NULL DEFAULT '0',
  `meta_key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `wp_comments`
--

CREATE TABLE `wp_comments` (
  `comment_ID` bigint UNSIGNED NOT NULL,
  `comment_post_ID` bigint UNSIGNED NOT NULL DEFAULT '0',
  `comment_author` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `comment_author_email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `comment_author_url` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `comment_author_IP` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `comment_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comment_date_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comment_content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `comment_karma` int NOT NULL DEFAULT '0',
  `comment_approved` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `comment_agent` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `comment_type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'comment',
  `comment_parent` bigint UNSIGNED NOT NULL DEFAULT '0',
  `user_id` bigint UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `wp_links`
--

CREATE TABLE `wp_links` (
  `link_id` bigint UNSIGNED NOT NULL,
  `link_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `link_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `link_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `link_target` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `link_description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `link_visible` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Y',
  `link_owner` bigint UNSIGNED NOT NULL DEFAULT '1',
  `link_rating` int NOT NULL DEFAULT '0',
  `link_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `link_rel` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `link_notes` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `link_rss` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `wp_options`
--

CREATE TABLE `wp_options` (
  `option_id` bigint UNSIGNED NOT NULL,
  `option_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `option_value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `autoload` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'yes'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `wp_options`
--

INSERT INTO `wp_options` (`option_id`, `option_name`, `option_value`, `autoload`) VALUES
(1, 'siteurl', 'http://localhost', 'yes'),
(2, 'home', 'http://localhost', 'yes'),
(3, 'blogname', 'Журнал &quot;Компьютерная оптика&quot;', 'yes'),
(4, 'blogdescription', 'Добро пожаловать', 'yes'),
(5, 'users_can_register', '0', 'yes'),
(6, 'admin_email', 'admin@example.com', 'yes'),
(7, 'start_of_week', '1', 'yes'),
(8, 'use_balanceTags', '0', 'yes'),
(9, 'use_smilies', '1', 'yes'),
(10, 'require_name_email', '1', 'yes'),
(11, 'comments_notify', '1', 'yes'),
(12, 'posts_per_rss', '10', 'yes'),
(13, 'rss_use_excerpt', '0', 'yes'),
(14, 'mailserver_url', 'mail.example.com', 'yes'),
(15, 'mailserver_login', 'login@example.com', 'yes'),
(16, 'mailserver_pass', 'password', 'yes'),
(17, 'mailserver_port', '110', 'yes'),
(18, 'default_category', '1', 'yes'),
(19, 'default_comment_status', 'open', 'yes'),
(20, 'default_ping_status', 'open', 'yes'),
(21, 'default_pingback_flag', '0', 'yes'),
(22, 'posts_per_page', '10', 'yes'),
(23, 'date_format', 'd.m.Y', 'yes'),
(24, 'time_format', 'H:i', 'yes'),
(25, 'links_updated_date_format', 'd.m.Y H:i', 'yes'),
(26, 'comment_moderation', '0', 'yes'),
(27, 'moderation_notify', '1', 'yes'),
(28, 'permalink_structure', '/%year%/%monthnum%/%day%/%postname%/', 'yes'),
(29, 'rewrite_rules', 'a:95:{s:11:\"^wp-json/?$\";s:22:\"index.php?rest_route=/\";s:14:\"^wp-json/(.*)?\";s:33:\"index.php?rest_route=/$matches[1]\";s:21:\"^index.php/wp-json/?$\";s:22:\"index.php?rest_route=/\";s:24:\"^index.php/wp-json/(.*)?\";s:33:\"index.php?rest_route=/$matches[1]\";s:17:\"^wp-sitemap\\.xml$\";s:23:\"index.php?sitemap=index\";s:17:\"^wp-sitemap\\.xsl$\";s:36:\"index.php?sitemap-stylesheet=sitemap\";s:23:\"^wp-sitemap-index\\.xsl$\";s:34:\"index.php?sitemap-stylesheet=index\";s:48:\"^wp-sitemap-([a-z]+?)-([a-z\\d_-]+?)-(\\d+?)\\.xml$\";s:75:\"index.php?sitemap=$matches[1]&sitemap-subtype=$matches[2]&paged=$matches[3]\";s:34:\"^wp-sitemap-([a-z]+?)-(\\d+?)\\.xml$\";s:47:\"index.php?sitemap=$matches[1]&paged=$matches[2]\";s:47:\"category/(.+?)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:52:\"index.php?category_name=$matches[1]&feed=$matches[2]\";s:42:\"category/(.+?)/(feed|rdf|rss|rss2|atom)/?$\";s:52:\"index.php?category_name=$matches[1]&feed=$matches[2]\";s:23:\"category/(.+?)/embed/?$\";s:46:\"index.php?category_name=$matches[1]&embed=true\";s:35:\"category/(.+?)/page/?([0-9]{1,})/?$\";s:53:\"index.php?category_name=$matches[1]&paged=$matches[2]\";s:17:\"category/(.+?)/?$\";s:35:\"index.php?category_name=$matches[1]\";s:44:\"tag/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:42:\"index.php?tag=$matches[1]&feed=$matches[2]\";s:39:\"tag/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:42:\"index.php?tag=$matches[1]&feed=$matches[2]\";s:20:\"tag/([^/]+)/embed/?$\";s:36:\"index.php?tag=$matches[1]&embed=true\";s:32:\"tag/([^/]+)/page/?([0-9]{1,})/?$\";s:43:\"index.php?tag=$matches[1]&paged=$matches[2]\";s:14:\"tag/([^/]+)/?$\";s:25:\"index.php?tag=$matches[1]\";s:45:\"type/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:50:\"index.php?post_format=$matches[1]&feed=$matches[2]\";s:40:\"type/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:50:\"index.php?post_format=$matches[1]&feed=$matches[2]\";s:21:\"type/([^/]+)/embed/?$\";s:44:\"index.php?post_format=$matches[1]&embed=true\";s:33:\"type/([^/]+)/page/?([0-9]{1,})/?$\";s:51:\"index.php?post_format=$matches[1]&paged=$matches[2]\";s:15:\"type/([^/]+)/?$\";s:33:\"index.php?post_format=$matches[1]\";s:48:\".*wp-(atom|rdf|rss|rss2|feed|commentsrss2)\\.php$\";s:18:\"index.php?feed=old\";s:20:\".*wp-app\\.php(/.*)?$\";s:19:\"index.php?error=403\";s:18:\".*wp-register.php$\";s:23:\"index.php?register=true\";s:32:\"feed/(feed|rdf|rss|rss2|atom)/?$\";s:27:\"index.php?&feed=$matches[1]\";s:27:\"(feed|rdf|rss|rss2|atom)/?$\";s:27:\"index.php?&feed=$matches[1]\";s:8:\"embed/?$\";s:21:\"index.php?&embed=true\";s:20:\"page/?([0-9]{1,})/?$\";s:28:\"index.php?&paged=$matches[1]\";s:27:\"comment-page-([0-9]{1,})/?$\";s:40:\"index.php?&page_id=653&cpage=$matches[1]\";s:41:\"comments/feed/(feed|rdf|rss|rss2|atom)/?$\";s:42:\"index.php?&feed=$matches[1]&withcomments=1\";s:36:\"comments/(feed|rdf|rss|rss2|atom)/?$\";s:42:\"index.php?&feed=$matches[1]&withcomments=1\";s:17:\"comments/embed/?$\";s:21:\"index.php?&embed=true\";s:44:\"search/(.+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:40:\"index.php?s=$matches[1]&feed=$matches[2]\";s:39:\"search/(.+)/(feed|rdf|rss|rss2|atom)/?$\";s:40:\"index.php?s=$matches[1]&feed=$matches[2]\";s:20:\"search/(.+)/embed/?$\";s:34:\"index.php?s=$matches[1]&embed=true\";s:32:\"search/(.+)/page/?([0-9]{1,})/?$\";s:41:\"index.php?s=$matches[1]&paged=$matches[2]\";s:14:\"search/(.+)/?$\";s:23:\"index.php?s=$matches[1]\";s:47:\"author/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:50:\"index.php?author_name=$matches[1]&feed=$matches[2]\";s:42:\"author/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:50:\"index.php?author_name=$matches[1]&feed=$matches[2]\";s:23:\"author/([^/]+)/embed/?$\";s:44:\"index.php?author_name=$matches[1]&embed=true\";s:35:\"author/([^/]+)/page/?([0-9]{1,})/?$\";s:51:\"index.php?author_name=$matches[1]&paged=$matches[2]\";s:17:\"author/([^/]+)/?$\";s:33:\"index.php?author_name=$matches[1]\";s:69:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/feed/(feed|rdf|rss|rss2|atom)/?$\";s:80:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&feed=$matches[4]\";s:64:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/(feed|rdf|rss|rss2|atom)/?$\";s:80:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&feed=$matches[4]\";s:45:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/embed/?$\";s:74:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&embed=true\";s:57:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/page/?([0-9]{1,})/?$\";s:81:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&paged=$matches[4]\";s:39:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/?$\";s:63:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]\";s:56:\"([0-9]{4})/([0-9]{1,2})/feed/(feed|rdf|rss|rss2|atom)/?$\";s:64:\"index.php?year=$matches[1]&monthnum=$matches[2]&feed=$matches[3]\";s:51:\"([0-9]{4})/([0-9]{1,2})/(feed|rdf|rss|rss2|atom)/?$\";s:64:\"index.php?year=$matches[1]&monthnum=$matches[2]&feed=$matches[3]\";s:32:\"([0-9]{4})/([0-9]{1,2})/embed/?$\";s:58:\"index.php?year=$matches[1]&monthnum=$matches[2]&embed=true\";s:44:\"([0-9]{4})/([0-9]{1,2})/page/?([0-9]{1,})/?$\";s:65:\"index.php?year=$matches[1]&monthnum=$matches[2]&paged=$matches[3]\";s:26:\"([0-9]{4})/([0-9]{1,2})/?$\";s:47:\"index.php?year=$matches[1]&monthnum=$matches[2]\";s:43:\"([0-9]{4})/feed/(feed|rdf|rss|rss2|atom)/?$\";s:43:\"index.php?year=$matches[1]&feed=$matches[2]\";s:38:\"([0-9]{4})/(feed|rdf|rss|rss2|atom)/?$\";s:43:\"index.php?year=$matches[1]&feed=$matches[2]\";s:19:\"([0-9]{4})/embed/?$\";s:37:\"index.php?year=$matches[1]&embed=true\";s:31:\"([0-9]{4})/page/?([0-9]{1,})/?$\";s:44:\"index.php?year=$matches[1]&paged=$matches[2]\";s:13:\"([0-9]{4})/?$\";s:26:\"index.php?year=$matches[1]\";s:58:\"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:68:\"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:88:\"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:83:\"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:83:\"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:64:\"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:53:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)/embed/?$\";s:91:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&name=$matches[4]&embed=true\";s:57:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)/trackback/?$\";s:85:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&name=$matches[4]&tb=1\";s:77:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:97:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&name=$matches[4]&feed=$matches[5]\";s:72:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:97:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&name=$matches[4]&feed=$matches[5]\";s:65:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)/page/?([0-9]{1,})/?$\";s:98:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&name=$matches[4]&paged=$matches[5]\";s:72:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)/comment-page-([0-9]{1,})/?$\";s:98:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&name=$matches[4]&cpage=$matches[5]\";s:61:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)(?:/([0-9]+))?/?$\";s:97:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&name=$matches[4]&page=$matches[5]\";s:47:\"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:57:\"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:77:\"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:72:\"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:72:\"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:53:\"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:64:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/comment-page-([0-9]{1,})/?$\";s:81:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&cpage=$matches[4]\";s:51:\"([0-9]{4})/([0-9]{1,2})/comment-page-([0-9]{1,})/?$\";s:65:\"index.php?year=$matches[1]&monthnum=$matches[2]&cpage=$matches[3]\";s:38:\"([0-9]{4})/comment-page-([0-9]{1,})/?$\";s:44:\"index.php?year=$matches[1]&cpage=$matches[2]\";s:27:\".?.+?/attachment/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:37:\".?.+?/attachment/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:57:\".?.+?/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:52:\".?.+?/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:52:\".?.+?/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:33:\".?.+?/attachment/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:16:\"(.?.+?)/embed/?$\";s:41:\"index.php?pagename=$matches[1]&embed=true\";s:20:\"(.?.+?)/trackback/?$\";s:35:\"index.php?pagename=$matches[1]&tb=1\";s:40:\"(.?.+?)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:47:\"index.php?pagename=$matches[1]&feed=$matches[2]\";s:35:\"(.?.+?)/(feed|rdf|rss|rss2|atom)/?$\";s:47:\"index.php?pagename=$matches[1]&feed=$matches[2]\";s:28:\"(.?.+?)/page/?([0-9]{1,})/?$\";s:48:\"index.php?pagename=$matches[1]&paged=$matches[2]\";s:35:\"(.?.+?)/comment-page-([0-9]{1,})/?$\";s:48:\"index.php?pagename=$matches[1]&cpage=$matches[2]\";s:24:\"(.?.+?)(?:/([0-9]+))?/?$\";s:47:\"index.php?pagename=$matches[1]&page=$matches[2]\";}', 'yes'),
(30, 'hack_file', '0', 'yes'),
(31, 'blog_charset', 'UTF-8', 'yes'),
(32, 'moderation_keys', '', 'no'),
(33, 'active_plugins', 'a:7:{i:0;s:39:\"aryo-activity-log/aryo-activity-log.php\";i:1;s:32:\"baw-login-logout-menu/bawllm.php\";i:2;s:48:\"capability-manager-enhanced/capsman-enhanced.php\";i:3;s:33:\"nav-menu-roles/nav-menu-roles.php\";i:4;s:27:\"wp-crontrol/wp-crontrol.php\";i:5;s:33:\"wp-force-login/wp-force-login.php\";i:6;s:43:\"wp-maintenance-mode/wp-maintenance-mode.php\";}', 'yes'),
(34, 'category_base', '', 'yes'),
(35, 'ping_sites', 'http://rpc.pingomatic.com/', 'yes'),
(36, 'comment_max_links', '2', 'yes'),
(37, 'gmt_offset', '3', 'yes'),
(38, 'default_email_category', '1', 'yes'),
(39, 'recently_edited', '', 'no'),
(40, 'template', 'cactus-child', 'yes'),
(41, 'stylesheet', 'cactus-child', 'yes'),
(44, 'comment_registration', '0', 'yes'),
(45, 'html_type', 'text/html', 'yes'),
(46, 'use_trackback', '0', 'yes'),
(47, 'default_role', 'subscriber', 'yes'),
(48, 'db_version', '49752', 'yes'),
(49, 'uploads_use_yearmonth_folders', '1', 'yes'),
(50, 'upload_path', '', 'yes'),
(51, 'blog_public', '0', 'yes'),
(52, 'default_link_category', '2', 'yes'),
(53, 'show_on_front', 'page', 'yes'),
(54, 'tag_base', '', 'yes'),
(55, 'show_avatars', '1', 'yes'),
(56, 'avatar_rating', 'G', 'yes'),
(57, 'upload_url_path', '', 'yes'),
(58, 'thumbnail_size_w', '150', 'yes'),
(59, 'thumbnail_size_h', '150', 'yes'),
(60, 'thumbnail_crop', '1', 'yes'),
(61, 'medium_size_w', '300', 'yes'),
(62, 'medium_size_h', '300', 'yes'),
(63, 'avatar_default', 'mystery', 'yes'),
(64, 'large_size_w', '1024', 'yes'),
(65, 'large_size_h', '1024', 'yes'),
(66, 'image_default_link_type', 'none', 'yes'),
(67, 'image_default_size', '', 'yes'),
(68, 'image_default_align', '', 'yes'),
(69, 'close_comments_for_old_posts', '0', 'yes'),
(70, 'close_comments_days_old', '14', 'yes'),
(71, 'thread_comments', '1', 'yes'),
(72, 'thread_comments_depth', '5', 'yes'),
(73, 'page_comments', '0', 'yes'),
(74, 'comments_per_page', '50', 'yes'),
(75, 'default_comments_page', 'newest', 'yes'),
(76, 'comment_order', 'asc', 'yes'),
(77, 'sticky_posts', 'a:0:{}', 'yes'),
(78, 'widget_categories', 'a:2:{i:2;a:4:{s:5:\"title\";s:0:\"\";s:5:\"count\";i:0;s:12:\"hierarchical\";i:0;s:8:\"dropdown\";i:0;}s:12:\"_multiwidget\";i:1;}', 'yes'),
(79, 'widget_text', 'a:2:{i:1;a:0:{}s:12:\"_multiwidget\";i:1;}', 'yes'),
(80, 'widget_rss', 'a:2:{i:1;a:0:{}s:12:\"_multiwidget\";i:1;}', 'yes'),
(81, 'uninstall_plugins', 'a:1:{s:39:\"aryo-activity-log/aryo-activity-log.php\";a:2:{i:0;s:15:\"AAL_Maintenance\";i:1;s:9:\"uninstall\";}}', 'no'),
(82, 'timezone_string', '', 'yes'),
(83, 'page_for_posts', '0', 'yes'),
(84, 'page_on_front', '653', 'yes'),
(85, 'default_post_format', '0', 'yes'),
(86, 'link_manager_enabled', '0', 'yes'),
(87, 'finished_splitting_shared_terms', '1', 'yes'),
(88, 'site_icon', '774', 'yes'),
(89, 'medium_large_size_w', '768', 'yes'),
(90, 'medium_large_size_h', '0', 'yes'),
(91, 'wp_page_for_privacy_policy', '3', 'yes'),
(92, 'show_comments_cookies_opt_in', '0', 'yes'),
(93, 'initial_db_version', '38590', 'yes'),
(94, 'wp_user_roles', 'a:8:{s:13:\"administrator\";a:2:{s:4:\"name\";s:13:\"Administrator\";s:12:\"capabilities\";a:63:{s:13:\"switch_themes\";b:1;s:11:\"edit_themes\";b:1;s:16:\"activate_plugins\";b:1;s:12:\"edit_plugins\";b:1;s:10:\"edit_users\";b:1;s:10:\"edit_files\";b:1;s:14:\"manage_options\";b:1;s:17:\"moderate_comments\";b:1;s:17:\"manage_categories\";b:1;s:12:\"manage_links\";b:1;s:12:\"upload_files\";b:1;s:6:\"import\";b:1;s:15:\"unfiltered_html\";b:1;s:10:\"edit_posts\";b:1;s:17:\"edit_others_posts\";b:1;s:20:\"edit_published_posts\";b:1;s:13:\"publish_posts\";b:1;s:10:\"edit_pages\";b:1;s:4:\"read\";b:1;s:8:\"level_10\";b:1;s:7:\"level_9\";b:1;s:7:\"level_8\";b:1;s:7:\"level_7\";b:1;s:7:\"level_6\";b:1;s:7:\"level_5\";b:1;s:7:\"level_4\";b:1;s:7:\"level_3\";b:1;s:7:\"level_2\";b:1;s:7:\"level_1\";b:1;s:7:\"level_0\";b:1;s:17:\"edit_others_pages\";b:1;s:20:\"edit_published_pages\";b:1;s:13:\"publish_pages\";b:1;s:12:\"delete_pages\";b:1;s:19:\"delete_others_pages\";b:1;s:22:\"delete_published_pages\";b:1;s:12:\"delete_posts\";b:1;s:19:\"delete_others_posts\";b:1;s:22:\"delete_published_posts\";b:1;s:20:\"delete_private_posts\";b:1;s:18:\"edit_private_posts\";b:1;s:18:\"read_private_posts\";b:1;s:20:\"delete_private_pages\";b:1;s:18:\"edit_private_pages\";b:1;s:18:\"read_private_pages\";b:1;s:12:\"delete_users\";b:1;s:12:\"create_users\";b:1;s:17:\"unfiltered_upload\";b:1;s:14:\"edit_dashboard\";b:1;s:14:\"update_plugins\";b:1;s:14:\"delete_plugins\";b:1;s:15:\"install_plugins\";b:1;s:13:\"update_themes\";b:1;s:14:\"install_themes\";b:1;s:11:\"update_core\";b:1;s:10:\"list_users\";b:1;s:12:\"remove_users\";b:1;s:13:\"promote_users\";b:1;s:18:\"edit_theme_options\";b:1;s:13:\"delete_themes\";b:1;s:6:\"export\";b:1;s:19:\"manage_capabilities\";b:1;s:26:\"view_all_aryo_activity_log\";b:1;}}s:6:\"editor\";a:2:{s:4:\"name\";s:6:\"Editor\";s:12:\"capabilities\";a:34:{s:17:\"moderate_comments\";b:1;s:17:\"manage_categories\";b:1;s:12:\"manage_links\";b:1;s:12:\"upload_files\";b:1;s:15:\"unfiltered_html\";b:1;s:10:\"edit_posts\";b:1;s:17:\"edit_others_posts\";b:1;s:20:\"edit_published_posts\";b:1;s:13:\"publish_posts\";b:1;s:10:\"edit_pages\";b:1;s:4:\"read\";b:1;s:7:\"level_7\";b:1;s:7:\"level_6\";b:1;s:7:\"level_5\";b:1;s:7:\"level_4\";b:1;s:7:\"level_3\";b:1;s:7:\"level_2\";b:1;s:7:\"level_1\";b:1;s:7:\"level_0\";b:1;s:17:\"edit_others_pages\";b:1;s:20:\"edit_published_pages\";b:1;s:13:\"publish_pages\";b:1;s:12:\"delete_pages\";b:1;s:19:\"delete_others_pages\";b:1;s:22:\"delete_published_pages\";b:1;s:12:\"delete_posts\";b:1;s:19:\"delete_others_posts\";b:1;s:22:\"delete_published_posts\";b:1;s:20:\"delete_private_posts\";b:1;s:18:\"edit_private_posts\";b:1;s:18:\"read_private_posts\";b:1;s:20:\"delete_private_pages\";b:1;s:18:\"edit_private_pages\";b:1;s:18:\"read_private_pages\";b:1;}}s:6:\"author\";a:2:{s:4:\"name\";s:6:\"Author\";s:12:\"capabilities\";a:10:{s:12:\"upload_files\";b:1;s:10:\"edit_posts\";b:1;s:20:\"edit_published_posts\";b:1;s:13:\"publish_posts\";b:1;s:4:\"read\";b:1;s:7:\"level_2\";b:1;s:7:\"level_1\";b:1;s:7:\"level_0\";b:1;s:12:\"delete_posts\";b:1;s:22:\"delete_published_posts\";b:1;}}s:11:\"contributor\";a:2:{s:4:\"name\";s:11:\"Contributor\";s:12:\"capabilities\";a:5:{s:10:\"edit_posts\";b:1;s:4:\"read\";b:1;s:7:\"level_1\";b:1;s:7:\"level_0\";b:1;s:12:\"delete_posts\";b:1;}}s:10:\"subscriber\";a:2:{s:4:\"name\";s:10:\"Subscriber\";s:12:\"capabilities\";a:2:{s:4:\"read\";b:1;s:7:\"level_0\";b:1;}}s:7:\"jeditor\";a:2:{s:4:\"name\";s:7:\"Jeditor\";s:12:\"capabilities\";a:0:{}}s:5:\"jtech\";a:2:{s:4:\"name\";s:5:\"Jtech\";s:12:\"capabilities\";a:0:{}}s:8:\"jteditor\";a:2:{s:4:\"name\";s:8:\"Jteditor\";s:12:\"capabilities\";a:0:{}}}', 'yes'),
(95, 'fresh_site', '0', 'yes'),
(96, 'WPLANG', 'ru_RU', 'yes'),
(97, 'widget_search', 'a:2:{i:2;a:1:{s:5:\"title\";s:0:\"\";}s:12:\"_multiwidget\";i:1;}', 'yes'),
(98, 'widget_recent-posts', 'a:2:{i:2;a:2:{s:5:\"title\";s:0:\"\";s:6:\"number\";i:5;}s:12:\"_multiwidget\";i:1;}', 'yes'),
(99, 'widget_recent-comments', 'a:2:{i:2;a:2:{s:5:\"title\";s:0:\"\";s:6:\"number\";i:5;}s:12:\"_multiwidget\";i:1;}', 'yes'),
(100, 'widget_archives', 'a:2:{i:2;a:3:{s:5:\"title\";s:0:\"\";s:5:\"count\";i:0;s:8:\"dropdown\";i:0;}s:12:\"_multiwidget\";i:1;}', 'yes'),
(101, 'widget_meta', 'a:2:{i:2;a:1:{s:5:\"title\";s:0:\"\";}s:12:\"_multiwidget\";i:1;}', 'yes'),
(102, 'sidebars_widgets', 'a:7:{s:19:\"wp_inactive_widgets\";a:6:{i:0;s:10:\"archives-2\";i:1;s:6:\"meta-2\";i:2;s:8:\"search-2\";i:3;s:12:\"categories-2\";i:4;s:14:\"recent-posts-2\";i:5;s:17:\"recent-comments-2\";}s:9:\"sidebar-1\";a:0:{}s:8:\"footer-1\";a:0:{}s:8:\"footer-2\";a:0:{}s:8:\"footer-3\";a:0:{}s:8:\"footer-4\";a:0:{}s:13:\"array_version\";i:3;}', 'yes'),
(103, 'widget_pages', 'a:1:{s:12:\"_multiwidget\";i:1;}', 'yes'),
(104, 'widget_calendar', 'a:1:{s:12:\"_multiwidget\";i:1;}', 'yes'),
(105, 'widget_media_audio', 'a:1:{s:12:\"_multiwidget\";i:1;}', 'yes'),
(106, 'widget_media_image', 'a:1:{s:12:\"_multiwidget\";i:1;}', 'yes'),
(107, 'widget_media_gallery', 'a:1:{s:12:\"_multiwidget\";i:1;}', 'yes'),
(108, 'widget_media_video', 'a:1:{s:12:\"_multiwidget\";i:1;}', 'yes'),
(111, 'widget_tag_cloud', 'a:1:{s:12:\"_multiwidget\";i:1;}', 'yes'),
(112, 'widget_nav_menu', 'a:1:{s:12:\"_multiwidget\";i:1;}', 'yes'),
(113, 'widget_custom_html', 'a:1:{s:12:\"_multiwidget\";i:1;}', 'yes'),
(114, 'cron', 'a:12:{i:1717890467;a:1:{s:34:\"wp_privacy_delete_old_export_files\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:6:\"hourly\";s:4:\"args\";a:0:{}s:8:\"interval\";i:3600;}}}i:1717903257;a:1:{s:18:\"wp_https_detection\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:10:\"twicedaily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:43200;}}}i:1717912035;a:1:{s:16:\"wp_update_themes\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:10:\"twicedaily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:43200;}}}i:1717912069;a:1:{s:17:\"wp_update_plugins\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:10:\"twicedaily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:43200;}}}i:1717926483;a:1:{s:16:\"wp_version_check\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:10:\"twicedaily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:43200;}}}i:1717946126;a:1:{s:32:\"recovery_mode_clean_expired_keys\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}}i:1717950392;a:1:{s:23:\"updraft_backup_database\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}}i:1717952400;a:1:{s:30:\"wp_scheduled_auto_draft_delete\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}}i:1717955250;a:2:{s:19:\"wp_scheduled_delete\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}s:25:\"delete_expired_transients\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}}i:1718209592;a:1:{s:14:\"updraft_backup\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:6:\"weekly\";s:4:\"args\";a:0:{}s:8:\"interval\";i:604800;}}}i:1718464857;a:1:{s:30:\"wp_site_health_scheduled_check\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:6:\"weekly\";s:4:\"args\";a:0:{}s:8:\"interval\";i:604800;}}}s:7:\"version\";i:2;}', 'yes'),
(144, 'recently_activated', 'a:1:{s:27:\"updraftplus/updraftplus.php\";i:1629475314;}', 'yes'),
(145, 'category_children', 'a:0:{}', 'yes'),
(147, 'current_theme', 'Cactus-Child', 'yes'),
(148, 'theme_mods_cactus-child', 'a:7:{i:0;b:0;s:18:\"nav_menu_locations\";a:1:{s:3:\"top\";i:3;}s:18:\"custom_css_post_id\";i:-1;s:16:\"header_textcolor\";s:6:\"0a0a0a\";s:16:\"background_color\";s:6:\"c9dbe2\";s:11:\"custom_logo\";i:759;s:16:\"sidebars_widgets\";a:2:{s:4:\"time\";i:1558541189;s:4:\"data\";a:6:{s:19:\"wp_inactive_widgets\";a:6:{i:0;s:10:\"archives-2\";i:1;s:6:\"meta-2\";i:2;s:8:\"search-2\";i:3;s:12:\"categories-2\";i:4;s:14:\"recent-posts-2\";i:5;s:17:\"recent-comments-2\";}s:9:\"sidebar-1\";a:0:{}s:8:\"footer-1\";a:0:{}s:8:\"footer-2\";a:0:{}s:8:\"footer-3\";a:0:{}s:8:\"footer-4\";a:0:{}}}}', 'yes'),
(149, 'theme_switched', '', 'yes'),
(178, 'nav_menu_options', 'a:1:{s:8:\"auto_add\";a:0:{}}', 'yes'),
(186, 'capsman_version', '', 'yes'),
(187, 'nav_menu_roles_db_version', '1.9.2', 'yes'),
(188, 'wpmm_settings', 'a:5:{s:7:\"general\";a:9:{s:6:\"status\";i:0;s:11:\"bypass_bots\";i:0;s:11:\"meta_robots\";i:0;s:11:\"redirection\";s:0:\"\";s:7:\"exclude\";a:3:{i:0;s:4:\"feed\";i:1;s:8:\"wp-login\";i:2;s:5:\"login\";}s:6:\"notice\";i:1;s:10:\"admin_link\";i:0;s:12:\"backend_role\";a:0:{}s:13:\"frontend_role\";a:0:{}}s:6:\"design\";a:10:{s:5:\"title\";s:60:\"Режим технического обслуживания\";s:7:\"heading\";s:60:\"Режим технического обслуживания\";s:13:\"heading_color\";s:7:\"#ffffff\";s:4:\"text\";s:249:\"<p>В данный момент сайт находится на техническом обслуживании.<br />Приносим извинения за временные неудобства.<br />Спасибо за понимание.</p>\";s:10:\"text_color\";s:7:\"#ffffff\";s:7:\"bg_type\";s:6:\"custom\";s:8:\"bg_color\";s:0:\"\";s:9:\"bg_custom\";s:73:\"http://89.186.234.80/COMaster2/wp-content/uploads/2019/01/maintenance.jpg\";s:13:\"bg_predefined\";s:7:\"bg1.jpg\";s:10:\"custom_css\";a:3:{s:13:\"heading_color\";s:28:\".wrap h1 { color: #ffffff; }\";s:10:\"text_color\";s:28:\".wrap h2 { color: #ffffff; }\";s:6:\"bg_url\";s:158:\".background { background: url(http://89.186.234.80/COMaster2/wp-content/uploads/2019/01/maintenance.jpg) no-repeat center top fixed; background-size: cover; }\";}}s:7:\"modules\";a:24:{s:16:\"countdown_status\";i:0;s:15:\"countdown_start\";s:19:\"2018-10-21 00:37:30\";s:17:\"countdown_details\";a:3:{s:4:\"days\";i:0;s:5:\"hours\";i:1;s:7:\"minutes\";i:0;}s:15:\"countdown_color\";s:0:\"\";s:16:\"subscribe_status\";i:0;s:14:\"subscribe_text\";s:40:\"Сообщить о готовности\";s:20:\"subscribe_text_color\";s:0:\"\";s:13:\"social_status\";i:0;s:13:\"social_target\";i:1;s:13:\"social_github\";s:0:\"\";s:15:\"social_dribbble\";s:0:\"\";s:14:\"social_twitter\";s:0:\"\";s:15:\"social_facebook\";s:0:\"\";s:16:\"social_instagram\";s:0:\"\";s:16:\"social_pinterest\";s:0:\"\";s:14:\"social_google+\";s:0:\"\";s:15:\"social_linkedin\";s:0:\"\";s:14:\"contact_status\";i:0;s:13:\"contact_email\";s:14:\"admin@example.com\";s:15:\"contact_effects\";s:20:\"move_top|move_bottom\";s:9:\"ga_status\";i:0;s:7:\"ga_code\";s:0:\"\";s:10:\"custom_css\";a:0:{}s:15:\"ga_anonymize_ip\";i:0;}s:3:\"bot\";a:6:{s:6:\"status\";i:0;s:4:\"name\";s:5:\"Admin\";s:6:\"avatar\";s:0:\"\";s:8:\"messages\";a:11:{s:2:\"01\";s:129:\"Привет! Меня зовут {bot_name}, я владелец этого сайта и буду рад помочь тебе.\";s:2:\"02\";s:57:\"У меня есть несколько вопросов.\";s:2:\"03\";s:27:\"Как тебя зовут?\";s:2:\"04\";s:38:\"Nice to meet you here, {visitor_name}!\";s:2:\"05\";s:83:\"Как видите, наш сайт будет открыт очень скоро!\";s:2:\"06\";s:198:\"Понимаю, что Вы очень хотели бы видеть всё уже прямо сейчас, но нам нужно ещё несколько дней, чтобы закончить.\";s:2:\"07\";s:63:\"Хотите быть первым, кто это увидит?\";s:4:\"08_1\";s:183:\"Здорово! Пожалуйста, оставьте свой e-mail здесь и я отправлю Вам сообщение, как только всё будет готово.\";s:4:\"08_2\";s:92:\"Очень жаль слышать это,  {visitor_name} :( Всего хорошего...\";s:2:\"09\";s:63:\"Понял! Спасибо и до скорой встречи!\";i:10;s:24:\"Хорошего дня!\";}s:9:\"responses\";a:4:{s:2:\"01\";s:44:\"Введите здесь Ваше имя...\";s:4:\"02_1\";s:42:\"Расскажи мне подробнее\";s:4:\"02_2\";s:12:\"Надоел\";s:2:\"03\";s:42:\"Укажите здесь Ваш e-mail...\";}s:10:\"custom_css\";a:0:{}}s:4:\"gdpr\";a:6:{s:6:\"status\";i:0;s:17:\"policy_page_label\";s:53:\"Политика конфиденциальности\";s:16:\"policy_page_link\";s:0:\"\";s:17:\"contact_form_tail\";s:475:\"Эта форма собирает Ваши имя и адрес электронной почты, чтобы у нас была возможность связаться с Вами. Пожалуйста, ознакомьтесь с нашей <a href=\"#\">Политикой конфиденциальности</a>, чтобы полностью понимать, как мы защищаем и управляем предоставленными Вами данными.\";s:19:\"subscribe_form_tail\";s:496:\"Эта форма собирает Ваш адрес электронной почты, чтобы у нас была возможность добавить его в наш список рассылки. Пожалуйста, ознакомьтесь с нашей <a href=\"#\">Политикой конфиденциальности</a>, чтобы полностью понимать, как мы защищаем и управляем предоставленными Вами данными.\";s:18:\"policy_page_target\";i:0;}}', 'yes'),
(189, 'wpmm_version', '2.1.2', 'yes'),
(924, 'db_upgraded', '', 'yes'),
(934, 'capsman_backup', 'a:5:{s:13:\"administrator\";a:2:{s:4:\"name\";s:13:\"Administrator\";s:12:\"capabilities\";a:62:{s:13:\"switch_themes\";b:1;s:11:\"edit_themes\";b:1;s:16:\"activate_plugins\";b:1;s:12:\"edit_plugins\";b:1;s:10:\"edit_users\";b:1;s:10:\"edit_files\";b:1;s:14:\"manage_options\";b:1;s:17:\"moderate_comments\";b:1;s:17:\"manage_categories\";b:1;s:12:\"manage_links\";b:1;s:12:\"upload_files\";b:1;s:6:\"import\";b:1;s:15:\"unfiltered_html\";b:1;s:10:\"edit_posts\";b:1;s:17:\"edit_others_posts\";b:1;s:20:\"edit_published_posts\";b:1;s:13:\"publish_posts\";b:1;s:10:\"edit_pages\";b:1;s:4:\"read\";b:1;s:8:\"level_10\";b:1;s:7:\"level_9\";b:1;s:7:\"level_8\";b:1;s:7:\"level_7\";b:1;s:7:\"level_6\";b:1;s:7:\"level_5\";b:1;s:7:\"level_4\";b:1;s:7:\"level_3\";b:1;s:7:\"level_2\";b:1;s:7:\"level_1\";b:1;s:7:\"level_0\";b:1;s:17:\"edit_others_pages\";b:1;s:20:\"edit_published_pages\";b:1;s:13:\"publish_pages\";b:1;s:12:\"delete_pages\";b:1;s:19:\"delete_others_pages\";b:1;s:22:\"delete_published_pages\";b:1;s:12:\"delete_posts\";b:1;s:19:\"delete_others_posts\";b:1;s:22:\"delete_published_posts\";b:1;s:20:\"delete_private_posts\";b:1;s:18:\"edit_private_posts\";b:1;s:18:\"read_private_posts\";b:1;s:20:\"delete_private_pages\";b:1;s:18:\"edit_private_pages\";b:1;s:18:\"read_private_pages\";b:1;s:12:\"delete_users\";b:1;s:12:\"create_users\";b:1;s:17:\"unfiltered_upload\";b:1;s:14:\"edit_dashboard\";b:1;s:14:\"update_plugins\";b:1;s:14:\"delete_plugins\";b:1;s:15:\"install_plugins\";b:1;s:13:\"update_themes\";b:1;s:14:\"install_themes\";b:1;s:11:\"update_core\";b:1;s:10:\"list_users\";b:1;s:12:\"remove_users\";b:1;s:13:\"promote_users\";b:1;s:18:\"edit_theme_options\";b:1;s:13:\"delete_themes\";b:1;s:6:\"export\";b:1;s:19:\"manage_capabilities\";b:1;}}s:6:\"editor\";a:2:{s:4:\"name\";s:6:\"Editor\";s:12:\"capabilities\";a:34:{s:17:\"moderate_comments\";b:1;s:17:\"manage_categories\";b:1;s:12:\"manage_links\";b:1;s:12:\"upload_files\";b:1;s:15:\"unfiltered_html\";b:1;s:10:\"edit_posts\";b:1;s:17:\"edit_others_posts\";b:1;s:20:\"edit_published_posts\";b:1;s:13:\"publish_posts\";b:1;s:10:\"edit_pages\";b:1;s:4:\"read\";b:1;s:7:\"level_7\";b:1;s:7:\"level_6\";b:1;s:7:\"level_5\";b:1;s:7:\"level_4\";b:1;s:7:\"level_3\";b:1;s:7:\"level_2\";b:1;s:7:\"level_1\";b:1;s:7:\"level_0\";b:1;s:17:\"edit_others_pages\";b:1;s:20:\"edit_published_pages\";b:1;s:13:\"publish_pages\";b:1;s:12:\"delete_pages\";b:1;s:19:\"delete_others_pages\";b:1;s:22:\"delete_published_pages\";b:1;s:12:\"delete_posts\";b:1;s:19:\"delete_others_posts\";b:1;s:22:\"delete_published_posts\";b:1;s:20:\"delete_private_posts\";b:1;s:18:\"edit_private_posts\";b:1;s:18:\"read_private_posts\";b:1;s:20:\"delete_private_pages\";b:1;s:18:\"edit_private_pages\";b:1;s:18:\"read_private_pages\";b:1;}}s:6:\"author\";a:2:{s:4:\"name\";s:6:\"Author\";s:12:\"capabilities\";a:10:{s:12:\"upload_files\";b:1;s:10:\"edit_posts\";b:1;s:20:\"edit_published_posts\";b:1;s:13:\"publish_posts\";b:1;s:4:\"read\";b:1;s:7:\"level_2\";b:1;s:7:\"level_1\";b:1;s:7:\"level_0\";b:1;s:12:\"delete_posts\";b:1;s:22:\"delete_published_posts\";b:1;}}s:11:\"contributor\";a:2:{s:4:\"name\";s:11:\"Contributor\";s:12:\"capabilities\";a:5:{s:10:\"edit_posts\";b:1;s:4:\"read\";b:1;s:7:\"level_1\";b:1;s:7:\"level_0\";b:1;s:12:\"delete_posts\";b:1;}}s:10:\"subscriber\";a:2:{s:4:\"name\";s:10:\"Subscriber\";s:12:\"capabilities\";a:2:{s:4:\"read\";b:1;s:7:\"level_0\";b:1;}}}', 'no'),
(935, 'capsman_backup_datestamp', '1547819438', 'no'),
(1506, 'activity_log_db_version', '1.0', 'yes'),
(1507, 'activity-log-settings', 'a:1:{s:13:\"logs_lifespan\";s:2:\"30\";}', 'yes'),
(1520, 'crontrol_schedules', 'a:0:{}', 'yes'),
(1534, 'intervalo_cron', '100', 'yes'),
(21973, 'widget_block', 'a:1:{s:12:\"_multiwidget\";i:1;}', 'yes'),
(21975, 'recovery_keys', 'a:0:{}', 'yes'),
(21976, 'https_detection_errors', 'a:1:{s:19:\"bad_response_source\";a:1:{i:0;s:55:\"It looks like the response did not come from this site.\";}}', 'yes'),
(21979, 'finished_updating_comment_type', '1', 'yes'),
(21981, 'admin_email_lifespan', '1645027238', 'yes'),
(21982, 'disallowed_keys', '', 'no'),
(21983, 'comment_previously_approved', '1', 'yes'),
(21984, 'auto_plugin_theme_update_emails', 'a:0:{}', 'no'),
(21985, 'auto_update_core_dev', 'enabled', 'yes'),
(21986, 'auto_update_core_minor', 'enabled', 'yes'),
(21987, 'auto_update_core_major', 'unset', 'yes'),
(21988, 'wp_force_deactivated_plugins', 'a:0:{}', 'yes'),
(21994, 'can_compress_scripts', '1', 'no'),
(22035, '_transient_health-check-site-status-result', '{\"good\":13,\"recommended\":3,\"critical\":3}', 'yes'),
(22080, '_site_transient_timeout_theme_roots', '1717891468', 'no'),
(22081, '_site_transient_theme_roots', 'a:1:{s:12:\"cactus-child\";s:7:\"/themes\";}', 'no');
INSERT INTO `wp_options` (`option_id`, `option_name`, `option_value`, `autoload`) VALUES
(22087, '_site_transient_timeout_php_check_7b25cf783e34adba01c24f953d6fff79', '1718494473', 'no'),
(22088, '_site_transient_php_check_7b25cf783e34adba01c24f953d6fff79', 'a:5:{s:19:\"recommended_version\";s:3:\"7.4\";s:15:\"minimum_version\";s:3:\"7.0\";s:12:\"is_supported\";b:1;s:9:\"is_secure\";b:1;s:13:\"is_acceptable\";b:1;}', 'no');

-- --------------------------------------------------------

--
-- Структура таблицы `wp_postmeta`
--

CREATE TABLE `wp_postmeta` (
  `meta_id` bigint UNSIGNED NOT NULL,
  `post_id` bigint UNSIGNED NOT NULL DEFAULT '0',
  `meta_key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `wp_postmeta`
--

INSERT INTO `wp_postmeta` (`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES
(45, 11, '_menu_item_type', 'custom'),
(46, 11, '_menu_item_menu_item_parent', '0'),
(47, 11, '_menu_item_object_id', '11'),
(48, 11, '_menu_item_object', 'custom'),
(49, 11, '_menu_item_target', ''),
(50, 11, '_menu_item_classes', 'a:1:{i:0;s:0:\"\";}'),
(51, 11, '_menu_item_xfn', ''),
(52, 11, '_menu_item_url', '#bawloginout#'),
(53, 351, '_wp_attached_file', '2017/09/cropped-COIcon-1-1.png'),
(54, 351, '_wp_attachment_metadata', 'a:5:{s:5:\"width\";i:512;s:6:\"height\";i:512;s:4:\"file\";s:30:\"2017/09/cropped-COIcon-1-1.png\";s:5:\"sizes\";a:2:{s:9:\"thumbnail\";a:4:{s:4:\"file\";s:30:\"cropped-COIcon-1-1-150x150.png\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:9:\"image/png\";}s:6:\"medium\";a:4:{s:4:\"file\";s:30:\"cropped-COIcon-1-1-300x300.png\";s:5:\"width\";i:300;s:6:\"height\";i:300;s:9:\"mime-type\";s:9:\"image/png\";}}s:10:\"image_meta\";a:12:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}}}'),
(55, 351, '_wp_attachment_context', 'site-icon'),
(81, 653, '_edit_last', '1'),
(82, 653, '_wp_page_template', 'page-templates/main.php'),
(107, 578, '_edit_last', '1'),
(108, 578, '_wp_page_template', 'page-templates/articles.php'),
(111, 582, '_edit_last', '1'),
(112, 582, '_wp_page_template', 'page-templates/articles_create.php'),
(113, 584, '_edit_last', '1'),
(114, 584, '_wp_page_template', 'page-templates/articles_view.php'),
(119, 597, '_edit_last', '1'),
(120, 597, '_wp_page_template', 'page-templates/experts.php'),
(128, 605, '_edit_last', '1'),
(129, 605, '_wp_page_template', 'default'),
(130, 617, '_edit_last', '1'),
(131, 617, '_wp_page_template', 'page-templates/experts_view.php'),
(196, 663, '_menu_item_type', 'post_type'),
(197, 663, '_menu_item_menu_item_parent', '0'),
(198, 663, '_menu_item_object_id', '578'),
(199, 663, '_menu_item_object', 'page'),
(200, 663, '_menu_item_target', ''),
(201, 663, '_menu_item_classes', 'a:1:{i:0;s:0:\"\";}'),
(202, 663, '_menu_item_xfn', ''),
(203, 663, '_menu_item_url', ''),
(228, 667, '_menu_item_type', 'post_type'),
(229, 667, '_menu_item_menu_item_parent', '0'),
(230, 667, '_menu_item_object_id', '597'),
(231, 667, '_menu_item_object', 'page'),
(232, 667, '_menu_item_target', ''),
(233, 667, '_menu_item_classes', 'a:1:{i:0;s:0:\"\";}'),
(234, 667, '_menu_item_xfn', ''),
(235, 667, '_menu_item_url', ''),
(316, 653, '_edit_lock', '1549379750:1'),
(319, 686, '_wp_attached_file', '2018/10/cropped-cropped-COIcon-1-300x300.png'),
(320, 686, '_wp_attachment_context', 'site-icon'),
(321, 686, '_wp_attachment_metadata', 'a:5:{s:5:\"width\";i:512;s:6:\"height\";i:512;s:4:\"file\";s:44:\"2018/10/cropped-cropped-COIcon-1-300x300.png\";s:5:\"sizes\";a:6:{s:9:\"thumbnail\";a:4:{s:4:\"file\";s:44:\"cropped-cropped-COIcon-1-300x300-150x150.png\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:9:\"image/png\";}s:6:\"medium\";a:4:{s:4:\"file\";s:44:\"cropped-cropped-COIcon-1-300x300-300x300.png\";s:5:\"width\";i:300;s:6:\"height\";i:300;s:9:\"mime-type\";s:9:\"image/png\";}s:13:\"site_icon-270\";a:4:{s:4:\"file\";s:44:\"cropped-cropped-COIcon-1-300x300-270x270.png\";s:5:\"width\";i:270;s:6:\"height\";i:270;s:9:\"mime-type\";s:9:\"image/png\";}s:13:\"site_icon-192\";a:4:{s:4:\"file\";s:44:\"cropped-cropped-COIcon-1-300x300-192x192.png\";s:5:\"width\";i:192;s:6:\"height\";i:192;s:9:\"mime-type\";s:9:\"image/png\";}s:13:\"site_icon-180\";a:4:{s:4:\"file\";s:44:\"cropped-cropped-COIcon-1-300x300-180x180.png\";s:5:\"width\";i:180;s:6:\"height\";i:180;s:9:\"mime-type\";s:9:\"image/png\";}s:12:\"site_icon-32\";a:4:{s:4:\"file\";s:42:\"cropped-cropped-COIcon-1-300x300-32x32.png\";s:5:\"width\";i:32;s:6:\"height\";i:32;s:9:\"mime-type\";s:9:\"image/png\";}}s:10:\"image_meta\";a:12:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}}}'),
(333, 584, '_edit_lock', '1541499524:1'),
(334, 578, '_edit_lock', '1541483604:1'),
(335, 582, '_edit_lock', '1541499535:1'),
(340, 697, '_wp_attached_file', '2018/10/COLogo_tr.png'),
(341, 697, '_wp_attachment_metadata', 'a:5:{s:5:\"width\";i:165;s:6:\"height\";i:50;s:4:\"file\";s:21:\"2018/10/COLogo_tr.png\";s:5:\"sizes\";a:1:{s:9:\"thumbnail\";a:4:{s:4:\"file\";s:20:\"COLogo_tr-150x50.png\";s:5:\"width\";i:150;s:6:\"height\";i:50;s:9:\"mime-type\";s:9:\"image/png\";}}s:10:\"image_meta\";a:12:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}}}'),
(342, 698, '_wp_attached_file', '2018/10/COIcon.png'),
(343, 698, '_wp_attachment_metadata', 'a:5:{s:5:\"width\";i:512;s:6:\"height\";i:512;s:4:\"file\";s:18:\"2018/10/COIcon.png\";s:5:\"sizes\";a:2:{s:9:\"thumbnail\";a:4:{s:4:\"file\";s:18:\"COIcon-150x150.png\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:9:\"image/png\";}s:6:\"medium\";a:4:{s:4:\"file\";s:18:\"COIcon-300x300.png\";s:5:\"width\";i:300;s:6:\"height\";i:300;s:9:\"mime-type\";s:9:\"image/png\";}}s:10:\"image_meta\";a:12:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}}}'),
(344, 699, '_wp_attached_file', '2018/10/cropped-COIcon.png'),
(345, 699, '_wp_attachment_context', 'site-icon'),
(346, 699, '_wp_attachment_metadata', 'a:5:{s:5:\"width\";i:512;s:6:\"height\";i:512;s:4:\"file\";s:26:\"2018/10/cropped-COIcon.png\";s:5:\"sizes\";a:6:{s:9:\"thumbnail\";a:4:{s:4:\"file\";s:26:\"cropped-COIcon-150x150.png\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:9:\"image/png\";}s:6:\"medium\";a:4:{s:4:\"file\";s:26:\"cropped-COIcon-300x300.png\";s:5:\"width\";i:300;s:6:\"height\";i:300;s:9:\"mime-type\";s:9:\"image/png\";}s:13:\"site_icon-270\";a:4:{s:4:\"file\";s:26:\"cropped-COIcon-270x270.png\";s:5:\"width\";i:270;s:6:\"height\";i:270;s:9:\"mime-type\";s:9:\"image/png\";}s:13:\"site_icon-192\";a:4:{s:4:\"file\";s:26:\"cropped-COIcon-192x192.png\";s:5:\"width\";i:192;s:6:\"height\";i:192;s:9:\"mime-type\";s:9:\"image/png\";}s:13:\"site_icon-180\";a:4:{s:4:\"file\";s:26:\"cropped-COIcon-180x180.png\";s:5:\"width\";i:180;s:6:\"height\";i:180;s:9:\"mime-type\";s:9:\"image/png\";}s:12:\"site_icon-32\";a:4:{s:4:\"file\";s:24:\"cropped-COIcon-32x32.png\";s:5:\"width\";i:32;s:6:\"height\";i:32;s:9:\"mime-type\";s:9:\"image/png\";}}s:10:\"image_meta\";a:12:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}}}'),
(349, 702, '_menu_item_type', 'post_type'),
(350, 702, '_menu_item_menu_item_parent', '0'),
(351, 702, '_menu_item_object_id', '653'),
(352, 702, '_menu_item_object', 'page'),
(353, 702, '_menu_item_target', ''),
(354, 702, '_menu_item_classes', 'a:1:{i:0;s:0:\"\";}'),
(355, 702, '_menu_item_xfn', ''),
(356, 702, '_menu_item_url', ''),
(427, 597, '_edit_lock', '1548937650:1'),
(428, 617, '_edit_lock', '1541530568:1'),
(429, 734, '_edit_lock', '1541529211:1'),
(430, 734, '_edit_last', '1'),
(431, 734, '_wp_page_template', 'page-templates/experts_create.php'),
(432, 736, '_edit_lock', '1541545083:1'),
(433, 736, '_edit_last', '1'),
(434, 736, '_wp_page_template', 'page-templates/experts_edit.php'),
(435, 605, '_edit_lock', '1541529184:1'),
(459, 743, '_edit_lock', '1541545390:1'),
(460, 743, '_edit_last', '1'),
(461, 743, '_wp_page_template', 'page-templates/articles_edit.php'),
(471, 750, '_edit_lock', '1551196664:1'),
(472, 750, '_edit_last', '1'),
(473, 750, '_wp_page_template', 'page-templates/reviews.php'),
(474, 754, '_edit_lock', '1541733437:1'),
(475, 754, '_edit_last', '1'),
(476, 754, '_wp_page_template', 'page-templates/reviews_edit.php'),
(477, 758, '_wp_attached_file', '2019/01/logo_eng.png'),
(478, 758, '_wp_attachment_metadata', 'a:5:{s:5:\"width\";i:375;s:6:\"height\";i:332;s:4:\"file\";s:20:\"2019/01/logo_eng.png\";s:5:\"sizes\";a:2:{s:9:\"thumbnail\";a:4:{s:4:\"file\";s:20:\"logo_eng-150x150.png\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:9:\"image/png\";}s:6:\"medium\";a:4:{s:4:\"file\";s:20:\"logo_eng-300x266.png\";s:5:\"width\";i:300;s:6:\"height\";i:266;s:9:\"mime-type\";s:9:\"image/png\";}}s:10:\"image_meta\";a:12:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}}}'),
(479, 759, '_wp_attached_file', '2019/01/cropped-logo_eng.png'),
(480, 759, '_wp_attachment_context', 'custom-logo'),
(481, 759, '_wp_attachment_metadata', 'a:5:{s:5:\"width\";i:56;s:6:\"height\";i:50;s:4:\"file\";s:28:\"2019/01/cropped-logo_eng.png\";s:5:\"sizes\";a:0:{}s:10:\"image_meta\";a:12:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}}}'),
(484, 762, '_wp_attached_file', '2019/01/cropped-logo_eng-1.png'),
(485, 762, '_wp_attachment_context', 'site-icon'),
(486, 762, '_wp_attachment_metadata', 'a:5:{s:5:\"width\";i:512;s:6:\"height\";i:512;s:4:\"file\";s:30:\"2019/01/cropped-logo_eng-1.png\";s:5:\"sizes\";a:6:{s:9:\"thumbnail\";a:4:{s:4:\"file\";s:30:\"cropped-logo_eng-1-150x150.png\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:9:\"image/png\";}s:6:\"medium\";a:4:{s:4:\"file\";s:30:\"cropped-logo_eng-1-300x300.png\";s:5:\"width\";i:300;s:6:\"height\";i:300;s:9:\"mime-type\";s:9:\"image/png\";}s:13:\"site_icon-270\";a:4:{s:4:\"file\";s:30:\"cropped-logo_eng-1-270x270.png\";s:5:\"width\";i:270;s:6:\"height\";i:270;s:9:\"mime-type\";s:9:\"image/png\";}s:13:\"site_icon-192\";a:4:{s:4:\"file\";s:30:\"cropped-logo_eng-1-192x192.png\";s:5:\"width\";i:192;s:6:\"height\";i:192;s:9:\"mime-type\";s:9:\"image/png\";}s:13:\"site_icon-180\";a:4:{s:4:\"file\";s:30:\"cropped-logo_eng-1-180x180.png\";s:5:\"width\";i:180;s:6:\"height\";i:180;s:9:\"mime-type\";s:9:\"image/png\";}s:12:\"site_icon-32\";a:4:{s:4:\"file\";s:28:\"cropped-logo_eng-1-32x32.png\";s:5:\"width\";i:32;s:6:\"height\";i:32;s:9:\"mime-type\";s:9:\"image/png\";}}s:10:\"image_meta\";a:12:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}}}'),
(487, 763, '_wp_attached_file', '2019/01/logo_eng-1.png'),
(488, 763, '_wp_attachment_metadata', 'a:5:{s:5:\"width\";i:375;s:6:\"height\";i:332;s:4:\"file\";s:22:\"2019/01/logo_eng-1.png\";s:5:\"sizes\";a:2:{s:9:\"thumbnail\";a:4:{s:4:\"file\";s:22:\"logo_eng-1-150x150.png\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:9:\"image/png\";}s:6:\"medium\";a:4:{s:4:\"file\";s:22:\"logo_eng-1-300x266.png\";s:5:\"width\";i:300;s:6:\"height\";i:266;s:9:\"mime-type\";s:9:\"image/png\";}}s:10:\"image_meta\";a:12:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}}}'),
(490, 765, '_wp_attached_file', '2019/01/logo_eng_small.png'),
(491, 765, '_wp_attachment_metadata', 'a:5:{s:5:\"width\";i:75;s:6:\"height\";i:75;s:4:\"file\";s:26:\"2019/01/logo_eng_small.png\";s:5:\"sizes\";a:0:{}s:10:\"image_meta\";a:12:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}}}'),
(492, 766, '_wp_attached_file', '2019/01/cropped-logo_eng_small.png'),
(493, 766, '_wp_attachment_context', 'site-icon'),
(494, 766, '_wp_attachment_metadata', 'a:5:{s:5:\"width\";i:512;s:6:\"height\";i:512;s:4:\"file\";s:34:\"2019/01/cropped-logo_eng_small.png\";s:5:\"sizes\";a:6:{s:9:\"thumbnail\";a:4:{s:4:\"file\";s:34:\"cropped-logo_eng_small-150x150.png\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:9:\"image/png\";}s:6:\"medium\";a:4:{s:4:\"file\";s:34:\"cropped-logo_eng_small-300x300.png\";s:5:\"width\";i:300;s:6:\"height\";i:300;s:9:\"mime-type\";s:9:\"image/png\";}s:13:\"site_icon-270\";a:4:{s:4:\"file\";s:34:\"cropped-logo_eng_small-270x270.png\";s:5:\"width\";i:270;s:6:\"height\";i:270;s:9:\"mime-type\";s:9:\"image/png\";}s:13:\"site_icon-192\";a:4:{s:4:\"file\";s:34:\"cropped-logo_eng_small-192x192.png\";s:5:\"width\";i:192;s:6:\"height\";i:192;s:9:\"mime-type\";s:9:\"image/png\";}s:13:\"site_icon-180\";a:4:{s:4:\"file\";s:34:\"cropped-logo_eng_small-180x180.png\";s:5:\"width\";i:180;s:6:\"height\";i:180;s:9:\"mime-type\";s:9:\"image/png\";}s:12:\"site_icon-32\";a:4:{s:4:\"file\";s:32:\"cropped-logo_eng_small-32x32.png\";s:5:\"width\";i:32;s:6:\"height\";i:32;s:9:\"mime-type\";s:9:\"image/png\";}}s:10:\"image_meta\";a:12:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}}}'),
(495, 667, '_nav_menu_role', 'a:2:{i:0;s:13:\"administrator\";i:1;s:7:\"jeditor\";}'),
(509, 770, '_wp_attached_file', '2019/01/version-2-1.jpg'),
(510, 770, '_wp_attachment_metadata', 'a:5:{s:5:\"width\";i:602;s:6:\"height\";i:433;s:4:\"file\";s:23:\"2019/01/version-2-1.jpg\";s:5:\"sizes\";a:2:{s:9:\"thumbnail\";a:4:{s:4:\"file\";s:23:\"version-2-1-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:6:\"medium\";a:4:{s:4:\"file\";s:23:\"version-2-1-300x216.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:216;s:9:\"mime-type\";s:10:\"image/jpeg\";}}s:10:\"image_meta\";a:12:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}}}'),
(518, 774, '_wp_attached_file', '2019/01/cropped-logo_eng_small-1.png'),
(519, 774, '_wp_attachment_context', 'site-icon'),
(520, 774, '_wp_attachment_metadata', 'a:5:{s:5:\"width\";i:512;s:6:\"height\";i:512;s:4:\"file\";s:36:\"2019/01/cropped-logo_eng_small-1.png\";s:5:\"sizes\";a:6:{s:9:\"thumbnail\";a:4:{s:4:\"file\";s:36:\"cropped-logo_eng_small-1-150x150.png\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:9:\"image/png\";}s:6:\"medium\";a:4:{s:4:\"file\";s:36:\"cropped-logo_eng_small-1-300x300.png\";s:5:\"width\";i:300;s:6:\"height\";i:300;s:9:\"mime-type\";s:9:\"image/png\";}s:13:\"site_icon-270\";a:4:{s:4:\"file\";s:36:\"cropped-logo_eng_small-1-270x270.png\";s:5:\"width\";i:270;s:6:\"height\";i:270;s:9:\"mime-type\";s:9:\"image/png\";}s:13:\"site_icon-192\";a:4:{s:4:\"file\";s:36:\"cropped-logo_eng_small-1-192x192.png\";s:5:\"width\";i:192;s:6:\"height\";i:192;s:9:\"mime-type\";s:9:\"image/png\";}s:13:\"site_icon-180\";a:4:{s:4:\"file\";s:36:\"cropped-logo_eng_small-1-180x180.png\";s:5:\"width\";i:180;s:6:\"height\";i:180;s:9:\"mime-type\";s:9:\"image/png\";}s:12:\"site_icon-32\";a:4:{s:4:\"file\";s:34:\"cropped-logo_eng_small-1-32x32.png\";s:5:\"width\";i:32;s:6:\"height\";i:32;s:9:\"mime-type\";s:9:\"image/png\";}}s:10:\"image_meta\";a:12:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}}}'),
(526, 776, '_wp_attached_file', '2019/01/maintenance.jpg'),
(527, 776, '_wp_attachment_metadata', 'a:5:{s:5:\"width\";i:1920;s:6:\"height\";i:1080;s:4:\"file\";s:23:\"2019/01/maintenance.jpg\";s:5:\"sizes\";a:5:{s:9:\"thumbnail\";a:4:{s:4:\"file\";s:23:\"maintenance-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:6:\"medium\";a:4:{s:4:\"file\";s:23:\"maintenance-300x169.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:169;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:12:\"medium_large\";a:4:{s:4:\"file\";s:23:\"maintenance-768x432.jpg\";s:5:\"width\";i:768;s:6:\"height\";i:432;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:5:\"large\";a:4:{s:4:\"file\";s:24:\"maintenance-1024x576.jpg\";s:5:\"width\";i:1024;s:6:\"height\";i:576;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:21:\"cactus-featured-image\";a:4:{s:4:\"file\";s:23:\"maintenance-960x720.jpg\";s:5:\"width\";i:960;s:6:\"height\";i:720;s:9:\"mime-type\";s:10:\"image/jpeg\";}}s:10:\"image_meta\";a:12:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"1\";s:8:\"keywords\";a:0:{}}}'),
(531, 779, '_edit_lock', '1549206648:1'),
(532, 779, '_wp_page_template', 'page-templates/reminder.php'),
(539, 787, '_edit_lock', '1551196825:1'),
(540, 787, '_edit_last', '1'),
(541, 787, '_wp_page_template', 'page-templates/versions.php'),
(542, 789, '_edit_lock', '1551196879:1'),
(543, 789, '_wp_page_template', 'page-templates/versions_edit.php'),
(544, 789, '_edit_last', '1'),
(551, 796, '_wp_attached_file', '2019/01/cropped-logo_eng-1-1.png'),
(552, 796, '_wp_attachment_context', 'custom-logo'),
(553, 796, '_wp_attachment_metadata', 'a:5:{s:5:\"width\";i:332;s:6:\"height\";i:332;s:4:\"file\";s:32:\"2019/01/cropped-logo_eng-1-1.png\";s:5:\"sizes\";a:2:{s:9:\"thumbnail\";a:4:{s:4:\"file\";s:32:\"cropped-logo_eng-1-1-150x150.png\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:9:\"image/png\";}s:6:\"medium\";a:4:{s:4:\"file\";s:32:\"cropped-logo_eng-1-1-300x300.png\";s:5:\"width\";i:300;s:6:\"height\";i:300;s:9:\"mime-type\";s:9:\"image/png\";}}s:10:\"image_meta\";a:12:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}}}'),
(554, 798, '_edit_lock', '1572548203:1'),
(555, 798, '_wp_page_template', 'page-templates/service.php');

-- --------------------------------------------------------

--
-- Структура таблицы `wp_posts`
--

CREATE TABLE `wp_posts` (
  `ID` bigint UNSIGNED NOT NULL,
  `post_author` bigint UNSIGNED NOT NULL DEFAULT '0',
  `post_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_date_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `post_title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `post_excerpt` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `post_status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'publish',
  `comment_status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `ping_status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `post_password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `post_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `to_ping` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pinged` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `post_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_modified_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_content_filtered` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `post_parent` bigint UNSIGNED NOT NULL DEFAULT '0',
  `guid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `menu_order` int NOT NULL DEFAULT '0',
  `post_type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'post',
  `post_mime_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `comment_count` bigint NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `wp_posts`
--

INSERT INTO `wp_posts` (`ID`, `post_author`, `post_date`, `post_date_gmt`, `post_content`, `post_title`, `post_excerpt`, `post_status`, `comment_status`, `ping_status`, `post_password`, `post_name`, `to_ping`, `pinged`, `post_modified`, `post_modified_gmt`, `post_content_filtered`, `post_parent`, `guid`, `menu_order`, `post_type`, `post_mime_type`, `comment_count`) VALUES
(11, 1, '2018-10-19 20:47:46', '2018-10-19 17:47:46', '', 'Войти|Выйти', '', 'publish', 'closed', 'closed', '', '%d0%b2%d0%be%d0%b9%d1%82%d0%b8%d0%b2%d1%8b%d0%b9%d1%82%d0%b8', '', '', '2019-01-28 11:59:55', '2019-01-28 08:59:55', '', 0, 'http://localhost/COMaster2/2018/10/19/%d0%b2%d0%be%d0%b9%d1%82%d0%b8%d0%b2%d1%8b%d0%b9%d1%82%d0%b8/', 5, 'nav_menu_item', '', 0),
(296, 1, '2017-09-17 18:19:13', '2017-09-17 15:19:13', '.site-nav > div > ul > li a:hover {\n	background-color:#EEEEEE;\n}\n\n.site-nav li > ul {\n    width: auto;\n}\n', 'cactus', '', 'publish', 'closed', 'closed', '', 'cactus', '', '', '2017-09-17 18:19:13', '2017-09-17 15:19:13', '', 0, 'http://localhost/WordPress/2017/09/17/cactus/', 0, 'custom_css', '', 0),
(351, 1, '2017-09-18 00:52:07', '2017-09-17 21:52:07', 'http://localhost/COMaster2/wp-content/uploads/2017/09/cropped-COIcon-1-1.png', 'cropped-COIcon.png', '', 'inherit', 'open', 'closed', '', 'cropped-coicon-png', '', '', '2017-09-18 00:52:07', '2017-09-17 21:52:07', '', 0, 'http://localhost/COMaster2/wp-content/uploads/2017/09/cropped-COIcon-1-1.png', 0, 'attachment', 'image/png', 0),
(578, 1, '2017-10-26 23:44:45', '2017-10-26 20:44:45', '', 'Статьи', '', 'publish', 'closed', 'closed', '', 'articles', '', '', '2018-11-06 08:53:24', '2018-11-06 05:53:24', '', 0, 'http://89.186.234.80/COMaster/?page_id=578', 2, 'page', '', 0),
(582, 1, '2017-10-26 23:46:04', '2017-10-26 20:46:04', '', 'Создать', '', 'publish', 'closed', 'closed', '', 'create', '', '', '2018-11-06 13:18:55', '2018-11-06 10:18:55', '', 578, 'http://89.186.234.80/COMaster/?page_id=582', 1, 'page', '', 0),
(584, 1, '2017-10-26 23:47:11', '2017-10-26 20:47:11', '', 'Детальная информация', '', 'publish', 'closed', 'closed', '', 'view', '', '', '2018-11-06 13:18:44', '2018-11-06 10:18:44', '', 578, 'http://89.186.234.80/COMaster/?page_id=584', 2, 'page', '', 0),
(597, 1, '2017-10-27 19:37:37', '2017-10-27 16:37:37', '', 'Эксперты', '', 'publish', 'closed', 'closed', '', 'experts', '', '', '2019-01-31 15:27:30', '2019-01-31 12:27:30', '', 0, 'http://89.186.234.80/COMaster/?page_id=597', 3, 'page', '', 0),
(605, 1, '2017-10-27 19:42:29', '2017-10-27 16:42:29', '', 'Выплаты', '', 'publish', 'closed', 'closed', '', 'payment', '', '', '2018-11-06 21:33:04', '2018-11-06 18:33:04', '', 597, 'http://89.186.234.80/COMaster/?page_id=605', 4, 'page', '', 0),
(617, 1, '2017-10-30 03:39:21', '2017-10-30 00:39:21', '', 'Детальная информация', '', 'publish', 'closed', 'closed', '', 'view', '', '', '2018-11-06 21:56:08', '2018-11-06 18:56:08', '', 597, 'http://89.186.234.80/COMaster/?page_id=617', 2, 'page', '', 0),
(653, 1, '2017-09-16 12:19:58', '2017-09-16 09:19:58', '', 'Главная', '', 'publish', 'closed', 'closed', '', '%d0%b3%d0%bb%d0%b0%d0%b2%d0%bd%d0%b0%d1%8f-%d1%81%d1%82%d1%80%d0%b0%d0%bd%d0%b8%d1%86%d0%b0', '', '', '2018-10-24 23:06:41', '2018-10-24 20:06:41', '', 0, 'http://localhost/WordPress/?page_id=7', 1, 'page', '', 0),
(663, 1, '2018-10-19 20:47:49', '2018-10-19 17:47:49', '', 'Архив', '', 'publish', 'closed', 'closed', '', '663', '', '', '2019-01-28 11:59:55', '2019-01-28 08:59:55', '', 0, 'http://localhost/COMaster2/2018/10/19/663/', 3, 'nav_menu_item', '', 0),
(667, 1, '2018-10-19 20:47:50', '2018-10-19 17:47:50', ' ', '', '', 'publish', 'closed', 'closed', '', '667', '', '', '2019-01-28 11:59:55', '2019-01-28 08:59:55', '', 0, 'http://localhost/COMaster2/2018/10/19/667/', 2, 'nav_menu_item', '', 0),
(684, 1, '2018-10-20 22:47:48', '2018-10-20 19:47:48', '', 'Главная', '', 'inherit', 'closed', 'closed', '', '653-revision-v1', '', '', '2018-10-20 22:47:48', '2018-10-20 19:47:48', '', 653, 'http://localhost/COMaster2/2018/10/20/653-revision-v1/', 0, 'revision', '', 0),
(686, 1, '2018-10-20 22:50:07', '2018-10-20 19:50:07', 'http://localhost/COMaster2/wp-content/uploads/2018/10/cropped-cropped-COIcon-1-300x300.png', 'cropped-cropped-COIcon-1-300x300.png', '', 'inherit', 'open', 'closed', '', 'cropped-cropped-coicon-1-300x300-png', '', '', '2018-10-20 22:50:07', '2018-10-20 19:50:07', '', 0, 'http://localhost/COMaster2/wp-content/uploads/2018/10/cropped-cropped-COIcon-1-300x300.png', 0, 'attachment', 'image/png', 0),
(689, 1, '2018-10-22 22:10:07', '2018-10-22 19:10:07', '', 'Детальная информация', '', 'inherit', 'closed', 'closed', '', '584-revision-v1', '', '', '2018-10-22 22:10:07', '2018-10-22 19:10:07', '', 584, 'http://localhost/COMaster2/2018/10/22/584-revision-v1/', 0, 'revision', '', 0),
(690, 1, '2018-10-25 00:25:25', '2018-10-24 21:25:25', '', 'Детальная информация о статье', '', 'inherit', 'closed', 'closed', '', '584-revision-v1', '', '', '2018-10-25 00:25:25', '2018-10-24 21:25:25', '', 584, 'http://localhost/COMaster2/2018/10/25/584-revision-v1/', 0, 'revision', '', 0),
(691, 1, '2018-10-25 00:28:19', '2018-10-24 21:28:19', '', 'Статьи', '', 'inherit', 'closed', 'closed', '', '578-revision-v1', '', '', '2018-10-25 00:28:19', '2018-10-24 21:28:19', '', 578, 'http://localhost/COMaster2/2018/10/25/578-revision-v1/', 0, 'revision', '', 0),
(692, 1, '2018-10-25 00:29:34', '2018-10-24 21:29:34', '', 'Статьи', '', 'inherit', 'closed', 'closed', '', '578-autosave-v1', '', '', '2018-10-25 00:29:34', '2018-10-24 21:29:34', '', 578, 'http://localhost/COMaster2/2018/10/25/578-autosave-v1/', 0, 'revision', '', 0),
(693, 1, '2018-10-25 02:34:02', '2018-10-24 23:34:02', '', 'Создать статью', '', 'inherit', 'closed', 'closed', '', '582-revision-v1', '', '', '2018-10-25 02:34:02', '2018-10-24 23:34:02', '', 582, 'http://localhost/COMaster2/2018/10/25/582-revision-v1/', 0, 'revision', '', 0),
(697, 1, '2018-10-26 15:47:03', '2018-10-26 12:47:03', '', 'COLogo_tr', '', 'inherit', 'open', 'closed', '', 'cologo_tr', '', '', '2018-10-26 15:47:03', '2018-10-26 12:47:03', '', 0, 'http://localhost/COMaster2/wp-content/uploads/2018/10/COLogo_tr.png', 0, 'attachment', 'image/png', 0),
(698, 1, '2018-10-26 15:47:44', '2018-10-26 12:47:44', '', 'COIcon', '', 'inherit', 'open', 'closed', '', 'coicon', '', '', '2018-10-26 15:47:44', '2018-10-26 12:47:44', '', 0, 'http://localhost/COMaster2/wp-content/uploads/2018/10/COIcon.png', 0, 'attachment', 'image/png', 0),
(699, 1, '2018-10-26 15:47:48', '2018-10-26 12:47:48', 'http://localhost/COMaster2/wp-content/uploads/2018/10/cropped-COIcon.png', 'cropped-COIcon.png', '', 'inherit', 'open', 'closed', '', 'cropped-coicon-png-2', '', '', '2018-10-26 15:47:48', '2018-10-26 12:47:48', '', 0, 'http://localhost/COMaster2/wp-content/uploads/2018/10/cropped-COIcon.png', 0, 'attachment', 'image/png', 0),
(702, 1, '2018-11-01 07:53:12', '2018-11-01 04:53:12', ' ', '', '', 'publish', 'closed', 'closed', '', '702', '', '', '2019-01-28 11:59:55', '2019-01-28 08:59:55', '', 0, 'http://localhost/COMaster2/?p=702', 1, 'nav_menu_item', '', 0),
(706, 1, '2018-11-06 10:18:18', '2018-11-06 07:18:18', '', 'Детальная информация', '', 'inherit', 'closed', 'closed', '', '584-revision-v1', '', '', '2018-11-06 10:18:18', '2018-11-06 07:18:18', '', 584, 'http://localhost/COMaster2/2018/11/06/584-revision-v1/', 0, 'revision', '', 0),
(731, 1, '2018-11-06 10:37:23', '2018-11-06 07:37:23', '', 'Рецензенты', '', 'inherit', 'closed', 'closed', '', '597-revision-v1', '', '', '2018-11-06 10:37:23', '2018-11-06 07:37:23', '', 597, 'http://localhost/COMaster2/2018/11/06/597-revision-v1/', 0, 'revision', '', 0),
(732, 1, '2018-11-06 13:18:55', '2018-11-06 10:18:55', '', 'Создать', '', 'inherit', 'closed', 'closed', '', '582-revision-v1', '', '', '2018-11-06 13:18:55', '2018-11-06 10:18:55', '', 582, 'http://localhost/COMaster2/2018/11/06/582-revision-v1/', 0, 'revision', '', 0),
(733, 1, '2018-11-06 13:19:05', '2018-11-06 10:19:05', '', 'Детальная информация', '', 'inherit', 'closed', 'closed', '', '617-revision-v1', '', '', '2018-11-06 13:19:05', '2018-11-06 10:19:05', '', 617, 'http://localhost/COMaster2/2018/11/06/617-revision-v1/', 0, 'revision', '', 0),
(734, 1, '2018-11-06 13:19:36', '2018-11-06 10:19:36', '', 'Создать', '', 'publish', 'closed', 'closed', '', 'create', '', '', '2018-11-06 21:33:31', '2018-11-06 18:33:31', '', 597, 'http://localhost/COMaster2/?page_id=734', 1, 'page', '', 0),
(735, 1, '2018-11-06 13:19:36', '2018-11-06 10:19:36', '', 'Создать', '', 'inherit', 'closed', 'closed', '', '734-revision-v1', '', '', '2018-11-06 13:19:36', '2018-11-06 10:19:36', '', 734, 'http://localhost/COMaster2/2018/11/06/734-revision-v1/', 0, 'revision', '', 0),
(736, 1, '2018-11-06 21:32:49', '2018-11-06 18:32:49', '', 'Редактировать', '', 'publish', 'closed', 'closed', '', 'edit', '', '', '2018-11-07 02:00:23', '2018-11-06 23:00:23', '', 597, 'http://localhost/COMaster2/?page_id=736', 2, 'page', '', 0),
(737, 1, '2018-11-06 21:32:49', '2018-11-06 18:32:49', '', 'Редактировать', '', 'inherit', 'closed', 'closed', '', '736-revision-v1', '', '', '2018-11-06 21:32:49', '2018-11-06 18:32:49', '', 736, 'http://localhost/COMaster2/2018/11/06/736-revision-v1/', 0, 'revision', '', 0),
(738, 1, '2018-11-06 21:33:04', '2018-11-06 18:33:04', '', 'Выплаты', '', 'inherit', 'closed', 'closed', '', '605-revision-v1', '', '', '2018-11-06 21:33:04', '2018-11-06 18:33:04', '', 605, 'http://localhost/COMaster2/2018/11/06/605-revision-v1/', 0, 'revision', '', 0),
(743, 1, '2018-11-07 02:03:00', '2018-11-06 23:03:00', '', 'Редактировать', '', 'publish', 'closed', 'closed', '', 'edit', '', '', '2018-11-07 02:03:10', '2018-11-06 23:03:10', '', 578, 'http://localhost/COMaster2/?page_id=743', 3, 'page', '', 0),
(744, 1, '2018-11-07 02:03:00', '2018-11-06 23:03:00', '', 'Редактирование', '', 'inherit', 'closed', 'closed', '', '743-revision-v1', '', '', '2018-11-07 02:03:00', '2018-11-06 23:03:00', '', 743, 'http://localhost/COMaster2/2018/11/07/743-revision-v1/', 0, 'revision', '', 0),
(745, 1, '2018-11-07 02:03:10', '2018-11-06 23:03:10', '', 'Редактировать', '', 'inherit', 'closed', 'closed', '', '743-revision-v1', '', '', '2018-11-07 02:03:10', '2018-11-06 23:03:10', '', 743, 'http://localhost/COMaster2/2018/11/07/743-revision-v1/', 0, 'revision', '', 0),
(750, 1, '2018-11-09 06:13:38', '2018-11-09 03:13:38', '', 'Рецензии', '', 'publish', 'closed', 'closed', '', 'reviews', '', '', '2018-11-09 06:19:08', '2018-11-09 03:19:08', '', 0, 'http://localhost/COMaster2/?page_id=750', 4, 'page', '', 0),
(751, 1, '2018-11-09 06:13:38', '2018-11-09 03:13:38', '', 'Рецензенты', '', 'inherit', 'closed', 'closed', '', '750-revision-v1', '', '', '2018-11-09 06:13:38', '2018-11-09 03:13:38', '', 750, 'http://localhost/COMaster2/2018/11/09/750-revision-v1/', 0, 'revision', '', 0),
(753, 1, '2018-11-09 06:19:08', '2018-11-09 03:19:08', '', 'Рецензии', '', 'inherit', 'closed', 'closed', '', '750-revision-v1', '', '', '2018-11-09 06:19:08', '2018-11-09 03:19:08', '', 750, 'http://localhost/COMaster2/2018/11/09/750-revision-v1/', 0, 'revision', '', 0),
(754, 1, '2018-11-09 06:19:31', '2018-11-09 03:19:31', '', 'Редактировать', '', 'publish', 'closed', 'closed', '', 'edit', '', '', '2018-11-09 06:19:31', '2018-11-09 03:19:31', '', 750, 'http://localhost/COMaster2/?page_id=754', 1, 'page', '', 0),
(755, 1, '2018-11-09 06:19:31', '2018-11-09 03:19:31', '', 'Редактировать', '', 'inherit', 'closed', 'closed', '', '754-revision-v1', '', '', '2018-11-09 06:19:31', '2018-11-09 03:19:31', '', 754, 'http://localhost/COMaster2/2018/11/09/754-revision-v1/', 0, 'revision', '', 0),
(758, 1, '2019-01-24 10:52:51', '2019-01-24 07:52:51', '', 'logo_eng', '', 'inherit', 'open', 'closed', '', 'logo_eng', '', '', '2019-01-24 10:52:51', '2019-01-24 07:52:51', '', 0, 'http://89.186.234.80/COMaster2/wp-content/uploads/2019/01/logo_eng.png', 0, 'attachment', 'image/png', 0),
(759, 1, '2019-01-24 10:53:09', '2019-01-24 07:53:09', 'http://89.186.234.80/COMaster2/wp-content/uploads/2019/01/cropped-logo_eng.png', 'cropped-logo_eng.png', '', 'inherit', 'open', 'closed', '', 'cropped-logo_eng-png', '', '', '2019-01-24 10:53:09', '2019-01-24 07:53:09', '', 0, 'http://89.186.234.80/COMaster2/wp-content/uploads/2019/01/cropped-logo_eng.png', 0, 'attachment', 'image/png', 0),
(762, 1, '2019-01-28 00:40:10', '2019-01-27 21:40:10', 'http://89.186.234.80/COMaster2/wp-content/uploads/2019/01/cropped-logo_eng-1.png', 'cropped-logo_eng-1.png', '', 'inherit', 'open', 'closed', '', 'cropped-logo_eng-1-png', '', '', '2019-01-28 00:40:10', '2019-01-27 21:40:10', '', 0, 'http://89.186.234.80/COMaster2/wp-content/uploads/2019/01/cropped-logo_eng-1.png', 0, 'attachment', 'image/png', 0),
(763, 1, '2019-01-28 00:40:39', '2019-01-27 21:40:39', '', 'logo_eng', '', 'inherit', 'open', 'closed', '', 'logo_eng-2', '', '', '2019-01-28 00:40:39', '2019-01-27 21:40:39', '', 0, 'http://89.186.234.80/COMaster2/wp-content/uploads/2019/01/logo_eng-1.png', 0, 'attachment', 'image/png', 0),
(765, 1, '2019-01-28 00:42:57', '2019-01-27 21:42:57', '', 'logo_eng_small', '', 'inherit', 'open', 'closed', '', 'logo_eng_small', '', '', '2019-01-28 00:42:57', '2019-01-27 21:42:57', '', 0, 'http://89.186.234.80/COMaster2/wp-content/uploads/2019/01/logo_eng_small.png', 0, 'attachment', 'image/png', 0),
(766, 1, '2019-01-28 00:43:04', '2019-01-27 21:43:04', 'http://89.186.234.80/COMaster2/wp-content/uploads/2019/01/cropped-logo_eng_small.png', 'cropped-logo_eng_small.png', '', 'inherit', 'open', 'closed', '', 'cropped-logo_eng_small-png', '', '', '2019-01-28 00:43:04', '2019-01-27 21:43:04', '', 0, 'http://89.186.234.80/COMaster2/wp-content/uploads/2019/01/cropped-logo_eng_small.png', 0, 'attachment', 'image/png', 0),
(770, 1, '2019-01-28 13:51:19', '2019-01-28 10:51:19', '', 'version-2-1', '', 'inherit', 'open', 'closed', '', 'version-2-1', '', '', '2019-01-28 13:51:19', '2019-01-28 10:51:19', '', 0, 'http://89.186.234.80/COMaster2/wp-content/uploads/2019/01/version-2-1.jpg', 0, 'attachment', 'image/jpeg', 0),
(774, 1, '2019-01-30 18:36:51', '2019-01-30 15:36:51', 'http://89.186.234.80/COMaster2/wp-content/uploads/2019/01/cropped-logo_eng_small-1.png', 'cropped-logo_eng_small-1.png', '', 'inherit', 'open', 'closed', '', 'cropped-logo_eng_small-1-png', '', '', '2019-01-30 18:36:51', '2019-01-30 15:36:51', '', 0, 'http://89.186.234.80/COMaster2/wp-content/uploads/2019/01/cropped-logo_eng_small-1.png', 0, 'attachment', 'image/png', 0),
(776, 1, '2019-01-31 09:51:29', '2019-01-31 06:51:29', '', 'maintenance', '', 'inherit', 'open', 'closed', '', 'maintenance', '', '', '2019-01-31 09:51:29', '2019-01-31 06:51:29', '', 0, 'http://89.186.234.80/COMaster2/wp-content/uploads/2019/01/maintenance.jpg', 0, 'attachment', 'image/jpeg', 0),
(777, 1, '2019-01-31 15:27:30', '2019-01-31 12:27:30', '', 'Эксперты', '', 'inherit', 'closed', 'closed', '', '597-revision-v1', '', '', '2019-01-31 15:27:30', '2019-01-31 12:27:30', '', 597, 'http://89.186.234.80/COMaster2/2019/01/31/597-revision-v1/', 0, 'revision', '', 0),
(779, 1, '2019-02-03 17:49:16', '2019-02-03 14:49:16', '', 'Автонапоминание', '', 'publish', 'closed', 'closed', '', 'reminder', '', '', '2019-02-03 17:57:21', '2019-02-03 14:57:21', '', 0, 'http://89.186.234.80/COMaster2/?page_id=779', 10, 'page', '', 0),
(780, 1, '2019-02-03 17:49:16', '2019-02-03 14:49:16', '', 'Автонапоминание', '', 'inherit', 'closed', 'closed', '', '779-revision-v1', '', '', '2019-02-03 17:49:16', '2019-02-03 14:49:16', '', 779, 'http://89.186.234.80/COMaster2/2019/02/03/779-revision-v1/', 0, 'revision', '', 0),
(781, 1, '2019-02-03 17:50:36', '2019-02-03 14:50:36', '<!-- wp:code -->\n<pre class=\"wp-block-code\"><code>time();</code></pre>\n<!-- /wp:code -->\n\n<!-- wp:paragraph -->\n<p></p>\n<!-- /wp:paragraph -->', 'Автонапоминание', '', 'inherit', 'closed', 'closed', '', '779-revision-v1', '', '', '2019-02-03 17:50:36', '2019-02-03 14:50:36', '', 779, 'http://89.186.234.80/COMaster2/2019/02/03/779-revision-v1/', 0, 'revision', '', 0),
(782, 1, '2019-02-03 17:51:13', '2019-02-03 14:51:13', '<!-- wp:code -->\n<pre class=\"wp-block-code\"><code>time();</code></pre>\n<!-- /wp:code -->\n\n<!-- wp:preformatted -->\n<pre class=\"wp-block-preformatted\">asdasdasd</pre>\n<!-- /wp:preformatted -->', 'Автонапоминание', '', 'inherit', 'closed', 'closed', '', '779-revision-v1', '', '', '2019-02-03 17:51:13', '2019-02-03 14:51:13', '', 779, 'http://89.186.234.80/COMaster2/2019/02/03/779-revision-v1/', 0, 'revision', '', 0),
(784, 1, '2019-02-03 17:54:02', '2019-02-03 14:54:02', '', 'Автонапоминание', '', 'inherit', 'closed', 'closed', '', '779-revision-v1', '', '', '2019-02-03 17:54:02', '2019-02-03 14:54:02', '', 779, 'http://89.186.234.80/COMaster2/2019/02/03/779-revision-v1/', 0, 'revision', '', 0),
(787, 1, '2019-02-26 18:59:25', '2019-02-26 15:59:25', '', 'Версии', '', 'publish', 'closed', 'closed', '', 'versions', '', '', '2019-02-26 19:00:25', '2019-02-26 16:00:25', '', 0, 'http://89.186.234.80/COMaster2/?page_id=787', 5, 'page', '', 0),
(788, 1, '2019-02-26 18:59:25', '2019-02-26 15:59:25', '', 'Версии', '', 'inherit', 'closed', 'closed', '', '787-revision-v1', '', '', '2019-02-26 18:59:25', '2019-02-26 15:59:25', '', 787, 'http://89.186.234.80/COMaster2/2019/02/26/787-revision-v1/', 0, 'revision', '', 0),
(789, 1, '2019-02-26 19:00:52', '2019-02-26 16:00:52', '', 'Редактировать', '', 'publish', 'closed', 'closed', '', 'edit', '', '', '2019-02-26 19:01:19', '2019-02-26 16:01:19', '', 787, 'http://89.186.234.80/COMaster2/?page_id=789', 1, 'page', '', 0),
(790, 1, '2019-02-26 19:00:52', '2019-02-26 16:00:52', '', 'Редактирование', '', 'inherit', 'closed', 'closed', '', '789-revision-v1', '', '', '2019-02-26 19:00:52', '2019-02-26 16:00:52', '', 789, 'http://89.186.234.80/COMaster2/2019/02/26/789-revision-v1/', 0, 'revision', '', 0),
(791, 1, '2019-02-26 19:01:19', '2019-02-26 16:01:19', '', 'Редактировать', '', 'inherit', 'closed', 'closed', '', '789-revision-v1', '', '', '2019-02-26 19:01:19', '2019-02-26 16:01:19', '', 789, 'http://89.186.234.80/COMaster2/2019/02/26/789-revision-v1/', 0, 'revision', '', 0),
(796, 1, '2019-05-22 18:46:08', '2019-05-22 15:46:08', 'http://89.186.234.80/COMaster2/wp-content/uploads/2019/01/cropped-logo_eng-1-1.png', 'cropped-logo_eng-1-1.png', '', 'inherit', 'open', 'closed', '', 'cropped-logo_eng-1-1-png', '', '', '2019-05-22 18:46:08', '2019-05-22 15:46:08', '', 0, 'http://89.186.234.80/COMaster2/wp-content/uploads/2019/01/cropped-logo_eng-1-1.png', 0, 'attachment', 'image/png', 0),
(798, 1, '2019-10-31 21:59:22', '2019-10-31 18:59:22', '', 'Service', '', 'publish', 'closed', 'closed', '', 'service', '', '', '2019-10-31 21:59:22', '2019-10-31 18:59:22', '', 0, 'http://89.186.234.80/COMaster2/?page_id=798', 11, 'page', '', 0),
(799, 1, '2019-10-31 21:59:22', '2019-10-31 18:59:22', '', 'Service', '', 'inherit', 'closed', 'closed', '', '798-revision-v1', '', '', '2019-10-31 21:59:22', '2019-10-31 18:59:22', '', 798, 'http://89.186.234.80/COMaster2/2019/10/31/798-revision-v1/', 0, 'revision', '', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `wp_termmeta`
--

CREATE TABLE `wp_termmeta` (
  `meta_id` bigint UNSIGNED NOT NULL,
  `term_id` bigint UNSIGNED NOT NULL DEFAULT '0',
  `meta_key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `wp_terms`
--

CREATE TABLE `wp_terms` (
  `term_id` bigint UNSIGNED NOT NULL,
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `slug` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `term_group` bigint NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `wp_terms`
--

INSERT INTO `wp_terms` (`term_id`, `name`, `slug`, `term_group`) VALUES
(1, 'Без рубрики', '%d0%b1%d0%b5%d0%b7-%d1%80%d1%83%d0%b1%d1%80%d0%b8%d0%ba%d0%b8', 0),
(2, 'Боковое меню', '%d0%b1%d0%be%d0%ba%d0%be%d0%b2%d0%be%d0%b5-%d0%bc%d0%b5%d0%bd%d1%8e', 0),
(3, 'Верхнее меню', '%d0%b2%d0%b5%d1%80%d1%85%d0%bd%d0%b5%d0%b5-%d0%bc%d0%b5%d0%bd%d1%8e', 0),
(4, 'Меню социальных ссылок', '%d0%bc%d0%b5%d0%bd%d1%8e-%d1%81%d0%be%d1%86%d0%b8%d0%b0%d0%bb%d1%8c%d0%bd%d1%8b%d1%85-%d1%81%d1%81%d1%8b%d0%bb%d0%be%d0%ba', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `wp_term_relationships`
--

CREATE TABLE `wp_term_relationships` (
  `object_id` bigint UNSIGNED NOT NULL DEFAULT '0',
  `term_taxonomy_id` bigint UNSIGNED NOT NULL DEFAULT '0',
  `term_order` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `wp_term_relationships`
--

INSERT INTO `wp_term_relationships` (`object_id`, `term_taxonomy_id`, `term_order`) VALUES
(11, 3, 0),
(663, 3, 0),
(667, 3, 0),
(702, 3, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `wp_term_taxonomy`
--

CREATE TABLE `wp_term_taxonomy` (
  `term_taxonomy_id` bigint UNSIGNED NOT NULL,
  `term_id` bigint UNSIGNED NOT NULL DEFAULT '0',
  `taxonomy` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent` bigint UNSIGNED NOT NULL DEFAULT '0',
  `count` bigint NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `wp_term_taxonomy`
--

INSERT INTO `wp_term_taxonomy` (`term_taxonomy_id`, `term_id`, `taxonomy`, `description`, `parent`, `count`) VALUES
(1, 1, 'category', '', 0, 0),
(2, 2, 'nav_menu', '', 0, 0),
(3, 3, 'nav_menu', '', 0, 4),
(4, 4, 'nav_menu', '', 0, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `wp_usermeta`
--

CREATE TABLE `wp_usermeta` (
  `umeta_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL DEFAULT '0',
  `meta_key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `wp_users`
--

CREATE TABLE `wp_users` (
  `ID` bigint UNSIGNED NOT NULL,
  `user_login` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `user_pass` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `user_nicename` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `user_email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `user_url` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `user_registered` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_activation_key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `user_status` int NOT NULL DEFAULT '0',
  `display_name` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `wp_wpmm_subscribers`
--

CREATE TABLE `wp_wpmm_subscribers` (
  `id_subscriber` bigint NOT NULL,
  `email` varchar(50) NOT NULL,
  `insert_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `wp_ab_articles`
--
ALTER TABLE `wp_ab_articles`
  ADD PRIMARY KEY (`ID_Article`),
  ADD KEY `Relation_2` (`ID_Issue`),
  ADD KEY `Relation_3` (`ID_Section`),
  ADD KEY `Relation_11` (`ID_CorAuthor`);

--
-- Индексы таблицы `wp_ab_chats`
--
ALTER TABLE `wp_ab_chats`
  ADD PRIMARY KEY (`ID_Chat`),
  ADD KEY `CRelation_1` (`ID_User`);

--
-- Индексы таблицы `wp_ab_experts`
--
ALTER TABLE `wp_ab_experts`
  ADD PRIMARY KEY (`ID_Expert`),
  ADD UNIQUE KEY `Mail` (`Mail`);

--
-- Индексы таблицы `wp_ab_issues`
--
ALTER TABLE `wp_ab_issues`
  ADD PRIMARY KEY (`ID_Issue`);

--
-- Индексы таблицы `wp_ab_letters`
--
ALTER TABLE `wp_ab_letters`
  ADD PRIMARY KEY (`ID_Letter`);

--
-- Индексы таблицы `wp_ab_reviews`
--
ALTER TABLE `wp_ab_reviews`
  ADD PRIMARY KEY (`ID_Review`),
  ADD KEY `Relation_4` (`ID_Article`),
  ADD KEY `Relation_5` (`ID_Expert`),
  ADD KEY `Relation_6` (`ID_Verdict`);

--
-- Индексы таблицы `wp_ab_sections`
--
ALTER TABLE `wp_ab_sections`
  ADD PRIMARY KEY (`ID_Section`),
  ADD KEY `Relation_1` (`ID_Editor`);

--
-- Индексы таблицы `wp_ab_verdicts`
--
ALTER TABLE `wp_ab_verdicts`
  ADD PRIMARY KEY (`ID_Verdict`);

--
-- Индексы таблицы `wp_ab_versions`
--
ALTER TABLE `wp_ab_versions`
  ADD PRIMARY KEY (`ID_Version`),
  ADD KEY `Relation_9` (`ID_Article`);

--
-- Индексы таблицы `wp_aryo_activity_log`
--
ALTER TABLE `wp_aryo_activity_log`
  ADD PRIMARY KEY (`histid`);

--
-- Индексы таблицы `wp_commentmeta`
--
ALTER TABLE `wp_commentmeta`
  ADD PRIMARY KEY (`meta_id`),
  ADD KEY `comment_id` (`comment_id`),
  ADD KEY `meta_key` (`meta_key`(191));

--
-- Индексы таблицы `wp_comments`
--
ALTER TABLE `wp_comments`
  ADD PRIMARY KEY (`comment_ID`),
  ADD KEY `comment_post_ID` (`comment_post_ID`),
  ADD KEY `comment_approved_date_gmt` (`comment_approved`,`comment_date_gmt`),
  ADD KEY `comment_date_gmt` (`comment_date_gmt`),
  ADD KEY `comment_parent` (`comment_parent`),
  ADD KEY `comment_author_email` (`comment_author_email`(10));

--
-- Индексы таблицы `wp_links`
--
ALTER TABLE `wp_links`
  ADD PRIMARY KEY (`link_id`),
  ADD KEY `link_visible` (`link_visible`);

--
-- Индексы таблицы `wp_options`
--
ALTER TABLE `wp_options`
  ADD PRIMARY KEY (`option_id`),
  ADD UNIQUE KEY `option_name` (`option_name`),
  ADD KEY `autoload` (`autoload`);

--
-- Индексы таблицы `wp_postmeta`
--
ALTER TABLE `wp_postmeta`
  ADD PRIMARY KEY (`meta_id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `meta_key` (`meta_key`(191));

--
-- Индексы таблицы `wp_posts`
--
ALTER TABLE `wp_posts`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `post_name` (`post_name`(191)),
  ADD KEY `type_status_date` (`post_type`,`post_status`,`post_date`,`ID`),
  ADD KEY `post_parent` (`post_parent`),
  ADD KEY `post_author` (`post_author`);

--
-- Индексы таблицы `wp_termmeta`
--
ALTER TABLE `wp_termmeta`
  ADD PRIMARY KEY (`meta_id`),
  ADD KEY `term_id` (`term_id`),
  ADD KEY `meta_key` (`meta_key`(191));

--
-- Индексы таблицы `wp_terms`
--
ALTER TABLE `wp_terms`
  ADD PRIMARY KEY (`term_id`),
  ADD KEY `slug` (`slug`(191)),
  ADD KEY `name` (`name`(191));

--
-- Индексы таблицы `wp_term_relationships`
--
ALTER TABLE `wp_term_relationships`
  ADD PRIMARY KEY (`object_id`,`term_taxonomy_id`),
  ADD KEY `term_taxonomy_id` (`term_taxonomy_id`);

--
-- Индексы таблицы `wp_term_taxonomy`
--
ALTER TABLE `wp_term_taxonomy`
  ADD PRIMARY KEY (`term_taxonomy_id`),
  ADD UNIQUE KEY `term_id_taxonomy` (`term_id`,`taxonomy`),
  ADD KEY `taxonomy` (`taxonomy`);

--
-- Индексы таблицы `wp_usermeta`
--
ALTER TABLE `wp_usermeta`
  ADD PRIMARY KEY (`umeta_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `meta_key` (`meta_key`(191));

--
-- Индексы таблицы `wp_users`
--
ALTER TABLE `wp_users`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `user_login_key` (`user_login`),
  ADD KEY `user_nicename` (`user_nicename`),
  ADD KEY `user_email` (`user_email`);

--
-- Индексы таблицы `wp_wpmm_subscribers`
--
ALTER TABLE `wp_wpmm_subscribers`
  ADD PRIMARY KEY (`id_subscriber`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `wp_ab_articles`
--
ALTER TABLE `wp_ab_articles`
  MODIFY `ID_Article` smallint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `wp_ab_chats`
--
ALTER TABLE `wp_ab_chats`
  MODIFY `ID_Chat` smallint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `wp_ab_experts`
--
ALTER TABLE `wp_ab_experts`
  MODIFY `ID_Expert` smallint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `wp_ab_issues`
--
ALTER TABLE `wp_ab_issues`
  MODIFY `ID_Issue` tinyint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `wp_ab_letters`
--
ALTER TABLE `wp_ab_letters`
  MODIFY `ID_Letter` tinyint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `wp_ab_reviews`
--
ALTER TABLE `wp_ab_reviews`
  MODIFY `ID_Review` smallint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `wp_ab_sections`
--
ALTER TABLE `wp_ab_sections`
  MODIFY `ID_Section` tinyint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `wp_ab_verdicts`
--
ALTER TABLE `wp_ab_verdicts`
  MODIFY `ID_Verdict` tinyint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `wp_ab_versions`
--
ALTER TABLE `wp_ab_versions`
  MODIFY `ID_Version` smallint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `wp_aryo_activity_log`
--
ALTER TABLE `wp_aryo_activity_log`
  MODIFY `histid` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `wp_commentmeta`
--
ALTER TABLE `wp_commentmeta`
  MODIFY `meta_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `wp_comments`
--
ALTER TABLE `wp_comments`
  MODIFY `comment_ID` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `wp_links`
--
ALTER TABLE `wp_links`
  MODIFY `link_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `wp_options`
--
ALTER TABLE `wp_options`
  MODIFY `option_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22089;

--
-- AUTO_INCREMENT для таблицы `wp_postmeta`
--
ALTER TABLE `wp_postmeta`
  MODIFY `meta_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=556;

--
-- AUTO_INCREMENT для таблицы `wp_posts`
--
ALTER TABLE `wp_posts`
  MODIFY `ID` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=802;

--
-- AUTO_INCREMENT для таблицы `wp_termmeta`
--
ALTER TABLE `wp_termmeta`
  MODIFY `meta_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `wp_terms`
--
ALTER TABLE `wp_terms`
  MODIFY `term_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `wp_term_taxonomy`
--
ALTER TABLE `wp_term_taxonomy`
  MODIFY `term_taxonomy_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `wp_usermeta`
--
ALTER TABLE `wp_usermeta`
  MODIFY `umeta_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `wp_users`
--
ALTER TABLE `wp_users`
  MODIFY `ID` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `wp_wpmm_subscribers`
--
ALTER TABLE `wp_wpmm_subscribers`
  MODIFY `id_subscriber` bigint NOT NULL AUTO_INCREMENT;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `wp_ab_articles`
--
ALTER TABLE `wp_ab_articles`
  ADD CONSTRAINT `Relation_11` FOREIGN KEY (`ID_CorAuthor`) REFERENCES `wp_ab_experts` (`ID_Expert`) ON UPDATE CASCADE,
  ADD CONSTRAINT `Relation_2` FOREIGN KEY (`ID_Issue`) REFERENCES `wp_ab_issues` (`ID_Issue`),
  ADD CONSTRAINT `Relation_3` FOREIGN KEY (`ID_Section`) REFERENCES `wp_ab_sections` (`ID_Section`) ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `wp_ab_chats`
--
ALTER TABLE `wp_ab_chats`
  ADD CONSTRAINT `CRelation_1` FOREIGN KEY (`ID_User`) REFERENCES `wp_users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `wp_ab_reviews`
--
ALTER TABLE `wp_ab_reviews`
  ADD CONSTRAINT `Relation_4` FOREIGN KEY (`ID_Article`) REFERENCES `wp_ab_articles` (`ID_Article`),
  ADD CONSTRAINT `Relation_5` FOREIGN KEY (`ID_Expert`) REFERENCES `wp_ab_experts` (`ID_Expert`),
  ADD CONSTRAINT `Relation_6` FOREIGN KEY (`ID_Verdict`) REFERENCES `wp_ab_verdicts` (`ID_Verdict`);

--
-- Ограничения внешнего ключа таблицы `wp_ab_sections`
--
ALTER TABLE `wp_ab_sections`
  ADD CONSTRAINT `Relation_1` FOREIGN KEY (`ID_Editor`) REFERENCES `wp_ab_experts` (`ID_Expert`);

--
-- Ограничения внешнего ключа таблицы `wp_ab_versions`
--
ALTER TABLE `wp_ab_versions`
  ADD CONSTRAINT `Relation_9` FOREIGN KEY (`ID_Article`) REFERENCES `wp_ab_articles` (`ID_Article`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
