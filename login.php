<?php
// ---------------------------------------------------------------------------------------------
//   Page de Login
//   
// ---------------------------------------------------------------------------------------------
//   Variables  : 
// ---------------------------------------------------------------------------------------------

	session_start();
	if (isset($_SESSION['uid']))
	  { $uid = $_SESSION['uid']; }

// ---- R�cup�re les variables transmises
	$rub="";
	$username="";
	$password="";
	$myid=0;
	$fonc="";
	if (isset($_REQUEST['rub']))
	  { $rub=$_REQUEST["rub"]; }
	$username=$_REQUEST["username"];
	$password=$_REQUEST["password"];
	$myid=$_REQUEST["myid"];
	$fonc=$_REQUEST["fonc"];
	
	if ($_REQUEST["varlogin"]!="")
	  {
	  	//eval("if (is_array(\$HTTP_".$_SERVER["REQUEST_METHOD"]."_VARS)) { foreach( \$HTTP_".$_SERVER["REQUEST_METHOD"]."_VARS as \$key=>\$value) { \$var .= \"&\$key=\$value\"; } }");
	  	//$var.="&rub=$rub";
		$var=$_REQUEST["varlogin"];
	  }
	else
	  {
	  	$var=$_SERVER["REQUEST_URI"];
	  }

	$var=preg_replace("/\/login.php/","",$var);

// ---- Charge les pr�requis
	require ("class/xtpl.inc.php");
	require ("class/mysql.inc.php");

// ---- Charge les variables
	require ("config/config.inc.php");
	require ("config/variables.inc.php");
	require ("modules/fonctions.inc.php");

	if ($MyOpt["timezone"]!="")
	  { date_default_timezone_set($MyOpt["timezone"]); }

// ---- Gestion des th�mes
	$theme="";
	if ( (isset($_REQUEST["settheme"])) && ($_REQUEST["settheme"]!="") )
	  {	
	  	$theme=$themes[$_REQUEST["settheme"]];
		$_SESSION['mytheme']=$theme;
	  }
	else if ((isset($_SESSION['mytheme'])) && ($_SESSION['mytheme']!=""))
	  {	$theme=$_SESSION['mytheme']; }
	else if ($_SESSION['mytheme']=="")
	  {
		if ((preg_match("/CPU iPhone OS/",$_SERVER["HTTP_USER_AGENT"])) ||
			(preg_match("/Linux; U; Android/",$_SERVER["HTTP_USER_AGENT"])) ||
			(preg_match("/iPad; U; CPU OS/",$_SERVER["HTTP_USER_AGENT"])) || 
			(preg_match("/Linux; Android/",$_SERVER["HTTP_USER_AGENT"])) 
			
		   )
		  {
			$theme="phone";
			$_SESSION['mytheme']=$theme;
		  }
		
	  }

// ---- Charge le num�ro de version
	require ("version.txt");

// ---- Test si l'on a valid� la page
	$ok=0;
	$errmsg="";
	
	if ($fonc == "Connecter")
//	if ($username!="")
	  {
		if ($password=="") { $password="nok"; }
		$username=strtolower($username);
		$username=preg_replace("/[\"'<>\\\;]/i","",$username);

		//preg_match("/^([^ ]*) (.*?)$/",$username,$t);

		$sql   = new mysql_class($mysqluser, $mysqlpassword, $hostname, $db,$port);
		$query = "SELECT id,prenom,nom,mail,password FROM p67_utilisateurs WHERE ((mail='$username' AND mail<>'') OR (initiales='$username' AND initiales<>'')) AND actif='oui' AND virtuel='non'";

		$res   = $sql->QueryRow($query);

		if (($res["id"]>0) && (md5($res["password"].md5(session_id()))==$password))
		  {
				$query="INSERT INTO ".$MyOpt["tbl"]."_login (username,dte_maj,header) VALUES ('".addslashes($res["prenom"])." ".addslashes($res["nom"])."','".now()."','".substr(addslashes($_SERVER["HTTP_USER_AGENT"]),0,200)."')";
				$sql->Insert($query);
				$_SESSION['uid']=$res["id"];

				$query="UPDATE p67_utilisateurs SET dte_login='".now()."' WHERE id='".$res["id"]."'";
				$sql->Update($query);

	
				echo "<HTML><HEAD><SCRIPT language=\"JavaScript\">function go() { document.location=\"$var\"; }</SCRIPT></HEAD><BODY onload=\"go();\"></BODY></HTML>";
				exit;

		  }
		else
		  {
			$errmsg="Votre mot de passe est incorrect.";
		  }
	  }
	else if ($fonc == "logout")
	  {
		$_SESSION['uid']="";
		echo "<HTML><HEAD><SCRIPT language=\"JavaScript\">function go() { document.location=\"index.php\"; }</SCRIPT></HEAD><BODY onload=\"go();\"></BODY></HTML>";
		exit;
	  }

// ---- Charge les templates
	$module="modules";
	$tmpl_prg = new XTemplate (MyRep("login.htm"));

	if ($tmpl_prg->text("main.unsecure")=="")
	  { $tmpl_prg->parse("main.secure"); }


// ---- Calcul de l'id
	$myid=md5(session_id());

// ---- Affiche la page
	$tmpl_prg->assign("myid", $myid);
	$tmpl_prg->assign("var", $var);
	$tmpl_prg->assign("errmsg", $errmsg);
	$tmpl_prg->assign("version", $version);
	$tmpl_prg->assign("site_title", $MyOpt["site_title"]);
	$tmpl_prg->assign("site_logo", $MyOpt["site_logo"]);

	$tmpl_prg->parse("main");
	echo $tmpl_prg->text("main");


// ---- D�charge les variables post�es
	eval ("foreach( \$_".$_SERVER["REQUEST_METHOD"]." as \$key=>\$value) { unset (\$_".$_SERVER['REQUEST_METHOD']."[\$key]); eval(\"\$_SESSION[\$key];\"); }");

?>
