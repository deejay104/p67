-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Client: 127.0.0.1
-- Généré le: Lun 25 Avril 2016 à 10:44
-- Version du serveur: 5.5.47-MariaDB-1ubuntu0.14.04.1
-- Version de PHP: 5.5.9-1ubuntu4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `p67`
--

-- --------------------------------------------------------

--
-- Structure de la table `p67_abonnement`
--

CREATE TABLE IF NOT EXISTS `p67_abonnement` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `abonum` varchar(8) COLLATE latin1_general_ci NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `dtedeb` date NOT NULL,
  `dtefin` date NOT NULL,
  `jour_num` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `jour_sem` char(1) COLLATE latin1_general_ci NOT NULL DEFAULT '-',
  `actif` enum('oui','non') COLLATE latin1_general_ci NOT NULL DEFAULT 'oui',
  `uid_maj` int(10) unsigned NOT NULL,
  `dte_maj` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `abonum` (`abonum`),
  UNIQUE KEY `abonum_2` (`abonum`),
  UNIQUE KEY `abonum_3` (`abonum`),
  UNIQUE KEY `abonum_4` (`abonum`),
  KEY `uid` (`uid`),
  KEY `uid_2` (`uid`),
  KEY `uid_3` (`uid`),
  KEY `uid_4` (`uid`),
  KEY `actif` (`actif`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `p67_abo_ligne`
--

CREATE TABLE IF NOT EXISTS `p67_abo_ligne` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `abonum` varchar(8) NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `mouvid` int(10) unsigned NOT NULL DEFAULT '0',
  `montant` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `abonum` (`abonum`,`uid`),
  KEY `mouvid` (`mouvid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Structure de la table `p67_actualites`
--

CREATE TABLE IF NOT EXISTS `p67_actualites` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `titre` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `dte_mail` datetime NOT NULL,
  `mail` enum('oui','non') NOT NULL DEFAULT 'non',
  `actif` enum('oui','non') NOT NULL DEFAULT 'oui',
  `uid_creat` int(10) unsigned NOT NULL,
  `dte_creat` datetime NOT NULL,
  `uid_modif` int(11) NOT NULL,
  `dte_modif` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid_creat` (`uid_creat`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Structure de la table `p67_bapteme`
--

CREATE TABLE IF NOT EXISTS `p67_bapteme` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `num` varchar(20) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `telephone` varchar(14) NOT NULL,
  `mail` varchar(100) NOT NULL,
  `nb` tinyint(3) unsigned NOT NULL,
  `dte` datetime NOT NULL,
  `actif` enum('oui','non') NOT NULL DEFAULT 'oui',
  `status` tinyint(3) unsigned NOT NULL,
  `type` enum('btm','vi') NOT NULL DEFAULT 'btm',
  `paye` enum('oui','non') NOT NULL DEFAULT 'non',
  `id_pilote` int(10) unsigned NOT NULL,
  `id_avion` int(10) unsigned NOT NULL,
  `id_resa` int(10) unsigned NOT NULL,
  `description` text NOT NULL,
  `uid_creat` int(10) unsigned NOT NULL,
  `dte_creat` datetime NOT NULL,
  `uid_maj` int(10) unsigned NOT NULL,
  `dte_maj` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_pilote` (`id_pilote`,`id_avion`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Structure de la table `p67_calendrier`
--

CREATE TABLE IF NOT EXISTS `p67_calendrier` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `description` text NOT NULL,
  `dte_deb` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `dte_fin` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `uid_pilote` int(10) unsigned NOT NULL DEFAULT '0',
  `uid_debite` int(10) unsigned NOT NULL DEFAULT '0',
  `uid_instructeur` int(10) unsigned NOT NULL DEFAULT '0',
  `uid_avion` smallint(5) unsigned NOT NULL DEFAULT '0',
  `destination` varchar(50) NOT NULL,
  `nbpersonne` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `accept` enum('oui','non') NOT NULL DEFAULT 'non',
  `temps` int(11) NOT NULL DEFAULT '0',
  `tarif` char(2) NOT NULL DEFAULT '',
  `prix` float NOT NULL DEFAULT '0',
  `tpsestime` int(11) NOT NULL,
  `tpsreel` int(11) NOT NULL,
  `horadeb` varchar(10) NOT NULL DEFAULT '0',
  `horafin` varchar(10) NOT NULL DEFAULT '0',
  `idmaint` int(10) unsigned NOT NULL,
  `potentiel` int(10) unsigned NOT NULL,
  `reel` enum('oui','non') NOT NULL DEFAULT 'oui',
  `edite` enum('oui','non') NOT NULL DEFAULT 'non',
  `dte_maj` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `uid_maj` int(10) unsigned NOT NULL DEFAULT '0',
  `actif` enum('oui','non') NOT NULL DEFAULT 'oui',
  PRIMARY KEY (`id`),
  KEY `uid_avion` (`uid_avion`),
  KEY `uid_pilote` (`uid_pilote`),
  KEY `uid_instructeur` (`uid_instructeur`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Structure de la table `p67_compte`
--

CREATE TABLE IF NOT EXISTS `p67_compte` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `tiers` int(10) unsigned NOT NULL DEFAULT '0',
  `montant` decimal(10,2) NOT NULL DEFAULT '0.00',
  `mouvement` varchar(100) NOT NULL DEFAULT '',
  `commentaire` tinytext NOT NULL,
  `date_valeur` date NOT NULL DEFAULT '0000-00-00',
  `dte` varchar(6) NOT NULL,
  `compte` varchar(10) NOT NULL,
  `pointe` char(1) NOT NULL DEFAULT '',
  `facture` varchar(10) NOT NULL,
  `uid_creat` int(10) unsigned NOT NULL DEFAULT '0',
  `date_creat` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `mouvement` (`mouvement`),
  KEY `tiers` (`tiers`),
  KEY `dte` (`dte`),
  KEY `compte` (`compte`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Structure de la table `p67_config`
--

CREATE TABLE IF NOT EXISTS `p67_config` (
  `param` varchar(20) NOT NULL,
  `value` varchar(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `p67_conso`
--

CREATE TABLE IF NOT EXISTS `p67_conso` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `idvol` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `idavion` smallint(5) unsigned NOT NULL DEFAULT '0',
  `quantite` float NOT NULL DEFAULT '0',
  `prix` float NOT NULL DEFAULT '0',
  `tiers` varchar(100) NOT NULL DEFAULT '',
  `uid_creat` smallint(5) unsigned NOT NULL DEFAULT '0',
  `dte_creat` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `uid_modif` smallint(5) unsigned NOT NULL DEFAULT '0',
  `dte_modif` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `idvol` (`idvol`),
  KEY `idavion` (`idavion`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Structure de la table `p67_document`
--

CREATE TABLE IF NOT EXISTS `p67_document` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `filename` varchar(20) NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `type` varchar(10) NOT NULL,
  `dossier` tinytext NOT NULL,
  `droit` varchar(3) NOT NULL,
  `actif` enum('oui','non') NOT NULL DEFAULT 'oui',
  `uid_creat` int(10) unsigned NOT NULL,
  `dte_creat` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`,`type`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Structure de la table `p67_droits`
--

CREATE TABLE IF NOT EXISTS `p67_droits` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `groupe` varchar(5) NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `uid_creat` int(10) unsigned NOT NULL,
  `dte_creat` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `groupe` (`groupe`,`uid`),
  KEY `groupe_2` (`groupe`),
  KEY `uid` (`uid`),
  KEY `groupe_3` (`groupe`),
  KEY `uid_2` (`uid`),
  KEY `groupe_4` (`groupe`),
  KEY `uid_3` (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Structure de la table `p67_export`
--

CREATE TABLE IF NOT EXISTS `p67_export` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `requete` text NOT NULL,
  `param` varchar(50) NOT NULL,
  `droit_r` char(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Structure de la table `p67_factures`
--

CREATE TABLE IF NOT EXISTS `p67_factures` (
  `id` varchar(10) NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `dteid` varchar(6) NOT NULL,
  `dte` date NOT NULL,
  `total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `paid` varchar(1) NOT NULL,
  `email` char(1) NOT NULL DEFAULT 'N',
  `comment` varchar(200) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `uid_2` (`uid`),
  KEY `uid_3` (`uid`),
  KEY `uid_4` (`uid`),
  KEY `dteid` (`dteid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `p67_forums`
--

CREATE TABLE IF NOT EXISTS `p67_forums` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `fid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `fil` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `titre` varchar(104) NOT NULL DEFAULT '',
  `message` text NOT NULL,
  `pseudo` varchar(104) NOT NULL DEFAULT '',
  `mail_diff` varchar(104) NOT NULL DEFAULT '',
  `actif` enum('oui','non') NOT NULL DEFAULT 'oui',
  `droit_r` char(3) NOT NULL DEFAULT '',
  `droit_w` char(3) NOT NULL DEFAULT '',
  `uid_creat` int(10) unsigned NOT NULL DEFAULT '0',
  `dte_creat` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `dte_maj` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `uid_maj` int(10) unsigned NOT NULL DEFAULT '0',
  `mailing` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fil` (`fil`),
  KEY `fid` (`fid`),
  KEY `uid_creat` (`uid_creat`),
  KEY `uid_maj` (`uid_maj`),
  KEY `actif` (`actif`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Structure de la table `p67_forums_lus`
--

CREATE TABLE IF NOT EXISTS `p67_forums_lus` (
  `forum_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `forum_msg` mediumint(8) unsigned DEFAULT NULL,
  `forum_usr` int(10) unsigned DEFAULT NULL,
  `forum_date` datetime DEFAULT NULL,
  PRIMARY KEY (`forum_id`),
  KEY `forum_msg` (`forum_msg`),
  KEY `forum_usr` (`forum_usr`),
  KEY `forum_usr_2` (`forum_usr`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='Tables pour les messages lus des forums' ;

-- --------------------------------------------------------

--
-- Structure de la table `p67_groupe`
--

CREATE TABLE IF NOT EXISTS `p67_groupe` (
  `groupe` varchar(5) NOT NULL,
  `description` varchar(200) NOT NULL,
  PRIMARY KEY (`groupe`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `p67_historique`
--

CREATE TABLE IF NOT EXISTS `p67_historique` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `class` varchar(20) NOT NULL,
  `table` varchar(20) NOT NULL,
  `idtable` bigint(20) unsigned NOT NULL,
  `uid_maj` int(10) unsigned NOT NULL DEFAULT '0',
  `dte_maj` datetime NOT NULL,
  `type` varchar(3) NOT NULL,
  `comment` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid_maj` (`uid_maj`),
  KEY `table` (`table`),
  KEY `idtable` (`idtable`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Structure de la table `p67_lache`
--

CREATE TABLE IF NOT EXISTS `p67_lache` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_avion` smallint(5) unsigned DEFAULT NULL,
  `uid_pilote` int(10) unsigned DEFAULT NULL,
  `uid_creat` int(10) unsigned NOT NULL DEFAULT '0',
  `dte_creat` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `uid_avion` (`id_avion`,`uid_pilote`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Structure de la table `p67_login`
--

CREATE TABLE IF NOT EXISTS `p67_login` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(100) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `dte_maj` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `header` varchar(200) COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ;

-- --------------------------------------------------------

--
-- Structure de la table `p67_maintatelier`
--

CREATE TABLE IF NOT EXISTS `p67_maintatelier` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `nom` varchar(200) NOT NULL DEFAULT '',
  `mail` varchar(200) NOT NULL DEFAULT '',
  `actif` enum('oui','non') NOT NULL DEFAULT 'oui',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Structure de la table `p67_maintenance`
--

CREATE TABLE IF NOT EXISTS `p67_maintenance` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid_ressource` smallint(5) unsigned NOT NULL DEFAULT '0',
  `uid_atelier` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `actif` enum('oui','non') NOT NULL DEFAULT 'oui',
  `status` enum('planifie','confirme','effectue','cloture','supprime') NOT NULL DEFAULT 'planifie',
  `dte_deb` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `dte_fin` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `potentiel` int(10) unsigned NOT NULL DEFAULT '0',
  `uid_lastresa` int(10) unsigned NOT NULL DEFAULT '0',
  `uid_creat` int(10) unsigned NOT NULL DEFAULT '0',
  `dte_creat` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `uid_maj` int(10) unsigned NOT NULL DEFAULT '0',
  `dte_maj` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `avion` (`uid_ressource`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Structure de la table `p67_maintfiche`
--

CREATE TABLE IF NOT EXISTS `p67_maintfiche` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `uid_avion` smallint(5) unsigned NOT NULL DEFAULT '0',
  `description` text NOT NULL,
  `uid_valid` int(10) unsigned NOT NULL DEFAULT '0',
  `dte_valid` datetime NOT NULL,
  `traite` enum('oui','non','ann','ref') NOT NULL DEFAULT 'non',
  `uid_planif` mediumint(8) unsigned NOT NULL,
  `uid_creat` int(10) unsigned NOT NULL DEFAULT '0',
  `dte_creat` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `uid_maj` int(10) unsigned NOT NULL DEFAULT '0',
  `dte_maj` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Structure de la table `p67_manips`
--

CREATE TABLE IF NOT EXISTS `p67_manips` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `titre` varchar(100) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `comment` text COLLATE latin1_general_ci NOT NULL,
  `type` varchar(100) COLLATE latin1_general_ci NOT NULL,
  `cout` decimal(10,2) NOT NULL DEFAULT '0.00',
  `facture` enum('oui','non') COLLATE latin1_general_ci NOT NULL DEFAULT 'non',
  `actif` enum('oui','non') COLLATE latin1_general_ci NOT NULL DEFAULT 'oui',
  `dte_manip` date NOT NULL DEFAULT '0000-00-00',
  `dte_limite` date NOT NULL,
  `uid_creat` int(10) unsigned NOT NULL DEFAULT '0',
  `dte_creat` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `uid_modif` int(10) unsigned NOT NULL DEFAULT '0',
  `dte_modif` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ;

-- --------------------------------------------------------

--
-- Structure de la table `p67_masses`
--

CREATE TABLE IF NOT EXISTS `p67_masses` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `uid_vol` mediumint(8) unsigned DEFAULT NULL,
  `uid_pilote` int(10) unsigned DEFAULT NULL,
  `uid_place` tinyint(3) unsigned DEFAULT NULL,
  `poids` char(3) DEFAULT NULL,
  `uid_creat` int(10) unsigned DEFAULT NULL,
  `dte_creat` datetime DEFAULT NULL,
  `uid_modif` int(10) unsigned DEFAULT NULL,
  `dte_modif` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `uid_vol` (`uid_vol`),
  KEY `uid_pilote` (`uid_pilote`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Structure de la table `p67_mouvement`
--

CREATE TABLE IF NOT EXISTS `p67_mouvement` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
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
  `vac` char(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Structure de la table `p67_navigation`
--

CREATE TABLE IF NOT EXISTS `p67_navigation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `titre` varchar(40) NOT NULL,
  `vitesse` int(10) unsigned NOT NULL,
  `dirvent` int(10) unsigned NOT NULL,
  `vitvent` int(10) unsigned NOT NULL,
  `uid_creat` int(10) unsigned NOT NULL,
  `dte_creat` datetime NOT NULL,
  `uid_modif` int(10) unsigned NOT NULL,
  `dte_modif` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Structure de la table `p67_navpoints`
--

CREATE TABLE IF NOT EXISTS `p67_navpoints` (
  `nom` varchar(20) NOT NULL,
  `description` varchar(200) NOT NULL,
  `lat` varchar(10) NOT NULL,
  `lon` varchar(10) NOT NULL,
  `icone` varchar(10) NOT NULL,
  PRIMARY KEY (`nom`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `p67_navroute`
--

CREATE TABLE IF NOT EXISTS `p67_navroute` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `idnav` int(10) unsigned NOT NULL,
  `ordre` int(10) unsigned NOT NULL,
  `nom` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idnav` (`idnav`,`nom`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Structure de la table `p67_participants`
--

CREATE TABLE IF NOT EXISTS `p67_participants` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `idmanip` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `idusr` int(10) unsigned NOT NULL DEFAULT '0',
  `participe` enum('Y','N') NOT NULL DEFAULT 'Y',
  `nb` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `uid_creat` int(10) unsigned NOT NULL DEFAULT '0',
  `dte_creat` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `idmanip` (`idmanip`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Structure de la table `p67_plage`
--

CREATE TABLE IF NOT EXISTS `p67_plage` (
  `id` varbinary(2) NOT NULL,
  `jour` char(1) NOT NULL,
  `plage` char(1) NOT NULL,
  `titre` varchar(20) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `libelle` varchar(50) NOT NULL,
  `deb` int(10) unsigned NOT NULL,
  `fin` int(10) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `p67_presence`
--

CREATE TABLE IF NOT EXISTS `p67_presence` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `dte` date NOT NULL,
  `type` varchar(10) NOT NULL,
  `dtedeb` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `dtefin` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `zone` char(3) NOT NULL DEFAULT '',
  `regime` varchar(3) NOT NULL,
  `tpspaye` int(11) NOT NULL DEFAULT '0',
  `tpsreel` int(11) NOT NULL DEFAULT '0',
  `age` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `handicap` varchar(3) NOT NULL DEFAULT 'non',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `type` (`type`),
  KEY `dte` (`dte`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Structure de la table `p67_prevision`
--

CREATE TABLE IF NOT EXISTS `p67_prevision` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `annee` smallint(5) unsigned NOT NULL DEFAULT '0',
  `mois` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `avion` smallint(5) unsigned NOT NULL DEFAULT '0',
  `heures` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `anne` (`annee`),
  KEY `mois` (`mois`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Structure de la table `p67_ressources`
--

CREATE TABLE IF NOT EXISTS `p67_ressources` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `nom` varchar(20) NOT NULL DEFAULT '',
  `immatriculation` varchar(6) NOT NULL DEFAULT '',
  `marque` varchar(20) NOT NULL DEFAULT '',
  `modele` varchar(20) NOT NULL DEFAULT '',
  `couleur` varchar(6) NOT NULL,
  `actif` enum('oui','non','off') NOT NULL DEFAULT 'oui',
  `tarif` varchar(6) NOT NULL DEFAULT '0',
  `tarif_reduit` varchar(6) NOT NULL DEFAULT '0',
  `tarif_double` varchar(6) NOT NULL DEFAULT '0',
  `tarif_inst` varchar(6) NOT NULL DEFAULT '0',
  `tarif_nue` varchar(6) NOT NULL DEFAULT '0',
  `typehora` varchar(3) NOT NULL DEFAULT 'min',
  `description` text NOT NULL,
  `places` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `puissance` smallint(5) unsigned NOT NULL DEFAULT '0',
  `charge` smallint(5) unsigned NOT NULL DEFAULT '0',
  `massemax` smallint(5) unsigned NOT NULL DEFAULT '0',
  `vitesse` smallint(5) unsigned NOT NULL DEFAULT '0',
  `autonomie` smallint(5) unsigned NOT NULL DEFAULT '0',
  `tolerance` tinytext NOT NULL,
  `centrage` text NOT NULL,
  `maintenance` varchar(200) NOT NULL DEFAULT '',
  `uid_maj` int(10) unsigned NOT NULL DEFAULT '0',
  `dte_maj` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Structure de la table `p67_roles`
--

CREATE TABLE IF NOT EXISTS `p67_roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `groupe` varchar(5) NOT NULL,
  `role` varchar(40) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `groupe` (`groupe`,`role`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Structure de la table `p67_tarifs`
--

CREATE TABLE IF NOT EXISTS `p67_tarifs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ress_id` int(10) unsigned NOT NULL,
  `code` varchar(2) NOT NULL,
  `nom` varchar(20) NOT NULL,
  `pilote` varchar(6) NOT NULL,
  `instructeur` varchar(6) NOT NULL,
  `reduction` int(11) NOT NULL,
  `defaut_pil` enum('oui','non') NOT NULL DEFAULT 'non',
  `defaut_ins` enum('oui','non') NOT NULL DEFAULT 'non',
  PRIMARY KEY (`id`),
  KEY `ress_id` (`ress_id`,`code`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Structure de la table `p67_type`
--

CREATE TABLE IF NOT EXISTS `p67_type` (
  `id` varchar(2) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `nom` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `libelle` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `p67_utilisateurs`
--

CREATE TABLE IF NOT EXISTS `p67_utilisateurs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nom` varchar(40) NOT NULL DEFAULT '',
  `prenom` varchar(40) NOT NULL DEFAULT '',
  `initiales` char(3) NOT NULL DEFAULT '',
  `password` varchar(50) NOT NULL DEFAULT '',
  `idcpt` int(10) unsigned NOT NULL,
  `sexe` enum('M','F','NA') NOT NULL DEFAULT 'NA',
  `pere` int(10) unsigned NOT NULL DEFAULT '0',
  `mere` int(10) unsigned NOT NULL DEFAULT '0',
  `mail` varchar(104) NOT NULL DEFAULT '',
  `notification` enum('oui','non') NOT NULL DEFAULT 'oui',
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
  `poids` tinyint(3) unsigned NOT NULL DEFAULT '75',
  `aff_rapide` char(1) NOT NULL DEFAULT 'n',
  `aff_mois` char(1) NOT NULL DEFAULT '',
  `aff_jour` date NOT NULL DEFAULT '0000-00-00',
  `aff_msg` tinyint(3) unsigned NOT NULL DEFAULT '0',
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
  `uid_maj` smallint(5) unsigned NOT NULL DEFAULT '0',
  `dte_maj` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `virtuel` (`virtuel`),
  KEY `actif` (`actif`),
  KEY `type_2` (`type`),
  KEY `virtuel_2` (`virtuel`),
  KEY `actif_2` (`actif`),
  KEY `type_3` (`type`),
  KEY `virtuel_3` (`virtuel`),
  KEY `actif_3` (`actif`),
  KEY `type_4` (`type`),
  KEY `virtuel_4` (`virtuel`),
  KEY `actif_4` (`actif`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `p67_vacances`
--

CREATE TABLE IF NOT EXISTS `p67_vacances` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `dtedeb` date NOT NULL,
  `dtefin` date NOT NULL,
  `comment` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;




-- Création des éléments de base
INSERT INTO `p67_utilisateurs` (`id`, `nom`, `prenom`, `initiales`, `password`, `idcpt`, `sexe`, `pere`, `mere`, `mail`, `notification`, `tel_fixe`, `tel_portable`, `tel_bureau`, `adresse1`, `adresse2`, `ville`, `codepostal`, `zone`, `profession`, `employeur`, `commentaire`, `avatar`, `droits`, `actif`, `virtuel`, `type`, `decouvert`, `tarif`, `dte_naissance`, `dte_licence`, `dte_medicale`, `dte_inscription`, `dte_login`, `poids`, `aff_rapide`, `aff_mois`, `aff_jour`, `aff_msg`, `num_caf`, `regime`, `type_repas`, `maladies`, `handicap`, `allergie_asthme`, `allergie_medicament`, `allergie_alimentaire`, `allergie_commentaire`, `remarque_sante`, `nom_medecin`, `tel_medecin`, `adr_medecin`, `aut_prelevement`, `uid_maj`, `dte_maj`) VALUES(1, 'admin', 'admin', 'adm', '21232f297a57a5a743894a0e4a801fc3', 1, 'M', 0, 0, '', 'oui', '', '', '', '', '', '', '', '', '', '', '', '', 'ADM', 'oui', 'non', 'membre', 0, 0, '0000-00-00', '0000-00-00', '0000-00-00', NOW(), NOW(), 0, 'y', '1', '2016-04-01', 61, '', 'GEN', 'standard', '', 'non', 'N', 'N', 'N', '', '', '', '', '', 'N', 1, NOW());
INSERT INTO `p67_utilisateurs` (`id`, `nom`, `prenom`, `initiales`, `password`, `idcpt`, `sexe`, `pere`, `mere`, `mail`, `notification`, `tel_fixe`, `tel_portable`, `tel_bureau`, `adresse1`, `adresse2`, `ville`, `codepostal`, `zone`, `profession`, `employeur`, `commentaire`, `avatar`, `droits`, `actif`, `virtuel`, `type`, `decouvert`, `tarif`, `dte_naissance`, `dte_licence`, `dte_medicale`, `dte_inscription`, `dte_login`, `poids`, `aff_rapide`, `aff_mois`, `aff_jour`, `aff_msg`, `num_caf`, `regime`, `type_repas`, `maladies`, `handicap`, `allergie_asthme`, `allergie_medicament`, `allergie_alimentaire`, `allergie_commentaire`, `remarque_sante`, `nom_medecin`, `tel_medecin`, `adr_medecin`, `aut_prelevement`, `uid_maj`, `dte_maj`) VALUES(2, 'banque', '', '', '', 2, 'NA', 0, 0, '', 'non', '', '', '', '', '', '', '', '', '', '', '', '', '', 'oui', 'oui', 'membre', 0, 0, '0000-00-00', '0000-00-00', '0000-00-00', NOW(), NOW(), 0, 'n', '', '0000-00-00', 0, '', 'general', 'standard', '', 'non', 'N', 'N', 'N', '', '', '', '', '', 'N', 1, NOW());
INSERT INTO `p67_utilisateurs` (`id`, `nom`, `prenom`, `initiales`, `password`, `idcpt`, `sexe`, `pere`, `mere`, `mail`, `notification`, `tel_fixe`, `tel_portable`, `tel_bureau`, `adresse1`, `adresse2`, `ville`, `codepostal`, `zone`, `profession`, `employeur`, `commentaire`, `avatar`, `droits`, `actif`, `virtuel`, `type`, `decouvert`, `tarif`, `dte_naissance`, `dte_licence`, `dte_medicale`, `dte_inscription`, `dte_login`, `poids`, `aff_rapide`, `aff_mois`, `aff_jour`, `aff_msg`, `num_caf`, `regime`, `type_repas`, `maladies`, `handicap`, `allergie_asthme`, `allergie_medicament`, `allergie_alimentaire`, `allergie_commentaire`, `remarque_sante`, `nom_medecin`, `tel_medecin`, `adr_medecin`, `aut_prelevement`, `uid_maj`, `dte_maj`) VALUES(3, 'club', '', '', '', 3, 'NA', 0, 0, '', 'non', '', '', '', '', '', '', '', '', '', '', '', '', '', 'oui', 'oui', 'membre', 0, 0, '0000-00-00', '0000-00-00', '0000-00-00', NOW(), NOW(), 0, 'n', '', '0000-00-00', 0, '', 'general', 'standard', '', 'non', 'N', 'N', 'N', '', '', '', '', '', 'N', 1, NOW());

INSERT INTO `p67_groupe` (`groupe`, `description`) VALUES ('ADM', 'Administrateurs'), ('ALL', 'Tout le monde'); 
INSERT INTO `p67_droits` (`id`, `groupe`, `uid`, `uid_creat`, `dte_creat`) VALUES (NULL, 'ADM', '1', '1', NOW()); 
INSERT INTO `p67_config` (`param`, `value`) VALUES ('dbversion', '462'); 

INSERT INTO `p67_ressources` (`nom`, `immatriculation`, `marque`, `modele`, `couleur`, `actif`, `tarif`, `tarif_reduit`, `tarif_double`, `tarif_inst`, `tarif_nue`, `typehora`, `description`, `places`, `puissance`, `charge`, `massemax`, `vitesse`, `autonomie`, `tolerance`, `centrage`, `maintenance`, `uid_maj`, `dte_maj`) VALUES
('Default', 'F-XXXX', '', '', '', 'oui', '0', '0', '0', '0', '0', 'min', '', 0, 0, 0, 0, 0, 0, '', '', '', 0, '0000-00-00 00:00:00');
