<?php
/*
    Easy Aero v2.14
    Copyright (C) 2018 Matthieu Isorez

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
	  { FatalError("Fichier de configuration introuvable","Il manque le fichier de configuration."); }
	if (!file_exists("config/variables.inc.php"))
	  { FatalError("Fichier des variables introuvable","Il manque le fichier de variables."); }

  	require ("config/config.inc.php");
	require ("config/variables.inc.php");

// ---- Charge le numéro de version
	require ("version.php");

// ---- Charge les class
	require ("class/user.inc.php");

// ---- Se connecte à la base MySQL
	require ("class/mysql.inc.php");

	$sql = new mysql_class($mysqluser, $mysqlpassword, $hostname, $db);
	$sql_cron = new mysql_class($mysqluser, $mysqlpassword, $hostname, $db);

// ---- Défini l'utilisateur d'execution du batch
	if ((!is_numeric($MyOpt["uid_system"])) || ($MyOpt["uid_system"]==0))
	  { FatalError("Compte systeme introuvable","Le compte systeme n'est pas défini."); }

	$gl_uid=$MyOpt["uid_system"];
	$uid=$gl_uid;

// ---- Timezone d'exécution
	if ($MyOpt["timezone"]!="")
	  { date_default_timezone_set($MyOpt["timezone"]); }
	
// ---- Fonction des informations de l'utilisateur
	if ($gl_uid>0)
	{
		$myuser = new user_class($gl_uid,$sql,true);
		$res_user=$myuser->data;
	}

	$module="modules";

// ---- Execute les taches planifiées
	$query="SELECT * FROM ".$MyOpt["tbl"]."_cron WHERE actif='oui' AND (nextrun<='".now()."' OR nextrun IS NULL)";
	$sql_cron->Query($query);

	for($gl_cron_i=0; $gl_cron_i<$sql_cron->rows; $gl_cron_i++)
	{
		$sql_cron->GetRow($gl_cron_i);

		$gl_myprint_txt="";
		$gl_res="";

		echo utf8_encode($sql_cron->data["description"])."\n";

		$mod=$sql_cron->data["module"];
		require(MyRep($sql_cron->data["script"].".cron.php"));

		echo "-----------------------------\n";
		echo $gl_myprint_txt;
		echo "-----------------------------\n\n";

		$q="UPDATE ".$MyOpt["tbl"]."_cron SET lastrun='".now()."', nextrun='".date("Y-m-d H:i:s",time()+$sql_cron->data["schedule"]*60)."', txtretour='".$gl_res."', txtlog='".addslashes($gl_myprint_txt)."' WHERE id='".$sql_cron->data["id"]."'";
		$sql->Update($q);
		
	}

// ---- Ferme la connexion à la base de données	
 	$sql->closedb();

?>
