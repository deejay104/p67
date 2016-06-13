<?php
/*
    SoceIt v2.0
    Copyright (C) 2012 Matthieu Isorez

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

	($Author: miniroot $)
	($Date: 2012-07-31 22:04:07 +0200 (mar., 31 juil. 2012) $)
	($Rev: 388 $)
*/

	set_time_limit(0);

	$mydir=preg_replace("/cron\.php/","",($_SERVER["SCRIPT_FILENAME"]!="") ? $_SERVER["SCRIPT_FILENAME"] : $_SERVER["argv"][0]);
	$mydir=preg_replace("/\/?scripts\//","",$mydir);
	if ($mydir=="")
	{
		$mydir=".";
	}
	echo "MyDir: '".$mydir."'\n";
	chdir($mydir);

// ---- Défini les variables globales
	$prof="";
	$gl_mode="batch";
	
	require ("modules/fonctions.inc.php");

// ---- Charge la config  
	if (!file_exists("config/config.inc.php"))
	  { FatalError("Fichier de configuration introuvable","Il manque le fichier de configuration 'config/config.inc.php'."); }
	if (!file_exists("config/variables.inc.php"))
	  { FatalError("Fichier des variables introuvable","Il manque le fichier de variables 'config/variables.inc.php'."); }

  	require ("config/config.inc.php");
	require ("config/variables.inc.php");

// ---- Charge le numéro de version
	require ("version.txt");

// ---- Charge les templates
	require ("class/xtpl.inc.php");

// ---- Charge les class
	require ("class/user.inc.php");

// ---- Se connecte à la base MySQL
	require ("class/mysql.inc.php");
	$sql = new mysql_class($mysqluser, $mysqlpassword, $hostname, $db, $port);

// ---- Défini l'utilisateur d'execution du batch
	$gl_uid=$MyOpt["uid_club"];
	$uid=$gl_uid;
	
	
// ---- Fonction des informations de l'utilisateur
	if ($gl_uid>0)
	  {
		$myuser = new user_class($gl_uid,$sql,true);
		$res_user=$myuser->data;
	  }

// ---- Template par default
	$tmpl="vide.htm";
	$tmpl_prg = new XTemplate (MyRep($tmpl));

// ---- Maj du template
	if (!isset($default_profile))
	{
		$default_profile="";
	}
	$tmpl_prg->assign("version", $version.(($default_profile!="") ? ".".eregi_replace("([a-z])[a-z]*_(.._)?([a-z])[a-z]*","\\1\\3",$default_profile) : ""));


// ---- Définition des variables
	$color   = "013366";
	$color2  = "FFFFFF";
	$color3  = "026AD3";
	$colfond = "FFFFFF";

	$MyOpt["col_fond"]["value"]=$color;
	$MyOpt["col_prg"]["value"]=$color2;
	$MyOpt["col_titre"]["value"]="AFC8E2";

	$module="modules";

// ---- Facture les lignes d'abonnement
	if ($MyOpt["module"]["abonnement"]=="on")
	{
		echo "\n\n----\nAbonnement\n";
		$mod="facturation";
	 	require(MyRep("factureabo.inc.php"));
	}

// ---- Envoie la notification pour les nouvelles factures
	if ($MyOpt["module"]["facture"]=="on")
	{
		echo "\n\n----\nFacturation\n";
		$mod="facturation";
	 	require(MyRep("facturemail.inc.php"));
	}

// ---- Envoie la notification pour la validité des licences
	if ($MyOpt["module"]["aviation"]=="on")
	{
		echo "\n\n----\nAviation\n";
		$mod="aviation";
	  require(MyRep("checkvalid.inc.php"));
	}
	
// ---- Affecte les blocs
	$tmpl_prg->assign("corps", $corps);

// ---- Affiche la page
	$tmpl_prg->parse("main");
	echo $tmpl_prg->text("main");

// ---- Ferme la connexion à la base de données	  
 	$sql->closedb();

?>
