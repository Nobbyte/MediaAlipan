SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE `files` (
  `id` int NOT NULL,
  `library_id` int NOT NULL,
  `media_id` int NOT NULL,
  `season` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `share_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `file_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `share_pwd` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `size` bigint DEFAULT NULL,
  `resolution` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `download_url` varchar(3000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `expire_time` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `library` (
  `id` int NOT NULL,
  `name` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '1',
  `notes` varchar(1000) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `medias` (
  `id` int NOT NULL,
  `name` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `library_id` int NOT NULL,
  `tmdb` json DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `system` (
  `id` int NOT NULL,
  `token` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `open_token` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `open_token_url` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `parent_file_id` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `drive_id` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tmdb_api_key` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `system` (`id`, `token`, `open_token`, `open_token_url`, `parent_file_id`, `drive_id`, `tmdb_api_key`) VALUES
(1, 'token', 'open_token', 'open_token_url', 'parent_file_id', 'drive_id', 'tmdb_api_key');


ALTER TABLE `files`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `library`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `medias`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `system`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `files`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `library`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `medias`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `system`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
