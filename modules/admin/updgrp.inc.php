<?
// ---- Refuse l'accès en direct
	if ((!isset($token)) || ($token==""))
	  { header("HTTP/1.0 401 Unauthorized"); exit; }

// ---- Vérifie les paramètres
	if (!isset($_GET["grp"]))
	{
	  	echo "GRP not provided.";
		error_log("GRP not provided.");
	  	exit;
	}
	$grp=$_GET["grp"];
	$grp=substr($grp,0,5);


	if (GetDroit("ModifGroupe"))
	{
		if (is_array($_POST['id']))
		{
			$q="DELETE FROM ".$MyOpt["tbl"]."_roles WHERE groupe='$grp'";
			$res=$sql->Delete($q);

			foreach ($_POST['id'] as $role)
			{
				$q="INSERT INTO ".$MyOpt["tbl"]."_roles SET groupe='$grp',role='$role'";
				$sql->Insert($q);
			}
		}
		else
		{
			$res=array();
			$res["result"]=utf8_encode("Pas de données à mettre à jour");
			echo json_encode($res);
		}
	}
	else
	{
		$res=array();
		$res["result"]=utf8_encode("Accès non authorisé");
	  	echo json_encode($res);
	}

?>