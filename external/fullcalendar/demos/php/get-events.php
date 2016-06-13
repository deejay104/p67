<?php

//--------------------------------------------------------------------------------------------------
// This script reads event data from a JSON file and outputs those events which are within the range
// supplied by the "start" and "end" GET parameters.
//
// An optional "timezone" GET parameter will force all ISO8601 date stings to a given timezone.
//
// Requires PHP 5.2.0 or higher.
//--------------------------------------------------------------------------------------------------

// Require our Event class and datetime utilities
require dirname(__FILE__) . '/utils.php';

// Short-circuit if the client did not give us a date range.
if (!isset($_GET['start']) || !isset($_GET['end'])) {
	die("Please provide a date range.");
}

// Parse the start/end parameters.
// These are assumed to be ISO8601 strings with no time nor timezone, like "2013-12-29".
// Since no timezone will be present, they will parsed as UTC.
$range_start = parseDateTime($_GET['start']);
$range_end = parseDateTime($_GET['end']);

$start=$_GET['start'];
$end=$_GET['end'];

// Parse the timezone parameter if it is present.
$timezone = null;
if (isset($_GET['timezone'])) {
	$timezone = new DateTimeZone($_GET['timezone']);
}

// Read and parse our events JSON file into an array of event data arrays.
//$json = file_get_contents(dirname(__FILE__) . '/../json/events.json');
//$input_arrays = json_decode($json, true);

	require ("../../../../class/mysql.inc.php");
	require ("../../../../config/config.inc.php");

	$sql   = new mysql_class($mysqluser, $mysqlpassword, $hostname, $db);

	$ii=0;

	$MyOpt["tbl"]="p67";
	$ress=4;
	$query ="SELECT cal.id,cal.dte_deb,cal.dte_fin,usr.nom AS nom ,usr.prenom AS prenom,usr.initiales,ins.nom AS insnom,ins.prenom AS insprenom,avion.immatriculation ";
	$query.="FROM ".$MyOpt["tbl"]."_calendrier AS cal ";
	$query.="LEFT JOIN ".$MyOpt["tbl"]."_utilisateurs AS usr ON cal.uid_pilote=usr.id ";
	$query.="LEFT JOIN ".$MyOpt["tbl"]."_utilisateurs AS ins ON cal.uid_instructeur=ins.id ";
	$query.="LEFT JOIN ".$MyOpt["tbl"]."_ressources AS avion ON cal.uid_avion=avion.id ";
	$query.="WHERE cal.actif='oui' AND cal.dte_fin>='".$start." 00:00:00' AND cal.dte_deb<='".$end." 00:00:00' AND cal.uid_avion='$ress' ORDER BY cal.dte_deb";

	$sql->Query($query);
	for($i=0; $i<$sql->rows; $i++)
	  {
			$sql->GetRow($i);
			$input_arrays[$ii]["title"]=$sql->data["initiales"];
			$input_arrays[$ii]["start"]=date("c",strtotime($sql->data["dte_deb"]));
			$input_arrays[$ii]["end"]=date("c",strtotime($sql->data["dte_fin"]));
			$ii=$ii+1;
		}

	$input_arrays[$ii]["title"]="";
	$input_arrays[$ii]["start"]=date("c",strtotime("2016-03-27 0:00"));
	$input_arrays[$ii]["end"]=date("c",strtotime("2016-03-27 6:00"));
	$input_arrays[$ii]["color"]='gray';
	$input_arrays[$ii]["rendering"]='background';
	$ii=$ii+1;
	$input_arrays[$ii]["title"]="";
	$input_arrays[$ii]["start"]=date("c",strtotime("2016-03-28 0:00"));
	$input_arrays[$ii]["end"]=date("c",strtotime("2016-03-28 6:10"));
	$input_arrays[$ii]["color"]='gray';
	$input_arrays[$ii]["rendering"]='background';
	$ii=$ii+1;
	$input_arrays[$ii]["title"]="";
	$input_arrays[$ii]["start"]=date("c",strtotime("2016-03-29 0:00"));
	$input_arrays[$ii]["end"]=date("c",strtotime("2016-03-29 6:20"));
	$input_arrays[$ii]["color"]='gray';
	$input_arrays[$ii]["rendering"]='background';
	$ii=$ii+1;


// Accumulate an output array of event data arrays.
$output_arrays = array();
foreach ($input_arrays as $array) {

	// Convert the input array into a useful Event object
	$event = new Event($array, $timezone);

	// If the event is in-bounds, add it to the output
	if ($event->isWithinDayRange($range_start, $range_end)) {
		$output_arrays[] = $event->toArray();
	}
}

// Send JSON to the client.
echo json_encode($input_arrays);