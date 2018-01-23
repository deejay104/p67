<?php
// ---------------------------------------------------------------------------------------------
//   Mise à jour de la structure de la base de données
// ---------------------------------------------------------------------------------------------
/*
    SoceIt v2.10 ($Revision: 456 $)
    Copyright (C) 2016 Matthieu Isorez

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
?>

<?php
	// Charset
	header('Content-type: text/html; charset=ISO-8859-1');

	error_reporting(E_ALL & ~E_NOTICE);
	set_time_limit (0);

// ---- Charge les prérequis
	$resume=true;
	require ("class/mysql.inc.php");

// ---- Charge les variables
	require ("version.txt");
	if (file_exists("config/variables.inc.php"))
	{
		require ("config/config.inc.php");
	}
	else
	{
		echo "Le fichier de configuration doit être rempli";
		exit;
	}
	if (file_exists("config/variables.inc.php"))
	{
		
		require ("config/variables.inc.php");
	}

	require ("modules/admin/variables.tmpl.php");
	require ("modules/fonctions.inc.php");

	echo "Version des programmes : $myrev<br />";
	echo "Nom de la base de données : $db <br /><br />";

// ---- Vérification des variables
	echo "Vérification de la présence des variables <br />";

	$nb=0;
	$MyOptTab=array();
	foreach ($MyOptTmpl as $nom=>$d)
	{
		if (is_array($d))
		{
			foreach($d as $var=>$dd)
		  {
				if(!isset($MyOpt[$nom][$var]))
			  {
			  	$MyOptTab[$nom][$var]=$dd;
			  	$nb=$nb+1;
			  	echo "Ajout : \$MyOpt[\"".$nom."\"][\"".$var."\"]='".$dd."'<br>";
			  }
			  else
			  {
			  	$MyOptTab[$nom][$var]=$MyOpt[$nom][$var];
			  }
			}
		}
		else
		{
			if(!isset($MyOpt[$nom]))
		  {
		  	$MyOptTab[$nom]["valeur"]=$d;
		  	$nb=$nb+1;
		  	echo "Ajout : \$MyOpt[\"".$nom."\"]='".$d."'<br>";
		  }
		  else
		  {
		  	$MyOptTab[$nom]["valeur"]=$MyOpt[$nom];
		  }
		}
	}

	if ($nb>0)
	{
		echo $nb." variables ajoutées<br>";

		$ret=GenereVariables($MyOptTab);
		echo $ret."<br />";
	}
	echo "<br />";

// ---- Connexion à la base de données
	$mysql   = new mysql_class($mysqluser, $mysqlpassword, $hostname, $db, $port);

	$query="CREATE TABLE IF NOT EXISTS `".$MyOpt["tbl"]."_config` (`param` VARCHAR( 20 ) NOT NULL ,`value` VARCHAR( 20 ) NOT NULL) ENGINE = MYISAM ";
	$res = $mysql->Update($query);

	$query="SELECT value FROM ".$MyOpt["tbl"]."_config WHERE param='dbversion'";
	$res=$mysql->QueryRow($query);
	$ver=$res["value"];

	if ($ver=="")
	{
	  	$ver="000";
		$query="INSERT INTO ".$MyOpt["tbl"]."_config (param,value) VALUES ('dbversion','$ver')";
		$mysql->Insert($query);
	}

	echo "Version actuelle de la base de données : $ver <br />";

// ---- 363
	if ($ver<363)
	  {
	  	echo "Update v363";
		
		$query="ALTER TABLE `p67_utilisateurs` ADD `pere` SMALLINT UNSIGNED NOT NULL AFTER `password` ;";
		$res = $mysql->Update($query);
		$query="ALTER TABLE `p67_utilisateurs` ADD `mere` SMALLINT UNSIGNED NOT NULL AFTER `pere` ;";
		$res = $mysql->Update($query);
		$query="ALTER TABLE `p67_utilisateurs` CHANGE `type` `type` ENUM( 'pilote', 'eleve', 'instructeur', 'invite', 'membre', 'parent', 'enfant' ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'pilote' ";
		$res = $mysql->Update($query);

		$query="UPDATE p67_config SET value='363' WHERE param='dbversion'";
		$mysql->Update($query);
		echo " [done]<br />";
	  }

// ---- 370
	if ($ver<370)
	  {
	  	echo "Update v370";
		
		$query="CREATE TABLE `p67_factures` (`id` VARCHAR( 10 ) NOT NULL ,`uid` INT UNSIGNED NOT NULL ,`dte` DATE NOT NULL ,`paid` VARCHAR( 1 ) NOT NULL ,`comment` VARCHAR( 200 ) NOT NULL ,PRIMARY KEY ( `id` )) ENGINE = MYISAM ";
		$res = $mysql->Update($query);

		$query="ALTER TABLE `p67_utilisateurs` CHANGE `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ";
		$res = $mysql->Update($query);

		$query="ALTER TABLE `p67_calendrier` CHANGE `uid_pilote` `uid_pilote` INT UNSIGNED NOT NULL DEFAULT '0',CHANGE `uid_debite` `uid_debite` INT UNSIGNED NOT NULL DEFAULT '0',CHANGE `uid_instructeur` `uid_instructeur` INT UNSIGNED NOT NULL DEFAULT '0',CHANGE `uid_maj` `uid_maj` INT UNSIGNED NOT NULL DEFAULT '0' ";
		$res = $mysql->Update($query);

		$query="ALTER TABLE `p67_compte` CHANGE `uid` `uid` INT UNSIGNED NOT NULL DEFAULT '0',CHANGE `tiers` `tiers` INT UNSIGNED NOT NULL DEFAULT '0',CHANGE `uid_creat` `uid_creat` INT UNSIGNED NOT NULL DEFAULT '0' ";
		$res = $mysql->Update($query);

		$query="ALTER TABLE `p67_forums` CHANGE `uid_creat` `uid_creat` INT UNSIGNED NOT NULL DEFAULT '0',CHANGE `uid_maj` `uid_maj` INT UNSIGNED NOT NULL DEFAULT '0' ";
		$res = $mysql->Update($query);

		$query="ALTER TABLE `p67_forums_lus` CHANGE `forum_usr` `forum_usr` INT UNSIGNED NULL DEFAULT NULL ";
		$res = $mysql->Update($query);

		$query="ALTER TABLE `p67_historique` CHANGE `uid_maj` `uid_maj` INT UNSIGNED NOT NULL DEFAULT '0' ";
		$res = $mysql->Update($query);

		$query="ALTER TABLE `p67_lache` CHANGE `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,CHANGE `uid_pilote` `uid_pilote` INT UNSIGNED NULL DEFAULT NULL ,CHANGE `uid_creat` `uid_creat` INT UNSIGNED NOT NULL DEFAULT '0' ";
		$res = $mysql->Update($query);

		$query="ALTER TABLE `p67_maintenance` CHANGE `uid_creat` `uid_creat` INT UNSIGNED NOT NULL DEFAULT '0',CHANGE `uid_maj` `uid_maj` INT UNSIGNED NOT NULL DEFAULT '0' ";
		$res = $mysql->Update($query);

		$query="ALTER TABLE `p67_maintfiche` CHANGE `uid_valid` `uid_valid` INT UNSIGNED NOT NULL DEFAULT '0',CHANGE `uid_creat` `uid_creat` INT UNSIGNED NOT NULL DEFAULT '0',CHANGE `uid_maj` `uid_maj` INT UNSIGNED NOT NULL DEFAULT '0' ";
		$res = $mysql->Update($query);

		$query="ALTER TABLE `p67_manips` CHANGE `uid_creat` `uid_creat` INT UNSIGNED NOT NULL DEFAULT '0',CHANGE `uid_modif` `uid_modif` INT UNSIGNED NOT NULL DEFAULT '0' ";
		$res = $mysql->Update($query);

		$query="ALTER TABLE `p67_masses` CHANGE `uid_pilote` `uid_pilote` INT UNSIGNED NULL DEFAULT NULL ,CHANGE `uid_creat` `uid_creat` INT UNSIGNED NULL DEFAULT NULL ,CHANGE `uid_modif` `uid_modif` INT UNSIGNED NULL DEFAULT NULL ";
		$res = $mysql->Update($query);

		$query="ALTER TABLE `p67_participants` CHANGE `idusr` `idusr` INT UNSIGNED NOT NULL DEFAULT '0',CHANGE `uid_creat` `uid_creat` INT UNSIGNED NOT NULL DEFAULT '0' ";
		$res = $mysql->Update($query);

		$query="ALTER TABLE `p67_ressources` CHANGE `uid_maj` `uid_maj` INT UNSIGNED NOT NULL DEFAULT '0' ";
		$res = $mysql->Update($query);

		$query="ALTER TABLE `p67_compte` ADD `facture` VARCHAR( 10 ) NOT NULL AFTER `pointe` ;";
		$res = $mysql->Update($query);

		$query="UPDATE p67_config SET value='370' WHERE param='dbversion'";
		$mysql->Update($query);

		echo " [done]<br />";
	  }

// ---- 374
	if ($ver<374)
	  {
	  	echo "Update v374";

		$query="ALTER TABLE `p67_factures` CHANGE `id` `id` VARCHAR( 10 ) NOT NULL ;";
		$mysql->Update($query);

		$query="ALTER TABLE `p67_factures` ADD `total` DECIMAL( 10, 2 ) NOT NULL DEFAULT '0.00' AFTER `dte` ;";
		$mysql->Update($query);

		$query="UPDATE p67_config SET value='374' WHERE param='dbversion'";
		$mysql->Update($query);

		echo " [done]<br />";
	  }

// ---- 376
	if ($ver<376)
	  {
	  	echo "Update v376";

		$sql[] = "ALTER TABLE `p67_mouvement` ADD `montant` DECIMAL( 10, 2 ) NOT NULL DEFAULT '0.00';";
		$sql[] = "ALTER TABLE `p67_utilisateurs` CHANGE `pere` `pere` INT UNSIGNED NOT NULL DEFAULT '0' ; ";
		$sql[] = "ALTER TABLE `p67_utilisateurs` CHANGE `mere` `mere` INT UNSIGNED NOT NULL DEFAULT '0' ; ";
		$sql[] = "ALTER TABLE `p67_utilisateurs` ADD INDEX ( `type` ) ; ";
		$sql[] = "ALTER TABLE `p67_utilisateurs` DROP PRIMARY KEY , ADD PRIMARY KEY ( `id` ) ; ";
		$sql[] = "ALTER TABLE `p67_utilisateurs` ADD INDEX ( `virtuel` ) ; ";
		$sql[] = "ALTER TABLE `p67_utilisateurs` ADD INDEX ( `actif` ) ; ";
		$sql[] = "ALTER TABLE `p67_utilisateurs` ADD `profession` VARCHAR( 50 ) NOT NULL AFTER `codepostal` ; ";
		$sql[] = "ALTER TABLE `p67_utilisateurs` ADD `employeur` VARCHAR( 50 ) NOT NULL AFTER `profession` ; ";
		$sql[] = "ALTER TABLE `p67_utilisateurs` ADD `num_caf` VARCHAR( 20 ) NOT NULL AFTER `aff_msg` ; ";
		$sql[] = "ALTER TABLE `p67_utilisateurs` ADD `sexe` ENUM( 'M', 'F', 'NA' ) NOT NULL DEFAULT 'NA' AFTER `password` ; ";
		$sql[] = "ALTER TABLE `p67_utilisateurs` ADD `nom_medecin` VARCHAR( 50 ) NOT NULL AFTER `num_caf` ; ";
		$sql[] = "ALTER TABLE `p67_utilisateurs` ADD `tel_medecin` VARCHAR( 20 ) NOT NULL AFTER `nom_medecin` ; ";
		$sql[] = "ALTER TABLE `p67_utilisateurs` ADD `adr_medecin` VARCHAR( 100 ) NOT NULL AFTER `tel_medecin` ; ";
		$sql[] = "ALTER TABLE `p67_utilisateurs` ADD `regime` ENUM('Standard','Halal','autre') NOT NULL DEFAULT 'Standard' AFTER `num_caf` ; ";
		$sql[] = "ALTER TABLE `p67_utilisateurs` ADD `maladies` TINYTEXT NOT NULL AFTER `regime` ; ";
		$sql[] = "ALTER TABLE `p67_utilisateurs` ADD `allergie_asthme` ENUM( 'Y', 'N' ) NOT NULL DEFAULT 'N' AFTER `maladies` ; ";
		$sql[] = "ALTER TABLE `p67_utilisateurs` ADD `allergie_medicament` ENUM( 'Y', 'N' ) NOT NULL DEFAULT 'N' AFTER `allergie_asthme` ; ";
		$sql[] = "ALTER TABLE `p67_utilisateurs` ADD `allergie_alimentaire` ENUM( 'Y', 'N' ) NOT NULL DEFAULT 'N' AFTER `allergie_medicament` ; ";
		$sql[] = "ALTER TABLE `p67_utilisateurs` ADD `allergie_commentaire` TEXT NOT NULL AFTER `allergie_alimentaire` ; ";
		$sql[] = "ALTER TABLE `p67_utilisateurs` ADD `remarque_sante` TEXT NOT NULL AFTER `allergie_commentaire` ; ";
		$sql[] = "CREATE TABLE `p67_abonnement` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT, `abonum` VARCHAR( 8 ) NOT NULL ,`uid` INT UNSIGNED NOT NULL ,`dtedeb` DATE NOT NULL ,`dtefin` DATE NOT NULL ,PRIMARY KEY ( `id` )) ENGINE = MYISAM ";
		$sql[] = "ALTER TABLE `p67_abonnement` ADD UNIQUE (`abonum` )";

		$sql[] = "ALTER TABLE `p67_abonnement` ADD INDEX ( `uid` ) ";

		$sql[] = "ALTER TABLE `p67_abonnement` ADD `uid_maj` INT UNSIGNED NOT NULL ,ADD `dte_maj` DATETIME NOT NULL ; ";

		$sql[] = "CREATE TABLE `p67_abo_ligne` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,`abonum` VARCHAR( 8 ) NOT NULL ,`uid` INT UNSIGNED NOT NULL ,`mouvid` TINYINT UNSIGNED NOT NULL ,`montant` DECIMAL( 10, 2 ) NOT NULL ,PRIMARY KEY ( `id` ) ,INDEX ( `abonum` , `uid` )) ENGINE = MYISAM";

		$sql[] = "ALTER TABLE `p67_factures` ADD INDEX ( `uid` ) ";


		UpdateDB($sql,"376");
	  }

// ---- 378
	if ($ver<378)
	  {
	  	echo "Update v378";

		$sql[] = "CREATE TABLE `p67_presence` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,`uid` INT UNSIGNED NOT NULL ,`dte` DATE NOT NULL ,`type` VARCHAR( 10 ) NOT NULL ,INDEX ( `uid` )) ENGINE = MYISAM ";
		
		$sql[] = "ALTER TABLE `p67_mouvement` ADD `j0` CHAR( 1 ) NOT NULL ;";
		$sql[] = "ALTER TABLE `p67_mouvement` ADD `j1` CHAR( 1 ) NOT NULL DEFAULT 'N';";
		$sql[] = "ALTER TABLE `p67_mouvement` ADD `j2` CHAR( 1 ) NOT NULL DEFAULT 'N';";
		$sql[] = "ALTER TABLE `p67_mouvement` ADD `j3` CHAR( 1 ) NOT NULL ;";
		$sql[] = "ALTER TABLE `p67_mouvement` ADD `j4` CHAR( 1 ) NOT NULL DEFAULT 'N';";
		$sql[] = "ALTER TABLE `p67_mouvement` ADD `j5` CHAR( 1 ) NOT NULL DEFAULT 'N';";
		$sql[] = "ALTER TABLE `p67_mouvement` ADD `j6` CHAR( 1 ) NOT NULL ;";
		$sql[] = "ALTER TABLE `p67_mouvement` ADD `vac` CHAR( 1 ) NOT NULL ;";

		UpdateDB($sql,"378");
	  }

// ---- 380
	if ($ver<380)
	  {
		$sql=array();
		$sql[] = "CREATE TABLE IF NOT EXISTS `p67_presence` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `uid` int(10) unsigned NOT NULL default '0',
  `dte` varchar(6) NOT NULL default '0',
  `dtedeb` datetime NOT NULL default '0000-00-00 00:00:00',
  `dtefin` datetime NOT NULL default '0000-00-00 00:00:00',
  `type` char(2) NOT NULL default '',
  `zone` char(3) NOT NULL default '',
  `tpspaye` int(11) NOT NULL default '0',
  `tpsreel` int(11) NOT NULL default '0',
  `age` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `uid` (`uid`),
  KEY `month` (`dte`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;"; 


		$sql[] = "ALTER TABLE `p67_presence` ADD `uid` int(10) unsigned NOT NULL default '0';";
		$sql[] = "ALTER TABLE `p67_presence` ADD `dte` varchar(6) NOT NULL default '0';";
		$sql[] = "ALTER TABLE `p67_presence` ADD `dtedeb` datetime NOT NULL default '0000-00-00 00:00:00';";
		$sql[] = "ALTER TABLE `p67_presence` ADD `dtefin` datetime NOT NULL default '0000-00-00 00:00:00';";
		$sql[] = "ALTER TABLE `p67_presence` ADD `type` char(2) NOT NULL default '';";
		$sql[] = "ALTER TABLE `p67_presence` ADD `zone` char(3) NOT NULL default '';";
		$sql[] = "ALTER TABLE `p67_presence` ADD `tpspaye` int(11) NOT NULL default '0';";
		$sql[] = "ALTER TABLE `p67_presence` ADD `tpsreel` int(11) NOT NULL default '0';";
		$sql[] = "ALTER TABLE `p67_presence` ADD `age` tinyint(3) unsigned NOT NULL default '0';";


		$sql[] = "CREATE TABLE `p67_vacances` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, `dtedeb` DATE NOT NULL, `dtefin` DATE NOT NULL, `comment` TEXT NOT NULL) ENGINE = MyISAM";
		$sql[] = 'ALTER TABLE `p67_utilisateurs` ADD `zone` VARCHAR(3) NOT NULL AFTER `codepostal`;';

		UpdateDB($sql,"380");
	  }


// ---- 381
	if ($ver<381)
	  {
		$sql=array();
		$sql[] = 'ALTER TABLE `p67_utilisateurs` CHANGE `type` `type` ENUM(\'pilote\',\'eleve\',\'instructeur\',\'invite\',\'membre\',\'parent\',\'enfant\',\'employe\') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT \'pilote\'';
		$sql[] = 'ALTER TABLE `p67_abonnement` ADD `jour_num` CHAR(1) NOT NULL DEFAULT \'0\' AFTER `dtefin`;'; 
		$sql[] = 'ALTER TABLE `p67_abonnement` ADD `jour_sem` CHAR(1) NOT NULL DEFAULT \'-\' AFTER `jour_num`;'; 

		UpdateDB($sql,"381");
	  }


// ---- 382
	if ($ver<382)
	  {
		$sql=array();
		$sql[] = 'ALTER TABLE `p67_utilisateurs` ADD `dte_inscription` DATE NOT NULL AFTER `dte_medicale`;'; 
		$sql[] = "ALTER TABLE `p67_utilisateurs` CHANGE `regime` `regime` VARCHAR(3) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'GEN'"; 
		$sql[] = 'ALTER TABLE `p67_utilisateurs` ADD `type_repas` VARCHAR(20) NOT NULL DEFAULT \'standard\' AFTER `regime`;'; 
		$sql[] = "UPDATE p67_utilisateurs SET regime='GEN';";
		$sql[] = "ALTER TABLE `p67_presence` ADD `type` VARCHAR(3) NOT NULL DEFAULT 'GEN' AFTER `zone`;"; 
		$sql[] = 'ALTER TABLE `p67_mouvement` CHANGE `vac` `j7` CHAR(1) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL';
		$sql[] = 'ALTER TABLE `p67_presence` ADD `regime` VARCHAR( 3 ) NOT NULL AFTER `zone` '; 
		UpdateDB($sql,"382");
	  }

// ---- 383
	if ($ver<383)
	  {
		$sql=array();
		$sql[] = 'ALTER TABLE `p67_mouvement` ADD `compte` VARCHAR(10) NOT NULL AFTER `description`;'; 
		$sql[] = 'ALTER TABLE `p67_utilisateurs` ADD `handicap` ENUM(\'oui\',\'non\') NOT NULL DEFAULT \'non\' AFTER `maladies`;';

		UpdateDB($sql,"383");
	  }


// ---- 385
	if ($ver<385)
	  {
	  	$sql=array();
		$sql[] = 'CREATE TABLE `'.$MyOpt["tbl"].'_document` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, `name` VARCHAR(100) NOT NULL, `filename` VARCHAR(20) NOT NULL, `uid` INT UNSIGNED NOT NULL, `type` VARCHAR(10) NOT NULL, `dossier` TINYTEXT NOT NULL, `droit` VARCHAR(3) NOT NULL, `uid_creat` INT UNSIGNED NOT NULL, `dte_creat` DATETIME NOT NULL, INDEX (`uid`, `type`)) ENGINE = MyISAM'; 
		$sql[] = 'ALTER TABLE `'.$MyOpt["tbl"].'_document` ADD `actif` ENUM(\'oui\',\'non\') NOT NULL DEFAULT \'oui\' AFTER `droit`;'; 

		UpdateDB($sql,"385");
	  }

// ---- 388
	if ($ver<388)
	  {
	  	$sql=array();
		$sql[] = "ALTER TABLE `".$MyOpt["tbl"]."_factures` ADD `email` CHAR(1) NOT NULL DEFAULT 'N' AFTER `paid`;"; 

		UpdateDB($sql,"388");
	  }

// ---- 388
	if ($ver<389)
	  {
	  	$sql=array();
		$sql[] = "ALTER TABLE `".$MyOpt["tbl"]."_factures` ADD `email` CHAR(1) NOT NULL DEFAULT 'N' AFTER `paid`;"; 
		$sql[] = 'ALTER TABLE `'.$MyOpt["tbl"].'_abonnement` ADD `actif` ENUM(\'oui\',\'non\') NOT NULL DEFAULT \'oui\' AFTER `jour_sem`;'; 
		$sql[] = 'ALTER TABLE `'.$MyOpt["tbl"].'_compte` CHANGE `facture` `facture` VARCHAR(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL'; 
		$sql[] = 'ALTER TABLE `'.$MyOpt["tbl"].'_utilisateurs` CHANGE `regime` `regime` VARCHAR(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT \'general\''; 
		$sql[] = 'ALTER TABLE `'.$MyOpt["tbl"].'_utilisateurs` DROP `enfant`';
		$sql[] = 'ALTER TABLE `'.$MyOpt["tbl"].'_ressources` CHANGE `actif` `actif` ENUM(\'oui\',\'non\',\'off\') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT \'oui\''; 
		$sql[] = 'ALTER TABLE `'.$MyOpt["tbl"].'_mouvement` ADD `ordre` VARCHAR(4) NOT NULL AFTER `id`;'; 
		UpdateDB($sql,"389");
	  }

// ---- 396
	if ($ver<396)
	  {
		require_once ("class/document.inc.php");
		require_once ("modules/fonctions.inc.php");

		if (is_dir("membres"))
		  {
			$rep=dir("membres");
			$rep->read();
			$rep->read();
	
			while($f = $rep->read())
			{
				preg_match("/^([0-9]*)\.[a-z]*$/",$f,$m);
				echo "Migration Avatar: ".$m[1]."<br />";
	
			  	$doc = new document_class(0,$mysql,"avatar");
			  	$doc->Import($m[1],"membres/$f");
				$doc->Resize(200,240);
	
			}
			rmdir("membres");
		  }
		$sql=array();

		UpdateDB($sql,"396");		
	  }


// ---- 405
	if ($ver<405)
	  {
		$sql=array();
		$sql[] = 'ALTER TABLE `'.$MyOpt["tbl"].'_presence` ADD `handicap` VARCHAR(3) NOT NULL DEFAULT \'non\';'; 
		$sql[] = 'ALTER TABLE `'.$MyOpt["tbl"].'_manips` ADD `type` VARCHAR(100) NOT NULL AFTER `comment`;'; 
		UpdateDB($sql,"405");
	  }

// ---- 406
	if ($ver<406)
	  {
		require_once ("class/document.inc.php");
		require_once ("modules/fonctions.inc.php");

		if (is_dir("ficjoint"))
		  {
			$rep=dir("ficjoint");
			$rep->read();
			$rep->read();
	
			while($f = $rep->read())
			{
				preg_match("/^f([0-9]*)$/",$f,$m);
				echo "Migration fichier forum id: ".$m[1]."<br />";
				$frep=dir("ficjoint/$f");
				$frep->read();
				$frep->read();

				$query="SELECT fid FROM ".$MyOpt["tbl"]."_forums WHERE id=".$m[1];
				$res=$mysql->QueryRow($query);
				$query="SELECT droit_r FROM ".$MyOpt["tbl"]."_forums WHERE id=".$res["fid"];
				$res=$mysql->QueryRow($query);
				if ($res["droit_r"]=="")
				  {
				  	$droit="ALL";
				  }
				else
				  {
				  	$droit=$res["droit_r"];
				  }
		
				while($file = $frep->read())
				{
					echo "Import fichier : ".$file."<br />";
	
				  	$doc = new document_class(0,$mysql,"forum");
				  	$doc->Import($m[1],"ficjoint/$f/$file",$file,$droit);

				}	
				rmdir("ficjoint/$f");
			}
			rmdir("ficjoint");
		  }

		$sql=array();

 		$sql[]="ALTER TABLE `".$MyOpt["tbl"]."_forums` ADD INDEX ( `fil` );";
 		$sql[]="ALTER TABLE `".$MyOpt["tbl"]."_forums` ADD INDEX ( `fid` );";
  		$sql[]="ALTER TABLE `".$MyOpt["tbl"]."_forums` ADD INDEX ( `uid_creat` );";
 		$sql[]="ALTER TABLE `".$MyOpt["tbl"]."_forums` ADD INDEX ( `uid_maj` );";
 		$sql[]="ALTER TABLE `".$MyOpt["tbl"]."_forums_lus` ADD INDEX ( `forum_usr` );";

		UpdateDB($sql,"406");
	  }

// ---- 411
	if ($ver<411)
	  {
	  	$sql=array();
		$sql[]="UPDATE `".$MyOpt["tbl"]."_document` SET droit='ALL' WHERE type='avatar';";
		$sql[]="UPDATE `".$MyOpt["tbl"]."_document` SET droit='ALL' WHERE type='forum' AND droit='';";
		UpdateDB($sql,"411");
	  }

// ---- 411
	if ($ver<417)
	  {
	  	$sql=array();
		$sql[]="ALTER TABLE `".$MyOpt["tbl"]."_manips` ADD `dte_limite` DATE NOT NULL AFTER `dte_manip` ;";
		$sql[]="ALTER TABLE `".$MyOpt["tbl"]."_manips` ADD `cout` DECIMAL( 10, 2 ) NOT NULL DEFAULT '0.00' AFTER `type` ;";
		$sql[]="ALTER TABLE `".$MyOpt["tbl"]."_manips` ADD `facture` ENUM( 'oui', 'non' ) NOT NULL DEFAULT 'non' AFTER `cout` ;";
		$sql[]="ALTER TABLE `".$MyOpt["tbl"]."_utilisateurs` ADD `idcpt` INT UNSIGNED NOT NULL AFTER `password` ;";
		$sql[]="UPDATE ".$MyOpt["tbl"]."_utilisateurs SET idcpt=id";
		$sql[]="ALTER TABLE `".$MyOpt["tbl"]."_abonnement` CHANGE `jour_num` `jour_num` TINYINT UNSIGNED NOT NULL DEFAULT '0' ";
		$sql[]="ALTER TABLE `".$MyOpt["tbl"]."_utilisateurs` ADD `aut_prelevement` ENUM( 'Y', 'N' ) NOT NULL DEFAULT 'N' AFTER `adr_medecin` ;";
		UpdateDB($sql,"417");
	  }
	if ($ver<418)
	  {
	  	$sql=array();
		$sql[]="CREATE TABLE IF NOT EXISTS `".$MyOpt["tbl"]."_type` (`id` varchar( 2 ) COLLATE utf8_unicode_ci NOT NULL ,`nom` varchar( 50 ) COLLATE utf8_unicode_ci NOT NULL ,`libelle` varchar( 50 ) COLLATE utf8_unicode_ci NOT NULL ,PRIMARY KEY ( `id` )) ENGINE = MYISAM DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci;";
		UpdateDB($sql,"418");
	  }
	if ($ver<419)
	  {
	  	$sql=array();

		$query="SELECT parent.id,parent.prenom, parent.nom, enfant.id AS idcpt, enfant.prenom, enfant.nom FROM `".$MyOpt["tbl"]."_utilisateurs` AS parent LEFT JOIN ".$MyOpt["tbl"]."_utilisateurs AS enfant ON parent.id=enfant.pere OR parent.id=enfant.mere WHERE parent.type<>'enfant' AND enfant.id>0";
		$mysql->Query($query);

		for($i=0; $i<$mysql->rows; $i++)
		  { 
			$mysql->GetRow($i);

			$sql[]="UPDATE ".$MyOpt["tbl"]."_utilisateurs SET idcpt='".$mysql->data["idcpt"]."' WHERE id='".$mysql->data["id"]."'";
		  }


		UpdateDB($sql,"419");
	  }

// ---- 420
	if ($ver<420)
	  {
	  	$sql=array();

		$sql[] = 'CREATE TABLE '.$MyOpt["tbl"].'_groupe (`groupe` VARCHAR(5) NOT NULL, `description` VARCHAR(200) NOT NULL, PRIMARY KEY (`groupe`)) ENGINE = MyISAM'; 
		$sql[] = 'CREATE TABLE '.$MyOpt["tbl"].'_droits (`groupe` VARCHAR(5) NOT NULL, `uid` INT UNSIGNED NOT NULL, `uid_creat` INT UNSIGNED NOT NULL, `dte_creat` DATETIME NOT NULL, INDEX (`groupe`, `uid`)) ENGINE = MyISAM'; 
		UpdateDB($sql,"420");

		$query="SELECT id,droits FROM `".$MyOpt["tbl"]."_utilisateurs` WHERE actif='oui'";
		$mysql->Query($query);
		$tusr=array();
		for($i=0; $i<$mysql->rows; $i++)
		  { 
			$mysql->GetRow($i);
			if ($mysql->data["droits"]!="")
			  {
				$tusr[$mysql->data["id"]]=$mysql->data["droits"];
			  }
		  }

		foreach($tusr as $id=>$grp)
		  {
			$trole=preg_split("/,/",$grp);
			foreach ($trole as $grp)
		  	  {
				$query ="INSERT INTO ".$MyOpt["tbl"]."_droits (`groupe` ,`uid`) ";
				$query.="VALUES ('$grp' , '$id')";
				$mysql->Insert($query);
		  	  }

		  }
	  }

// ---- 421
	if ($ver<421)
	  {
	  	$sql=array();
		$sql[] = 'ALTER TABLE `'.$MyOpt["tbl"].'_factures` ADD `dteid` VARCHAR( 6 ) NOT NULL AFTER `uid` ;';
		$sql[] = 'ALTER TABLE `'.$MyOpt["tbl"].'_factures` ADD INDEX ( `dteid` ) ';
		$sql[] = 'ALTER TABLE `'.$MyOpt["tbl"].'_compte` ADD `dte` VARCHAR( 6 ) NOT NULL AFTER `date_valeur` , ADD `compte` VARCHAR( 10 ) NOT NULL AFTER `dte` ;';
		$sql[] = 'ALTER TABLE `'.$MyOpt["tbl"].'_compte` ADD INDEX ( `dte` ) ';
		$sql[] = 'ALTER TABLE `'.$MyOpt["tbl"].'_compte` ADD INDEX ( `compte` )'; 
		UpdateDB($sql,"421");
		
		$query="SELECT * FROM `".$MyOpt["tbl"]."_factures` WHERE dteid=''";
		$mysql->Query($query);
		$fac=array();
		for($i=0; $i<$mysql->rows; $i++)
		  { 
			$mysql->GetRow($i);
			$fac[$mysql->data["id"]]=$mysql->data["dte"];
		  }

		foreach($fac as $id=>$dte)
		  {
		  	$query="UPDATE ".$MyOpt["tbl"]."_factures SET dteid='".date("Ym",strtotime($dte))."' WHERE id='$id'";
		  	$mysql->Update($query);
		  }		

		$query="SELECT * FROM `".$MyOpt["tbl"]."_mouvement` WHERE compte<>''";
		$mysql->Query($query);
		$mvt=array();
		for($i=0; $i<$mysql->rows; $i++)
		  { 
			$mysql->GetRow($i);
			$mvt[$i]["desc"]=$mysql->data["description"];
			$mvt[$i]["cpt"]=$mysql->data["compte"];
		  }

		foreach($mvt as $id=>$data)
		  {
		  	$query="UPDATE ".$MyOpt["tbl"]."_compte SET compte='".$data["cpt"]."' WHERE mouvement='".addslashes($data["desc"])."'";
		  	$mysql->Update($query);
		  }
		  
		$query="UPDATE ".$MyOpt["tbl"]."_compte AS cpt JOIN (SELECT id,DATE_FORMAT(date_valeur,'%Y%m') AS srcdte FROM p67_compte) AS srctbl ON cpt.id=srctbl.id SET cpt.dte=srctbl.srcdte";
		$mysql->Update($query);

	  }

// ---- 427
	if ($ver<427)
	  {
	  	$sql=array();
		$sql[] = 'CREATE TABLE `'.$MyOpt["tbl"].'_bapteme` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, `nom` VARCHAR(50) NOT NULL, `telephone` VARCHAR(14) NOT NULL, `dte` DATETIME NOT NULL, `id_pilote` INT UNSIGNED NOT NULL, `id_avion` INT UNSIGNED NOT NULL, `description` TEXT NOT NULL, `uid_creat` INT UNSIGNED NOT NULL, `dte_creat` DATETIME NOT NULL, `uid_maj` INT UNSIGNED NOT NULL, `dte_maj` DATETIME NOT NULL, INDEX (`id_pilote`, `id_avion`)) ENGINE = MyISAM'; 
		$sql[] = 'ALTER TABLE `'.$MyOpt["tbl"].'_bapteme` ADD `actif` ENUM(\'oui\',\'non\') NOT NULL DEFAULT \'oui\' AFTER `dte`;'; 
		$sql[] = 'ALTER TABLE `'.$MyOpt["tbl"].'_bapteme` ADD `status` TINYINT UNSIGNED NOT NULL AFTER `actif`;'; 
		$sql[] = 'ALTER TABLE `'.$MyOpt["tbl"].'_bapteme` ADD `mail` VARCHAR(100) NOT NULL AFTER `telephone`;'; 
		$sql[] = 'ALTER TABLE `'.$MyOpt["tbl"].'_bapteme` ADD `nb` TINYINT UNSIGNED NOT NULL AFTER `mail` ;';
		$sql[] = 'ALTER TABLE `'.$MyOpt["tbl"].'_bapteme` ADD `id_resa` INT UNSIGNED NOT NULL AFTER `id_avion` ;';
		UpdateDB($sql,"427");

	  }

// ---- 428
	if ($ver<428)
	  {
	  	$sql=array();
		$sql[] = "ALTER TABLE ".$MyOpt["tbl"]."_bapteme ADD `type` ENUM( 'btm', 'vi' ) NOT NULL DEFAULT 'btm' AFTER `status` ;";
		$sql[] = "ALTER TABLE ".$MyOpt["tbl"]."_bapteme ADD `paye` ENUM( 'oui', 'non' ) NOT NULL DEFAULT 'non' AFTER `type` ;";
		UpdateDB($sql,"428");
	  }


	if ($ver<429)
	  {
		$sql[] = "ALTER TABLE `".$MyOpt["tbl"]."_calendrier` ADD `accept` ENUM( 'oui', 'non' ) NOT NULL DEFAULT 'non' AFTER `nbpersonne` ;";
		UpdateDB($sql,"429");
	  }

// ---- 430
	if ($ver<430)
	  {
	  	$sql=array();
		$sql[] = "ALTER TABLE `".$MyOpt["tbl"]."_prevision` CHANGE `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT";
		$sql[] = 'CREATE TABLE `'.$MyOpt["tbl"].'_export` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, `nom` VARCHAR(50) NOT NULL, `description` TEXT NOT NULL, `requete` TEXT NOT NULL, `param` VARCHAR(50) NOT NULL, `droit_r` CHAR(3) NOT NULL) ENGINE = MyISAM'; 
		UpdateDB($sql,"430");
	  }

// ---- 431
	if ($ver<431)
	  {
	  	$sql=array();
		$sql[] = "INSERT INTO `p67_export` (`id`, `nom`, `description`, `requete`, `param`, `droit_r`) VALUES 
			(1, 'Presence', 'Export de la liste des présences', 'SELECT presence.dte, usr.prenom, usr.nom, CONCAT(UPPER(usr.nom),'' '',usr.prenom) AS membre, usr.num_caf, presence.type AS typeid, type.nom AS type, type.libelle, presence.zone, presence.regime, presence.age, presence.handicap, SUM(tpspaye) AS tpspaye, SUM(tpsreel) AS tpsreel \r\nFROM $tbl_presence AS presence \r\nLEFT JOIN $tbl_utilisateurs AS usr ON presence.uid=usr.id \r\nLEFT JOIN $tbl_type AS type ON presence.type=type.id \r\nWHERE presence.dte like ''$dte%'' GROUP BY presence.dte,presence.uid,presence.type\r\n', 'dte', 'ALL'), 
			(2, 'Facturation', 'Export pour la facturation', 'SELECT cpt.dte,usr.prenom,usr.nom,CONCAT(UPPER(usr.nom),'' '',usr.prenom) AS membre, usr.num_caf, usr.zone, usr.regime, FLOOR(DATEDIFF(''2011-12-31'',dte_naissance)/365) AS age, cpt.compte, SUM(cpt.montant) AS montant FROM $tbl_compte AS cpt LEFT JOIN $tbl_utilisateurs AS usr ON cpt.tiers=usr.id WHERE cpt.uid=2 AND cpt.mouvement<>''Réglement'' AND cpt.dte like ''$dte%'' GROUP BY cpt.dte,cpt.tiers, cpt.compte ORDER BY cpt.dte,membre,cpt.compte', 'dte', 'ALL');";

		UpdateDB($sql,"431");
	  }

// ---- 435
	if ($ver<435)
	  {
	  	$sql=array();
		$sql[] = 'CREATE TABLE `'.$MyOpt["tbl"].'_plage` (`id` VARBINARY(2) NOT NULL, `jour` CHAR(1) NOT NULL, `plage` CHAR(1) NOT NULL, `titre` VARCHAR(20) NOT NULL, `nom` VARCHAR(50) NOT NULL, `libelle` VARCHAR(50) NOT NULL, `deb` INT UNSIGNED NOT NULL , `fin` INT UNSIGNED NOT NULL)'; 
		$sql[] = 'ALTER TABLE `'.$MyOpt["tbl"].'_abo_plage` ADD INDEX(`id`)'; 
		$sql[] = 'ALTER TABLE `'.$MyOpt["tbl"].'_abo_plage` ADD INDEX(`jour`)'; 
		$sql[] = 'ALTER TABLE `'.$MyOpt["tbl"].'_abo_plage` ADD INDEX(`plage`)'; 
		$sql[] = 'ALTER TABLE `'.$MyOpt["tbl"].'_abo_ligne` ADD INDEX(`mouvid`)'; 
		$sql[] = 'ALTER TABLE `'.$MyOpt["tbl"].'_abonnement` ADD INDEX(`actif`)'; 
		$sql[] = 'ALTER TABLE `'.$MyOpt["tbl"].'_presence` ADD INDEX(`type`)'; 
		$sql[] = 'ALTER TABLE `'.$MyOpt["tbl"].'_presence` ADD INDEX(`dte`)'; 

		UpdateDB($sql,"435");
	  }


// ---- 437
	if ($ver<437)
	  {
	  	$sql=array();
		$sql[] = 'ALTER TABLE `'.$MyOpt["tbl"].'_mouvement` ADD `actif` ENUM(\'oui\',\'non\') NOT NULL DEFAULT \'oui\' AFTER `montant`;'; 
		UpdateDB($sql,"437");
	  }

// ---- 438
	if ($ver<438)
	  {
	  	$sql=array();
		$sql[] = 'ALTER TABLE `'.$MyOpt["tbl"].'_mouvement` CHANGE `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT;'; 
		$sql[] = "ALTER TABLE `".$MyOpt["tbl"]."_abo_ligne` CHANGE `mouvid` `mouvid` INT UNSIGNED NOT NULL DEFAULT '0';";
		UpdateDB($sql,"438");
	  }



// ---- 439
	if ($ver<439)
	  {
	  	$sql=array();
		$sql[] = 'ALTER TABLE `'.$MyOpt["tbl"].'_bapteme` ADD `num` VARCHAR(20) NOT NULL AFTER `id`;'; 
		UpdateDB($sql,"439");
	  }

// ---- 441
	if ($ver<441)
	  {
	  	$sql=array();
		$sql[] = 'CREATE TABLE `'.$MyOpt["tbl"].'_tarifs` (`id` INT UNSIGNED NOT NULL, `ress_id` INT UNSIGNED NOT NULL, `code` VARCHAR(2) NOT NULL, `nom` VARCHAR(20) NOT NULL, `pilote` VARCHAR(6) NOT NULL, `instructeur` VARCHAR(6) NOT NULL, PRIMARY KEY (`id`), INDEX (`ress_id`, `code`)) ENGINE = MyISAM'; 
		$sql[] = 'ALTER TABLE `'.$MyOpt["tbl"].'_tarifs` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT'; 
		$sql[] = 'ALTER TABLE `'.$MyOpt["tbl"].'_tarifs` ADD `reduction` INT NOT NULL;'; 
		$sql[] = 'ALTER TABLE `'.$MyOpt["tbl"].'_tarifs` ADD `defaut_pil` ENUM(\'oui\',\'non\') NOT NULL DEFAULT \'non\';'; 
		$sql[] = 'ALTER TABLE `'.$MyOpt["tbl"].'_tarifs` ADD `defaut_ins` ENUM(\'oui\',\'non\') NOT NULL DEFAULT \'non\';'; 
		$sql[] = 'ALTER TABLE `'.$MyOpt["tbl"].'_calendrier` CHANGE `tarif` `tarif` CHAR(2) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT \'\''; 
		UpdateDB($sql,"441");
	  }


// ---- 444
	if ($ver<444)
	  {
	  	$sql=array();
			$sql[]='ALTER TABLE `p67_calendrier` ADD `potentiel` INT UNSIGNED NOT NULL AFTER `idmaint`; ';
			UpdateDB($sql,"444");
	  }



// ---- 446
	if ($ver<446)
	  {
	  	$sql=array();
	  	$sql[] = "CREATE TABLE IF NOT EXISTS `".$MyOpt["tbl"]."_actualites` (  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,  `titre` varchar(150) NOT NULL,  `message` text NOT NULL,  `dte_mail` datetime NOT NULL,  `mail` enum('oui','non') NOT NULL DEFAULT 'non',  `actif` enum('oui','non') NOT NULL DEFAULT 'oui',  `uid_creat` int(10) unsigned NOT NULL,  `dte_creat` datetime NOT NULL, `uid_modif` int(11) NOT NULL,  `dte_modif` datetime NOT NULL,  PRIMARY KEY (`id`), KEY `uid_creat` (`uid_creat`)) ENGINE=MyISAM  DEFAULT CHARSET=latin1";
			$sql[] = "ALTER TABLE `".$MyOpt["tbl"]."_utilisateurs` ADD `notification` ENUM('oui','non') NOT NULL DEFAULT 'oui' AFTER `mail`;";
			$sql[] = "ALTER TABLE `".$MyOpt["tbl"]."_login` ADD `header` TEXT NOT NULL;";

			UpdateDB($sql,"446");


			echo "Migration News ";			
			$query="SELECT * FROM `".$MyOpt["tbl"]."_forums` WHERE fid=1";
			$mysql->Query($query);
			$news=array();
			for($i=0; $i<$mysql->rows; $i++)
			  { 
					$mysql->GetRow($i);
					$news[$i]=$mysql->data;
			  }

			$query="DELETE FROM ".$MyOpt["tbl"]."_actualites";
			$mysql->Delete($query);


			foreach($news as $id=>$d)
				{
					$query="INSERT INTO ".$MyOpt["tbl"]."_actualites (titre,message,uid_creat,dte_creat,uid_modif,dte_modif) VALUES ('".addslashes($d["titre"])."','".addslashes(strip_tags( preg_replace("/&nbsp;/si"," ",preg_replace("/<br>/si","\n",$d["message"]))))."','".$d["uid_creat"]."','".$d["dte_creat"]."','".$d["uid_maj"]."','".$d["dte_maj"]."')";
					$mysql->Insert($query);
				}
			echo "[done]<br />";			

	  }

// ---- 
	$nver=449;
	if ($ver<$nver)
	  {
	  	$sql=array();
			$sql[] = "ALTER TABLE `".$MyOpt["tbl"]."_utilisateurs` ADD `dte_login` DATETIME NOT NULL AFTER `dte_inscription`;";
			$sql[] = "CREATE TABLE IF NOT EXISTS `".$MyOpt["tbl"]."_navpoints` (`nom` varchar(20) NOT NULL, `description` varchar(200) NOT NULL, `lat` varchar(10) NOT NULL, `lon` varchar(10) NOT NULL, `icone` varchar(10) NOT NULL, PRIMARY KEY (`nom`)) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
			$sql[] = "CREATE TABLE IF NOT EXISTS `".$MyOpt["tbl"]."_navroute` (`id` int(10) unsigned NOT NULL AUTO_INCREMENT, `idnav` int(10) unsigned NOT NULL, `ordre` int(10) unsigned NOT NULL, `nom` varchar(20) NOT NULL, PRIMARY KEY (`id`), KEY `idnav` (`idnav`,`nom`)) ENGINE=MyISAM  DEFAULT CHARSET=latin1;";
			$sql[] = "CREATE TABLE IF NOT EXISTS `".$MyOpt["tbl"]."_navigation` (`id` int(10) unsigned NOT NULL AUTO_INCREMENT,`titre` varchar(40) NOT NULL,`uid_creat` int(10) unsigned NOT NULL,`dte_creat` datetime NOT NULL,`uid_modif` int(10) unsigned NOT NULL,`dte_modif` datetime NOT NULL,PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
			$sql[] = "ALTER TABLE `".$MyOpt["tbl"]."_navigation` ADD `vitesse` INT UNSIGNED NOT NULL AFTER `titre`, ADD `dirvent` INT UNSIGNED NOT NULL AFTER `vitesse`, ADD `vitvent` INT UNSIGNED NOT NULL AFTER `dirvent`;";
			$sql[] = "ALTER TABLE `".$MyOpt["tbl"]."_forums` ADD `actif` ENUM('oui','non') NOT NULL DEFAULT 'oui' AFTER `mail_diff`, ADD INDEX (`actif`) ;";
			$sql[] = "ALTER TABLE `".$MyOpt["tbl"]."_calendrier` ENGINE = InnoDB;";
			$sql[] = "ALTER TABLE `".$MyOpt["tbl"]."_actualites` ENGINE = InnoDB;";
			$sql[] = "ALTER TABLE `".$MyOpt["tbl"]."_bapteme` ENGINE = InnoDB;";
			$sql[] = "ALTER TABLE `".$MyOpt["tbl"]."_masses` ENGINE = InnoDB;";
			UpdateDB($sql,$nver);
		}

// ----
	$nver=455;
	if ($ver<$nver)
	  {
	  	$sql=array();
			$sql[]= "CREATE TABLE IF NOT EXISTS `".$MyOpt["tbl"]."_roles` (`id` int(10) unsigned NOT NULL AUTO_INCREMENT,`groupe` varchar(5) NOT NULL,`role` varchar(40) NOT NULL,PRIMARY KEY (`id`),KEY `groupe` (`groupe`,`role`)) ENGINE=MyISAM DEFAULT CHARSET=latin1 ;";
			$sql[]= "ALTER TABLE `".$MyOpt["tbl"]."_droits` ADD `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST ;";
			$sql[]= "ALTER TABLE `".$MyOpt["tbl"]."_droits` ADD INDEX ( `groupe` ) ;";
			$sql[]= "ALTER TABLE `".$MyOpt["tbl"]."_droits` ADD INDEX ( `uid` ) ;";
			UpdateDB($sql,$nver);

			$query="TRUNCATE ".$MyOpt["tbl"]."_roles";
			$mysql->Update($query);

			
			require_once ("config/roles.inc.php");

			foreach($Droits as $grp=>$tr)
			{
				echo $grp." : ";
				foreach($tr as $role=>$d)
				{
					echo $role.",";
					$query="INSERT INTO ".$MyOpt["tbl"]."_roles SET groupe='".$grp."',role='".$role."'";
					$mysql->Insert($query);
				}
				echo "<br>";
			}
		}

// ----
	$nver=463;
	if ($ver<$nver)
	{
	  	$sql=array();
		$sql[]= "ALTER TABLE `".$MyOpt["tbl"]."_compte` ADD `mid` INT UNSIGNED NOT NULL AFTER `id`, ADD INDEX (`mid`);";
		UpdateDB($sql,$nver);
	}

	
// ----
	$nver=464;
	if ($ver<$nver)
	{
	  	$sql=array();
		$query="SELECT value FROM ".$MyOpt["tbl"]."_config WHERE param='mvtid'";
		$res=$mysql->QueryRow($query);
		$mvtid=$res["value"];

		$sql[]= "SELECT value FROM ".$MyOpt["tbl"]."_config WHERE param='mvtid'";
		if ($mvtid=="")
		{
			$query="SELECT MAX(id) AS mvtid FROM ".$MyOpt["tbl"]."_compte";
			$res=$mysql->QueryRow($query);
			$mvtid=$res["mvtid"]+1;

			$sql[]= "INSERT INTO ".$MyOpt["tbl"]."_config (param,value) VALUES ('mvtid','$mvtid')";
		}
		UpdateDB($sql,$nver);
	}

// ----
	$nver=467;
	if ($ver<$nver)
	{
	  	$sql=array();
		$sql[]="CREATE TABLE `".$MyOpt["tbl"]."_echeance` (`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,`typeid` int(10) UNSIGNED NOT NULL,`uid` int(10) UNSIGNED NOT NULL,`dte_echeance` date NOT NULL,`paye` ENUM('oui','non') NOT NULL DEFAULT 'non', `actif` ENUM('oui','non') NOT NULL DEFAULT 'oui',`dte_create` datetime NOT NULL,`uid_create` int(10) UNSIGNED NOT NULL,`dte_maj` datetime NOT NULL,`uid_maj` int(10) UNSIGNED NOT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB";
		$sql[]="ALTER TABLE `".$MyOpt["tbl"]."_echeance` ADD INDEX ( `typeid` ) ;";
		$sql[]="ALTER TABLE `".$MyOpt["tbl"]."_echeance` ADD INDEX ( `uid` ) ;";

		$sql[]="CREATE TABLE `".$MyOpt["tbl"]."_echeancetype` ( `id` INT UNSIGNED NOT NULL AUTO_INCREMENT, `description` VARCHAR(100) NOT NULL , `poste` INT NOT NULL , `cout` DECIMAL(10,2) NOT NULL DEFAULT '0', `resa` ENUM('obligatoire','instructeur','facultatif') NOT NULL, `droit` VARCHAR(3) NOT NULL , `multi` ENUM('oui','non') NOT NULL DEFAULT 'non' , PRIMARY KEY (`id`)) ENGINE = InnoDB;";
		$sql[]="ALTER TABLE `".$MyOpt["tbl"]."_echeancetype` ADD INDEX ( `poste` ) ;";
		
		// Convertir toutes les dates actuelles en échéances
		// Créé le type d'échéances
		// - Médicale
		// - PPL
		$sql[]="INSERT INTO ".$MyOpt["tbl"]."_echeancetype SET description='SEP', resa='instructeur',multi='non';";
		$sql[]="INSERT INTO ".$MyOpt["tbl"]."_echeancetype SET description='Certificat Médical', resa='instructeur',multi='non';";
		UpdateDB($sql,$nver);		

		echo "Copie des echeances...";		
		$query="DELETE FROM p67_echeance";
		$mysql->Delete($query);

		// Liste des users
		//   Pour chacun ajouter les 2 dates si la date éxiste
		$query="SELECT id,dte_licence,dte_medicale FROM `".$MyOpt["tbl"]."_utilisateurs";
		$mysql->Query($query);
		$tabEcheance=array();
		for($i=0; $i<$mysql->rows; $i++)
		  { 
				$mysql->GetRow($i);
				$tabEcheance[$mysql->data["id"]][1]=$mysql->data["dte_licence"];
				$tabEcheance[$mysql->data["id"]][2]=$mysql->data["dte_medicale"];
		  }
		foreach ($tabEcheance as $uid=>$d)
		{
			$q="INSERT INTO ".$MyOpt["tbl"]."_echeance SET typeid='1',uid='".$uid."',dte_echeance='".$d[1]."';";
			$mysql->Insert($q);
			$q="INSERT INTO ".$MyOpt["tbl"]."_echeance SET typeid='2',uid='".$uid."',dte_echeance='".$d[2]."';";
			$mysql->Insert($q);
		}
		
	}

// ----
	$nver=468;
	if ($ver<$nver)
	{
	  	$sql=array();
		$sql[]="CREATE TABLE `".$MyOpt["tbl"]."_utildonneesdef` (`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,`ordre` tinyint(3) UNSIGNED NOT NULL,`nom` varchar(20) COLLATE latin1_general_ci NOT NULL,`type` varchar(10) COLLATE latin1_general_ci NOT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;";
		
		$sql[]="CREATE TABLE `".$MyOpt["tbl"]."_utildonnees` (`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,`did` int(10) UNSIGNED NOT NULL,`uid` int(11) NOT NULL,`valeur` varchar(255) COLLATE latin1_general_ci NOT NULL, PRIMARY KEY (`id`), KEY `uid` (`uid`), KEY `dataid` (`did`)) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;";
  
		UpdateDB($sql,$nver);
	}

// ----
	$nver=469;
	if ($ver<$nver)
	{
		$query="SELECT MAX(id) AS mvtid FROM ".$MyOpt["tbl"]."_compte";
		$res=$mysql->QueryRow($query);
		$mvtid=$res["mvtid"]+1;

		$sql=array();
		$sql[]="CREATE TABLE `".$MyOpt["tbl"]."_comptetemp` (`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,`deb` int(10) unsigned NOT NULL DEFAULT '0',`cre` int(10) unsigned NOT NULL DEFAULT '0',`ventilation` text COLLATE latin1_general_ci NOT NULL,`montant` decimal(10,2) NOT NULL DEFAULT '0.00',`poste` int(10) NOT NULL DEFAULT '0',`commentaire` tinytext COLLATE latin1_general_ci NOT NULL,`date_valeur` date NOT NULL DEFAULT '0000-00-00',`compte` varchar(10) COLLATE latin1_general_ci NOT NULL, `facture` varchar(10) COLLATE latin1_general_ci NOT NULL, `status` varchar(10) COLLATE latin1_general_ci NOT NULL DEFAULT '0',`uid_creat` int(10) unsigned NOT NULL DEFAULT '0',`date_creat` datetime NOT NULL DEFAULT '0000-00-00 00:00:00', PRIMARY KEY (`id`)) ENGINE=InnoDB  AUTO_INCREMENT=".$mvtid." DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;";
		$sql[]="ALTER TABLE `".$MyOpt["tbl"]."_ressources` ADD `poste` INT UNSIGNED NOT NULL AFTER `actif`;";
		$sql[]="ALTER TABLE `".$MyOpt["tbl"]."_ressources` ADD INDEX(`poste`);";
 
		UpdateDB($sql,$nver);
	}

// ----
	$nver=470;
	if ($ver<$nver)
	{
		$sql=array();
		$sql[]="ALTER TABLE `".$MyOpt["tbl"]."_compte` ADD `rembfact` VARCHAR(10) NOT NULL AFTER `facture`;";
		$sql[]="UPDATE `".$MyOpt["tbl"]."_compte` SET rembfact=facture WHERE tiers=".$MyOpt["uid_banque"]." AND facture<>'NOFAC'";
		$sql[]="UPDATE `".$MyOpt["tbl"]."_compte` SET facture='NOFAC' WHERE tiers=".$MyOpt["uid_banque"]." AND rembfact<>''";
		$sql[]="UPDATE `".$MyOpt["tbl"]."_compte` SET facture='NOFAC' WHERE facture=''";

		$sql[]="ALTER TABLE `".$MyOpt["tbl"]."_comptetemp` ADD `facture` VARCHAR(10) NOT NULL AFTER `compte`;";
		$sql[]="ALTER TABLE `".$MyOpt["tbl"]."_comptetemp` ADD `rembfact` VARCHAR(10) NOT NULL AFTER `facture`;";
		$sql[]="ALTER TABLE `".$MyOpt["tbl"]."_compte` ADD INDEX(`facture`);";
		$sql[]="ALTER TABLE `".$MyOpt["tbl"]."_compte` ADD INDEX(`rembfact`);";

		UpdateDB($sql,$nver);
	}
// ----
	$nver=471;
	if ($ver<$nver)
	{
		$sql=array();
		$sql[]="ALTER TABLE `".$MyOpt["tbl"]."_utildonneesdef` ADD `actif` ENUM('oui','non') NOT NULL DEFAULT 'oui' AFTER `type`;";
		UpdateDB($sql,$nver);
	}
	
// ----
	$nver=472;
	if ($ver<$nver)
	{
		$sql=array();
		$sql[]="ALTER TABLE `".$MyOpt["tbl"]."_utilisateurs` ADD `disponibilite` ENUM('dispo','occupe') NOT NULL DEFAULT 'dispo' AFTER `notification`;";
		$sql[]="ALTER TABLE `".$MyOpt["tbl"]."_compte` ADD `signature` VARCHAR(64) NOT NULL AFTER `rembfact`, ADD `precedent` VARCHAR(64) NOT NULL AFTER `signature`;";

		echo "Signe les transactions de la table de compte...";		

		$mysql_gen = new mysql_class($mysqluser, $mysqlpassword, $hostname, $db, $port);

		$query="SELECT * FROM `".$MyOpt["tbl"]."_compte";
		$mysql->Query($query);

		$lastid=0;
		for($i=0; $i<$mysql->rows; $i++)
		{ 
			$mysql->GetRow($i);

			$query="SELECT id,signature FROM ".$MyOpt["tbl"]."_compte WHERE id='".$lastid."'";
			$res=$mysql_gen->QueryRow($query);
			$lastid=$mysql->data["id"];

			// Signe la transaction
			$montant=number_format($mysql->data["montant"],2,'.','');
			$signature=md5($mysql->data["id"]."_".$mysql->data["uid"]."_".$mysql->data["tiers"]."_".$montant."_".$mysql->data["date_valeur"]."_".$res["id"]."_".$res["signature"]);

			$query="UPDATE ".$MyOpt["tbl"]."_compte SET ";
			$query.="signature='".$signature."', ";
			$query.="precedent='".$res["signature"]."' ";
			$query.="WHERE id='".$mysql->data["id"]."'";
			$mysql_gen->Update($query);

		}
		echo " [".$mysql->rows."]<br />";		

		$sql[]="ALTER TABLE `".$MyOpt["tbl"]."_compte` ADD UNIQUE(`signature`);";
		$sql[]="CREATE TABLE ".$MyOpt["tbl"]."_disponibilite` ( `id` INT UNSIGNED NOT NULL AUTO_INCREMENT , `uid` INT UNSIGNED NOT NULL , `dte_deb` DATETIME NOT NULL , `dte_fin` DATETIME NOT NULL , `uid_maj` INT UNSIGNED NOT NULL , `dte_maj` DATETIME NOT NULL , PRIMARY KEY (`id`), INDEX `uid` (`uid`)) ENGINE = InnoDB;";
		
		UpdateDB($sql,$nver);

	}
		
// *********************************************************************************************************

	echo "<a href='".$MyOpt["host"]."'>-Retour au site-</a>";
	
// *********************************************************************************************************

function UpdateDB($sql,$setver)
  { global $mysql,$err,$MyOpt;
  	echo "Update v".$setver;
		$mysql_err=0;

  	foreach($sql as $i=>$query)
	  {
	  	$mysql->Update($query);
	  }

		if ($mysql_err==0)
	  {
			$query="UPDATE ".$MyOpt["tbl"]."_config SET value='$setver' WHERE param='dbversion'";
			$mysql->Update($query);
			echo " [done]<br />";
		  }
		else
	  {
			echo " [error]<br />";
	  }
  }

?>
