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
-- Struktura tabulky `prefix_acl`
--

CREATE TABLE `prefix_acl` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_role` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'vazba na role',
  `id_resource` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'vazba na zdroj',
  `id_privilege` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'vazba na opravneni',
  `active` tinyint(1) DEFAULT '0' COMMENT 'aktivni',
  `position` bigint(20) UNSIGNED DEFAULT '0' COMMENT 'poradi'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='acl';

--
-- Vypisuji data pro tabulku `prefix_acl`
--

INSERT INTO `prefix_acl` (`id`, `id_role`, `id_resource`, `id_privilege`, `active`, `position`) VALUES
(156, 1, 1, 1, 1, 0),
(157, 1, 1, 2, 1, 0),
(158, 1, 2, 3, 1, 0),
(159, 1, 2, 4, 1, 0);

--
-- Klíče pro exportované tabulky
--

--
-- Klíče pro tabulku `prefix_acl`
--
ALTER TABLE `prefix_acl`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_resource_privilege_UNIQUE` (`id_role`,`id_resource`,`id_privilege`),
  ADD KEY `fk_acl_acl_privilege_idx` (`id_privilege`),
  ADD KEY `fk_acl_acl_role_idx` (`id_role`),
  ADD KEY `fk_acl_acl_resource_idx` (`id_resource`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `prefix_acl`
--
ALTER TABLE `prefix_acl`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=160;
--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `prefix_acl`
--
ALTER TABLE `prefix_acl`
  ADD CONSTRAINT `fk_acl_acl_privilege` FOREIGN KEY (`id_privilege`) REFERENCES `prefix_acl_privilege` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_acl_acl_resource` FOREIGN KEY (`id_resource`) REFERENCES `prefix_acl_resource` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_acl_acl_role` FOREIGN KEY (`id_role`) REFERENCES `prefix_acl_role` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
