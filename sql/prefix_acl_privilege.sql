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
-- Struktura tabulky `prefix_acl_privilege`
--

CREATE TABLE `prefix_acl_privilege` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `privilege` varchar(255) DEFAULT NULL COMMENT 'opravneni'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='acl opravneni';

--
-- Vypisuji data pro tabulku `prefix_acl_privilege`
--

INSERT INTO `prefix_acl_privilege` (`id`, `privilege`) VALUES
(3, 'delete'),
(1, 'insert'),
(4, 'show'),
(2, 'update');

--
-- Klíče pro exportované tabulky
--

--
-- Klíče pro tabulku `prefix_acl_privilege`
--
ALTER TABLE `prefix_acl_privilege`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `privilege_UNIQUE` (`privilege`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `prefix_acl_privilege`
--
ALTER TABLE `prefix_acl_privilege`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
