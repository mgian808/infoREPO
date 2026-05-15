-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Creato il: Apr 24, 2026 alle 09:50
-- Versione del server: 8.0.45
-- Versione PHP: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `skillswap`
--

DELIMITER $$
--
-- Procedure
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_matches` (IN `myId` INT)   BEGIN
    SELECT DISTINCT 
            u.username, 
            u.idUtente,
            u.classe, 
            v_altri.materia, 
            v_altri.argomento 
        FROM utenti u
        JOIN voti v_altri ON u.idUtente = v_altri.idUtente
        JOIN voti v_miei ON v_altri.materia = v_miei.materia
        WHERE v_miei.idUtente = myId
          AND v_miei.tipo = 'CERCO'
          AND v_altri.tipo = 'OFFRO'
          AND u.idUtente != myId;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Struttura della tabella `materie`
--

CREATE TABLE `materie` (
  `idMateria` int NOT NULL,
  `idUtente` int NOT NULL,
  `materia` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `descrizione` text COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `materie`
--

INSERT INTO `materie` (`idMateria`, `idUtente`, `materia`, `descrizione`) VALUES
(2, 1, 'Matematica', 'bleahhh'),
(3, 1, 'Italiano', 'che noiaaaa'),
(4, 2, 'Matematica', 'che schifooo'),
(5, 2, 'Italiano', 'che palleefefefef'),
(6, 2, 'Storia', 'oooooo basta sono tutti morti'),
(7, 1, 'Informatica', 'ci sta lab'),
(8, 2, 'informatica', 'yeaaaah'),
(9, 3, 'Storia', 'non mi piace per niente'),
(10, 3, 'Italiano', 'mhhhhhh che palleee'),
(11, 3, 'Matematica', 'non ne posso più');

-- --------------------------------------------------------

--
-- Struttura della tabella `messaggi`
--

CREATE TABLE `messaggi` (
  `idMessaggio` int NOT NULL,
  `idMittente` int NOT NULL,
  `idDestinatario` int NOT NULL,
  `testo` text COLLATE utf8mb4_general_ci NOT NULL,
  `ip_mittente` varchar(45) COLLATE utf8mb4_general_ci NOT NULL,
  `data_invio` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `letto` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `messaggi`
--

INSERT INTO `messaggi` (`idMessaggio`, `idMittente`, `idDestinatario`, `testo`, `ip_mittente`, `data_invio`, `letto`) VALUES
(1, 1, 2, 'cccc', '172.21.0.1', '2026-03-13 10:07:37', 1),
(2, 1, 2, 'cc', '172.21.0.1', '2026-03-13 10:07:38', 1),
(3, 1, 2, 'c', '172.21.0.1', '2026-03-13 10:07:39', 1),
(4, 1, 2, 'c', '172.21.0.1', '2026-03-13 10:07:40', 1),
(5, 1, 2, 'c', '172.21.0.1', '2026-03-13 10:07:41', 1),
(6, 1, 2, 'ciao', '172.21.0.1', '2026-03-13 10:19:33', 1),
(7, 2, 1, 'ciao fra', '172.21.0.1', '2026-03-13 10:20:08', 1),
(8, 2, 1, 'dimmi tutto', '172.21.0.1', '2026-03-13 10:20:13', 1),
(9, 1, 2, 'no niente chill', '172.21.0.1', '2026-03-13 10:34:24', 1),
(10, 1, 2, 'come stai?', '172.21.0.1', '2026-03-13 10:35:05', 1),
(11, 3, 2, 'Ciao skitto, ho assolutamente bisogno di una mano con gli integrali indefiniti... ci possiamo sentire anche oggi per me, rispondimi il prima possibile grazie', '::1', '2026-03-15 18:20:34', 1),
(12, 2, 3, 'ciao marta, si se vuoi verso le 4 di questo pomeriggio sono libero...', '::1', '2026-03-15 18:21:42', 1),
(13, 2, 3, 'appena posso ti mando un link di meet così mi fai vedere cosa non riesci a fare', '::1', '2026-03-15 18:22:09', 1),
(14, 3, 2, 'top grazie milleeee', '::1', '2026-03-15 18:31:59', 1),
(15, 3, 1, 'Ciao purple, ho bisogno di un aiuto con giovanni verga...plssss', '::1', '2026-03-15 20:50:36', 1),
(16, 1, 3, 'sisi ok', '::1', '2026-03-15 21:21:11', 1),
(17, 1, 3, 'se vuoi domani ci sono', '::1', '2026-03-15 21:21:27', 1),
(18, 1, 3, 'mi dai una mano con gli integrali indefiniti?', '172.21.0.1', '2026-03-31 07:43:41', 0),
(19, 1, 2, 'o', '172.21.0.1', '2026-04-24 08:23:18', 1),
(20, 1, 2, 'o', '172.21.0.1', '2026-04-24 08:23:20', 1),
(21, 1, 2, 'o', '172.21.0.1', '2026-04-24 08:23:20', 1),
(22, 1, 2, 'o', '172.21.0.1', '2026-04-24 08:23:21', 1),
(23, 1, 2, 'o', '172.21.0.1', '2026-04-24 08:23:22', 1),
(24, 1, 2, 'o', '172.21.0.1', '2026-04-24 08:23:23', 1),
(25, 1, 2, 'o', '172.21.0.1', '2026-04-24 08:23:24', 1),
(26, 1, 2, 'o', '172.21.0.1', '2026-04-24 08:23:25', 1),
(27, 1, 2, 'o', '172.21.0.1', '2026-04-24 08:23:26', 1),
(28, 1, 2, 'o', '172.21.0.1', '2026-04-24 08:23:27', 1),
(29, 1, 2, 'o', '172.21.0.1', '2026-04-24 08:23:27', 1),
(30, 1, 2, 'o', '172.21.0.1', '2026-04-24 08:23:28', 1),
(31, 1, 2, 'o', '172.21.0.1', '2026-04-24 08:23:29', 1),
(32, 1, 2, 'o', '172.21.0.1', '2026-04-24 08:23:30', 1),
(33, 1, 2, 'o', '172.21.0.1', '2026-04-24 08:23:31', 1),
(34, 1, 2, 'o', '172.21.0.1', '2026-04-24 08:23:32', 1),
(35, 1, 2, 'oo', '172.21.0.1', '2026-04-24 08:23:34', 1),
(36, 1, 2, 'o', '172.21.0.1', '2026-04-24 08:23:36', 1),
(37, 1, 2, 'o', '172.21.0.1', '2026-04-24 08:23:37', 1),
(38, 1, 2, 'o', '172.21.0.1', '2026-04-24 08:23:39', 1),
(39, 1, 2, 'o', '172.21.0.1', '2026-04-24 08:24:01', 1),
(40, 1, 2, 'o', '172.21.0.1', '2026-04-24 08:24:03', 1),
(41, 1, 2, 'o', '172.21.0.1', '2026-04-24 08:24:04', 1),
(42, 1, 2, 'o', '172.21.0.1', '2026-04-24 08:24:05', 1),
(43, 1, 2, 'o', '172.21.0.1', '2026-04-24 08:24:06', 1),
(44, 1, 2, 'o', '172.21.0.1', '2026-04-24 08:24:07', 1),
(45, 1, 2, 'o', '172.21.0.1', '2026-04-24 08:24:08', 1),
(46, 1, 2, 'o', '172.21.0.1', '2026-04-24 08:24:09', 1),
(47, 1, 2, 'o', '172.21.0.1', '2026-04-24 08:24:10', 1),
(48, 1, 2, 'o', '172.21.0.1', '2026-04-24 08:24:12', 1),
(49, 1, 2, 'o', '172.21.0.1', '2026-04-24 08:24:13', 1),
(50, 1, 2, 'o', '172.21.0.1', '2026-04-24 08:24:13', 1),
(51, 1, 2, 'o', '172.21.0.1', '2026-04-24 08:24:15', 1),
(52, 1, 2, 'o', '172.21.0.1', '2026-04-24 08:24:16', 1),
(53, 1, 2, 'o', '172.21.0.1', '2026-04-24 08:24:17', 1),
(54, 1, 2, 'o', '172.21.0.1', '2026-04-24 08:24:19', 1),
(55, 1, 2, 'o', '172.21.0.1', '2026-04-24 08:24:20', 1),
(56, 1, 2, 'o', '172.21.0.1', '2026-04-24 08:24:21', 1),
(57, 1, 2, 'o', '172.21.0.1', '2026-04-24 08:24:23', 1),
(58, 1, 2, 'o', '172.21.0.1', '2026-04-24 08:24:24', 1),
(59, 1, 2, 'a', '172.21.0.1', '2026-04-24 08:24:42', 1),
(60, 1, 2, 'a', '172.21.0.1', '2026-04-24 08:25:39', 1),
(61, 1, 3, 'a', '172.21.0.1', '2026-04-24 08:25:44', 0),
(62, 1, 3, 'a', '172.21.0.1', '2026-04-24 08:26:23', 0),
(63, 1, 2, 'a', '172.21.0.1', '2026-04-24 08:26:32', 1),
(64, 1, 2, 'a', '172.21.0.1', '2026-04-24 08:29:55', 1),
(65, 1, 2, 'a', '172.21.0.1', '2026-04-24 08:30:04', 1),
(66, 1, 2, 'a', '172.21.0.1', '2026-04-24 08:36:59', 1),
(67, 1, 3, 'a', '172.21.0.1', '2026-04-24 08:37:05', 0),
(68, 1, 3, 'a', '172.21.0.1', '2026-04-24 08:37:06', 0),
(69, 1, 3, 'a', '172.21.0.1', '2026-04-24 08:37:07', 0),
(70, 1, 3, 'a', '172.21.0.1', '2026-04-24 08:37:08', 0),
(71, 1, 3, 'e', '172.21.0.1', '2026-04-24 08:37:10', 0),
(72, 1, 3, 'e', '172.21.0.1', '2026-04-24 08:37:11', 0),
(73, 1, 3, 'e', '172.21.0.1', '2026-04-24 08:37:12', 0),
(74, 1, 3, 'i', '172.21.0.1', '2026-04-24 08:37:17', 0),
(75, 1, 3, 'i', '172.21.0.1', '2026-04-24 08:37:18', 0),
(76, 1, 3, 'o', '172.21.0.1', '2026-04-24 08:37:20', 0),
(77, 1, 3, 'o', '172.21.0.1', '2026-04-24 08:37:21', 0),
(78, 1, 3, '1', '172.21.0.1', '2026-04-24 08:37:28', 0),
(79, 1, 3, '2', '172.21.0.1', '2026-04-24 08:37:29', 0),
(80, 1, 3, '3', '172.21.0.1', '2026-04-24 08:37:30', 0),
(81, 1, 3, '4', '172.21.0.1', '2026-04-24 08:37:31', 0),
(82, 1, 3, '4', '172.21.0.1', '2026-04-24 08:37:33', 0),
(83, 2, 1, 'kkk', '172.21.0.1', '2026-04-24 09:12:03', 1),
(84, 2, 1, 'quando vuoi', '172.21.0.1', '2026-04-24 09:14:33', 1),
(85, 2, 1, '1', '172.21.0.1', '2026-04-24 09:27:59', 1),
(86, 2, 1, '1', '172.21.0.1', '2026-04-24 09:27:59', 1),
(87, 2, 1, '2', '172.21.0.1', '2026-04-24 09:28:00', 1),
(88, 2, 1, '2', '172.21.0.1', '2026-04-24 09:28:00', 1),
(89, 2, 1, '3', '172.21.0.1', '2026-04-24 09:28:01', 1),
(90, 2, 1, '3', '172.21.0.1', '2026-04-24 09:28:01', 1),
(91, 2, 1, '4', '172.21.0.1', '2026-04-24 09:28:03', 1),
(92, 2, 1, '4', '172.21.0.1', '2026-04-24 09:28:03', 1),
(93, 2, 1, '5', '172.21.0.1', '2026-04-24 09:28:04', 1),
(94, 2, 1, '5', '172.21.0.1', '2026-04-24 09:28:04', 1),
(95, 2, 1, '6', '172.21.0.1', '2026-04-24 09:28:46', 1),
(96, 2, 1, '6', '172.21.0.1', '2026-04-24 09:28:46', 1),
(97, 2, 3, 'ciao', '172.21.0.1', '2026-04-24 09:28:55', 0),
(98, 2, 3, 'ciao', '172.21.0.1', '2026-04-24 09:28:55', 0),
(99, 2, 3, 'd', '172.21.0.1', '2026-04-24 09:29:42', 0),
(100, 2, 3, 'd', '172.21.0.1', '2026-04-24 09:29:42', 0),
(101, 2, 3, 'd', '172.21.0.1', '2026-04-24 09:32:03', 0),
(102, 2, 3, 'd', '172.21.0.1', '2026-04-24 09:32:03', 0),
(103, 2, 3, 'f', '172.21.0.1', '2026-04-24 09:32:07', 0),
(104, 2, 3, 'f', '172.21.0.1', '2026-04-24 09:32:07', 0),
(105, 2, 3, 'r', '172.21.0.1', '2026-04-24 09:34:52', 0),
(106, 2, 3, 'i', '172.21.0.1', '2026-04-24 09:34:56', 0),
(107, 2, 1, 'cos\'è dc?', '172.21.0.1', '2026-04-24 09:35:13', 1),
(108, 1, 2, 'la merda di sburella', '172.21.0.1', '2026-04-24 09:35:39', 0);

-- --------------------------------------------------------

--
-- Struttura della tabella `stress_log`
--

CREATE TABLE `stress_log` (
  `id_stress` int NOT NULL,
  `idUtente` int NOT NULL,
  `livello` int NOT NULL,
  `data_voto` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `stress_log`
--

INSERT INTO `stress_log` (`id_stress`, `idUtente`, `livello`, `data_voto`) VALUES
(54, 1, 6, '2026-04-14 00:00:00'),
(55, 1, 5, '2026-04-21 00:00:00'),
(56, 2, 6, '2026-04-21 00:00:00'),
(57, 3, 4, '2026-04-21 00:00:00'),
(59, 4, 7, '2026-04-21 09:39:51'),
(60, 5, 6, '2026-04-21 09:43:26'),
(61, 5, 4, '2026-04-24 10:11:09'),
(62, 1, 9, '2026-04-24 10:44:13'),
(63, 2, 10, '2026-04-24 11:08:35');

-- --------------------------------------------------------

--
-- Struttura della tabella `utenti`
--

CREATE TABLE `utenti` (
  `idUtente` int NOT NULL,
  `nome` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `cognome` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `indirizzo` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `dataNascita` date NOT NULL,
  `provinciaIstituto` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `comuneIstituto` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `nomeIstituto` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `classe` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `utenti`
--

INSERT INTO `utenti` (`idUtente`, `nome`, `cognome`, `username`, `indirizzo`, `dataNascita`, `provinciaIstituto`, `comuneIstituto`, `nomeIstituto`, `classe`, `telefono`, `email`, `password`) VALUES
(1, 'Mattia', 'Giancristofaro', 'purplehaze', 'via lago di garda 7', '2008-01-14', 'VENEZIA', 'VENEZIA', 'VETF04000T', '5', '3396344400', 'mgiancry@gmail.com', '$2y$10$j/ursAP0TFVoTRyDQC6yeObQmzHGS5FT4op03j9uXLnWhX2z8lvKm'),
(2, 'ciccio', 'bello', 'skitto', 'via sant\'elena 9', '2026-02-20', 'VENEZIA', 'VENEZIA', 'CARLO ZUCCANTE (VETF04000T)', '5', '3473030022', 'ciccio@bello.com', '$2y$10$XncMC.JaQ/7lEtO0PMGzRuJGY4bXkjNv1FSOQA6RBestMt61jXvLS'),
(3, 'Marta', 'Lodi', 'LodiMartaa', 'via umberto sailer 8', '2009-03-14', 'VENEZIA', 'DOLO', 'CESARE MUSATTI (VERH03000V)', '4', '3480096298', 'marta@lodi.com', '$2y$10$Hoxf3xCJABHNpQju0/O2kuTlW6nfqeZxfDxz9WCWGyjtGWcqReowO'),
(4, 'marco', 'castagna', 'markc', 'via sant\'elena 69', '2008-04-01', 'BOLOGNA', 'BOLOGNA', 'ISTITUTO PENALE MINORILE -\"P. SICILIANI\" (BORH022065)', '4', '3696781230', 'marco@castagna.com', '$2y$10$40hyEIxWes60Np6POBgIy.FKkRiYzv2ZpWXjTMnOQp0tXCSjJliuC'),
(5, 'luigi', 'verdi', 'giggi', 'via zingara 20', '2009-10-20', 'VENEZIA', 'VENEZIA', 'I.I.S. LUIGI STEFANINI (VEPM02000G)', '3', '3204567803', 'luigi@verdi.com', '$2y$10$zgmEVD.j0yEAGTW5UtEQq.QNq3C/9e2jxPse041tROJI.WhETwPG.');

-- --------------------------------------------------------

--
-- Struttura della tabella `voti`
--

CREATE TABLE `voti` (
  `idVoto` int NOT NULL,
  `idUtente` int NOT NULL,
  `materia` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `argomento` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `voto` decimal(4,2) NOT NULL,
  `tipo` enum('OFFRO','CERCO','NEUTRO') COLLATE utf8mb4_general_ci NOT NULL,
  `data_inserimento` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `voti`
--

INSERT INTO `voti` (`idVoto`, `idUtente`, `materia`, `argomento`, `voto`, `tipo`, `data_inserimento`) VALUES
(1, 1, 'Matematica', 'integrali indefiniti', 3.00, 'CERCO', '2026-02-27 09:47:55'),
(2, 1, 'Matematica', 'integrali indefiniti', 5.00, 'CERCO', '2026-02-27 09:48:07'),
(3, 1, 'Italiano', 'dante alighieri', 7.00, 'NEUTRO', '2026-02-27 09:48:34'),
(4, 1, 'Italiano', 'giovanni verga', 8.00, 'OFFRO', '2026-02-27 09:49:04'),
(5, 2, 'Matematica', 'integrali indefiniti', 7.75, 'OFFRO', '2026-02-27 09:51:31'),
(6, 2, 'Italiano', 'giovanni verga', 4.50, 'CERCO', '2026-02-27 09:51:45'),
(7, 2, 'Storia', 'prima guerra mondiale', 7.00, 'NEUTRO', '2026-02-27 09:52:05'),
(8, 1, 'Informatica', 'php', 9.00, 'OFFRO', '2026-02-27 10:19:26'),
(9, 2, 'informatica', 'programmazione php', 3.75, 'CERCO', '2026-02-27 10:20:23'),
(10, 3, 'Storia', 'prima guerra mondiale', 4.00, 'CERCO', '2026-03-15 18:15:20'),
(11, 3, 'Italiano', 'verga', 4.00, 'CERCO', '2026-03-15 18:17:39'),
(12, 3, 'Matematica', 'integrali', 3.00, 'CERCO', '2026-03-15 18:19:05'),
(13, 3, 'Matematica', 'integrali indefiniti', 7.75, 'OFFRO', '2026-03-31 00:00:00');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `materie`
--
ALTER TABLE `materie`
  ADD PRIMARY KEY (`idMateria`),
  ADD KEY `idUtente` (`idUtente`);

--
-- Indici per le tabelle `messaggi`
--
ALTER TABLE `messaggi`
  ADD PRIMARY KEY (`idMessaggio`),
  ADD KEY `idMittente` (`idMittente`),
  ADD KEY `idDestinatario` (`idDestinatario`),
  ADD KEY `idx_rate_limit` (`idMittente`,`data_invio`);

--
-- Indici per le tabelle `stress_log`
--
ALTER TABLE `stress_log`
  ADD PRIMARY KEY (`id_stress`,`data_voto`) USING BTREE,
  ADD UNIQUE KEY `idUtente` (`idUtente`,`data_voto`);

--
-- Indici per le tabelle `utenti`
--
ALTER TABLE `utenti`
  ADD PRIMARY KEY (`idUtente`);

--
-- Indici per le tabelle `voti`
--
ALTER TABLE `voti`
  ADD PRIMARY KEY (`idVoto`),
  ADD KEY `idUtente` (`idUtente`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `materie`
--
ALTER TABLE `materie`
  MODIFY `idMateria` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT per la tabella `messaggi`
--
ALTER TABLE `messaggi`
  MODIFY `idMessaggio` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;

--
-- AUTO_INCREMENT per la tabella `stress_log`
--
ALTER TABLE `stress_log`
  MODIFY `id_stress` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT per la tabella `utenti`
--
ALTER TABLE `utenti`
  MODIFY `idUtente` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT per la tabella `voti`
--
ALTER TABLE `voti`
  MODIFY `idVoto` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `materie`
--
ALTER TABLE `materie`
  ADD CONSTRAINT `materie_ibfk_1` FOREIGN KEY (`idUtente`) REFERENCES `utenti` (`idUtente`);

--
-- Limiti per la tabella `messaggi`
--
ALTER TABLE `messaggi`
  ADD CONSTRAINT `messaggi_ibfk_1` FOREIGN KEY (`idMittente`) REFERENCES `utenti` (`idUtente`) ON DELETE CASCADE,
  ADD CONSTRAINT `messaggi_ibfk_2` FOREIGN KEY (`idDestinatario`) REFERENCES `utenti` (`idUtente`) ON DELETE CASCADE;

--
-- Limiti per la tabella `stress_log`
--
ALTER TABLE `stress_log`
  ADD CONSTRAINT `stress_log_ibfk_1` FOREIGN KEY (`idUtente`) REFERENCES `utenti` (`idUtente`) ON DELETE CASCADE;

--
-- Limiti per la tabella `voti`
--
ALTER TABLE `voti`
  ADD CONSTRAINT `voti_ibfk_1` FOREIGN KEY (`idUtente`) REFERENCES `utenti` (`idUtente`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
