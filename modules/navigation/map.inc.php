<?
// ---- Refuse l'accès en direct
	if ((!isset($token)) || ($token==""))
	  { header("HTTP/1.0 401 Unauthorized"); exit; }

// ----
	$l=640;
	$h=480;

	$nid=$_REQUEST['nid'];

	if (!is_numeric($nid))
	{
	  	echo "ID not provided.";
		error_log("ID not provided. : ".$nid);
	  	exit;
	}

// ---- Header de la page
	header("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Pragma: no-cache");
	header('Content-type: image/png');

// ---- Create image
	$img = imagecreate($l+20, $h+10);
	$white = imagecolorallocate ($img, 255, 255, 255);
	$black = imagecolorallocate($img, 0, 0, 0);
	$grey1 = imagecolorallocate($img, 240, 240, 240);
	$grey2 = imagecolorallocate($img, 170, 170, 170);
	$textcolor = imagecolorallocate($img, 0, 0, 0);

	imagefill($img,0,0,$white); 

	error_log("start");

// ---- Load trace
	$q="SELECT MIN(lon) AS minx,MIN(lat) AS miny, MAX(lon) AS maxx, MAX(lat) AS maxy FROM ".$MyOpt["tbl"]."_navroute AS rte WHERE rte.idnav='".$nid."'";
	$query="SELECT MIN(wpt.lon) AS minx,MIN(wpt.lat)  AS miny,MAX(wpt.lon) AS maxx,MAX(wpt.lat) AS maxy FROM ".$MyOpt["tbl"]."_navroute AS rte LEFT JOIN ".$MyOpt["tbl"]."_navpoints AS wpt ON rte.nom=wpt.nom WHERE rte.idnav='".$nid."'";
	error_log($q);
	$res=$sql->QueryRow($query);


	$query="SELECT rte.id,rte.nom,wpt.description,wpt.lon,wpt.lat FROM ".$MyOpt["tbl"]."_navroute AS rte LEFT JOIN ".$MyOpt["tbl"]."_navpoints AS wpt ON rte.nom=wpt.nom WHERE rte.idnav='".$nid."' ORDER BY ordre";
	$sql->Query($query);

	error_log($query);		

	$tabPoints=array();
	$lastx=0;
	$lasty=0;

	for($i=0; $i<$sql->rows; $i++)
	{
		$sql->GetRow($i);
		
		$newx=10+($sql->data["lon"]-$res["minx"])*($l-50)/($res["maxx"]-$res["minx"]);
		$newy=$h-($sql->data["lat"]-$res["miny"])*$h/($res["maxy"]-$res["miny"]);

		error_log("TRACE: minx:".$res["minx"]." maxx:".$res["maxx"]." x:".$sql->data["lon"]." newx:".$newx);		
		if ($lastx==0)
		{
			$lastx=$newx;
		}
		if ($lasty==0)
		{
			$lasty=$newy;
		}
		imageline($img,$lastx,$lasty,$newx,$newy,$black);
		imagestring($img, 2, $newx, $newy, $sql->data["nom"], $textcolor);

		$lastx=$newx;
		$lasty=$newy;
	}


// ---- Show image	
	imagepng($img);

?>