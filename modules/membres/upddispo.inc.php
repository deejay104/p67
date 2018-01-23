<?
// ---- Refuse l'accès en direct
	if ((!isset($token)) || ($token==""))
	  { header("HTTP/1.0 401 Unauthorized"); exit; }

// ---- Vérifie les paramètres
	// Short-circuit if the client did not give us a date range.
	if (!isset($_GET['jstart']) || !isset($_GET['jend'])) {
		die("Please provide a date range.");
	}

	$jstart=$_GET['jstart'];
	$jend=$_GET['jend'];

	$fh=date("O",floor($jstart)/1000+4*3600)/100;
	$jstart=date("Y-m-d H:i:s",floor($jstart)/1000-$fh*3600);
	$fh=date("O",floor($jend)/1000+4*3600)/100;
	$jend=date("Y-m-d H:i:s",floor($jend)/1000-$fh*3600);
	
	
	if ($_GET["id"]==0)
	{
		if (!isset($_GET['mid']) || !is_numeric($_GET['mid'])) {
			die("Please provide a member id.");
		}
		$mid=$_GET['mid'];
		$query="INSERT ".$MyOpt["tbl"]."_disponibilite SET uid='".$mid."', dte_deb='".$jstart."',dte_fin='".$jend."', uid_maj='".$gl_uid."', dte_maj='".now()."'";
		$sql->Insert($query);
	}
	else
	{
		if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
			die("Please provide an event id.");
		}

		$id=$_GET['id'];

		$query="UPDATE ".$MyOpt["tbl"]."_disponibilite SET dte_deb='".$jstart."',dte_fin='".$jend."' WHERE id='".$id."'";
		$sql->Update($query);
	}
?>