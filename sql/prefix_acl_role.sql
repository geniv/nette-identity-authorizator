-- phpMyAdmin SQL Dump
-- version 4.6.6deb4
-- https://www.phpmyadmin.net/
--
-- Počítač: localhost:3306
-- Vytvořeno: Sob 27. led 2018, 20:26
-- Verze serveru: 10.1.26-MariaDB-0+deb9u1
-- Verze PHP: 7.0.27-0+deb9u1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáze: `netteweb`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `prefix_acl_role`
--

CREATE TABLE `prefix_acl_role` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role` varchar(50) DEFAULT NULL COMMENT 'role'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='acl role';

--
-- Vypisuji data pro tabulku `prefix_acl_role`
--

INSERT INTO `prefix_acl_role` (`id`, `role`) VALUES
(1, 'admin'),
(2, 'moderator');

--
-- Klíče pro exportované tabulky
--

--
-- Klíče pro tabulku `prefix_acl_role`
--
ALTER TABLE `prefix_acl_role`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_UNIQUE` (`role`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `prefix_acl_role`
--
ALTER TABLE `prefix_acl_role`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
