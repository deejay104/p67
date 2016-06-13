<?
	$fh=date("O",floor($jstart)/1000+4*3600)/100;
	$jstart=date("Y-m-d H:i:s",floor($jstart)/1000-$fh*3600);
	$fh=date("O",floor($jend)/1000+4*3600)/100;
	$jend=date("Y-m-d H:i:s",floor($jend)/1000-$fh*3600);
	
	if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
		die("Please provide an event id.");
	}


	$query="SELECT edite FROM ".$MyOpt["tbl"]."_calendrier WHERE id='".$_GET['id']."'";
	$res=$sql->QueryRow($query);

	if ($res["edite"]=='oui')
	  {	
			$query="UPDATE ".$MyOpt["tbl"]."_calendrier SET dte_deb='".$jstart."',dte_fin='".$jend."' WHERE id='".$_GET['id']."'";
			$sql->Update($query);
		}
	exit;
?>