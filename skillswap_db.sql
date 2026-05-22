-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Mag 22, 2026 alle 06:12
-- Versione del server: 10.4.32-MariaDB
-- Versione PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `skillswapprova`
--

DELIMITER $$
--
-- Procedure
--
CREATE PROCEDURE `get_matches` (IN `myId` INT)   BEGIN
    SELECT DISTINCT
        u.idUtente, u.username, u.nomeIstituto, u.classe,
        v_loro.materia_norm AS materia_offerta,
        v_mio.materia_norm  AS materia_cercata
    FROM utenti u
    JOIN voti v_loro       ON v_loro.idUtente      = u.idUtente AND v_loro.tipo = 'OFFRO'
    JOIN voti v_mio        ON v_mio.idUtente        = myId      AND v_mio.tipo  = 'CERCO'
                           AND v_mio.materia_norm   = v_loro.materia_norm
    JOIN voti v_offro_mio  ON v_offro_mio.idUtente  = myId      AND v_offro_mio.tipo  = 'OFFRO'
    JOIN voti v_cerco_loro ON v_cerco_loro.idUtente = u.idUtente AND v_cerco_loro.tipo = 'CERCO'
                           AND v_cerco_loro.materia_norm = v_offro_mio.materia_norm
    WHERE u.idUtente != myId;
END$$

CREATE PROCEDURE `get_matches_scored` (IN `myId` INT)   BEGIN
    DECLARE myIstituto VARCHAR(255);
    DECLARE myClasse   TINYINT;
    SELECT nomeIstituto, classe INTO myIstituto, myClasse
    FROM utenti WHERE idUtente = myId;

    SELECT
        u.idUtente,
        u.username,
        u.nomeIstituto,
        u.classe,
        -- materia che l'altro offre e io cerco
        v_loro_offro.materia_norm  AS materia_offerta,
        v_loro_offro.voto          AS voto_loro,
        -- materia che io offro e l'altro cerca
        v_offro_io.materia_norm    AS materia_cercata,
        v_offro_io.voto            AS voto_mio,
        (
            IF(u.nomeIstituto = myIstituto, 50, 0)
            + IF(u.classe = myClasse, 20, 0)
            + IF(v_loro_offro.voto >= 8, 30, IF(v_loro_offro.voto >= 6, 10, 0))
        ) AS score_bonus

    FROM utenti u

    -- L'altro offre qualcosa che io cerco
    LEFT JOIN voti v_cerco_io
        ON  v_cerco_io.idUtente = myId
        AND v_cerco_io.tipo     = 'CERCO'
    LEFT JOIN voti v_loro_offro
        ON  v_loro_offro.idUtente     = u.idUtente
        AND v_loro_offro.tipo         = 'OFFRO'
        AND v_loro_offro.materia_norm = v_cerco_io.materia_norm

    -- Io offro qualcosa che l'altro cerca
    LEFT JOIN voti v_cerco_loro
        ON  v_cerco_loro.idUtente = u.idUtente
        AND v_cerco_loro.tipo     = 'CERCO'
    LEFT JOIN voti v_offro_io
        ON  v_offro_io.idUtente     = myId
        AND v_offro_io.tipo         = 'OFFRO'
        AND v_offro_io.materia_norm  = v_cerco_loro.materia_norm

    WHERE u.idUtente != myId
      AND (v_loro_offro.idVoto IS NOT NULL OR v_offro_io.idVoto IS NOT NULL)

    ORDER BY score_bonus DESC;

END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Struttura della tabella `daily_answers`
--

CREATE TABLE `daily_answers` (
  `id` int(11) NOT NULL,
  `idUtente` int(11) NOT NULL,
  `domanda_idx` tinyint(3) UNSIGNED NOT NULL,
  `domanda_id` int(11) NOT NULL DEFAULT 1,
  `risposta` varchar(200) NOT NULL,
  `data_risposta` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `daily_answers`
--

INSERT INTO `daily_answers` (`id`, `idUtente`, `domanda_idx`, `domanda_id`, `risposta`, `data_risposta`) VALUES
(1, 1, 0, 10, 'Non studio', '2026-05-22'),
(2, 21, 0, 10, 'Riposo un po e poi studio', '2026-05-22'),
(3, 18, 0, 10, 'Mangio e poi studio', '2026-05-22'),
(4, 24, 0, 10, 'Dipende dal giorno', '2026-05-22'),
(5, 25, 0, 10, 'Mangio e poi studio', '2026-05-22'),
(6, 23, 0, 10, 'Studio subito', '2026-05-22'),
(7, 22, 0, 10, 'Riposo un po e poi studio', '2026-05-22'),
(8, 19, 0, 10, 'Dipende dal giorno', '2026-05-22'),
(9, 20, 0, 10, 'Dipende dal giorno', '2026-05-22'),
(10, 17, 0, 10, 'Mangio e poi studio', '2026-05-22'),
(11, 16, 0, 10, 'Studio subito', '2026-05-22'),
(12, 5, 0, 10, 'Mangio e poi studio', '2026-05-22'),
(13, 4, 0, 10, 'Non studio', '2026-05-22'),
(14, 3, 0, 10, 'Dipende dal giorno', '2026-05-22'),
(15, 2, 0, 10, 'Non studio', '2026-05-22');

-- --------------------------------------------------------

--
-- Struttura della tabella `domande_giornaliere`
--

CREATE TABLE `domande_giornaliere` (
  `id` int(11) NOT NULL,
  `testo` varchar(500) NOT NULL,
  `opzioni` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `domande_giornaliere`
--

INSERT INTO `domande_giornaliere` (`id`, `testo`, `opzioni`) VALUES
(1, 'Qual e la materia piu pesante che hai avuto oggi?', '[\"Matematica\",\"Fisica\",\"Italiano/Storia\",\"Scienze\",\"Lingue\",\"Altro\"]'),
(2, 'Come ti senti dopo le lezioni di oggi?', '[\"Energico\",\"Stanco\",\"Confuso\",\"Soddisfatto\",\"Stressato\"]'),
(3, 'Quanto hai studiato ieri sera?', '[\"Per niente\",\"Meno di 1 ora\",\"1-2 ore\",\"2-3 ore\",\"Piu di 3 ore\"]'),
(4, 'Cosa ti manca di piu per capire meglio?', '[\"Spiegazioni piu chiare\",\"Esercizi pratici\",\"Qualcuno con cui studiare\",\"Piu tempo\",\"Vado bene cosi\"]'),
(5, 'Preferisci studiare:', '[\"Da solo in silenzio\",\"Con musica\",\"In gruppo\",\"Con un tutor\",\"Non mi piace studiare\"]'),
(6, 'Come valuti la difficolta della giornata?', '[\"Facilissima\",\"Normale\",\"Difficile\",\"Durissima\",\"Non sono andato a scuola\"]'),
(7, 'Quale metodo usi di piu per studiare?', '[\"Ripetizione ad alta voce\",\"Mappe concettuali\",\"Riassunti scritti\",\"Esercizi\",\"Ascolto e basta\"]'),
(8, 'Quanto tempo impieghi ad arrivare a scuola?', '[\"Meno di 10 minuti\",\"10-20 minuti\",\"20-40 minuti\",\"Piu di 40 minuti\",\"Vado in presenza solo alcuni giorni\"]'),
(9, 'Come ti senti rispetto agli esami che si avvicinano?', '[\"Tranquillo\",\"Un po agitato\",\"Abbastanza stressato\",\"Nel panico\",\"Non ho esami a breve\"]'),
(10, 'Cosa fai appena torni a casa da scuola?', '[\"Studio subito\",\"Mangio e poi studio\",\"Riposo un po e poi studio\",\"Non studio\",\"Dipende dal giorno\"]'),
(11, 'Quanto e utile per te studiare in gruppo?', '[\"Molto utile\",\"Abbastanza utile\",\"Poco utile\",\"Per niente utile\",\"Non lo ho mai provato\"]'),
(12, 'Qual e il momento della giornata in cui studi meglio?', '[\"La mattina\",\"Il pomeriggio\",\"La sera\",\"La notte\",\"Non ho un momento preciso\"]');

-- --------------------------------------------------------

--
-- Struttura della tabella `materie`
--

CREATE TABLE `materie` (
  `idMateria` int(11) NOT NULL,
  `idUtente` int(11) NOT NULL,
  `materia` varchar(100) NOT NULL,
  `descrizione` text DEFAULT NULL,
  `materia_norm` varchar(100) GENERATED ALWAYS AS (lcase(trim(`materia`))) STORED
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
(11, 3, 'Matematica', 'non ne posso più'),
(12, 1, 'Storia', 'sono tutti mortiii'),
(13, 17, 'Matematica', 'Analisi e calcolo'),
(14, 17, 'Italiano', 'Ho difficolta con i testi letterari'),
(15, 18, 'Italiano', 'Temi, analisi del testo, autori 800-900'),
(16, 18, 'Storia', 'Storia moderna e contemporanea'),
(17, 19, 'Informatica', 'PHP, Python, basi di dati'),
(18, 19, 'Matematica', 'Analisi matematica'),
(19, 20, 'Storia', 'Dal Rinascimento alla guerra fredda'),
(20, 21, 'Informatica', 'Programmazione web e database'),
(21, 22, 'Italiano', 'Letteratura italiana dalle origini al 900'),
(22, 23, 'Matematica', 'Analisi e calcolo avanzato'),
(23, 24, 'Matematica', 'Algebra e analisi'),
(24, 24, 'Fisica', 'Fisica classica e moderna'),
(25, 25, 'Matematica', 'Calcolo e geometria'),
(26, 5, 'Matematica', 'mi riesce bene dai'),
(27, 5, 'Fisica', 'non capisco pk non capisco'),
(28, 5, 'Filosofia', 'filosofeggiando'),
(29, 3, 'Filosofia', 'si'),
(30, 3, 'Fisica', 'sisisi');

-- --------------------------------------------------------

--
-- Struttura della tabella `messaggi`
--

CREATE TABLE `messaggi` (
  `idMessaggio` int(11) NOT NULL,
  `idMittente` int(11) NOT NULL,
  `idDestinatario` int(11) NOT NULL,
  `testo` text NOT NULL,
  `ip_mittente` varchar(45) NOT NULL,
  `data_invio` timestamp NULL DEFAULT current_timestamp(),
  `letto` tinyint(1) DEFAULT 0
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
(109, 1, 2, 'ciao', '172.21.0.1', '2026-04-28 07:00:15', 1),
(110, 1, 3, 'pk mi ghostiii', '172.21.0.1', '2026-04-28 07:12:04', 0),
(111, 1, 3, 'o', '172.21.0.1', '2026-04-28 07:22:17', 0),
(112, 1, 3, 'a', '172.21.0.1', '2026-04-28 07:22:19', 0),
(113, 1, 3, 'e', '172.21.0.1', '2026-04-28 07:22:19', 0),
(114, 1, 3, 'i', '172.21.0.1', '2026-04-28 07:22:20', 0),
(115, 1, 3, 'o', '172.21.0.1', '2026-04-28 07:22:21', 0),
(116, 1, 3, 't', '172.21.0.1', '2026-04-28 07:22:21', 0),
(117, 1, 3, 'i', '172.21.0.1', '2026-04-28 07:22:22', 0),
(118, 1, 3, 'r', '172.21.0.1', '2026-04-28 07:22:22', 0),
(119, 1, 3, 'n', '172.21.0.1', '2026-04-28 07:22:23', 0),
(120, 1, 3, 'g', '172.21.0.1', '2026-04-28 07:22:24', 0),
(121, 1, 3, 'c', '172.21.0.1', '2026-04-28 07:22:24', 0),
(122, 1, 3, 'd', '172.21.0.1', '2026-04-28 07:22:25', 0),
(123, 1, 3, 'h', '172.21.0.1', '2026-04-28 07:22:25', 0),
(124, 1, 3, 'k', '172.21.0.1', '2026-04-28 07:22:26', 0),
(125, 1, 3, 'g', '172.21.0.1', '2026-04-28 07:22:26', 0),
(126, 1, 3, 'f', '172.21.0.1', '2026-04-28 07:22:27', 0),
(127, 1, 3, 'd', '172.21.0.1', '2026-04-28 07:22:27', 0),
(128, 1, 3, 'f', '172.21.0.1', '2026-04-28 07:22:28', 0),
(129, 1, 3, 'g', '172.21.0.1', '2026-04-28 07:22:28', 0),
(130, 1, 3, 'f', '172.21.0.1', '2026-04-28 07:22:28', 0),
(131, 1, 3, 'd', '172.21.0.1', '2026-04-28 07:22:29', 0),
(132, 1, 3, 's', '172.21.0.1', '2026-04-28 07:22:29', 0),
(133, 1, 3, 'w', '172.21.0.1', '2026-04-28 07:22:29', 0),
(134, 1, 3, 'e', '172.21.0.1', '2026-04-28 07:22:30', 0),
(135, 1, 3, 'r', '172.21.0.1', '2026-04-28 07:22:30', 0),
(136, 1, 3, 'o', '172.21.0.1', '2026-04-28 07:23:23', 0),
(137, 1, 3, '1', '172.21.0.1', '2026-04-28 07:47:03', 0),
(138, 1, 3, '2', '172.21.0.1', '2026-04-28 07:47:04', 0),
(139, 1, 3, '3', '172.21.0.1', '2026-04-28 07:47:05', 0),
(140, 1, 3, '4', '172.21.0.1', '2026-04-28 07:47:07', 0),
(141, 1, 3, '5', '172.21.0.1', '2026-04-28 07:47:08', 0),
(142, 1, 3, '6', '172.21.0.1', '2026-04-28 07:47:09', 0),
(143, 1, 3, '7', '172.21.0.1', '2026-04-28 07:47:10', 0),
(144, 1, 3, '8', '172.21.0.1', '2026-04-28 07:47:11', 0),
(145, 1, 3, '9', '172.21.0.1', '2026-04-28 07:47:12', 0),
(146, 1, 3, '1', '172.21.0.1', '2026-04-28 07:47:20', 0),
(147, 1, 3, '2', '172.21.0.1', '2026-04-28 07:47:21', 0),
(148, 1, 3, '3', '172.21.0.1', '2026-04-28 07:47:22', 0),
(149, 1, 3, '4', '172.21.0.1', '2026-04-28 07:47:23', 0),
(150, 1, 3, '5', '172.21.0.1', '2026-04-28 07:47:24', 0),
(151, 1, 3, '6', '172.21.0.1', '2026-04-28 07:47:25', 0),
(152, 1, 3, '7', '172.21.0.1', '2026-04-28 07:47:26', 0),
(153, 1, 3, '8', '172.21.0.1', '2026-04-28 07:47:27', 0),
(154, 1, 3, '9', '172.21.0.1', '2026-04-28 07:47:28', 0),
(155, 1, 3, '1', '172.21.0.1', '2026-04-28 07:47:33', 0),
(156, 1, 3, '1', '172.21.0.1', '2026-04-28 07:47:35', 0),
(157, 1, 3, '2', '172.21.0.1', '2026-04-28 07:47:36', 0),
(158, 1, 3, '3', '172.21.0.1', '2026-04-28 07:47:37', 0),
(159, 1, 3, '4', '172.21.0.1', '2026-04-28 07:47:38', 0),
(160, 1, 3, '5', '172.21.0.1', '2026-04-28 07:47:39', 0),
(161, 1, 3, '6', '172.21.0.1', '2026-04-28 07:47:40', 0),
(162, 1, 3, 'Ciao LodiMartaa! Ti propongo uno SkillSwap: io ti insegno Italiano (giovanni verga) e tu mi insegni Matematica (integrali indefiniti). Che ne dici?', '::1', '2026-05-22 00:17:49', 0),
(163, 1, 2, 'Ciao skitto! Ti propongo uno SkillSwap: io ti insegno Italiano (giovanni verga) e tu mi insegni Matematica (integrali indefiniti). Che ne dici?', '::1', '2026-05-22 00:19:02', 0),
(164, 1, 17, 'Ciao Sara! Ti propongo uno SkillSwap: io ti insegno Italiano (giovanni verga) e tu mi insegni Matematica (integrali indefiniti). Che ne dici?', '::1', '2026-03-10 08:00:00', 1),
(165, 17, 1, 'Ciao! Si ottima idea, ho proprio bisogno di aiuto con verga. Quando sei disponibile?', '::1', '2026-03-10 08:30:00', 1),
(166, 1, 17, 'Perfetto, questa settimana sono libero dal pomeriggio. Ti scrivo domani', '::1', '2026-03-10 08:45:00', 1),
(167, 1, 18, 'Ciao Elena! Ti propongo uno SkillSwap: io ti insegno Informatica (php) e tu mi insegni Italiano (giovanni verga). Che ne dici?', '::1', '2026-03-12 09:00:00', 1),
(168, 18, 1, 'Ciao! Volentieri, ho un esame di php tra due settimane e sono in panico', '::1', '2026-03-12 09:20:00', 1),
(169, 1, 18, 'Tranquilla, ci organizziamo. Ti mando un link meet quando vuoi', '::1', '2026-03-12 09:35:00', 1),
(170, 19, 1, 'Ciao purplehaze! Ho visto che sei bravo in italiano, potresti aiutarmi con verga? In cambio posso aiutarti con php', '::1', '2026-03-20 13:00:00', 1),
(171, 1, 19, 'Certo, quando vuoi. Anche domani se ti va', '::1', '2026-03-20 13:30:00', 1),
(172, 18, 17, 'Ciao Sara, possiamo fare uno swap? Io ti aiuto con italiano e tu con matematica', '::1', '2026-04-01 09:00:00', 1),
(173, 17, 18, 'Si certo! Quando sei disponibile?', '::1', '2026-04-01 09:20:00', 1),
(174, 21, 19, 'Ciao Davide! Ti propongo uno SkillSwap: io ti insegno Informatica e tu mi insegni Storia. Che ne dici?', '::1', '2026-04-05 14:00:00', 1),
(175, 22, 1, 'Ciao! Ho bisogno di aiuto con php, in cambio posso aiutarti con italiano o storia', '::1', '2026-04-10 06:00:00', 1),
(176, 1, 22, 'Perfetto, possiamo organizzarci questa settimana', '::1', '2026-04-10 06:30:00', 0),
(177, 21, 19, 'ciao davide... mi potresti rispondere?', '::1', '2026-05-22 02:00:27', 0),
(178, 21, 19, 'avrei ancora bisogno di una mano in storia :(', '::1', '2026-05-22 02:00:51', 0),
(179, 21, 22, 'Ciao andrea_c! Ti propongo uno SkillSwap: io ti insegno Informatica (php) e tu mi insegni Italiano (giovanni verga). Che ne dici?', '::1', '2026-05-22 02:01:08', 0);

-- --------------------------------------------------------

--
-- Struttura della tabella `skillswap_sessions`
--

CREATE TABLE `skillswap_sessions` (
  `idSession` int(11) NOT NULL,
  `idUtente1` int(11) NOT NULL,
  `idUtente2` int(11) NOT NULL,
  `materia1` varchar(100) DEFAULT NULL,
  `materia2` varchar(100) DEFAULT NULL,
  `stato` enum('PROPOSTA','ACCETTATA','COMPLETATA','RIFIUTATA') NOT NULL DEFAULT 'PROPOSTA',
  `data_creazione` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `stress_log`
--

CREATE TABLE `stress_log` (
  `idLog` int(11) NOT NULL,
  `idUtente` int(11) NOT NULL,
  `livello` tinyint(3) UNSIGNED NOT NULL CHECK (`livello` between 1 and 10),
  `data_voto` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `stress_log`
--

INSERT INTO `stress_log` (`idLog`, `idUtente`, `livello`, `data_voto`) VALUES
(1, 1, 7, '2026-05-21 20:39:54'),
(2, 1, 8, '2026-05-21 23:42:06'),
(3, 2, 6, '2026-05-12 01:53:10'),
(4, 3, 8, '2026-05-12 01:53:10'),
(5, 5, 5, '2026-05-12 01:53:10'),
(6, 1, 7, '2026-05-13 01:53:10'),
(7, 17, 4, '2026-05-13 01:53:10'),
(8, 18, 9, '2026-05-13 01:53:10'),
(9, 2, 5, '2026-05-14 01:53:10'),
(10, 19, 7, '2026-05-14 01:53:10'),
(11, 21, 6, '2026-05-14 01:53:10'),
(12, 3, 8, '2026-05-15 01:53:10'),
(13, 4, 7, '2026-05-15 01:53:10'),
(14, 20, 5, '2026-05-15 01:53:10'),
(15, 1, 6, '2026-05-16 01:53:10'),
(16, 17, 8, '2026-05-16 01:53:10'),
(17, 22, 4, '2026-05-16 01:53:10'),
(18, 2, 9, '2026-05-17 01:53:10'),
(19, 18, 6, '2026-05-17 01:53:10'),
(20, 19, 7, '2026-05-17 01:53:10'),
(21, 3, 5, '2026-05-18 01:53:10'),
(22, 21, 8, '2026-05-18 01:53:10'),
(23, 4, 6, '2026-05-18 01:53:10'),
(24, 1, 7, '2026-05-19 01:53:10'),
(25, 17, 5, '2026-05-19 01:53:10'),
(26, 20, 9, '2026-05-19 01:53:10'),
(27, 2, 6, '2026-05-20 01:53:10'),
(28, 22, 4, '2026-05-20 01:53:10'),
(29, 5, 7, '2026-05-20 01:53:10'),
(30, 3, 8, '2026-05-21 01:53:10'),
(31, 18, 6, '2026-05-21 01:53:10'),
(32, 19, 5, '2026-05-21 01:53:10'),
(33, 21, 7, '2026-05-21 01:53:10'),
(34, 17, 6, '2026-05-22 01:53:10'),
(35, 18, 4, '2026-05-22 01:53:10'),
(36, 19, 8, '2026-05-22 01:53:10'),
(37, 21, 5, '2026-05-22 01:53:10'),
(38, 22, 7, '2026-05-22 01:53:10'),
(39, 23, 5, '2026-05-19 02:14:37'),
(40, 24, 7, '2026-05-19 02:14:37'),
(41, 23, 6, '2026-05-20 02:14:37'),
(42, 25, 8, '2026-05-20 02:14:37'),
(43, 24, 4, '2026-05-21 02:14:37'),
(44, 25, 6, '2026-05-21 02:14:37'),
(45, 23, 7, '2026-05-22 02:14:37'),
(46, 24, 5, '2026-05-22 02:14:37'),
(47, 25, 9, '2026-05-22 02:14:37');

-- --------------------------------------------------------

--
-- Struttura della tabella `utenti`
--

CREATE TABLE `utenti` (
  `idUtente` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `cognome` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `indirizzo` varchar(255) NOT NULL,
  `dataNascita` date NOT NULL,
  `provinciaIstituto` varchar(100) NOT NULL,
  `comuneIstituto` varchar(100) NOT NULL,
  `nomeIstituto` varchar(100) NOT NULL,
  `classe` varchar(20) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `ruolo` enum('user','admin') NOT NULL DEFAULT 'user',
  `punti` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `utenti`
--

INSERT INTO `utenti` (`idUtente`, `nome`, `cognome`, `username`, `indirizzo`, `dataNascita`, `provinciaIstituto`, `comuneIstituto`, `nomeIstituto`, `classe`, `telefono`, `email`, `password`, `ruolo`, `punti`) VALUES
(1, 'Mattia', 'Giancristofaro', 'purplehaze', 'via lago di garda 7', '2008-01-14', 'VENEZIA', 'VENEZIA', 'VETF04000T', '5', '3396344400', 'mgiancry@gmail.com', '$2y$10$JDMzCYP.N/k0Z5.V58LVYeswhdUpx10uQ5ncgSKkjNeB93aFCked6', 'admin', 12),
(2, 'ciccio', 'bello', 'skitto', 'via sant\'elena 9', '2026-02-20', 'VENEZIA', 'VENEZIA', 'CARLO ZUCCANTE (VETF04000T)', '5', '3473030022', 'ciccio@bello.com', '$2y$10$XncMC.JaQ/7lEtO0PMGzRuJGY4bXkjNv1FSOQA6RBestMt61jXvLS', 'user', 0),
(3, 'Marta', 'Lodi', 'LodiMartaa', 'via umberto sailer 8', '2009-03-14', 'VENEZIA', 'DOLO', 'CESARE MUSATTI (VERH03000V)', '4', '3480096298', 'marta@lodi.com', '$2y$10$Hoxf3xCJABHNpQju0/O2kuTlW6nfqeZxfDxz9WCWGyjtGWcqReowO', 'user', 2),
(4, 'marco', 'castagna', 'markc', 'via sant\'elena 69', '2008-04-01', 'BOLOGNA', 'BOLOGNA', 'ISTITUTO PENALE MINORILE -\"P. SICILIANI\" (BORH022065)', '4', '3696781230', 'marco@castagna.com', '$2y$10$40hyEIxWes60Np6POBgIy.FKkRiYzv2ZpWXjTMnOQp0tXCSjJliuC', 'user', 0),
(5, 'luigi', 'verdi', 'giggi', 'via zingara 20', '2009-10-20', 'VENEZIA', 'VENEZIA', 'I.I.S. LUIGI STEFANINI (VEPM02000G)', '3', '3204567803', 'luigi@verdi.com', '$2y$10$zgmEVD.j0yEAGTW5UtEQq.QNq3C/9e2jxPse041tROJI.WhETwPG.', 'user', 0),
(16, 'alessio', 'ceccato', 'aleCheck', 'Via Lago di Santa Croce 9', '2009-05-20', 'ALESSANDRIA', 'CASALE MONFERRATO', 'CESARE BALBO (ALIS009005)', '3', '3473030022', 'alessio@ceccato.com', '$2y$10$eZUWyrlQUj8JRdD0pgAyIOeYKBWjW89aT/xvPDyBE2HpjsCETD87q', 'user', 10),
(17, 'Sara', 'Fontana', 'sara_f', 'via roma 12', '2008-06-10', 'VENEZIA', 'VENEZIA', 'VETF04000T', '5', '3331234567', 'sara@fontana.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 28),
(18, 'Elena', 'Russo', 'elena_r', 'via mazzini 5', '2008-09-23', 'VENEZIA', 'VENEZIA', 'VETF04000T', '5', '3341234567', 'elena@russo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 22),
(19, 'Davide', 'Ferrari', 'davide_f', 'via garibaldi 8', '2007-12-01', 'VENEZIA', 'VENEZIA', 'CARLO ZUCCANTE (VETF04000T)', '5', '3351234567', 'davide@ferrari.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 30),
(20, 'Giulia', 'Marini', 'giulia_m', 'via leopardi 3', '2009-03-17', 'VENEZIA', 'MESTRE', 'I.I.S. LUIGI STEFANINI (VEPM02000G)', '3', '3361234567', 'giulia@marini.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 18),
(21, 'Luca', 'Bianchi', 'luca_b', 'via dante 22', '2008-07-14', 'VENEZIA', 'MESTRE', 'I.I.S. LUIGI STEFANINI (VEPM02000G)', '3', '3371234567', 'luca@bianchi.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 29),
(22, 'Andrea', 'Conti', 'andrea_c', 'via verdi 11', '2007-05-28', 'VENEZIA', 'DOLO', 'CESARE MUSATTI (VERH03000V)', '4', '3381234567', 'andrea@conti.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 20),
(23, 'Federica', 'Neri', 'fede_n', 'via torino 4', '2008-02-14', 'VENEZIA', 'VENEZIA', 'VETF04000T', '5', '3391111111', 'federica@neri.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 14),
(24, 'Tommaso', 'Greco', 'tommy_g', 'via napoli 7', '2007-08-30', 'VENEZIA', 'MESTRE', 'I.I.S. LUIGI STEFANINI (VEPM02000G)', '4', '3392222222', 'tommaso@greco.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 16),
(25, 'Valentina', 'Bruno', 'vale_b', 'via firenze 2', '2009-01-05', 'VENEZIA', 'VENEZIA', 'VETF04000T', '5', '3393333333', 'valentina@bruno.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 12);

-- --------------------------------------------------------

--
-- Struttura della tabella `voti`
--

CREATE TABLE `voti` (
  `idVoto` int(11) NOT NULL,
  `idUtente` int(11) NOT NULL,
  `materia` varchar(100) NOT NULL,
  `argomento` varchar(255) NOT NULL,
  `voto` decimal(4,2) NOT NULL,
  `tipo` enum('OFFRO','CERCO','NEUTRO') NOT NULL,
  `data_inserimento` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `materia_norm` varchar(100) GENERATED ALWAYS AS (lcase(trim(`materia`))) STORED
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
(13, 3, 'Matematica', 'integrali indefiniti', 4.00, 'CERCO', '2026-05-22 03:34:36'),
(14, 1, 'Storia', 'prima guerra mondiale', 7.00, 'OFFRO', '2026-05-21 22:00:00'),
(15, 17, 'Matematica', 'integrali indefiniti', 9.00, 'OFFRO', '2026-01-14 23:00:00'),
(16, 17, 'Matematica', 'derivate', 8.50, 'OFFRO', '2026-01-14 23:00:00'),
(17, 17, 'Italiano', 'giovanni verga', 5.00, 'CERCO', '2026-01-15 23:00:00'),
(18, 17, 'Informatica', 'php', 4.00, 'CERCO', '2026-01-15 23:00:00'),
(19, 18, 'Italiano', 'giovanni verga', 9.00, 'OFFRO', '2026-01-13 23:00:00'),
(20, 18, 'Storia', 'prima guerra mondiale', 8.00, 'OFFRO', '2026-01-13 23:00:00'),
(21, 18, 'Matematica', 'integrali indefiniti', 4.50, 'CERCO', '2026-01-14 23:00:00'),
(22, 18, 'Informatica', 'php', 5.00, 'CERCO', '2026-01-14 23:00:00'),
(23, 19, 'Informatica', 'php', 9.00, 'OFFRO', '2026-01-12 23:00:00'),
(24, 19, 'Matematica', 'derivate', 8.00, 'OFFRO', '2026-01-12 23:00:00'),
(25, 19, 'Italiano', 'giovanni verga', 5.00, 'CERCO', '2026-01-13 23:00:00'),
(26, 19, 'Storia', 'prima guerra mondiale', 4.00, 'CERCO', '2026-01-13 23:00:00'),
(27, 20, 'Storia', 'prima guerra mondiale', 8.50, 'OFFRO', '2026-01-31 23:00:00'),
(28, 20, 'Matematica', 'integrali', 4.00, 'CERCO', '2026-02-01 23:00:00'),
(29, 20, 'Informatica', 'programmazione', 3.00, 'CERCO', '2026-02-01 23:00:00'),
(30, 21, 'Informatica', 'php', 8.00, 'OFFRO', '2026-02-04 23:00:00'),
(31, 21, 'Matematica', 'integrali', 7.00, 'OFFRO', '2026-02-04 23:00:00'),
(32, 21, 'Storia', 'prima guerra mondiale', 4.00, 'CERCO', '2026-02-05 23:00:00'),
(33, 21, 'Italiano', 'giovanni verga', 5.00, 'CERCO', '2026-02-05 23:00:00'),
(34, 22, 'Italiano', 'giovanni verga', 8.50, 'OFFRO', '2026-02-09 23:00:00'),
(35, 22, 'Storia', 'rinascimento', 7.00, 'OFFRO', '2026-02-09 23:00:00'),
(36, 22, 'Informatica', 'php', 4.00, 'CERCO', '2026-02-10 23:00:00'),
(37, 22, 'Matematica', 'integrali indefiniti', 5.00, 'CERCO', '2026-02-10 23:00:00'),
(38, 23, 'Matematica', 'integrali indefiniti', 8.50, 'OFFRO', '2026-02-14 23:00:00'),
(39, 23, 'Matematica', 'equazioni differenziali', 9.00, 'OFFRO', '2026-02-14 23:00:00'),
(40, 23, 'Storia', 'secondo dopoguerra', 5.00, 'CERCO', '2026-02-15 23:00:00'),
(41, 23, 'Fisica', 'termodinamica', 4.50, 'CERCO', '2026-02-15 23:00:00'),
(42, 24, 'Matematica', 'derivate', 8.00, 'OFFRO', '2026-02-28 23:00:00'),
(43, 24, 'Fisica', 'elettromagnetismo', 8.50, 'OFFRO', '2026-02-28 23:00:00'),
(44, 24, 'Lingue', 'inglese grammatica', 4.00, 'CERCO', '2026-03-01 23:00:00'),
(45, 25, 'Matematica', 'integrali', 8.00, 'OFFRO', '2026-03-09 23:00:00'),
(46, 25, 'Scienze', 'biologia cellulare', 4.50, 'CERCO', '2026-03-09 23:00:00'),
(47, 5, 'Matematica', 'integrali indefiniti', 8.00, 'OFFRO', '2026-05-22 03:48:21'),
(48, 5, 'Fisica', 'termodinamica', 4.00, 'CERCO', '2026-01-19 23:00:00'),
(49, 16, 'Matematica', 'derivate', 9.00, 'OFFRO', '2026-01-19 23:00:00'),
(50, 16, 'Fisica', 'elettromagnetismo', 5.00, 'CERCO', '2026-01-19 23:00:00'),
(51, 3, 'Fisica', 'termodinamica', 7.00, 'OFFRO', '2026-05-19 22:00:00'),
(52, 4, 'Matematica', 'integrali indefiniti', 8.50, 'OFFRO', '2026-01-19 23:00:00'),
(53, 4, 'Fisica', 'termodinamica', 4.00, 'CERCO', '2026-01-19 23:00:00'),
(54, 16, 'Matematica', 'derivate', 9.00, 'OFFRO', '2026-01-19 23:00:00'),
(55, 16, 'Chimica', 'acidi e basi', 5.00, 'CERCO', '2026-01-19 23:00:00'),
(56, 5, 'Italiano', 'giovanni verga', 4.00, 'CERCO', '2026-05-21 22:00:00'),
(57, 3, 'Italiano', 'giovanni verga', 9.00, 'OFFRO', '2026-05-21 22:00:00');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `daily_answers`
--
ALTER TABLE `daily_answers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_risposta` (`idUtente`,`data_risposta`,`domanda_idx`),
  ADD KEY `idx_da_data` (`data_risposta`,`domanda_idx`),
  ADD KEY `fk_domanda` (`domanda_id`);

--
-- Indici per le tabelle `domande_giornaliere`
--
ALTER TABLE `domande_giornaliere`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `materie`
--
ALTER TABLE `materie`
  ADD PRIMARY KEY (`idMateria`),
  ADD KEY `idUtente` (`idUtente`),
  ADD KEY `idx_materie_norm` (`materia_norm`);

--
-- Indici per le tabelle `messaggi`
--
ALTER TABLE `messaggi`
  ADD PRIMARY KEY (`idMessaggio`),
  ADD KEY `idMittente` (`idMittente`),
  ADD KEY `idDestinatario` (`idDestinatario`),
  ADD KEY `idx_rate_limit` (`idMittente`,`data_invio`);

--
-- Indici per le tabelle `skillswap_sessions`
--
ALTER TABLE `skillswap_sessions`
  ADD PRIMARY KEY (`idSession`),
  ADD KEY `idx_session_u1` (`idUtente1`),
  ADD KEY `idx_session_u2` (`idUtente2`);

--
-- Indici per le tabelle `stress_log`
--
ALTER TABLE `stress_log`
  ADD PRIMARY KEY (`idLog`),
  ADD KEY `idx_stress_data` (`data_voto`),
  ADD KEY `idx_stress_utente` (`idUtente`);

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
  ADD KEY `idUtente` (`idUtente`),
  ADD KEY `idx_voti_norm` (`materia_norm`),
  ADD KEY `idx_voti_tipo` (`tipo`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `daily_answers`
--
ALTER TABLE `daily_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT per la tabella `domande_giornaliere`
--
ALTER TABLE `domande_giornaliere`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT per la tabella `materie`
--
ALTER TABLE `materie`
  MODIFY `idMateria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT per la tabella `messaggi`
--
ALTER TABLE `messaggi`
  MODIFY `idMessaggio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=180;

--
-- AUTO_INCREMENT per la tabella `skillswap_sessions`
--
ALTER TABLE `skillswap_sessions`
  MODIFY `idSession` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `stress_log`
--
ALTER TABLE `stress_log`
  MODIFY `idLog` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT per la tabella `utenti`
--
ALTER TABLE `utenti`
  MODIFY `idUtente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT per la tabella `voti`
--
ALTER TABLE `voti`
  MODIFY `idVoto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `daily_answers`
--
ALTER TABLE `daily_answers`
  ADD CONSTRAINT `daily_answers_ibfk_1` FOREIGN KEY (`idUtente`) REFERENCES `utenti` (`idUtente`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_domanda` FOREIGN KEY (`domanda_id`) REFERENCES `domande_giornaliere` (`id`) ON DELETE CASCADE;

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
-- Limiti per la tabella `skillswap_sessions`
--
ALTER TABLE `skillswap_sessions`
  ADD CONSTRAINT `skillswap_sessions_ibfk_1` FOREIGN KEY (`idUtente1`) REFERENCES `utenti` (`idUtente`) ON DELETE CASCADE,
  ADD CONSTRAINT `skillswap_sessions_ibfk_2` FOREIGN KEY (`idUtente2`) REFERENCES `utenti` (`idUtente`) ON DELETE CASCADE;

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
