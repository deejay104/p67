<?php
/*
    SoceIt v2.5 ($Revision: 456 $)
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
*/
?>
<?php
// ---- Header de la page
	// Date du pass�
	header("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
	
	// toujours modifi�
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	
	// HTTP/1.1
	header("Cache-Control: no-store, no-cache, must-revalidate");
	
	// HTTP/1.0
	header("Pragma: no-cache");

	// Charset
	header('Content-type: text/html; charset=ISO-8859-1');

	//error_reporting( E_ALL ^ E_NOTICE ^ E_DEPRECATED );

// ---- Gestion des droits
	session_start();

	if ((isset($_SESSION['uid'])) && ($_SESSION['uid']>0))
	  { $uid = $_SESSION['uid']; }
	else
	  { include "login.php"; exit; }

// ---- D�fini les variables globales
	$prof="";
	$gl_mode="";
	$gl_uid=$uid;
	
// ---- R�cup�re les param�tres
	$e ="foreach( \$_REQUEST as \$key=>\$value) {"."\n";
	$e.="if (!isset(\$_SESSION[\"\$key\"])) {"."\n";

	$e.="  if (is_array(\$value)) {"."\n";
	$e.="      foreach(\$value as \$k=>\$v) { if (!is_array(\$v)) { \$value[\$k]=stripslashes(\$v); } } \$\$key=\$value;"."\n";
	$e.="  } else if (get_magic_quotes_gpc()) {"."\n";
	$e.="      \$\$key = stripslashes(\$value);"."\n";
	$e.="  } else {"."\n";
	$e.="    \$\$key = \$value;"."\n";
	$e.="} } }"."\n";

	eval($e);



// ---- V�rifie la variable $mod
	if (!preg_match("/^[a-z0-9_]*$/",$mod))
	  { $mod = ""; }
	if (trim($mod)=="")
	  { $mod = ""; }

// ---- V�rifie la variable $rub
	if (!preg_match("/^[a-z0-9_]*$/",$rub))
	  { $rub = "index"; }
	if (trim($rub)=="")
	  { $rub = "index"; }

// ---- V�rifie la langue
	$lang="fr";

// ---- Charge la config  
	if (!file_exists("config/config.inc.php"))
	  { FatalError("Fichier de configuration introuvable","Il manque le fichier de configuration 'config/config.inc.php'."); }
	if (!file_exists("config/variables.inc.php"))
	  { FatalError("Fichier des variables introuvable","Il manque le fichier de variables 'config/variables.inc.php'."); }

  require ("config/config.inc.php");
	require ("config/variables.inc.php");
	require ("modules/fonctions.inc.php");

	if ($MyOpt["timezone"]!="")
	  { date_default_timezone_set($MyOpt["timezone"]); }

// ---- Gestion des th�mes

// Mozilla/5.0 (iPhone; U; CPU iPhone OS 4_3_2 like Mac OS X; fr-fr) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8H7 Safari/6533.18.5
// Mozilla/5.0 (Linux; U; Android 2.2.1; fr-fr; HTC_Wildfire-orange-LS Build/FRG83D) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1
// Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.1; XBLWP7; ZuneWP7)
// Mozilla/5.0 (iPad; U; CPU OS 4_3_3 like Mac OS X; en-us) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8J3 Safari/6533.18.5


	$theme="";
	if ($_REQUEST["settheme"]!="")
	  {	
	  	$theme=$themes[$_REQUEST["settheme"]];
		$_SESSION['mytheme']=$theme;
	  }
	else if (isset($_SESSION['mytheme']))
	  {	$theme=$_SESSION['mytheme']; }
	else if ($_SESSION['mytheme']="")
	  {
		if ((preg_match("/CPU iPhone OS/",$_SERVER["HTTP_USER_AGENT"])) ||
			(preg_match("/Linux; U; Android/",$_SERVER["HTTP_USER_AGENT"])) ||
			(preg_match("/iPad; U; CPU OS/",$_SERVER["HTTP_USER_AGENT"]))
		   )
		  {
			$theme="phone";
			$_SESSION['mytheme']=$theme;
		  }
		
	  }

// ---- Charge les variables et fonctions
	$module="modules".(($default_profile!="") ? ".$default_profile" : "");

// ---- Charge le num�ro de version
	require ("version.txt");

// ---- Charge les templates
	require ("class/xtpl.inc.php");

// ---- Charge les class
	require ("class/user.inc.php");
	require ("class/ressources.inc.php");

// ---- Se connecte � la base MySQL
	require ("class/mysql.inc.php");
	$sql = new mysql_class($mysqluser, $mysqlpassword, $hostname, $db, $port);

// ---- Fonction des informations de l'utilisateur
	if ($uid>0)
	  {
			$myuser = new user_class($uid,$sql,true);
			$res_user=$myuser->data;
	  }

	if (($MyOpt["maintenance"]=="on") && (!GetDroit("ADM")))
	  {
	  	echo "Ce site est en maintenance.<br/>";
	  	echo "Merci de retenter votre connexion dans quelques instant.<br/>";
	  	exit;
	  }	  	

// ---- Template par default
	if (!preg_match("/[a-z]+/i",$tmpl))
	  { $tmpl="default"; }
	$tmpl="$tmpl.htm";
	$tmpl_prg = new XTemplate (MyRep($tmpl));

// ---- Maj du template
	$tmpl_prg->assign("uid", $uid);
	$tmpl_prg->assign("username", $myuser->aff("prenom")." ".$myuser->aff("nom"));
	$tmpl_prg->assign("version", $version.(($default_profile!="") ? ".".preg_replace("/([a-z])[a-z]*_(.._)?([a-z])[a-z]*/i","\\1\\3",$default_profile) : "").(($MyOpt["maintenance"]=="on") ? " - MAINTENANCE ACTIVE" : ""));

  $tmpl_prg->assign("site_logo", $MyOpt["site_logo"]);
	$tmpl_prg->assign("site_title", $MyOpt["site_title"]);

/*
	if ($MyOpt["help"][$mod][$rub]!="")
	  {
		$tmpl_prg->assign("helplink", $MyOpt["help"][$mod][$rub]);
		$tmpl_prg->parse("main.help");
	  }
*/

// ---- Flag pour ne pouvoir poster qu'une seule fois les m�mes infos
	if (!isset($_SESSION["checkpost"]))
	  {
		$checkpost=1;
		$_SESSION["checkpost"]=1;
	  }
	else
	  {
		$checkpost=$checkpost+1;
	  	$_SESSION["checkpost"]=$_SESSION["checkpost"]+1;
	  }

	if (!isset($_SESSION["tab_checkpost"]))
	  { 
		$tab_checkpost[""]="ok";
		$_SESSION["tab_checkpost"][""]="ok";
	  }


// ---- D�finition des variables
	$gl_myprint_txt="";

// ---- Initialisation des variables
	$tmpl_prg->assign("rub", ucwords($rub));
	$tmpl_prg->assign("module", ucwords($mod));

	$tmpl_prg->assign("date_expire", date("r"));
	//Mon, 22 Jul 2002 11:12:01 GMT
	
// ---- Affichages du menu
	foreach($MyOpt["menu"] as $menu=>$droit) {
		if ( ( ($droit=="x") || (($droit=="") && ($myuser->data["type"]!="invite")) || ((GetDroit($droit)) && ($droit!="")) ) && ($droit!="-") )
		  { 
		  	$tmpl_prg->parse("main.menu_".$menu); 
		  	$tmpl_prg->parse("main.menu_".$menu."_sm"); 
		  }
	}

// ---- Charge la rubrique
	$affrub=$rub;
	while ($affrub!="")
	  {
			$oldrub=$affrub;
	
			// Initialise les variables
			$infos="";
			$icone="";
			$corps="";
			
			// Charge la rubrique
			if (MyRep("$affrub.inc.php")!="")
			  {
			  	$rub=$affrub;
			  	require(MyRep("$affrub.inc.php"));
			  }
			else
			  { FatalError("Fichier introuvable","Fichier : $affrub.inc.php"); }
			
			if ($affrub==$oldrub)
			  { $affrub=""; }
	  }
	
// ---- Affecte les blocs
	$tmpl_prg->assign("icone", $icone);
	$tmpl_prg->assign("infos", $infos);
	$tmpl_prg->assign("corps", $corps);

	$tmpl_prg->assign("myprint", $gl_myprint_txt);


// ---- Affiche la page
	$tmpl_prg->parse("main");
	echo $tmpl_prg->text("main");

// ---- Ferme la connexion � la base de donn�es	  
    	$sql->closedb();

// ---- D�charge les variables post�es
//	eval ("foreach( \$_".$_SERVER["REQUEST_METHOD"]." as \$key=>\$value) { unset (\$_".$_SERVER['REQUEST_METHOD']."[\$key]);  }");


function Purge($txt)
{
	$p[]="/  /";	$r[]=" ";
	$p[]="/   /";	$r[]=" ";
	$p[]="/    /";	$r[]=" ";
	$p[]="/     /";	$r[]=" ";
	$p[]="/\t/";	$r[]=" ";
	$p[]="/\r/";	$r[]="";
	$p[]="/\n/";	$r[]="";
	
	
	$txt=preg_replace($p,$r,$txt);
	return $txt;
}
?>
