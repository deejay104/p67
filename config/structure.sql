-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 29, 2018 at 11:29 AM
-- Server version: 10.0.33-MariaDB-0ubuntu0.16.04.1
-- PHP Version: 7.0.22-0ubuntu0.16.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `p67`
--

-- --------------------------------------------------------

--
-- Table structure for table `p67_abonnement`
--

CREATE TABLE `p67_abonnement` (
  `id` int(10) UNSIGNED NOT NULL,
  `abonum` varchar(8) COLLATE latin1_general_ci NOT NULL,
  `uid` int(10) UNSIGNED NOT NULL,
  `dtedeb` date NOT NULL,
  `dtefin` date NOT NULL,
  `jour_num` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `jour_sem` char(1) COLLATE latin1_general_ci NOT NULL DEFAULT '-',
  `actif` enum('oui','non') COLLATE latin1_general_ci NOT NULL DEFAULT 'oui',
  `uid_maj` int(10) UNSIGNED NOT NULL,
  `dte_maj` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `p67_abo_ligne`
--

CREATE TABLE `p67_abo_ligne` (
  `id` int(10) UNSIGNED NOT NULL,
  `abonum` varchar(8) NOT NULL,
  `uid` int(10) UNSIGNED NOT NULL,
  `mouvid` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `montant` decimal(10,2) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `p67_actualites`
--

CREATE TABLE `p67_actualites` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `titre` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `dte_mail` datetime NOT NULL,
  `mail` enum('oui','non') NOT NULL DEFAULT 'non',
  `actif` enum('oui','non') NOT NULL DEFAULT 'oui',
  `uid_creat` int(10) UNSIGNED NOT NULL,
  `dte_creat` datetime NOT NULL,
  `uid_modif` int(11) NOT NULL,
  `dte_modif` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `p67_bapteme`
--

CREATE TABLE `p67_bapteme` (
  `id` int(10) UNSIGNED NOT NULL,
  `num` varchar(20) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `telephone` varchar(14) NOT NULL,
  `mail` varchar(100) NOT NULL,
  `nb` tinyint(3) UNSIGNED NOT NULL,
  `dte` datetime NOT NULL,
  `actif` enum('oui','non') NOT NULL DEFAULT 'oui',
  `status` tinyint(3) UNSIGNED NOT NULL,
  `type` enum('btm','vi') NOT NULL DEFAULT 'btm',
  `paye` enum('oui','non') NOT NULL DEFAULT 'non',
  `id_pilote` int(10) UNSIGNED NOT NULL,
  `id_avion` int(10) UNSIGNED NOT NULL,
  `id_resa` int(10) UNSIGNED NOT NULL,
  `description` text NOT NULL,
  `uid_creat` int(10) UNSIGNED NOT NULL,
  `dte_creat` datetime NOT NULL,
  `uid_maj` int(10) UNSIGNED NOT NULL,
  `dte_maj` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `p67_calendrier`
--

CREATE TABLE `p67_calendrier` (
  `id` int(10) UNSIGNED NOT NULL,
  `description` text NOT NULL,
  `dte_deb` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `dte_fin` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `uid_pilote` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `uid_debite` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `uid_instructeur` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `uid_avion` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `destination` varchar(50) NOT NULL,
  `nbpersonne` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `invite` enum('oui','non') NOT NULL DEFAULT 'non',
  `accept` enum('oui','non') NOT NULL DEFAULT 'non',
  `temps` int(11) NOT NULL DEFAULT '0',
  `tarif` char(2) NOT NULL DEFAULT '',
  `prix` float NOT NULL DEFAULT '0',
  `tpsestime` int(11) NOT NULL,
  `tpsreel` int(11) NOT NULL,
  `horadeb` varchar(10) NOT NULL DEFAULT '0',
  `horafin` varchar(10) NOT NULL DEFAULT '0',
  `idmaint` int(10) UNSIGNED NOT NULL,
  `potentiel` int(10) UNSIGNED NOT NULL,
  `reel` enum('oui','non') NOT NULL DEFAULT 'oui',
  `edite` enum('oui','non') NOT NULL DEFAULT 'non',
  `dte_maj` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `uid_maj` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `actif` enum('oui','non') NOT NULL DEFAULT 'oui'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `p67_compte`
--

CREATE TABLE `p67_compte` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `mid` int(10) UNSIGNED NOT NULL,
  `uid` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `tiers` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `montant` decimal(10,2) NOT NULL DEFAULT '0.00',
  `mouvement` varchar(100) NOT NULL DEFAULT '',
  `commentaire` tinytext NOT NULL,
  `date_valeur` date NOT NULL DEFAULT '0000-00-00',
  `dte` varchar(6) NOT NULL,
  `compte` varchar(10) NOT NULL,
  `pointe` char(1) NOT NULL DEFAULT '',
  `facture` varchar(10) NOT NULL,
  `rembfact` varchar(10) NOT NULL,
  `signature` varchar(64) NOT NULL,
  `precedent` varchar(64) NOT NULL,
  `uid_creat` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `date_creat` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `p67_comptetemp`
--

CREATE TABLE `p67_comptetemp` (
  `id` int(10) UNSIGNED NOT NULL,
  `deb` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `cre` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `ventilation` text COLLATE latin1_general_ci NOT NULL,
  `montant` decimal(10,2) NOT NULL DEFAULT '0.00',
  `poste` int(10) NOT NULL DEFAULT '0',
  `commentaire` tinytext COLLATE latin1_general_ci NOT NULL,
  `date_valeur` date NOT NULL DEFAULT '0000-00-00',
  `compte` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `facture` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `rembfact` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `status` varchar(10) COLLATE latin1_general_ci NOT NULL DEFAULT '0',
  `uid_creat` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `date_creat` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `p67_config`
--

CREATE TABLE `p67_config` (
  `param` varchar(20) NOT NULL,
  `value` varchar(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `p67_conso`
--

CREATE TABLE `p67_conso` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `idvol` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `idavion` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `quantite` float NOT NULL DEFAULT '0',
  `prix` float NOT NULL DEFAULT '0',
  `tiers` varchar(100) NOT NULL DEFAULT '',
  `uid_creat` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `dte_creat` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `uid_modif` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `dte_modif` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `p67_cron`
--

CREATE TABLE `p67_cron` (
  `id` int(10) UNSIGNED NOT NULL,
  `description` varchar(40) COLLATE latin1_general_ci NOT NULL,
  `module` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `script` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `schedule` int(10) UNSIGNED NOT NULL,
  `lastrun` datetime DEFAULT NULL,
  `nextrun` datetime DEFAULT NULL,
  `txtretour` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `txtlog` text COLLATE latin1_general_ci NOT NULL,
  `actif` enum('oui','non') COLLATE latin1_general_ci NOT NULL DEFAULT 'oui'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `p67_disponibilite`
--

CREATE TABLE `p67_disponibilite` (
  `id` int(10) UNSIGNED NOT NULL,
  `uid` int(10) UNSIGNED NOT NULL,
  `dte_deb` datetime NOT NULL,
  `dte_fin` datetime NOT NULL,
  `uid_maj` int(10) UNSIGNED NOT NULL,
  `dte_maj` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `p67_document`
--

CREATE TABLE `p67_document` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `filename` varchar(20) NOT NULL,
  `uid` int(10) UNSIGNED NOT NULL,
  `type` varchar(10) NOT NULL,
  `dossier` tinytext NOT NULL,
  `droit` varchar(3) NOT NULL,
  `actif` enum('oui','non') NOT NULL DEFAULT 'oui',
  `uid_creat` int(10) UNSIGNED NOT NULL,
  `dte_creat` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `p67_droits`
--

CREATE TABLE `p67_droits` (
  `id` int(10) UNSIGNED NOT NULL,
  `groupe` varchar(5) NOT NULL,
  `uid` int(10) UNSIGNED NOT NULL,
  `uid_creat` int(10) UNSIGNED NOT NULL,
  `dte_creat` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `p67_echeance`
--

CREATE TABLE `p67_echeance` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `typeid` int(10) UNSIGNED NOT NULL,
  `uid` int(10) UNSIGNED NOT NULL,
  `dte_echeance` date NOT NULL,
  `paye` enum('oui','non') NOT NULL DEFAULT 'non',
  `actif` enum('oui','non') NOT NULL DEFAULT 'oui',
  `dte_create` datetime NOT NULL,
  `uid_create` int(10) UNSIGNED NOT NULL,
  `dte_maj` datetime NOT NULL,
  `uid_maj` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `p67_echeancetype`
--

CREATE TABLE `p67_echeancetype` (
  `id` int(10) UNSIGNED NOT NULL,
  `description` varchar(100) NOT NULL,
  `poste` int(11) NOT NULL,
  `cout` decimal(10,2) NOT NULL DEFAULT '0.00',
  `resa` enum('obligatoire','instructeur','facultatif') NOT NULL,
  `droit` varchar(3) NOT NULL,
  `multi` enum('oui','non') NOT NULL DEFAULT 'non',
  `notif` enum('oui','non') NOT NULL DEFAULT 'non',
  `delai` tinyint(3) UNSIGNED NOT NULL DEFAULT '30'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `p67_export`
--

CREATE TABLE `p67_export` (
  `id` int(10) UNSIGNED NOT NULL,
  `nom` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `requete` text NOT NULL,
  `param` varchar(50) NOT NULL,
  `droit_r` char(3) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `p67_factures`
--

CREATE TABLE `p67_factures` (
  `id` varchar(10) NOT NULL,
  `uid` int(10) UNSIGNED NOT NULL,
  `dteid` varchar(6) NOT NULL,
  `dte` date NOT NULL,
  `total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `paid` varchar(1) NOT NULL,
  `email` char(1) NOT NULL DEFAULT 'N',
  `comment` varchar(200) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `p67_forums`
--

CREATE TABLE `p67_forums` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `fid` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `fil` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `titre` varchar(104) NOT NULL DEFAULT '',
  `message` text NOT NULL,
  `pseudo` varchar(104) NOT NULL DEFAULT '',
  `mail_diff` varchar(104) NOT NULL DEFAULT '',
  `actif` enum('oui','non') NOT NULL DEFAULT 'oui',
  `droit_r` char(3) NOT NULL DEFAULT '',
  `droit_w` char(3) NOT NULL DEFAULT '',
  `uid_creat` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `dte_creat` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `dte_maj` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `uid_maj` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `mailing` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `p67_forums_lus`
--

CREATE TABLE `p67_forums_lus` (
  `forum_id` mediumint(8) UNSIGNED NOT NULL,
  `forum_msg` mediumint(8) UNSIGNED DEFAULT NULL,
  `forum_usr` int(10) UNSIGNED DEFAULT NULL,
  `forum_date` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Tables pour les messages lus des forums';

-- --------------------------------------------------------

--
-- Table structure for table `p67_groupe`
--

CREATE TABLE `p67_groupe` (
  `groupe` varchar(5) NOT NULL,
  `description` varchar(200) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `p67_historique`
--

CREATE TABLE `p67_historique` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `class` varchar(20) NOT NULL,
  `table` varchar(20) NOT NULL,
  `idtable` bigint(20) UNSIGNED NOT NULL,
  `uid_maj` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `dte_maj` datetime NOT NULL,
  `type` varchar(3) NOT NULL,
  `comment` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `p67_lache`
--

CREATE TABLE `p67_lache` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_avion` smallint(5) UNSIGNED DEFAULT NULL,
  `uid_pilote` int(10) UNSIGNED DEFAULT NULL,
  `uid_creat` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `dte_creat` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `p67_login`
--

CREATE TABLE `p67_login` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(100) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `dte_maj` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `header` varchar(200) COLLATE latin1_general_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `p67_maintatelier`
--

CREATE TABLE `p67_maintatelier` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `nom` varchar(200) NOT NULL DEFAULT '',
  `mail` varchar(200) NOT NULL DEFAULT '',
  `actif` enum('oui','non') NOT NULL DEFAULT 'oui'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `p67_maintenance`
--

CREATE TABLE `p67_maintenance` (
  `id` int(10) UNSIGNED NOT NULL,
  `uid_ressource` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `uid_atelier` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `actif` enum('oui','non') NOT NULL DEFAULT 'oui',
  `status` enum('planifie','confirme','effectue','cloture','supprime') NOT NULL DEFAULT 'planifie',
  `dte_deb` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `dte_fin` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `potentiel` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `uid_lastresa` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `uid_creat` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `dte_creat` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `uid_maj` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `dte_maj` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `p67_maintfiche`
--

CREATE TABLE `p67_maintfiche` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `uid_avion` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `description` text NOT NULL,
  `uid_valid` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `dte_valid` datetime NOT NULL,
  `traite` enum('oui','non','ann','ref') NOT NULL DEFAULT 'non',
  `uid_planif` mediumint(8) UNSIGNED NOT NULL,
  `uid_creat` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `dte_creat` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `uid_maj` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `dte_maj` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `p67_manips`
--

CREATE TABLE `p67_manips` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `titre` varchar(100) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `comment` text COLLATE latin1_general_ci NOT NULL,
  `type` varchar(100) COLLATE latin1_general_ci NOT NULL,
  `cout` decimal(10,2) NOT NULL DEFAULT '0.00',
  `facture` enum('oui','non') COLLATE latin1_general_ci NOT NULL DEFAULT 'non',
  `actif` enum('oui','non') COLLATE latin1_general_ci NOT NULL DEFAULT 'oui',
  `dte_manip` date NOT NULL DEFAULT '0000-00-00',
  `dte_limite` date NOT NULL,
  `uid_creat` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `dte_creat` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `uid_modif` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `dte_modif` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `p67_masses`
--

CREATE TABLE `p67_masses` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `uid_vol` mediumint(8) UNSIGNED DEFAULT NULL,
  `uid_pilote` int(10) UNSIGNED DEFAULT NULL,
  `uid_place` tinyint(3) UNSIGNED DEFAULT NULL,
  `poids` char(3) DEFAULT NULL,
  `uid_creat` int(10) UNSIGNED DEFAULT NULL,
  `dte_creat` datetime DEFAULT NULL,
  `uid_modif` int(10) UNSIGNED DEFAULT NULL,
  `dte_modif` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `p67_mouvement`
--

CREATE TABLE `p67_mouvement` (
  `id` int(10) UNSIGNED NOT NULL,
  `ordre` varchar(4) NOT NULL,
  `description` varchar(100) NOT NULL DEFAULT '',
  `compte` varchar(10) NOT NULL,
  `debiteur` char(3) NOT NULL DEFAULT '0',
  `crediteur` char(3) NOT NULL DEFAULT '0',
  `montant` decimal(10,2) NOT NULL DEFAULT '0.00',
  `actif` enum('oui','non') NOT NULL DEFAULT 'oui',
  `j0` char(1) NOT NULL,
  `j1` char(1) NOT NULL DEFAULT 'N',
  `j2` char(1) NOT NULL DEFAULT 'N',
  `j3` char(1) NOT NULL,
  `j4` char(1) NOT NULL DEFAULT 'N',
  `j5` char(1) NOT NULL DEFAULT 'N',
  `j6` char(1) NOT NULL,
  `j7` char(1) NOT NULL,
  `vac` char(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `p67_navigation`
--

CREATE TABLE `p67_navigation` (
  `id` int(10) UNSIGNED NOT NULL,
  `titre` varchar(40) NOT NULL,
  `vitesse` int(10) UNSIGNED NOT NULL,
  `dirvent` int(10) UNSIGNED NOT NULL,
  `vitvent` int(10) UNSIGNED NOT NULL,
  `uid_creat` int(10) UNSIGNED NOT NULL,
  `dte_creat` datetime NOT NULL,
  `uid_modif` int(10) UNSIGNED NOT NULL,
  `dte_modif` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `p67_navpoints`
--

CREATE TABLE `p67_navpoints` (
  `nom` varchar(20) NOT NULL,
  `description` varchar(200) NOT NULL,
  `lat` varchar(10) NOT NULL,
  `lon` varchar(10) NOT NULL,
  `icone` varchar(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `p67_navroute`
--

CREATE TABLE `p67_navroute` (
  `id` int(10) UNSIGNED NOT NULL,
  `idnav` int(10) UNSIGNED NOT NULL,
  `ordre` int(10) UNSIGNED NOT NULL,
  `nom` varchar(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `p67_participants`
--

CREATE TABLE `p67_participants` (
  `id` int(10) UNSIGNED NOT NULL,
  `idmanip` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `idusr` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `participe` enum('Y','N') NOT NULL DEFAULT 'Y',
  `nb` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `uid_creat` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `dte_creat` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `p67_plage`
--

CREATE TABLE `p67_plage` (
  `id` varbinary(2) NOT NULL,
  `jour` char(1) NOT NULL,
  `plage` char(1) NOT NULL,
  `titre` varchar(20) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `libelle` varchar(50) NOT NULL,
  `deb` int(10) UNSIGNED NOT NULL,
  `fin` int(10) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `p67_presence`
--

CREATE TABLE `p67_presence` (
  `id` int(10) UNSIGNED NOT NULL,
  `uid` int(10) UNSIGNED NOT NULL,
  `dte` date NOT NULL,
  `type` varchar(10) NOT NULL,
  `dtedeb` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `dtefin` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `zone` char(3) NOT NULL DEFAULT '',
  `regime` varchar(3) NOT NULL,
  `tpspaye` int(11) NOT NULL DEFAULT '0',
  `tpsreel` int(11) NOT NULL DEFAULT '0',
  `age` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `handicap` varchar(3) NOT NULL DEFAULT 'non'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `p67_prevision`
--

CREATE TABLE `p67_prevision` (
  `id` int(10) UNSIGNED NOT NULL,
  `annee` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `mois` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `avion` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `heures` smallint(5) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `p67_ressources`
--

CREATE TABLE `p67_ressources` (
  `id` smallint(6) NOT NULL,
  `nom` varchar(20) NOT NULL DEFAULT '',
  `immatriculation` varchar(6) NOT NULL DEFAULT '',
  `marque` varchar(20) NOT NULL DEFAULT '',
  `modele` varchar(20) NOT NULL DEFAULT '',
  `couleur` varchar(6) NOT NULL,
  `actif` enum('oui','non','off') NOT NULL DEFAULT 'oui',
  `poste` int(10) UNSIGNED NOT NULL,
  `tarif` varchar(6) NOT NULL DEFAULT '0',
  `tarif_reduit` varchar(6) NOT NULL DEFAULT '0',
  `tarif_double` varchar(6) NOT NULL DEFAULT '0',
  `tarif_inst` varchar(6) NOT NULL DEFAULT '0',
  `tarif_nue` varchar(6) NOT NULL DEFAULT '0',
  `typehora` varchar(3) NOT NULL DEFAULT 'min',
  `description` text NOT NULL,
  `places` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `puissance` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `charge` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `massemax` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `vitesse` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `autonomie` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `tolerance` tinytext NOT NULL,
  `centrage` text NOT NULL,
  `maintenance` varchar(200) NOT NULL DEFAULT '',
  `uid_maj` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `dte_maj` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `p67_roles`
--

CREATE TABLE `p67_roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `groupe` varchar(5) NOT NULL,
  `role` varchar(40) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `p67_tarifs`
--

CREATE TABLE `p67_tarifs` (
  `id` int(10) UNSIGNED NOT NULL,
  `ress_id` int(10) UNSIGNED NOT NULL,
  `code` varchar(2) NOT NULL,
  `nom` varchar(20) NOT NULL,
  `pilote` varchar(6) NOT NULL,
  `instructeur` varchar(6) NOT NULL,
  `reduction` int(11) NOT NULL,
  `defaut_pil` enum('oui','non') NOT NULL DEFAULT 'non',
  `defaut_ins` enum('oui','non') NOT NULL DEFAULT 'non'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `p67_type`
--

CREATE TABLE `p67_type` (
  `id` varchar(2) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `nom` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `libelle` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `p67_utildonnees`
--

CREATE TABLE `p67_utildonnees` (
  `id` int(10) UNSIGNED NOT NULL,
  `did` int(10) UNSIGNED NOT NULL,
  `uid` int(11) NOT NULL,
  `valeur` varchar(255) COLLATE latin1_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `p67_utildonneesdef`
--

CREATE TABLE `p67_utildonneesdef` (
  `id` int(10) UNSIGNED NOT NULL,
  `ordre` tinyint(3) UNSIGNED NOT NULL,
  `nom` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `type` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `actif` enum('oui','non') COLLATE latin1_general_ci NOT NULL DEFAULT 'oui'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `p67_utilisateurs`
--

CREATE TABLE `p67_utilisateurs` (
  `id` int(10) UNSIGNED NOT NULL,
  `nom` varchar(40) NOT NULL DEFAULT '',
  `prenom` varchar(40) NOT NULL DEFAULT '',
  `initiales` char(3) NOT NULL DEFAULT '',
  `password` varchar(50) NOT NULL DEFAULT '',
  `idcpt` int(10) UNSIGNED NOT NULL,
  `sexe` enum('M','F','NA') NOT NULL DEFAULT 'NA',
  `pere` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `mere` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `mail` varchar(104) NOT NULL DEFAULT '',
  `notification` enum('oui','non') NOT NULL DEFAULT 'oui',
  `disponibilite` enum('dispo','occupe') NOT NULL DEFAULT 'dispo',
  `tel_fixe` varchar(20) NOT NULL DEFAULT '',
  `tel_portable` varchar(20) NOT NULL DEFAULT '',
  `tel_bureau` varchar(20) NOT NULL DEFAULT '',
  `adresse1` varchar(255) NOT NULL DEFAULT '',
  `adresse2` varchar(255) NOT NULL DEFAULT '',
  `ville` varchar(100) NOT NULL DEFAULT '',
  `codepostal` varchar(10) NOT NULL DEFAULT '',
  `zone` varchar(3) NOT NULL,
  `profession` varchar(50) NOT NULL,
  `employeur` varchar(50) NOT NULL,
  `commentaire` text NOT NULL,
  `avatar` varchar(50) NOT NULL DEFAULT '',
  `droits` varchar(100) NOT NULL DEFAULT '',
  `actif` enum('oui','non','off') NOT NULL DEFAULT 'oui',
  `virtuel` enum('oui','non') NOT NULL DEFAULT 'non',
  `type` enum('pilote','eleve','instructeur','invite','membre','parent','enfant','employe') NOT NULL DEFAULT 'pilote',
  `decouvert` smallint(6) NOT NULL DEFAULT '0',
  `tarif` smallint(6) NOT NULL DEFAULT '0',
  `dte_naissance` date NOT NULL DEFAULT '0000-00-00',
  `dte_licence` date NOT NULL DEFAULT '0000-00-00',
  `dte_medicale` date NOT NULL DEFAULT '0000-00-00',
  `dte_inscription` date NOT NULL,
  `dte_login` datetime NOT NULL,
  `poids` tinyint(3) UNSIGNED NOT NULL DEFAULT '75',
  `aff_rapide` char(1) NOT NULL DEFAULT 'n',
  `aff_mois` char(1) NOT NULL DEFAULT '',
  `aff_jour` date NOT NULL DEFAULT '0000-00-00',
  `aff_msg` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `num_caf` varchar(20) NOT NULL,
  `regime` varchar(20) NOT NULL DEFAULT 'general',
  `type_repas` varchar(20) NOT NULL DEFAULT 'standard',
  `maladies` tinytext NOT NULL,
  `handicap` enum('oui','non') NOT NULL DEFAULT 'non',
  `allergie_asthme` enum('Y','N') NOT NULL DEFAULT 'N',
  `allergie_medicament` enum('Y','N') NOT NULL DEFAULT 'N',
  `allergie_alimentaire` enum('Y','N') NOT NULL DEFAULT 'N',
  `allergie_commentaire` text NOT NULL,
  `remarque_sante` text NOT NULL,
  `nom_medecin` varchar(50) NOT NULL,
  `tel_medecin` varchar(20) NOT NULL,
  `adr_medecin` varchar(100) NOT NULL,
  `aut_prelevement` enum('Y','N') NOT NULL DEFAULT 'N',
  `uid_maj` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `dte_maj` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `p67_vacances`
--

CREATE TABLE `p67_vacances` (
  `id` int(10) UNSIGNED NOT NULL,
  `dtedeb` date NOT NULL,
  `dtefin` date NOT NULL,
  `comment` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `p67_abonnement`
--
ALTER TABLE `p67_abonnement`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `abonum` (`abonum`),
  ADD UNIQUE KEY `abonum_2` (`abonum`),
  ADD UNIQUE KEY `abonum_3` (`abonum`),
  ADD UNIQUE KEY `abonum_4` (`abonum`),
  ADD KEY `uid` (`uid`),
  ADD KEY `uid_2` (`uid`),
  ADD KEY `uid_3` (`uid`),
  ADD KEY `uid_4` (`uid`),
  ADD KEY `actif` (`actif`);

--
-- Indexes for table `p67_abo_ligne`
--
ALTER TABLE `p67_abo_ligne`
  ADD PRIMARY KEY (`id`),
  ADD KEY `abonum` (`abonum`,`uid`),
  ADD KEY `mouvid` (`mouvid`);

--
-- Indexes for table `p67_actualites`
--
ALTER TABLE `p67_actualites`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid_creat` (`uid_creat`);

--
-- Indexes for table `p67_bapteme`
--
ALTER TABLE `p67_bapteme`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pilote` (`id_pilote`,`id_avion`);

--
-- Indexes for table `p67_calendrier`
--
ALTER TABLE `p67_calendrier`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid_avion` (`uid_avion`),
  ADD KEY `uid_pilote` (`uid_pilote`),
  ADD KEY `uid_instructeur` (`uid_instructeur`);

--
-- Indexes for table `p67_compte`
--
ALTER TABLE `p67_compte`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `signature` (`signature`),
  ADD UNIQUE KEY `signature_2` (`signature`),
  ADD KEY `uid` (`uid`),
  ADD KEY `mouvement` (`mouvement`),
  ADD KEY `tiers` (`tiers`),
  ADD KEY `dte` (`dte`),
  ADD KEY `compte` (`compte`),
  ADD KEY `mid` (`mid`),
  ADD KEY `facture` (`facture`),
  ADD KEY `facture_2` (`facture`),
  ADD KEY `rembfact` (`rembfact`);

--
-- Indexes for table `p67_comptetemp`
--
ALTER TABLE `p67_comptetemp`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `p67_conso`
--
ALTER TABLE `p67_conso`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idvol` (`idvol`),
  ADD KEY `idavion` (`idavion`);

--
-- Indexes for table `p67_cron`
--
ALTER TABLE `p67_cron`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `p67_disponibilite`
--
ALTER TABLE `p67_disponibilite`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`);

--
-- Indexes for table `p67_document`
--
ALTER TABLE `p67_document`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`,`type`);

--
-- Indexes for table `p67_droits`
--
ALTER TABLE `p67_droits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `groupe` (`groupe`,`uid`),
  ADD KEY `groupe_2` (`groupe`),
  ADD KEY `uid` (`uid`);

--
-- Indexes for table `p67_echeance`
--
ALTER TABLE `p67_echeance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `typeid` (`typeid`),
  ADD KEY `uid` (`uid`);

--
-- Indexes for table `p67_echeancetype`
--
ALTER TABLE `p67_echeancetype`
  ADD PRIMARY KEY (`id`),
  ADD KEY `poste` (`poste`);

--
-- Indexes for table `p67_export`
--
ALTER TABLE `p67_export`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `p67_factures`
--
ALTER TABLE `p67_factures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `uid_2` (`uid`),
  ADD KEY `uid_3` (`uid`),
  ADD KEY `uid_4` (`uid`),
  ADD KEY `dteid` (`dteid`);

--
-- Indexes for table `p67_forums`
--
ALTER TABLE `p67_forums`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fil` (`fil`),
  ADD KEY `fid` (`fid`),
  ADD KEY `uid_creat` (`uid_creat`),
  ADD KEY `uid_maj` (`uid_maj`),
  ADD KEY `actif` (`actif`);

--
-- Indexes for table `p67_forums_lus`
--
ALTER TABLE `p67_forums_lus`
  ADD PRIMARY KEY (`forum_id`),
  ADD KEY `forum_msg` (`forum_msg`),
  ADD KEY `forum_usr` (`forum_usr`),
  ADD KEY `forum_usr_2` (`forum_usr`);

--
-- Indexes for table `p67_groupe`
--
ALTER TABLE `p67_groupe`
  ADD PRIMARY KEY (`groupe`);

--
-- Indexes for table `p67_historique`
--
ALTER TABLE `p67_historique`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid_maj` (`uid_maj`),
  ADD KEY `table` (`table`),
  ADD KEY `idtable` (`idtable`);

--
-- Indexes for table `p67_lache`
--
ALTER TABLE `p67_lache`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid_avion` (`id_avion`,`uid_pilote`);

--
-- Indexes for table `p67_login`
--
ALTER TABLE `p67_login`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `p67_maintatelier`
--
ALTER TABLE `p67_maintatelier`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `p67_maintenance`
--
ALTER TABLE `p67_maintenance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `avion` (`uid_ressource`);

--
-- Indexes for table `p67_maintfiche`
--
ALTER TABLE `p67_maintfiche`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `p67_manips`
--
ALTER TABLE `p67_manips`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `p67_masses`
--
ALTER TABLE `p67_masses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid_vol` (`uid_vol`),
  ADD KEY `uid_pilote` (`uid_pilote`);

--
-- Indexes for table `p67_mouvement`
--
ALTER TABLE `p67_mouvement`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `p67_navigation`
--
ALTER TABLE `p67_navigation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `p67_navpoints`
--
ALTER TABLE `p67_navpoints`
  ADD PRIMARY KEY (`nom`);

--
-- Indexes for table `p67_navroute`
--
ALTER TABLE `p67_navroute`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idnav` (`idnav`,`nom`);

--
-- Indexes for table `p67_participants`
--
ALTER TABLE `p67_participants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idmanip` (`idmanip`);

--
-- Indexes for table `p67_presence`
--
ALTER TABLE `p67_presence`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `type` (`type`),
  ADD KEY `dte` (`dte`);

--
-- Indexes for table `p67_prevision`
--
ALTER TABLE `p67_prevision`
  ADD PRIMARY KEY (`id`),
  ADD KEY `anne` (`annee`),
  ADD KEY `mois` (`mois`);

--
-- Indexes for table `p67_ressources`
--
ALTER TABLE `p67_ressources`
  ADD PRIMARY KEY (`id`),
  ADD KEY `poste` (`poste`);

--
-- Indexes for table `p67_roles`
--
ALTER TABLE `p67_roles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `groupe` (`groupe`,`role`);

--
-- Indexes for table `p67_tarifs`
--
ALTER TABLE `p67_tarifs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ress_id` (`ress_id`,`code`);

--
-- Indexes for table `p67_type`
--
ALTER TABLE `p67_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `p67_utildonnees`
--
ALTER TABLE `p67_utildonnees`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `dataid` (`did`);

--
-- Indexes for table `p67_utildonneesdef`
--
ALTER TABLE `p67_utildonneesdef`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `p67_utilisateurs`
--
ALTER TABLE `p67_utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `type` (`type`),
  ADD KEY `virtuel` (`virtuel`),
  ADD KEY `actif` (`actif`),
  ADD KEY `type_2` (`type`),
  ADD KEY `virtuel_2` (`virtuel`),
  ADD KEY `actif_2` (`actif`),
  ADD KEY `type_3` (`type`),
  ADD KEY `virtuel_3` (`virtuel`),
  ADD KEY `actif_3` (`actif`),
  ADD KEY `type_4` (`type`),
  ADD KEY `virtuel_4` (`virtuel`),
  ADD KEY `actif_4` (`actif`);

--
-- Indexes for table `p67_vacances`
--
ALTER TABLE `p67_vacances`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `p67_abonnement`
--
ALTER TABLE `p67_abonnement`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `p67_abo_ligne`
--
ALTER TABLE `p67_abo_ligne`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `p67_actualites`
--
ALTER TABLE `p67_actualites`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3234;
--
-- AUTO_INCREMENT for table `p67_bapteme`
--
ALTER TABLE `p67_bapteme`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=207;
--
-- AUTO_INCREMENT for table `p67_calendrier`
--
ALTER TABLE `p67_calendrier`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16188;
--
-- AUTO_INCREMENT for table `p67_compte`
--
ALTER TABLE `p67_compte`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36463;
--
-- AUTO_INCREMENT for table `p67_comptetemp`
--
ALTER TABLE `p67_comptetemp`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36316;
--
-- AUTO_INCREMENT for table `p67_conso`
--
ALTER TABLE `p67_conso`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `p67_cron`
--
ALTER TABLE `p67_cron`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `p67_disponibilite`
--
ALTER TABLE `p67_disponibilite`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT for table `p67_document`
--
ALTER TABLE `p67_document`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `p67_droits`
--
ALTER TABLE `p67_droits`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=384;
--
-- AUTO_INCREMENT for table `p67_echeance`
--
ALTER TABLE `p67_echeance`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=432;
--
-- AUTO_INCREMENT for table `p67_echeancetype`
--
ALTER TABLE `p67_echeancetype`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `p67_export`
--
ALTER TABLE `p67_export`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `p67_forums`
--
ALTER TABLE `p67_forums`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1020;
--
-- AUTO_INCREMENT for table `p67_forums_lus`
--
ALTER TABLE `p67_forums_lus`
  MODIFY `forum_id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=519617;
--
-- AUTO_INCREMENT for table `p67_historique`
--
ALTER TABLE `p67_historique`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28774;
--
-- AUTO_INCREMENT for table `p67_lache`
--
ALTER TABLE `p67_lache`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=303;
--
-- AUTO_INCREMENT for table `p67_login`
--
ALTER TABLE `p67_login`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63817;
--
-- AUTO_INCREMENT for table `p67_maintatelier`
--
ALTER TABLE `p67_maintatelier`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `p67_maintenance`
--
ALTER TABLE `p67_maintenance`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=148;
--
-- AUTO_INCREMENT for table `p67_maintfiche`
--
ALTER TABLE `p67_maintfiche`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=263;
--
-- AUTO_INCREMENT for table `p67_manips`
--
ALTER TABLE `p67_manips`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=229;
--
-- AUTO_INCREMENT for table `p67_masses`
--
ALTER TABLE `p67_masses`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1881;
--
-- AUTO_INCREMENT for table `p67_mouvement`
--
ALTER TABLE `p67_mouvement`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;
--
-- AUTO_INCREMENT for table `p67_navigation`
--
ALTER TABLE `p67_navigation`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `p67_navroute`
--
ALTER TABLE `p67_navroute`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;
--
-- AUTO_INCREMENT for table `p67_participants`
--
ALTER TABLE `p67_participants`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1744;
--
-- AUTO_INCREMENT for table `p67_presence`
--
ALTER TABLE `p67_presence`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `p67_prevision`
--
ALTER TABLE `p67_prevision`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=349;
--
-- AUTO_INCREMENT for table `p67_ressources`
--
ALTER TABLE `p67_ressources`
  MODIFY `id` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `p67_roles`
--
ALTER TABLE `p67_roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=153;
--
-- AUTO_INCREMENT for table `p67_tarifs`
--
ALTER TABLE `p67_tarifs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;
--
-- AUTO_INCREMENT for table `p67_utildonnees`
--
ALTER TABLE `p67_utildonnees`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `p67_utildonneesdef`
--
ALTER TABLE `p67_utildonneesdef`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `p67_utilisateurs`
--
ALTER TABLE `p67_utilisateurs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=212;
--
-- AUTO_INCREMENT for table `p67_vacances`
--
ALTER TABLE `p67_vacances`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
  
  


-- Création des éléments de base
INSERT INTO `p67_utilisateurs` (`id`, `nom`, `prenom`, `initiales`, `password`, `idcpt`, `sexe`, `pere`, `mere`, `mail`, `notification`, `tel_fixe`, `tel_portable`, `tel_bureau`, `adresse1`, `adresse2`, `ville`, `codepostal`, `zone`, `profession`, `employeur`, `commentaire`, `avatar`, `droits`, `actif`, `virtuel`, `type`, `decouvert`, `tarif`, `dte_naissance`, `dte_licence`, `dte_medicale`, `dte_inscription`, `dte_login`, `poids`, `aff_rapide`, `aff_mois`, `aff_jour`, `aff_msg`, `num_caf`, `regime`, `type_repas`, `maladies`, `handicap`, `allergie_asthme`, `allergie_medicament`, `allergie_alimentaire`, `allergie_commentaire`, `remarque_sante`, `nom_medecin`, `tel_medecin`, `adr_medecin`, `aut_prelevement`, `uid_maj`, `dte_maj`) VALUES(1, 'admin', 'admin', 'adm', '21232f297a57a5a743894a0e4a801fc3', 1, 'M', 0, 0, '', 'oui', '', '', '', '', '', '', '', '', '', '', '', '', 'ADM', 'oui', 'non', 'membre', 0, 0, '0000-00-00', '0000-00-00', '0000-00-00', NOW(), NOW(), 0, 'y', '1', '2016-04-01', 61, '', 'GEN', 'standard', '', 'non', 'N', 'N', 'N', '', '', '', '', '', 'N', 1, NOW());
INSERT INTO `p67_utilisateurs` (`id`, `nom`, `prenom`, `initiales`, `password`, `idcpt`, `sexe`, `pere`, `mere`, `mail`, `notification`, `tel_fixe`, `tel_portable`, `tel_bureau`, `adresse1`, `adresse2`, `ville`, `codepostal`, `zone`, `profession`, `employeur`, `commentaire`, `avatar`, `droits`, `actif`, `virtuel`, `type`, `decouvert`, `tarif`, `dte_naissance`, `dte_licence`, `dte_medicale`, `dte_inscription`, `dte_login`, `poids`, `aff_rapide`, `aff_mois`, `aff_jour`, `aff_msg`, `num_caf`, `regime`, `type_repas`, `maladies`, `handicap`, `allergie_asthme`, `allergie_medicament`, `allergie_alimentaire`, `allergie_commentaire`, `remarque_sante`, `nom_medecin`, `tel_medecin`, `adr_medecin`, `aut_prelevement`, `uid_maj`, `dte_maj`) VALUES(2, 'banque', '', '', '', 2, 'NA', 0, 0, '', 'non', '', '', '', '', '', '', '', '', '', '', '', '', '', 'oui', 'oui', 'membre', 0, 0, '0000-00-00', '0000-00-00', '0000-00-00', NOW(), NOW(), 0, 'n', '', '0000-00-00', 0, '', 'general', 'standard', '', 'non', 'N', 'N', 'N', '', '', '', '', '', 'N', 1, NOW());
INSERT INTO `p67_utilisateurs` (`id`, `nom`, `prenom`, `initiales`, `password`, `idcpt`, `sexe`, `pere`, `mere`, `mail`, `notification`, `tel_fixe`, `tel_portable`, `tel_bureau`, `adresse1`, `adresse2`, `ville`, `codepostal`, `zone`, `profession`, `employeur`, `commentaire`, `avatar`, `droits`, `actif`, `virtuel`, `type`, `decouvert`, `tarif`, `dte_naissance`, `dte_licence`, `dte_medicale`, `dte_inscription`, `dte_login`, `poids`, `aff_rapide`, `aff_mois`, `aff_jour`, `aff_msg`, `num_caf`, `regime`, `type_repas`, `maladies`, `handicap`, `allergie_asthme`, `allergie_medicament`, `allergie_alimentaire`, `allergie_commentaire`, `remarque_sante`, `nom_medecin`, `tel_medecin`, `adr_medecin`, `aut_prelevement`, `uid_maj`, `dte_maj`) VALUES(3, 'club', '', '', '', 3, 'NA', 0, 0, '', 'non', '', '', '', '', '', '', '', '', '', '', '', '', '', 'oui', 'oui', 'membre', 0, 0, '0000-00-00', '0000-00-00', '0000-00-00', NOW(), NOW(), 0, 'n', '', '0000-00-00', 0, '', 'general', 'standard', '', 'non', 'N', 'N', 'N', '', '', '', '', '', 'N', 1, NOW());
INSERT INTO `p67_utilisateurs` (`id`, `nom`, `prenom`, `initiales`, `password`, `idcpt`, `sexe`, `pere`, `mere`, `mail`, `notification`, `tel_fixe`, `tel_portable`, `tel_bureau`, `adresse1`, `adresse2`, `ville`, `codepostal`, `zone`, `profession`, `employeur`, `commentaire`, `avatar`, `droits`, `actif`, `virtuel`, `type`, `decouvert`, `tarif`, `dte_naissance`, `dte_licence`, `dte_medicale`, `dte_inscription`, `dte_login`, `poids`, `aff_rapide`, `aff_mois`, `aff_jour`, `aff_msg`, `num_caf`, `regime`, `type_repas`, `maladies`, `handicap`, `allergie_asthme`, `allergie_medicament`, `allergie_alimentaire`, `allergie_commentaire`, `remarque_sante`, `nom_medecin`, `tel_medecin`, `adr_medecin`, `aut_prelevement`, `uid_maj`, `dte_maj`) VALUES(4, 'system', '', '', '', 4, 'NA', 0, 0, '', 'non', '', '', '', '', '', '', '', '', '', '', '', '', '', 'oui', 'oui', 'membre', 0, 0, '0000-00-00', '0000-00-00', '0000-00-00', NOW(), NOW(), 0, 'n', '', '0000-00-00', 0, '', 'general', 'standard', '', 'non', 'N', 'N', 'N', '', '', '', '', '', 'N', 1, NOW());

INSERT INTO `p67_groupe` (`groupe`, `description`) VALUES ('ADM', 'Administrateurs'), ('ALL', 'Tout le monde'); 
INSERT INTO `p67_droits` (`id`, `groupe`, `uid`, `uid_creat`, `dte_creat`) VALUES (NULL, 'ADM', '1', '1', NOW()); 
INSERT INTO `p67_config` (`param`, `value`) VALUES ('dbversion', '474'); 

INSERT INTO `p67_ressources` (`nom`, `immatriculation`, `marque`, `modele`, `couleur`, `actif`, `tarif`, `tarif_reduit`, `tarif_double`, `tarif_inst`, `tarif_nue`, `typehora`, `description`, `places`, `puissance`, `charge`, `massemax`, `vitesse`, `autonomie`, `tolerance`, `centrage`, `maintenance`, `uid_maj`, `dte_maj`) VALUES
('Default', 'F-XXXX', '', '', '', 'oui', '0', '0', '0', '0', '0', 'min', '', 0, 0, 0, 0, 0, 0, '', '', '', 0, '0000-00-00 00:00:00');



