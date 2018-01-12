<?
// ---- Autorisation d'accшs
	session_start();

	if ((isset($_SESSION['uid'])) && ($_SESSION['uid']>0))
	{
		$uid = $_SESSION['uid'];
	}
	else
	{
		header("HTTP/1.0 401 Unauthorized"); exit;
	}

// ---- Header
  	// HTTP/1.1
	header("Cache-Control: no-store, no-cache, must-revalidate");
	
	// HTTP/1.0
	header("Pragma: no-cache");

	// Charset
	header('Content-type: text/html; charset=ISO-8859-1');

// ---- Charge les informations standards
	if (!file_exists("config/config.inc.php"))
	{
		$res=array();
		$res["result"]="Fichier de configuration introuvable";
	  	echo json_encode($res);
		exit;
	}

  	require ("modules/fonctions.inc.php");
  	require ("config/config.inc.php");
  	require ("config/variables.inc.php");

	if ($MyOpt["timezone"]!="")
	  { date_default_timezone_set($MyOpt["timezone"]); }

// ---- Se connecte ра la base MySQL
	require ("class/mysql.inc.php");
	$sql = new mysql_class($mysqluser, $mysqlpassword, $hostname, $db,$port);

// ---- Charge les informations de l'utilisateur connectщ
	require ("class/user.inc.php");
	$myuser = new user_class($uid,$sql,true);

	$token=$uid;
	$module="modules";

// ---- Vщrifie la variable $mod
	$mod=$_REQUEST["mod"];
	if (!preg_match("/^[a-z0-9_]*$/",$mod))
	  { $mod = ""; }
	if (trim($mod)=="")
	  { $mod = ""; }

// ---- Vщrifie la variable $rub
	$rub=$_REQUEST["rub"];
	if (!preg_match("/^[a-z0-9_]*$/",$rub))
	  { $rub = ""; }
	if (trim($rub)=="")
	  { $rub = ""; }

// ---- Charge la page
	if (($mod!="") && ($rub!=""))
	{
		require("modules/".$mod."/".$rub.".inc.php");
	}
	else
	{
		echo "{  \"result\": \"\" }\n";
	}
 ?>