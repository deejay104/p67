<?php
// ---- Refuse l'accès en direct
	if ((!isset($token)) || ($token==""))
	  { header("HTTP/1.0 401 Unauthorized"); exit; }

// ---- Vérifie les paramètres
	require_once ("class/manifestation.inc.php");


	// Short-circuit if the client did not give us a date range.
	if (!isset($_GET['start']) || !isset($_GET['end'])) {
		die("Please provide a date range.");
	}


	$start=$_GET['start'];
	$end=$_GET['end'];

	$ii=0;
	
	// Affichage des manifestations
	$tmanip=GetManifestation($sql,$start,$end);

	if (is_array($tmanip))
	  {
			foreach($tmanip as $r)
			  {
					$m=new manip_class($r,$sql);
		
					$input_arrays[$ii]["id"]=$m->id;
					$input_arrays[$ii]["title"]=utf8_encode($m->titre);
					$input_arrays[$ii]["start"]=date("c",strtotime($m->dte_manip));
					$input_arrays[$ii]["end"]=date("c",strtotime($m->dte_manip));
					$input_arrays[$ii]["color"]='#38a9e3';
					$ii=$ii+1;
			  }
		}

	// Send JSON to the client.
	echo json_encode($input_arrays);

?>