<?
// ---- Refuse l'accès en direct
	if ((!isset($token)) || ($token==""))
	  { header("HTTP/1.0 401 Unauthorized"); exit; }

// ---- Vérifie les paramètres
	$ret=array();
	if (!isset($_GET["mois"]))
	{
		$ret["result"]=utf8_encode("NOK");
		echo json_encode($ret);
		error_log("mois not provided.");
	  	exit;
	}
	$mois=$_GET["mois"];

	if (!isset($_GET["dte"]))
	{
		$ret["result"]=utf8_encode("NOK");
		echo json_encode($ret);
		error_log("dte not provided.");
	  	exit;
	}
	$dte=$_GET["dte"];

	if (!isset($_GET["ress"]))
	{
		$ret["result"]=utf8_encode("NOK");
		echo json_encode($ret);
		error_log("ress not provided.");
	  	exit;
	}
	$ress=$_GET["ress"];

	if (!isset($_GET["var"]))
	{
		$ret["result"]=utf8_encode("NOK");
		echo json_encode($ret);
		error_log("var not provided.");
	  	exit;
	}
	$var=$_GET["var"];

	if ((!is_numeric($mois)) && (($mois<1) || ($mois>12)))
	{
		$ret["result"]=utf8_encode("NOK");
		error_log("mois is not a number");
		echo json_encode($ret);
	  	exit;
	}
	if ((!is_numeric($dte)) && ($mois<1))
	{
		$ret["result"]=utf8_encode("NOK");
		error_log("dte is not a number");
		echo json_encode($ret);
	  	exit;
	}
	if ((!is_numeric($ress)) && ($ress<1))
	{
		$ret["result"]=utf8_encode("NOK");
		error_log("ress is not a number");
		echo json_encode($ret);
	  	exit;
	}
	if ((!is_numeric($var)) && ($var<1))
	{
		$ret["result"]=utf8_encode("NOK");
		error_log("var is not a number");
		echo json_encode($ret);
	  	exit;
	}

	
// ---- Update la valeur
	$sql->show=false;

	$q="SELECT id FROM ".$MyOpt["tbl"]."_prevision WHERE annee='".$dte."' AND mois='".$mois."' AND avion='".$ress."'";
	$res=$sql->QueryRow($q);

	if ($res["id"]>0)
	{
		$r=$sql->Edit("_prevision",$MyOpt["tbl"]."_prevision",$res["id"],array("heures"=>$var));
	}
	else
	{
		$r=$sql->Edit("_prevision",$MyOpt["tbl"]."_prevision",$res["id"],array("annee"=>$dte,"mois"=>$mois,"avion"=>$ress,"heures"=>$var));
	}

	if ($r=="NOK")
	{
		$ret["result"]="NOK";
	}
	else
	{
		$ret["result"]="OK";
	}
		
	// $q="SELECT id FROM ".$MyOpt["tbl"]."_prevision WHERE annee='".$dte."' AND mois='".$mois."' AND avion='".$ress."'";
	// $res=$sql->QueryRow($q);

	// if ($res["id"]>0)
	// {
		// $q="UPDATE ".$MyOpt["tbl"]."_prevision SET heures='".$var."' WHERE id='".$res["id"]."'";
		// $nb=$sql->Update($q);
		// if ($sql->mysql_ErrorMsg=="")
		// {
			// $ret["result"]=utf8_encode("OK");
		// }
		// else
		// {
			// $ret["result"]=utf8_encode("NOK");
		// }
	// }
	// else
	// {
		// $q="INSERT ".$MyOpt["tbl"]."_prevision SET annee='".$dte."', mois='".$mois."', avion='".$ress."', heures='".$var."'";
		// $id=$sql->Insert($q);
		// if ($id>0)
		// {
			// $ret["result"]=utf8_encode("OK");
		// }
		// else
		// {
			// $ret["result"]=utf8_encode("NOK");
		// }
	// }
		// error_log($q);

	echo json_encode($ret);

?>