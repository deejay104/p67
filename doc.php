<?
// ---- Gestion des droits
	session_start();

	if ((isset($_SESSION['uid'])) && ($_SESSION['uid']>0))
	  { $uid = $_SESSION['uid']; }
	else
	  { echo "Access denied !"; exit; }

// ---- Variables
	if ($_REQUEST["id"]!="")
	  {
		$id=$_REQUEST["id"];
	  }
	else
	  {
		echo "Incorrect filename."; exit;
	  }

	if (!file_exists("config/config.inc.php"))
	  { echo "Fichier de configuration introuvable","Il manque le fichier de configuration 'config/config.inc.php'."; exit;}

  	require ("modules/fonctions.inc.php");
  	require ("config/config.inc.php");
  	require ("config/variables.inc.php");

// ---- Se connecte � la base MySQL
	require ("class/mysql.inc.php");
	$sql = new mysql_class($mysqluser, $mysqlpassword, $hostname, $db,$port);

// ---- Charge les informations de l'utilisateur connect�
	require ("class/user.inc.php");
	$myuser = new user_class($uid,$sql,true);

// ---- Charge le document
	require ("class/document.inc.php");
	$doc = new document_class($id,$sql);

// ---- Delete document
	if ($_REQUEST["fonc"]=="delete")
	{
		$doc->delete();
		echo "<script>opener.location.reload(); window.close();</script>";
		exit;		
	}


//	header("Content-Type: image/jpeg");
//	header('Content-Disposition: inline; filename="'.substr($name,strrpos($name,"/")+1,strlen($name)-strrpos($name,"/")).'";');

// ---- Renvoie le contenu du fichier
	$doc->Download($_REQUEST["GET"]["mode"]);

?>
