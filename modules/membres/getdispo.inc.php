<?php
// ---- Refuse l'accès en direct
	if ((!isset($token)) || ($token==""))
	  { header("HTTP/1.0 401 Unauthorized"); exit; }

// ---- Vérifie les paramètres
	// Short-circuit if the client did not give us a date range.
	if (!isset($_GET['start']) || !isset($_GET['end'])) {
		die("Please provide a date range.");
	}

	if (!isset($_GET['mid']) || !is_numeric($_GET['mid'])) {
		die("Please provide a member id.");
	}

	$mid=$_GET['mid'];
	$start=$_GET['start'];
	$end=$_GET['end'];

	$input_arrays=array();

// ---- Charge les disponibilités
	$usr=new user_class($mid,$sql,false,true);

	$q="SELECT * FROM ".$MyOpt["tbl"]."_disponibilite WHERE dte_fin>='".$start." 00:00:00' AND dte_deb<='".$end." 00:00:00' AND uid='".$mid."'";
	$sql->Query($q);
	for($i=0; $i<$sql->rows; $i++)
	{
		$sql->GetRow($i);

		$input_arrays[$i]["id"]=$sql->data["id"];
		$input_arrays[$i]["title"]=utf8_encode(($usr->data["disponibilite"]=="dispo") ? "Occupé" : "Disponible");
		$input_arrays[$i]["start"]=date("c",strtotime($sql->data["dte_deb"]));
		$input_arrays[$i]["end"]=date("c",strtotime($sql->data["dte_fin"]));
	}
	
// ---- Send JSON to the client.
	echo json_encode($input_arrays);
	
?>
  