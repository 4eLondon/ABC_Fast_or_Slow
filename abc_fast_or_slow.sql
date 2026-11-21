-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Nov 11, 2026 at 03:41 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `abc_fast_or_slow`
--

-- --------------------------------------------------------

--
-- Table structure for table `answers`
--

CREATE TABLE `answers` (
  `id` int(11) NOT NULL,
  `round_id` int(11) NOT NULL,
  `room_player_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `value` varchar(40) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `label` varchar(30) NOT NULL,
  `hint` varchar(60) NOT NULL DEFAULT '',
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `label`, `hint`, `is_default`, `is_active`, `created_at`) VALUES
(1, 'Boy', 'A boy\'s name', 1, 1, '2026-11-10 23:19:02'),
(2, 'Girl', 'A girl\'s name', 1, 1, '2026-11-10 23:19:02'),
(3, 'Animal', 'Any animal', 1, 1, '2026-11-10 23:19:02'),
(4, 'Place', 'City, country, town', 1, 1, '2026-11-10 23:19:02'),
(5, 'Food', 'Something you can eat', 1, 1, '2026-11-10 23:19:02'),
(6, 'Thing', 'An everyday object', 0, 1, '2026-11-10 23:19:02'),
(7, 'Brand', 'A company or product', 0, 1, '2026-11-10 23:19:02'),
(8, 'Job', 'An occupation', 0, 1, '2026-11-10 23:19:02'),
(9, 'Movie', 'A film title', 0, 1, '2026-11-10 23:19:02');

-- --------------------------------------------------------

--
-- Table structure for table `flags`
--

CREATE TABLE `flags` (
  `answer_id` int(11) NOT NULL,
  `flagger_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `code` char(4) NOT NULL,
  `mode` enum('local','multi') NOT NULL DEFAULT 'multi',
  `host_id` int(11) NOT NULL,
  `status` enum('lobby','playing','finished','cancelled') NOT NULL DEFAULT 'lobby',
  `phase` enum('lobby','letter','answering','review','results','finished') NOT NULL DEFAULT 'lobby',
  `letter_picker` enum('alphabet','wheel') NOT NULL DEFAULT 'alphabet',
  `default_speed` enum('fast','slow') NOT NULL DEFAULT 'slow',
  `fast_seconds` smallint(5) UNSIGNED NOT NULL DEFAULT 30,
  `slow_seconds` smallint(5) UNSIGNED NOT NULL DEFAULT 90,
  `total_rounds` tinyint(3) UNSIGNED NOT NULL DEFAULT 5,
  `version` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `winner_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `started_at` datetime DEFAULT NULL,
  `finished_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `room_categories`
--

CREATE TABLE `room_categories` (
  `room_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `room_players`
--

CREATE TABLE `room_players` (
  `id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_units` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `last_seen` datetime DEFAULT NULL,
  `left_at` datetime DEFAULT NULL,
  `joined_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rounds`
--

CREATE TABLE `rounds` (
  `id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `number` tinyint(3) UNSIGNED NOT NULL,
  `caller_id` int(11) NOT NULL,
  `speed` enum('fast','slow') DEFAULT NULL,
  `letter` char(1) DEFAULT NULL,
  `started_at` datetime DEFAULT NULL,
  `deadline_at` datetime DEFAULT NULL,
  `stopped_at` datetime DEFAULT NULL,
  `stopped_by` int(11) DEFAULT NULL,
  `end_reason` enum('first_finished','timer','host') DEFAULT NULL,
  `scored_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `round_scores`
--

CREATE TABLE `round_scores` (
  `id` int(11) NOT NULL,
  `round_id` int(11) NOT NULL,
  `room_player_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `status` enum('accepted','rejected','blank','wrong_letter') NOT NULL,
  `units` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `duplicates` tinyint(3) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `submissions`
--

CREATE TABLE `submissions` (
  `round_id` int(11) NOT NULL,
  `room_player_id` int(11) NOT NULL,
  `submitted_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('player','admin') NOT NULL DEFAULT 'player',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `color` char(7) NOT NULL DEFAULT '#4F8CFF',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `role`, `status`, `color`, `created_at`) VALUES
(1, 'admin_one', 'admin1@abcgame.test', '$2y$10$jTXlpnc5E3g52CuGMfe0Vuv0x672FFR1pDYMMo2RRuWhpwose.ymS', 'admin', 'active', '#FF6B6B', '2026-11-10 23:19:02'),
(2, 'admin_two', 'admin2@abcgame.test', '$2y$10$jTXlpnc5E3g52CuGMfe0Vuv0x672FFR1pDYMMo2RRuWhpwose.ymS', 'admin', 'active', '#FFB84D', '2026-11-10 23:19:02'),
(3, 'kemar', 'kemar@abcgame.test', '$2y$10$jTXlpnc5E3g52CuGMfe0Vuv0x672FFR1pDYMMo2RRuWhpwose.ymS', 'player', 'active', '#4F8CFF', '2026-11-10 23:19:02'),
(4, 'tanya', 'tanya@abcgame.test', '$2y$10$jTXlpnc5E3g52CuGMfe0Vuv0x672FFR1pDYMMo2RRuWhpwose.ymS', 'player', 'active', '#3DD6A5', '2026-11-10 23:19:02'),
(5, 'andre', 'andre@abcgame.test', '$2y$10$jTXlpnc5E3g52CuGMfe0Vuv0x672FFR1pDYMMo2RRuWhpwose.ymS', 'player', 'active', '#B388FF', '2026-11-10 23:19:02'),
(6, 'shanice', 'shanice@abcgame.test', '$2y$10$jTXlpnc5E3g52CuGMfe0Vuv0x672FFR1pDYMMo2RRuWhpwose.ymS', 'player', 'active', '#FF8FB1', '2026-11-10 23:19:02'),
(7, 'jerome', 'jerome@abcgame.test', '$2y$10$jTXlpnc5E3g52CuGMfe0Vuv0x672FFR1pDYMMo2RRuWhpwose.ymS', 'player', 'active', '#5CE1E6', '2026-11-10 23:19:02'),
(8, 'nadia', 'nadia@abcgame.test', '$2y$10$jTXlpnc5E3g52CuGMfe0Vuv0x672FFR1pDYMMo2RRuWhpwose.ymS', 'player', 'active', '#FFD166', '2026-11-10 23:19:02'),
(9, 'devon', 'devon@abcgame.test', '$2y$10$jTXlpnc5E3g52CuGMfe0Vuv0x672FFR1pDYMMo2RRuWhpwose.ymS', 'player', 'active', '#8AC926', '2026-11-10 23:19:02'),
(10, 'alicia', 'alicia@abcgame.test', '$2y$10$jTXlpnc5E3g52CuGMfe0Vuv0x672FFR1pDYMMo2RRuWhpwose.ymS', 'player', 'active', '#FF9F68', '2026-11-10 23:19:02');

-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

CREATE TABLE `votes` (
  `id` int(11) NOT NULL,
  `answer_id` int(11) NOT NULL,
  `voter_id` int(11) NOT NULL,
  `vote` enum('accept','reject') NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `answers`
--
ALTER TABLE `answers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_answer` (`round_id`,`room_player_id`,`category_id`),
  ADD KEY `fk_ans_player` (`room_player_id`),
  ADD KEY `fk_ans_category` (`category_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_categories_label` (`label`);

--
-- Indexes for table `flags`
--
ALTER TABLE `flags`
  ADD PRIMARY KEY (`answer_id`,`flagger_id`),
  ADD KEY `fk_flags_flagger` (`flagger_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_rooms_code` (`code`),
  ADD KEY `fk_rooms_host` (`host_id`),
  ADD KEY `fk_rooms_winner` (`winner_id`);

--
-- Indexes for table `room_categories`
--
ALTER TABLE `room_categories`
  ADD PRIMARY KEY (`room_id`,`category_id`),
  ADD KEY `fk_rc_category` (`category_id`);

--
-- Indexes for table `room_players`
--
ALTER TABLE `room_players`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_room_user` (`room_id`,`user_id`),
  ADD KEY `fk_rp_user` (`user_id`);

--
-- Indexes for table `rounds`
--
ALTER TABLE `rounds`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_round_number` (`room_id`,`number`),
  ADD KEY `fk_rounds_caller` (`caller_id`),
  ADD KEY `fk_rounds_stopper` (`stopped_by`);

--
-- Indexes for table `round_scores`
--
ALTER TABLE `round_scores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_score` (`round_id`,`room_player_id`,`category_id`),
  ADD KEY `fk_rs_player` (`room_player_id`),
  ADD KEY `fk_rs_category` (`category_id`);

--
-- Indexes for table `submissions`
--
ALTER TABLE `submissions`
  ADD PRIMARY KEY (`round_id`,`room_player_id`),
  ADD KEY `fk_sub_player` (`room_player_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_users_username` (`username`),
  ADD UNIQUE KEY `uq_users_email` (`email`);

--
-- Indexes for table `votes`
--
ALTER TABLE `votes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_vote` (`answer_id`,`voter_id`),
  ADD KEY `fk_votes_voter` (`voter_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `answers`
--
ALTER TABLE `answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `room_players`
--
ALTER TABLE `room_players`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rounds`
--
ALTER TABLE `rounds`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `round_scores`
--
ALTER TABLE `round_scores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `votes`
--
ALTER TABLE `votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `answers`
--
ALTER TABLE `answers`
  ADD CONSTRAINT `fk_ans_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `fk_ans_player` FOREIGN KEY (`room_player_id`) REFERENCES `room_players` (`id`),
  ADD CONSTRAINT `fk_ans_round` FOREIGN KEY (`round_id`) REFERENCES `rounds` (`id`);

--
-- Constraints for table `flags`
--
ALTER TABLE `flags`
  ADD CONSTRAINT `fk_flags_answer` FOREIGN KEY (`answer_id`) REFERENCES `answers` (`id`),
  ADD CONSTRAINT `fk_flags_flagger` FOREIGN KEY (`flagger_id`) REFERENCES `room_players` (`id`);

--
-- Constraints for table `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `fk_rooms_host` FOREIGN KEY (`host_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_rooms_winner` FOREIGN KEY (`winner_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `room_categories`
--
ALTER TABLE `room_categories`
  ADD CONSTRAINT `fk_rc_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `fk_rc_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`);

--
-- Constraints for table `room_players`
--
ALTER TABLE `room_players`
  ADD CONSTRAINT `fk_rp_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`),
  ADD CONSTRAINT `fk_rp_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `rounds`
--
ALTER TABLE `rounds`
  ADD CONSTRAINT `fk_rounds_caller` FOREIGN KEY (`caller_id`) REFERENCES `room_players` (`id`),
  ADD CONSTRAINT `fk_rounds_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`),
  ADD CONSTRAINT `fk_rounds_stopper` FOREIGN KEY (`stopped_by`) REFERENCES `room_players` (`id`);

--
-- Constraints for table `round_scores`
--
ALTER TABLE `round_scores`
  ADD CONSTRAINT `fk_rs_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `fk_rs_player` FOREIGN KEY (`room_player_id`) REFERENCES `room_players` (`id`),
  ADD CONSTRAINT `fk_rs_round` FOREIGN KEY (`round_id`) REFERENCES `rounds` (`id`);

--
-- Constraints for table `submissions`
--
ALTER TABLE `submissions`
  ADD CONSTRAINT `fk_sub_player` FOREIGN KEY (`room_player_id`) REFERENCES `room_players` (`id`),
  ADD CONSTRAINT `fk_sub_round` FOREIGN KEY (`round_id`) REFERENCES `rounds` (`id`);

--
-- Constraints for table `votes`
--
ALTER TABLE `votes`
  ADD CONSTRAINT `fk_votes_answer` FOREIGN KEY (`answer_id`) REFERENCES `answers` (`id`),
  ADD CONSTRAINT `fk_votes_voter` FOREIGN KEY (`voter_id`) REFERENCES `room_players` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
