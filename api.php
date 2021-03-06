<?
// ---- Autorisation d'acc�s
	session_start();
	require ("class/mysql.inc.php");

	if (file_exists("config/config.inc.php"))
	{
		require ("config/config.inc.php"); 
	}
	else
	{
		header("HTTP/1.0 401 Unauthorized"); exit;
	}
	if (file_exists("config/variables.inc.php"))
	  { require ("config/variables.inc.php"); }

	if ((isset($_SESSION['uid'])) && ($_SESSION['uid']>0))
	{
		$uid = $_SESSION['uid'];
	}
	else if (($_REQUEST["mod"]=="admin") && ($_REQUEST["rub"]=="update"))
	{
 		$sql = new mysql_class($mysqluser, $mysqlpassword, $hostname, $db,$port);
		$sql->show=false;
		$query = "SELECT * FROM ".$MyOpt["tbl"]."_config";
		$res  = $sql->QueryRow($query);
		
		if (!is_array($res))
		{
			$uid=0;
			$token="sys";
		}
		else
		{
			header("HTTP/1.0 401 Unauthorized"); exit;
		}

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

	if ($MyOpt["timezone"]!="")
	  { date_default_timezone_set($MyOpt["timezone"]); }

// ---- Se connecte � la base MySQL
	$sql = new mysql_class($mysqluser, $mysqlpassword, $hostname, $db,$port);

// ---- Charge les informations de l'utilisateur connect�
	require ("class/user.inc.php");
	if ($uid>0)
	{
		$myuser = new user_class($uid,$sql,true);
		$token=$uid;
	}

	$module="modules";
	$gl_mode="api";

// ---- V�rifie la variable $mod
	$mod=$_REQUEST["mod"];
	if (!preg_match("/^[a-z0-9_]*$/",$mod))
	  { $mod = ""; }
	if (trim($mod)=="")
	  { $mod = ""; }

// ---- V�rifie la variable $rub
	$rub=$_REQUEST["rub"];
	if (!preg_match("/^[a-z0-9_]*$/",$rub))
	  { $rub = ""; }
	if (trim($rub)=="")
	  { $rub = ""; }

// ---- Charge la page
	if (($mod!="") && ($rub!=""))
	{
		if (file_exists("modules/".$mod."/".$rub.".api.php"))
		{
			require("modules/".$mod."/".$rub.".api.php");
		}
		else if (file_exists("modules/".$mod."/".$rub.".inc.php"))
		{
			require("modules/".$mod."/".$rub.".inc.php");
		}
		else
		{
			echo "{  \"result\": \"File not found\" }\n";
		}
	}
	else
	{
		echo "{  \"result\": \"\" }\n";
	}
 ?>