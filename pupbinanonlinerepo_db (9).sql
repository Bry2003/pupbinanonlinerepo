-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 12, 2026 at 08:15 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pupbinanonlinerepo_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `archive_files`
--

CREATE TABLE `archive_files` (
  `id` int(30) NOT NULL,
  `archive_id` int(30) NOT NULL,
  `file_path` text NOT NULL,
  `original_name` varchar(250) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `archive_files`
--

INSERT INTO `archive_files` (`id`, `archive_id`, `file_path`, `original_name`, `date_created`) VALUES
(1, 73, 'https://filestoredintel.s3.us-east-1.amazonaws.com/Files/support/archive-73-0-chapter4_205.docx', 'chapter4_205.docx', '2026-01-07 07:01:04'),
(2, 74, 'https://filestoredintel.s3.us-east-1.amazonaws.com/Files/support/archive-74-0-chapter4_205.docx', 'chapter4_205.docx', '2026-01-07 07:02:08'),
(3, 75, 'https://filestoredintel.s3.us-east-1.amazonaws.com/Files/support/archive-75-0-CommEase-Full-Manuscript_watermark-1.docx', 'CommEase-Full-Manuscript_watermark-1.docx', '2026-01-07 11:06:16'),
(4, 76, 'https://filestoredintel.s3.us-east-1.amazonaws.com/Files/support/archive-76-0-Screenshot_2025-09-12_111916.png', 'Screenshot 2025-09-12 111916.png', '2026-01-10 18:07:15'),
(5, 76, 'https://filestoredintel.s3.us-east-1.amazonaws.com/Files/support/archive-76-1-Screenshot_2025-09-12_114704.png', 'Screenshot 2025-09-12 114704.png', '2026-01-10 18:07:15'),
(6, 76, 'https://filestoredintel.s3.us-east-1.amazonaws.com/Files/support/archive-76-2-Screenshot_2025-09-13_170450.png', 'Screenshot 2025-09-13 170450.png', '2026-01-10 18:07:16'),
(7, 76, 'https://filestoredintel.s3.us-east-1.amazonaws.com/Files/support/archive-76-3-Screenshot_2025-09-13_204323.png', 'Screenshot 2025-09-13 204323.png', '2026-01-10 18:07:17'),
(8, 76, 'https://filestoredintel.s3.us-east-1.amazonaws.com/Files/support/archive-76-4-Screenshot_2025-09-13_205342.png', 'Screenshot 2025-09-13 205342.png', '2026-01-10 18:07:20'),
(9, 77, 'https://filestoredintel.s3.us-east-1.amazonaws.com/Files/support/archive-77-0-Screenshot_2025-09-12_111916.png', 'Screenshot 2025-09-12 111916.png', '2026-01-10 20:36:43'),
(10, 77, 'https://filestoredintel.s3.us-east-1.amazonaws.com/Files/support/archive-77-1-Screenshot_2025-09-12_114704.png', 'Screenshot 2025-09-12 114704.png', '2026-01-10 20:36:44'),
(11, 77, 'https://filestoredintel.s3.us-east-1.amazonaws.com/Files/support/archive-77-2-Screenshot_2025-09-13_170450.png', 'Screenshot 2025-09-13 170450.png', '2026-01-10 20:36:45'),
(12, 77, 'https://filestoredintel.s3.us-east-1.amazonaws.com/Files/support/archive-77-3-Screenshot_2025-09-13_204323.png', 'Screenshot 2025-09-13 204323.png', '2026-01-10 20:36:48'),
(13, 77, 'https://filestoredintel.s3.us-east-1.amazonaws.com/Files/support/archive-77-4-Screenshot_2025-09-13_205342.png', 'Screenshot 2025-09-13 205342.png', '2026-01-10 20:36:49'),
(14, 78, 'https://filestoredintel.s3.us-east-1.amazonaws.com/Files/support/archive-78-0-Untitled_document__4_.pdf', 'Untitled document (4).pdf', '2026-01-12 23:55:52');

-- --------------------------------------------------------

--
-- Table structure for table `archive_list`
--

CREATE TABLE `archive_list` (
  `id` int(30) NOT NULL,
  `archive_code` varchar(100) NOT NULL,
  `curriculum_id` int(30) NOT NULL,
  `year` year(4) NOT NULL,
  `title` text NOT NULL,
  `abstract` text NOT NULL,
  `members` text NOT NULL,
  `banner_path` text NOT NULL,
  `document_path` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `student_id` int(30) DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `archive_list`
--

INSERT INTO `archive_list` (`id`, `archive_code`, `curriculum_id`, `year`, `title`, `abstract`, `members`, `banner_path`, `document_path`, `status`, `student_id`, `date_created`, `date_updated`) VALUES
(60, '2025120001', 0, '2025', 'CommEase: Enchancing Communication Tools with Customizable Button and Hand Gesture Suppport', '&lt;p&gt;Hello&lt;/p&gt;', '&lt;p&gt;&lt;span class=&quot;x3jgonx&quot; style=&quot;white-space-collapse: preserve;&quot;&gt;Begino, Lilac Erica M.&lt;/span&gt;&lt;br&gt;&lt;span class=&quot;x3jgonx&quot; style=&quot;white-space-collapse: preserve;&quot;&gt;Diolata, Justine Irish R.&lt;/span&gt;&lt;br&gt;&lt;span class=&quot;x3jgonx&quot; style=&quot;white-space-collapse: preserve;&quot;&gt;Enciso, Happy P.&lt;/span&gt;&lt;br&gt;&lt;span class=&quot;x3jgonx&quot; style=&quot;white-space-collapse: preserve;&quot;&gt;Panganiban, Genesis Jamaica M.&lt;/span&gt;&lt;br&gt;&lt;span class=&quot;x3jgonx&quot; style=&quot;white-space-collapse: preserve;&quot;&gt;Perez, Stephen Mathew C.&lt;/span&gt;&lt;br&gt;&lt;br&gt;&lt;/p&gt;', 'https://filestoredintel.s3.us-east-1.amazonaws.com/Files/banners/archive-60.png?v=1764814319', 'https://filestoredintel.s3.us-east-1.amazonaws.com/Files/pdf/archive-60.pdf?v=1764672387', 1, 25, '2025-12-02 18:45:57', '2025-12-03 18:11:59'),
(63, '2025120004', 35, '2025', 'Lipat Express: Booking Management System ', '&lt;p&gt;---&lt;/p&gt;', '&lt;p&gt;&lt;span class=&quot;x3jgonx&quot; style=&quot;white-space-collapse: preserve;&quot;&gt;Buladaco, Jaime D. &lt;/span&gt;&lt;br&gt;&lt;span class=&quot;x3jgonx&quot; style=&quot;white-space-collapse: preserve;&quot;&gt;Geronaga, Nicko Ronem L. &lt;/span&gt;&lt;br&gt;&lt;span class=&quot;x3jgonx&quot; style=&quot;white-space-collapse: preserve;&quot;&gt;Llemes, Charisse R. &lt;/span&gt;&lt;br&gt;&lt;span class=&quot;x3jgonx&quot; style=&quot;white-space-collapse: preserve;&quot;&gt;Tolosa, Missy Janelle M. &lt;/span&gt;&lt;br&gt;&lt;span class=&quot;x3jgonx&quot; style=&quot;white-space-collapse: preserve;&quot;&gt;Tula&ntilde;a, Ace V.&lt;/span&gt;&lt;/p&gt;', 'https://filestoredintel.s3.us-east-1.amazonaws.com/Files/banners/archive-63.png?v=1764814438', 'https://filestoredintel.s3.us-east-1.amazonaws.com/Files/pdf/archive-63.pdf?v=1764809575', 1, 25, '2025-12-03 16:52:52', '2025-12-03 18:13:58'),
(64, '2025120002', 32, '2025', 'IntelHub: The Research Repository for Polytechnic  University of the Philippines (BiÃ±an Campus)', '&lt;p&gt;---&lt;/p&gt;', '&lt;p&gt;Caballero Bryan H. &lt;br&gt;&amp;nbsp;Cardeno Raven S &lt;br&gt;&amp;nbsp;Mendoza Enzo Charles G.&lt;br&gt;&amp;nbsp;Ibarbia Christian Nel A.&lt;/p&gt;', 'https://filestoredintel.s3.us-east-1.amazonaws.com/Files/banners/archive-64.png?v=1764811097', 'https://filestoredintel.s3.us-east-1.amazonaws.com/Files/pdf/archive-64.pdf?v=1764811098', 1, 28, '2025-12-03 17:18:16', '2025-12-03 17:19:38'),
(65, '2026010001', 35, '0000', '', '', '', '', '', 0, 26, '2026-01-07 00:16:03', NULL),
(66, '2026010002', 35, '0000', '', '', '', '', '', 0, 26, '2026-01-07 00:16:34', NULL),
(67, '2026010003', 35, '0000', '', '', '', '', '', 0, 26, '2026-01-07 00:16:45', NULL),
(68, '2026010004', 35, '2026', 'kgg', '&lt;p&gt;mn&lt;/p&gt;', '&lt;p&gt;nbhhj&lt;/p&gt;', 'https://filestoredintel.s3.us-east-1.amazonaws.com/Files/banners/archive-68.jpg?v=1767716259', 'https://filestoredintel.s3.us-east-1.amazonaws.com/Files/pdf/archive-68.pdf?v=1767716272', 0, 26, '2026-01-07 00:17:36', '2026-01-07 00:17:52'),
(69, '2026010005', 35, '0000', '', '', '', '', '', 0, 26, '2026-01-07 00:23:15', NULL),
(70, '2026010006', 35, '2026', 'jhjhg', '&lt;p&gt;jhj&lt;/p&gt;', '&lt;p&gt;&amp;nbsp;nbbmnb&lt;/p&gt;', 'https://filestoredintel.s3.us-east-1.amazonaws.com/Files/banners/archive-70.jpg?v=1767716645', 'https://filestoredintel.s3.us-east-1.amazonaws.com/Files/pdf/archive-70.pdf?v=1767716654', 2, 26, '2026-01-07 00:24:01', '2026-01-07 04:45:25'),
(71, '2026010007', 35, '2026', 'rirur', '&lt;p&gt;rkkjjrk&lt;/p&gt;', '&lt;p&gt;rjrlr&lt;/p&gt;', 'https://filestoredintel.s3.us-east-1.amazonaws.com/Files/banners/archive-71.jpg?v=1767726689', 'https://filestoredintel.s3.us-east-1.amazonaws.com/Files/pdf/archive-71.pdf?v=1767726694', 0, 26, '2026-01-07 03:11:26', '2026-01-07 03:11:34'),
(72, '2026010008', 29, '0000', '', '', '', '', '', 0, 39, '2026-01-07 06:50:26', NULL),
(73, '2026010009', 29, '2026', 'haysss', '&lt;p&gt;djhdfkjhdf&lt;/p&gt;', '&lt;p&gt;slkkhhadkj&lt;/p&gt;', 'https://filestoredintel.s3.us-east-1.amazonaws.com/Files/banners/archive-73.jpg?v=1767740395', 'https://filestoredintel.s3.us-east-1.amazonaws.com/Files/pdf/archive-73.pdf?v=1767740444', 1, 40, '2026-01-07 06:59:50', '2026-01-07 07:06:12'),
(74, '2026010010', 29, '2026', 'haysss', '&lt;p&gt;djhdfkjhdf&lt;/p&gt;', '&lt;p&gt;slkkhhadkj&lt;/p&gt;', 'https://filestoredintel.s3.us-east-1.amazonaws.com/Files/banners/archive-74.jpg?v=1767740469', 'https://filestoredintel.s3.us-east-1.amazonaws.com/Files/pdf/archive-74.pdf?v=1767740510', 0, 40, '2026-01-07 07:01:05', '2026-01-07 07:01:50'),
(75, '2026010011', 29, '2026', 'rjhwrh', '&lt;p&gt;lwhhjowr&lt;/p&gt;', '&lt;p&gt;kfknsflk&lt;/p&gt;', 'https://filestoredintel.s3.us-east-1.amazonaws.com/Files/banners/archive-75.jpg?v=1767755163', 'https://filestoredintel.s3.us-east-1.amazonaws.com/Files/pdf/archive-75.pdf?v=1767755165', 0, 40, '2026-01-07 11:06:00', '2026-01-07 11:06:05'),
(76, '2026010012', 29, '2026', 'eyrioyuweru', '&lt;p&gt;leihhoiew&lt;/p&gt;', '&lt;p&gt;ewoieoir&lt;/p&gt;', 'https://filestoredintel.s3.us-east-1.amazonaws.com/Files/banners/archive-76.jpg?v=1768039630', 'https://filestoredintel.s3.us-east-1.amazonaws.com/Files/pdf/archive-76.pdf?v=1768039633', 0, 40, '2026-01-10 18:07:06', '2026-01-10 18:07:13'),
(77, '2026010013', 32, '2026', 'IntelHub', '&lt;p&gt;--&lt;/p&gt;', '&lt;p&gt;--&lt;/p&gt;', 'https://filestoredintel.s3.us-east-1.amazonaws.com/Files/banners/archive-77.png?v=1768048570', 'https://filestoredintel.s3.us-east-1.amazonaws.com/Files/pdf/archive-77.pdf?v=1768048597', 1, 43, '2026-01-10 20:36:04', '2026-01-10 20:40:55'),
(78, '2026010014', 29, '2003', 'jkjhhjh', '&lt;p&gt;mgjhgh&lt;/p&gt;', '&lt;p&gt;jkjkjh&lt;/p&gt;', 'https://filestoredintel.s3.us-east-1.amazonaws.com/Files/banners/archive-78.jpg?v=1768233345', 'https://filestoredintel.s3.us-east-1.amazonaws.com/Files/pdf/archive-78.pdf?v=1768233348', 0, 40, '2026-01-12 23:55:42', '2026-01-12 23:55:48');

-- --------------------------------------------------------

--
-- Table structure for table `chat_keys`
--

CREATE TABLE `chat_keys` (
  `id` int(30) NOT NULL,
  `owner_type` tinyint(1) NOT NULL,
  `owner_id` int(30) NOT NULL,
  `public_key` longtext NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chat_keys`
--

INSERT INTO `chat_keys` (`id`, `owner_type`, `owner_id`, `public_key`, `date_created`) VALUES
(1, 2, 26, '-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEApYqaYw35CBJF2YILFYiC\nEt0P9Q0oBGD5Btwi3g12VNqQ7362dTFXPINB5Rj5yS+Tug4bo82NtydmSzRSxtCG\nKGlRNCDs6HYeYUFn3UA8vL5S0TdgBsHwVvonHUWpGjadCdiOsFnir7QXhik4L8Aa\nO0rN3MIMl4xV6PnfkZdgk/+AaKjIkftFqPX1lzYg/WociNMMZV1kujeYlhE0cc53\ncE4QRCFcajoVQjAYePeRBN0bXSKRN1aANq7PJLFub+yzR0gqmWKkdEZLO2Vjsbrf\n7acbyYJzSch6O+OXtxlUdfFuLt4IIjSuKRYjkPGeOZM784P1fQKa7LSoqHSOiY4G\nBwIDAQAB\n-----END PUBLIC KEY-----', '2025-12-02 23:42:44'),
(2, 1, 1, '-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAgcyd2kpEzhsZplHH40ys\nHG8/MmqIwDMYEjCPesaDKnh06HLnYV9etQn4+8xKJGEEdzhw9VnCg/3Xwv7j6RU0\nWul/Xpo0LSCW1mFiNEXRQNjcJX2Zq8rVmb9vRK3VWe7yremYS/dSLAVZTbZrJk8P\nLd1eporkedpO559P/9+hBAR49Jf6CwBS70ivVDSg79+LRXFAh6pbTCgWDdf4drEf\n3w0D9n8Z8NoPnUX84c9zJiDQG7na5PuU8tyHVIAc/tSRrgicbEiq0L/nscJlpD1F\n1UhZWw58EVgE7Py3Zo69eoy7hNKFzgcU31JY99gP5X0/G+i1aHsdToYo1H6eBAwT\nmwIDAQAB\n-----END PUBLIC KEY-----', '2025-12-02 23:50:56');

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` int(30) NOT NULL,
  `thread_id` int(30) NOT NULL,
  `sender_type` tinyint(1) NOT NULL,
  `sender_id` int(30) NOT NULL,
  `recipient_type` tinyint(1) NOT NULL,
  `recipient_id` int(30) NOT NULL,
  `algorithm` varchar(50) NOT NULL,
  `iv` varchar(255) DEFAULT NULL,
  `enc_key` longtext DEFAULT NULL,
  `enc_key_sender` longtext DEFAULT NULL,
  `ciphertext` longtext NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_by_user` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_by_admin` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chat_messages`
--

INSERT INTO `chat_messages` (`id`, `thread_id`, `sender_type`, `sender_id`, `recipient_type`, `recipient_id`, `algorithm`, `iv`, `enc_key`, `enc_key_sender`, `ciphertext`, `date_created`, `deleted_by_user`, `deleted_by_admin`) VALUES
(1, 1, 1, 1, 2, 26, 'AES-GCM+RSA-OAEP', 'E2OxVpHpPdYK23aB', 'JYknSI4GbuVTG/HgWm0RzdQl7pWpD91LUwSEeK4nhwtlCd1MEjIYqB3v40OnwOfxIa+g5kVJAesPp87yUhGuFoCHdi/HTqbuPlAVlKm9g/iU2wDnHa+x6CvIeR8QP2xIvPwWvE09Ps/aNA7mh3Qz65YWsdr2Hw0bxd9aKRYKKMMG4POIqR+chaN1+82NK9lYK05+9VE/Z93kCDxIpkpfhPQjPbbjdKA/w36E6B4jiaFJ9NbRErJ+nx+iKHe9MPeXLLKGsp/VemI6f/Wc3ijp21LaDhTXTTS6mHJd5zT3kCZvObCZHkAQeUvMhud6bJZVVLzJFOlGYeX1+zMsanzUgw==', NULL, 'Kb5cZEKMfO/10lKA5Yv+WMfy', '2025-12-02 23:54:48', 0, 1),
(2, 1, 1, 1, 2, 26, 'AES-GCM+RSA-OAEP', 'PwIACQSouSvCSn5i', 'nF3V31ihN1resb1HhsQqI73Epr0WXuCK0a0+bCoQQI552tsuyZOW5eCP40hc58G4H4CpYLhJwOSWkCue4odKwEH0JOJDWOflShwxQFRWlzyCCBvb11o1tKnBkqne4WwmULkRycwkGJD+luLUQP1oa1t77Fu17yujiBOKvCEJchD0wsctHR6gDSbs9GdT4KkkGCdiEjj+uZUIbHYv1qM1/uBN7/Qo1aNbbmGvn25PsCdpwYlcBuvZBSyXOxNOh71BcF1JbRAWpbQazsoF4zgzYL/EhEQIhKFBg9bcqbIo+Pw3whVroJMGZ0eRkZICis6CgJIcl7qf6q5sh5GjYXJk1A==', NULL, 'ndWBRXLXPTRB2fR9fZliog1f', '2025-12-02 23:55:07', 0, 1),
(3, 1, 1, 1, 2, 26, 'AES-GCM+RSA-OAEP', 'xWDegHeMYqZa8NOJ', 'fLfymN3bZgOUusoe68S/S+TwKSRwZBWNRbRv/Gu81SzyLvUaocCmdQAmo+eRKqDmgWnwQLsPEoXXJn/OImVL9b6KczXFuDX1f99zNuOfznt6Orw7GkB14+fXtbdCc5/TvuTaooftMByVBcwPPjuskvCbj0JpsFkDq+DTGzEBKYwczzo5AazTLEQeKl11TSbY7MixIPWqDZd2EZ4pn+PMr9DRgnHvZ638NXZvice46tD3IW9WbgpTofZZXH0rTR/QV+codqwHDEqgfFEcYzD1nYjjf5bVBK2Wj7H6JVsq6SukgDj836y8SXr/w3iAU06eVO+GMWfZfUIZG9imIT3T5Q==', NULL, 'SuLpS4U740aiCsF1V8yfUBEZ', '2025-12-02 23:56:39', 0, 0),
(4, 1, 1, 1, 2, 26, 'AES-GCM+RSA-OAEP', '+v8amCOUc+8nUE+w', 'Wj4lHOrrQTCJkjRPOjoFnJAlefV8H8ewRHQ/tE+31Mo2eSiojck7yDQ0qq5eUYcgpLInlJ1TQ6nWBW7i5N2C7YzmET7C0EZNtcIE2b1rvv9ft6qH29RUprdg9xGzp3WMaAd12iAQFbTY4Mtq8ngufL++d6fLgw/KOckpw2OMLaKdwPNFtlW8JqshuP/vn3BzLghcucuTZCfEF6RV+NkCkyrj/TX/gxdCWsRv7A1rKgNmyzQtDyXed6+dj50jN7CNYY7OgPtOiDibNVf3o4LJO2HC7MGnMOWF/HdTZM5j/T6jZCP0lltShTE406OD9QJPPnKsKAh3z31OArfLqy/7vg==', NULL, 'CXTiXLLs5SWZzOhYPvX13Q6Q', '2025-12-02 23:59:12', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `chat_threads`
--

CREATE TABLE `chat_threads` (
  `id` int(30) NOT NULL,
  `user_id` int(30) NOT NULL,
  `admin_id` int(30) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chat_threads`
--

INSERT INTO `chat_threads` (`id`, `user_id`, `admin_id`, `date_created`) VALUES
(1, 26, 1, '2025-12-02 23:47:32');

-- --------------------------------------------------------

--
-- Table structure for table `curriculum_list`
--

CREATE TABLE `curriculum_list` (
  `id` int(30) NOT NULL,
  `department_id` int(30) NOT NULL,
  `name` text NOT NULL,
  `description` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `curriculum_list`
--

INSERT INTO `curriculum_list` (`id`, `department_id`, `name`, `description`, `status`, `date_created`, `date_updated`) VALUES
(40, 22, 'BS Psychology', 'Bachelor of Science in Psychology', 1, '2025-09-09 05:30:16', NULL),
(41, 23, 'BSEd English', 'Bachelor of Secondary Education Major in English', 1, '2025-09-09 05:30:16', NULL),
(42, 23, 'BSEd Social Studies', 'Bachelor of Secondary Education Major in Social Studies', 1, '2025-09-09 05:30:16', NULL),
(43, 23, 'BEEd', 'Bachelor of Elementary Education', 1, '2025-09-09 05:30:16', NULL),
(44, 24, 'BSIT', 'Bachelor of Science in Information Technology', 1, '2025-09-09 05:30:16', NULL),
(45, 25, 'BS Computer Engineering', 'Bachelor of Science in Computer Engineering', 1, '2025-09-09 05:30:16', NULL),
(46, 25, 'BS Industrial Engineering', 'Bachelor of Science in Industrial Engineering', 1, '2025-09-09 05:30:16', NULL),
(47, 26, 'BSBA-HRM', 'Bachelor of Science in Business Administration Major in Human Resource Management', 1, '2025-09-09 05:30:16', NULL),
(48, 24, 'Diploma in IT', 'Diploma in Information Technology', 1, '2025-09-09 05:30:16', NULL),
(49, 25, 'Diploma in CET', 'Diploma in Computer Engineering Technology', 1, '2025-09-09 05:30:16', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `department_list`
--

CREATE TABLE `department_list` (
  `id` int(30) NOT NULL,
  `name` text NOT NULL,
  `description` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department_list`
--

INSERT INTO `department_list` (`id`, `name`, `description`, `status`, `date_created`, `date_updated`) VALUES
(28, 'Bachelor of Elementary Education ', '', 1, '2025-12-02 18:02:56', NULL),
(29, 'Bachelor of Science in Accountancy ', '', 1, '2025-12-02 18:03:10', NULL),
(30, 'Bachelor of Science in Business Administration ', '', 1, '2025-12-02 18:03:31', NULL),
(31, 'Bachelor of Science in Computer Engineering ', '', 1, '2025-12-02 18:03:55', NULL),
(32, 'Bachelor of Science in Industrial Engineering ', '', 1, '2025-12-02 18:04:07', NULL),
(33, 'Diploma in Computer Engineering ', '', 1, '2025-12-02 18:04:18', NULL),
(34, 'Diploma in Information Communication Technology ', '', 1, '2025-12-02 18:04:26', NULL),
(35, 'Bachelor of Science in Information Technology ', '', 1, '2025-12-02 18:04:45', NULL),
(36, 'Bachelor of Secondary Education, major in Social Studies ', '', 1, '2025-12-02 18:05:12', NULL),
(37, 'Bachelor of Secondary Education, major in English  ', '', 1, '2025-12-02 18:05:30', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `faqs`
--

CREATE TABLE `faqs` (
  `id` int(30) NOT NULL,
  `question` text NOT NULL,
  `answer` mediumtext NOT NULL,
  `tags` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `id_registry`
--

CREATE TABLE `id_registry` (
  `id` int(30) NOT NULL,
  `account_type` varchar(20) NOT NULL,
  `id_number` varchar(100) NOT NULL,
  `name` text NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `id_registry`
--

INSERT INTO `id_registry` (`id`, `account_type`, `id_number`, `name`, `status`, `date_created`, `date_updated`) VALUES
(9, '', 'id_Number', '', 1, '2025-12-02 18:28:45', NULL),
(10, '', '2022-00235-BN-0', '', 1, '2025-12-02 18:28:45', NULL),
(11, '', '2022-00243-BN-0', '', 1, '2025-12-02 18:28:45', NULL),
(12, '', '2022-00239-BN-0', '', 1, '2025-12-02 18:28:45', NULL),
(13, '', '2022-00429-BN-0', '', 1, '2025-12-02 18:28:45', NULL),
(14, '', '2022-00263-BN-0', '', 1, '2025-12-02 18:28:45', NULL),
(15, '', '2022-00262-BN-0', '', 1, '2025-12-02 18:28:45', NULL),
(16, '', '2022-00431-BN-0', '', 1, '2025-12-02 18:28:45', NULL),
(17, '', '2022-00424-BN-0', '', 1, '2025-12-02 18:28:45', NULL),
(18, '', '2022-00433-BN-0', '', 1, '2025-12-02 18:28:45', NULL),
(19, '', '2022-00259-BN-0', '', 1, '2025-12-02 18:28:45', NULL),
(20, '', '2022-00237-BN-0', '', 1, '2025-12-02 18:28:45', NULL),
(21, '', '2022-00254-BN-0', '', 1, '2025-12-02 18:28:45', NULL),
(22, '', '2022-00275-BN-0', '', 1, '2025-12-02 18:28:45', NULL),
(23, '', '2022-00247-BN-0', '', 1, '2025-12-02 18:28:45', NULL),
(24, '', '2022-00473-BN-0', '', 1, '2025-12-02 18:28:45', NULL),
(25, '', '2022-00246-BN-0', '', 1, '2025-12-02 18:28:45', NULL),
(26, '', '2022-00427-BN-0', '', 1, '2025-12-02 18:28:45', NULL),
(27, '', '2022-00252-BN-0', '', 1, '2025-12-02 18:28:45', NULL),
(28, '', '2022-00545-BN-0', '', 1, '2025-12-02 18:28:45', NULL),
(29, '', '2022-00420-BN-0', '', 1, '2025-12-02 18:28:45', NULL),
(30, '', '2022-00264-BN-0', '', 1, '2025-12-02 18:28:45', NULL),
(31, '', '2022-00435-BN-0', '', 1, '2025-12-02 18:28:45', NULL),
(32, '', '2022-00274-BN-0', '', 1, '2025-12-02 18:28:45', NULL),
(33, '', '2022-00430-BN-0', '', 1, '2025-12-02 18:28:45', NULL),
(34, '', '2022-00251-BN-0', '', 1, '2025-12-02 18:28:45', NULL),
(35, '', '2022-00268-BN-0', '', 1, '2025-12-02 18:28:45', NULL),
(36, '', '2022-00244-BN-0', '', 1, '2025-12-02 18:28:45', NULL),
(37, '', '2022-00426-BN-0', '', 1, '2025-12-02 18:28:45', NULL),
(38, '', '2022-00253-BN-0', '', 1, '2025-12-02 18:28:45', NULL),
(39, '', '2022-00249-BN-0', '', 1, '2025-12-02 18:28:45', NULL),
(40, '', '2022-00245-BN-0', '', 1, '2025-12-02 18:28:45', NULL),
(41, '', '2022-00234-BN-0', '', 1, '2025-12-02 18:28:45', NULL),
(42, '', '2022-00250-BN-0', '', 1, '2025-12-02 18:28:45', NULL),
(43, '', '2022-00267-BN-0', '', 1, '2025-12-02 18:28:45', NULL),
(44, '', '2022-00271-BN-0', '', 1, '2025-12-02 18:28:45', NULL),
(45, '', '2021-00548-BN-1', '', 1, '2025-12-02 18:28:45', NULL),
(46, '', '2022-00238-BN-0', '', 1, '2025-12-02 18:28:45', NULL),
(47, '', '2022-00248-BN-0', '', 1, '2025-12-02 18:28:45', NULL),
(48, '', '2022-00422-BN-0', '', 1, '2025-12-02 18:28:45', NULL),
(49, '', '2022-00474-BN-0', '', 1, '2025-12-02 18:28:45', NULL),
(50, '', '2022-00273-BN-0', '', 1, '2025-12-02 18:28:45', NULL),
(51, '', '2022-00258-BN-0', '', 1, '2025-12-02 18:28:45', NULL),
(52, '', '2022-00269-BN-0', '', 1, '2025-12-02 18:28:45', NULL),
(53, '', '2022-00421-BN-0', '', 1, '2025-12-02 18:28:45', NULL),
(54, '', '2022-00428-BN-0', '', 1, '2025-12-02 18:28:45', NULL),
(55, '', '2022-00240-BN-0', '', 1, '2025-12-02 18:28:45', NULL),
(56, '', '2022-00242-BN-0', '', 1, '2025-12-02 18:28:45', NULL),
(57, '', '2022-00432-BN-0', '', 1, '2025-12-02 18:28:45', NULL),
(58, '', '2022-00255-BN-0', '', 1, '2025-12-02 18:28:45', NULL),
(59, '', '2022-00260-BN-0', '', 1, '2025-12-02 18:28:45', NULL),
(60, '', '2022-00434-BN-0', '', 1, '2025-12-02 18:28:45', NULL),
(61, '', '2022-00272-BN-0', '', 1, '2025-12-02 18:28:45', NULL),
(62, '', '2022-00261-BN-0', '', 1, '2025-12-02 18:28:45', NULL),
(63, '', '2022-00256-BN-0', '', 1, '2025-12-02 18:28:45', NULL),
(64, '', '2022-00270-BN-0', '', 1, '2025-12-02 18:28:45', NULL),
(65, '', '2022-00241-BN-0', '', 1, '2025-12-02 18:28:45', NULL),
(66, '', '2022-00436-BN-0', '', 1, '2025-12-02 18:28:45', NULL),
(67, '', '2022-00236-BN-0', '', 1, '2025-12-02 18:28:45', NULL),
(68, '', '2022-00425-BN-0', '', 1, '2025-12-02 18:28:45', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `page_views`
--

CREATE TABLE `page_views` (
  `id` int(11) NOT NULL,
  `page_slug` varchar(255) NOT NULL,
  `viewed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `page_views`
--

INSERT INTO `page_views` (`id`, `page_slug`, `viewed_at`) VALUES
(1, 'homepage', '2026-01-10 10:09:15'),
(2, 'homepage', '2026-01-10 10:14:28'),
(3, 'homepage', '2026-01-10 10:15:25'),
(4, 'homepage', '2026-01-10 10:49:15'),
(5, 'homepage', '2026-01-12 14:16:25'),
(6, 'homepage', '2026-01-12 14:18:30'),
(7, 'homepage', '2026-01-12 14:18:54'),
(8, 'homepage', '2026-01-12 14:22:43'),
(9, 'homepage', '2026-01-12 14:24:32'),
(10, 'homepage', '2026-01-12 14:24:56'),
(11, 'homepage', '2026-01-12 14:25:07'),
(12, 'homepage', '2026-01-12 14:25:57'),
(13, 'homepage', '2026-01-12 14:28:00'),
(14, 'homepage', '2026-01-12 14:28:23'),
(15, 'homepage', '2026-01-12 14:28:40'),
(16, 'homepage', '2026-01-12 14:29:02'),
(17, 'homepage', '2026-01-12 14:30:47'),
(18, 'homepage', '2026-01-12 14:31:13'),
(19, 'homepage', '2026-01-12 14:33:20'),
(20, 'homepage', '2026-01-12 14:33:39'),
(21, 'homepage', '2026-01-12 14:33:50'),
(22, 'homepage', '2026-01-12 14:34:07'),
(23, 'homepage', '2026-01-12 14:34:16'),
(24, 'homepage', '2026-01-12 14:34:39'),
(25, 'homepage', '2026-01-12 14:36:18'),
(26, 'homepage', '2026-01-12 14:36:54'),
(27, 'homepage', '2026-01-12 14:37:18'),
(28, 'homepage', '2026-01-12 14:37:41'),
(29, 'homepage', '2026-01-12 14:38:02'),
(30, 'homepage', '2026-01-12 14:38:24'),
(31, 'homepage', '2026-01-12 14:39:06'),
(32, 'homepage', '2026-01-12 14:52:46'),
(33, 'homepage', '2026-01-12 14:53:36'),
(34, 'homepage', '2026-01-12 14:54:32'),
(35, 'homepage', '2026-01-12 14:56:14'),
(36, 'homepage', '2026-01-12 14:56:33'),
(37, 'homepage', '2026-01-12 14:56:43'),
(38, 'homepage', '2026-01-12 14:58:42'),
(39, 'homepage', '2026-01-12 14:59:43'),
(40, 'homepage', '2026-01-12 15:00:55'),
(41, 'homepage', '2026-01-12 15:04:09'),
(42, 'homepage', '2026-01-12 15:04:53'),
(43, 'homepage', '2026-01-12 15:06:45'),
(44, 'homepage', '2026-01-12 15:10:57'),
(45, 'homepage', '2026-01-12 15:11:59'),
(46, 'homepage', '2026-01-12 15:12:20'),
(47, 'homepage', '2026-01-12 15:14:25'),
(48, 'homepage', '2026-01-12 15:16:43'),
(49, 'homepage', '2026-01-12 15:19:01'),
(50, 'homepage', '2026-01-12 15:20:53'),
(51, 'homepage', '2026-01-12 15:21:19'),
(52, 'homepage', '2026-01-12 15:24:36'),
(53, 'homepage', '2026-01-12 15:25:01'),
(54, 'homepage', '2026-01-12 15:26:50'),
(55, 'homepage', '2026-01-12 15:29:52'),
(56, 'homepage', '2026-01-12 15:32:43'),
(57, 'homepage', '2026-01-12 15:38:38'),
(58, 'homepage', '2026-01-12 15:39:00'),
(59, 'homepage', '2026-01-12 15:39:39'),
(60, 'homepage', '2026-01-12 15:40:47'),
(61, 'homepage', '2026-01-12 15:43:36'),
(62, 'homepage', '2026-01-12 15:44:20'),
(63, 'homepage', '2026-01-12 15:46:48'),
(64, 'homepage', '2026-01-12 15:48:41'),
(65, 'homepage', '2026-01-12 15:50:01'),
(66, 'homepage', '2026-01-12 15:50:33'),
(67, 'homepage', '2026-01-12 16:05:30'),
(68, 'homepage', '2026-01-12 16:17:52'),
(69, 'homepage', '2026-01-12 16:49:37'),
(70, 'homepage', '2026-01-12 17:03:38'),
(71, 'homepage', '2026-01-12 17:19:57'),
(72, 'homepage', '2026-01-12 17:20:58'),
(73, 'homepage', '2026-01-12 17:24:24'),
(74, 'homepage', '2026-01-12 17:25:56'),
(75, 'homepage', '2026-01-12 18:11:26'),
(76, 'homepage', '2026-01-12 18:16:56'),
(77, 'homepage', '2026-01-12 18:27:28');

-- --------------------------------------------------------

--
-- Table structure for table `page_visits`
--

CREATE TABLE `page_visits` (
  `id` int(10) UNSIGNED NOT NULL,
  `page_name` varchar(150) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `visit_date` date NOT NULL,
  `visit_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `page_visits`
--

INSERT INTO `page_visits` (`id`, `page_name`, `ip_address`, `visit_date`, `visit_time`) VALUES
(1, 'homepage.php', '::1', '2026-01-10', '2026-01-10 10:09:15'),
(2, 'homepage.php', '::1', '2026-01-12', '2026-01-12 14:16:25'),
(3, 'homepage.php', '::1', '2026-01-13', '2026-01-12 16:05:30');

-- --------------------------------------------------------

--
-- Table structure for table `student_list`
--

CREATE TABLE `student_list` (
  `id` int(30) NOT NULL,
  `firstname` text NOT NULL,
  `middlename` text NOT NULL,
  `lastname` text NOT NULL,
  `student_number` varchar(50) DEFAULT NULL,
  `department_id` int(30) DEFAULT NULL,
  `adviser_id` int(30) DEFAULT NULL,
  `curriculum_id` int(30) DEFAULT NULL,
  `email` text NOT NULL,
  `password` text NOT NULL,
  `gender` varchar(50) NOT NULL,
  `account_type` varchar(20) DEFAULT NULL,
  `id_number` varchar(50) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `avatar` text NOT NULL,
  `id_doc_url` text DEFAULT NULL,
  `live_face_url` text DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_list`
--

INSERT INTO `student_list` (`id`, `firstname`, `middlename`, `lastname`, `student_number`, `department_id`, `adviser_id`, `curriculum_id`, `email`, `password`, `gender`, `account_type`, `id_number`, `status`, `avatar`, `id_doc_url`, `live_face_url`, `date_created`, `date_updated`) VALUES
(36, 'Test', '', 'Student', NULL, NULL, NULL, NULL, 'test1767738516@example.com', 'e10adc3949ba59abbe56e057f20f883e', 'Male', NULL, NULL, 1, '', NULL, NULL, '2026-01-07 06:28:36', NULL),
(37, 'Test', '', 'Student', NULL, 28, NULL, NULL, 'test1767738573@example.com', 'e10adc3949ba59abbe56e057f20f883e', 'Male', NULL, NULL, 1, '', NULL, NULL, '2026-01-07 06:29:33', NULL),
(38, 'Test', '', 'Student', NULL, 28, NULL, NULL, 'test1767738777@example.com', 'e10adc3949ba59abbe56e057f20f883e', 'Male', NULL, NULL, 1, '', NULL, NULL, '2026-01-07 06:32:57', NULL),
(39, 'bryan', 'hubilla', 'caballero', NULL, 29, 5, NULL, 'cababryan936@gmail.com', '0192023a7bbd73250516f069df18b500', 'Male', NULL, '0987654', 1, '', NULL, NULL, '2026-01-07 06:46:29', NULL),
(40, 'xtian', 'ayson', 'ibarbia', NULL, 29, 5, NULL, 'cababryan836@gmail.com', '2dd6b6d0e5da882993827998917c14f1', 'Male', NULL, '24678965', 1, '', NULL, NULL, '2026-01-07 06:57:51', NULL),
(41, 'bryanyryyr', 'hdrh', 'caballero', NULL, 30, 5, NULL, 'admin', '0192023a7bbd73250516f069df18b500', 'Male', NULL, '3653553', 1, '', NULL, NULL, '2026-01-10 18:11:59', NULL),
(42, 'loyd', 'a', 'viray', NULL, 32, 7, NULL, 'loydviray@gmail.com', '4e95c2bef8546dd66bbd4f18ed92e575', 'Male', NULL, '2022-00232-BN-0', 1, '', NULL, NULL, '2026-01-10 19:00:46', NULL),
(43, 'Matthew', 'A. ', 'Pascua', NULL, 32, 8, NULL, 'matthew@gmail.com', '7edeb90a6ed6e0141e9f28faca7f3af6', 'Male', NULL, '2022-00234-BN-0', 1, '', NULL, NULL, '2026-01-10 20:29:11', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `system_info`
--

CREATE TABLE `system_info` (
  `id` int(30) NOT NULL,
  `meta_field` text NOT NULL,
  `meta_value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_info`
--

INSERT INTO `system_info` (`id`, `meta_field`, `meta_value`) VALUES
(1, 'name', 'PUP BINAN ONLINE REPOSITORY SYSTEM'),
(6, 'short_name', 'INTELHUB'),
(11, 'logo', 'https://filestoredintel.s3.us-east-1.amazonaws.com/Files/system/logo-1763554666-ebbf1312.png?v=1763554670'),
(13, 'user_avatar', 'uploads/user_avatar.jpg'),
(14, 'cover', 'https://filestoredintel.s3.us-east-1.amazonaws.com/Files/system/cover-1768229170-fe5316eb.mp4?v=1768229235'),
(15, 'content', 'Array'),
(16, 'email', 'info@sample.com'),
(17, 'contact', '+1234567890'),
(18, 'from_time', '11:00'),
(19, 'to_time', '21:30'),
(20, 'address', 'Philippines'),
(21, 'theme_maroon', '#800000'),
(22, 'theme_accent', '#282725'),
(23, 'theme_text_dark', '#535050'),
(24, 'theme_secondary', '#adadad'),
(25, 'font_family', 'Trajan Pro'),
(26, 'login_title', 'Hi, PUPian'),
(27, 'login_subtitle', 'Please click or tap your destination.'),
(28, 'motto', ''),
(29, 'school_address', ''),
(30, 'contact_number', ''),
(31, 'email_address', '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(50) NOT NULL,
  `firstname` varchar(250) NOT NULL,
  `middlename` text DEFAULT NULL,
  `lastname` varchar(250) NOT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `avatar` text DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `type` tinyint(1) NOT NULL DEFAULT 0,
  `status` int(1) NOT NULL DEFAULT 1 COMMENT '0=not verified, 1 = verified',
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstname`, `middlename`, `lastname`, `username`, `password`, `avatar`, `last_login`, `type`, `status`, `date_added`, `date_updated`) VALUES
(1, 'Super', NULL, 'Admin', 'admin', '0192023a7bbd73250516f069df18b500', 'https://filestoredintel.s3.us-east-1.amazonaws.com/Files/avatars/user-1-1764674158-82abb3.jpg?v=1764674161', NULL, 1, 1, '2021-01-20 14:02:37', '2026-01-07 04:29:23'),
(3, '1. ', NULL, 'Admin', 'admin1', '0192023a7bbd73250516f069df18b500', 'https://filestoredintel.s3.us-east-1.amazonaws.com/Files/avatars/user-3-1764704010-bd4398.png?v=1764704011', NULL, 2, 1, '2025-12-02 11:33:30', '2025-12-02 11:33:31'),
(4, '2. ', NULL, 'Admin', 'admin2', '0192023a7bbd73250516f069df18b500', 'https://filestoredintel.s3.us-east-1.amazonaws.com/Files/avatars/user-4-1764704454-6caeec.png?v=1764704454', NULL, 2, 1, '2025-12-02 11:40:53', '2025-12-02 11:40:54'),
(5, 'Enzo', NULL, 'mendoza', 'enzo', '0192023a7bbd73250516f069df18b500', NULL, NULL, 2, 1, '2026-01-07 03:33:49', NULL),
(6, 'Test', NULL, 'Adviser', 'adviser', 'fd7ab343a521997a51080cb54c8edb37', NULL, NULL, 2, 1, '2026-01-07 04:29:23', NULL),
(7, 'matthew', NULL, 'pascua', 'matthew1', '0192023a7bbd73250516f069df18b500', NULL, NULL, 2, 1, '2026-01-10 18:58:53', NULL),
(8, 'Christian', NULL, 'Ibarbia', 'christian12@gmail.com', 'd4d9065d65d304680f358179ed4ad7c6', NULL, NULL, 2, 1, '2026-01-10 20:24:17', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `visitors`
--

CREATE TABLE `visitors` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `visit_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `visitors`
--

INSERT INTO `visitors` (`id`, `ip_address`, `visit_date`) VALUES
(1, '::1', '2026-01-10 10:09:15'),
(2, '::1', '2026-01-10 10:14:28'),
(3, '::1', '2026-01-10 10:15:25'),
(4, '::1', '2026-01-10 10:49:15'),
(5, '::1', '2026-01-12 14:16:25'),
(6, '::1', '2026-01-12 14:18:30'),
(7, '::1', '2026-01-12 14:18:54'),
(8, '::1', '2026-01-12 14:22:43'),
(9, '::1', '2026-01-12 14:24:32'),
(10, '::1', '2026-01-12 14:24:56'),
(11, '::1', '2026-01-12 14:25:07'),
(12, '::1', '2026-01-12 14:25:57'),
(13, '::1', '2026-01-12 14:28:00'),
(14, '::1', '2026-01-12 14:28:23'),
(15, '::1', '2026-01-12 14:28:40'),
(16, '::1', '2026-01-12 14:29:02'),
(17, '::1', '2026-01-12 14:30:47'),
(18, '::1', '2026-01-12 14:31:13'),
(19, '::1', '2026-01-12 14:33:20'),
(20, '::1', '2026-01-12 14:33:39'),
(21, '::1', '2026-01-12 14:33:50'),
(22, '::1', '2026-01-12 14:34:07'),
(23, '::1', '2026-01-12 14:34:16'),
(24, '::1', '2026-01-12 14:34:39'),
(25, '::1', '2026-01-12 14:36:18'),
(26, '::1', '2026-01-12 14:36:54'),
(27, '::1', '2026-01-12 14:37:18'),
(28, '::1', '2026-01-12 14:37:41'),
(29, '::1', '2026-01-12 14:38:02'),
(30, '::1', '2026-01-12 14:38:24'),
(31, '::1', '2026-01-12 14:39:06'),
(32, '::1', '2026-01-12 14:52:46'),
(33, '::1', '2026-01-12 14:53:36'),
(34, '::1', '2026-01-12 14:54:32'),
(35, '::1', '2026-01-12 14:56:14'),
(36, '::1', '2026-01-12 14:56:33'),
(37, '::1', '2026-01-12 14:56:43'),
(38, '::1', '2026-01-12 14:58:42'),
(39, '::1', '2026-01-12 14:59:43'),
(40, '::1', '2026-01-12 15:00:55'),
(41, '::1', '2026-01-12 15:04:09'),
(42, '::1', '2026-01-12 15:04:53'),
(43, '::1', '2026-01-12 15:06:45'),
(44, '::1', '2026-01-12 15:10:57'),
(45, '::1', '2026-01-12 15:11:59'),
(46, '::1', '2026-01-12 15:12:20'),
(47, '::1', '2026-01-12 15:14:25'),
(48, '::1', '2026-01-12 15:16:43'),
(49, '::1', '2026-01-12 15:19:01'),
(50, '::1', '2026-01-12 15:20:53'),
(51, '::1', '2026-01-12 15:21:19'),
(52, '::1', '2026-01-12 15:24:36'),
(53, '::1', '2026-01-12 15:25:01'),
(54, '::1', '2026-01-12 15:26:50'),
(55, '::1', '2026-01-12 15:29:52'),
(56, '::1', '2026-01-12 15:32:43'),
(57, '::1', '2026-01-12 15:38:38'),
(58, '::1', '2026-01-12 15:39:00'),
(59, '::1', '2026-01-12 15:39:39'),
(60, '::1', '2026-01-12 15:40:47'),
(61, '::1', '2026-01-12 15:43:36'),
(62, '::1', '2026-01-12 15:44:20'),
(63, '::1', '2026-01-12 15:46:48'),
(64, '::1', '2026-01-12 15:48:41'),
(65, '::1', '2026-01-12 15:50:01'),
(66, '::1', '2026-01-12 15:50:33'),
(67, '::1', '2026-01-12 16:05:30'),
(68, '::1', '2026-01-12 16:17:52'),
(69, '::1', '2026-01-12 16:49:37'),
(70, '::1', '2026-01-12 17:03:38'),
(71, '::1', '2026-01-12 17:19:57'),
(72, '::1', '2026-01-12 17:20:58'),
(73, '::1', '2026-01-12 17:24:24'),
(74, '::1', '2026-01-12 17:25:56'),
(75, '::1', '2026-01-12 18:11:26'),
(76, '::1', '2026-01-12 18:16:56'),
(77, '::1', '2026-01-12 18:27:28');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `archive_files`
--
ALTER TABLE `archive_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `archive_id` (`archive_id`);

--
-- Indexes for table `archive_list`
--
ALTER TABLE `archive_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `curriculum_id` (`curriculum_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `chat_keys`
--
ALTER TABLE `chat_keys`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `owner_unique` (`owner_type`,`owner_id`);

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `t_idx` (`thread_id`);

--
-- Indexes for table `chat_threads`
--
ALTER TABLE `chat_threads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ua_idx` (`user_id`,`admin_id`);

--
-- Indexes for table `curriculum_list`
--
ALTER TABLE `curriculum_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `department_list`
--
ALTER TABLE `department_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `faqs`
--
ALTER TABLE `faqs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `id_registry`
--
ALTER TABLE `id_registry`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_type_id` (`account_type`,`id_number`);

--
-- Indexes for table `page_views`
--
ALTER TABLE `page_views`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_page_slug` (`page_slug`),
  ADD KEY `idx_viewed_at` (`viewed_at`),
  ADD KEY `idx_page_slug_viewed_at` (`page_slug`,`viewed_at`);

--
-- Indexes for table `page_visits`
--
ALTER TABLE `page_visits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_page_visits_name_date` (`page_name`,`visit_date`),
  ADD KEY `idx_page_visits_date` (`visit_date`);

--
-- Indexes for table `student_list`
--
ALTER TABLE `student_list`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`) USING HASH,
  ADD KEY `department_id` (`department_id`),
  ADD KEY `curriculum_id` (`curriculum_id`);

--
-- Indexes for table `system_info`
--
ALTER TABLE `system_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `visitors`
--
ALTER TABLE `visitors`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `archive_files`
--
ALTER TABLE `archive_files`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `archive_list`
--
ALTER TABLE `archive_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `chat_keys`
--
ALTER TABLE `chat_keys`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `chat_threads`
--
ALTER TABLE `chat_threads`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `curriculum_list`
--
ALTER TABLE `curriculum_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `department_list`
--
ALTER TABLE `department_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `faqs`
--
ALTER TABLE `faqs`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `id_registry`
--
ALTER TABLE `id_registry`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `page_views`
--
ALTER TABLE `page_views`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT for table `page_visits`
--
ALTER TABLE `page_visits`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `student_list`
--
ALTER TABLE `student_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `system_info`
--
ALTER TABLE `system_info`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `visitors`
--
ALTER TABLE `visitors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `archive_files`
--
ALTER TABLE `archive_files`
  ADD CONSTRAINT `fk_archive_file` FOREIGN KEY (`archive_id`) REFERENCES `archive_list` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD CONSTRAINT `chat_messages_thread_fk` FOREIGN KEY (`thread_id`) REFERENCES `chat_threads` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
