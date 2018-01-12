<?
// ---- Refuse l'accès en direct
	if ((!isset($token)) || ($token==""))
	  { header("HTTP/1.0 401 Unauthorized"); exit; }

// ---- Vérifie les paramètres
	if (!isset($_GET['jstart']))
	{
		die("Please provide a date range.");
	}

	$jstart=$_GET['jstart'];
	$jend=$_GET['jend'];

	$fh=date("O",floor($jstart)/1000+4*3600)/100;
	$jstart=date("Y-m-d H:i:s",floor($jstart)/1000-$fh*3600);
	$fh=date("O",floor($jend)/1000+4*3600)/100;
	$jend=date("Y-m-d H:i:s",floor($jend)/1000-$fh*3600);
	
	if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
		die("Please provide an event id.");
	}

	$id=$_GET['id'];
	
	$query="UPDATE ".$MyOpt["tbl"]."_manips SET dte_manip='".$jstart."' WHERE id='".$id."'";
	$sql->Update($query);

?>