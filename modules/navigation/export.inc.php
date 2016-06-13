<?
	if (!is_numeric($id))
	  { $id=0; }


	if ($id>0)
	  {

			$query="SELECT titre,uid_creat FROM ".$MyOpt["tbl"]."_navigation WHERE id='".$id."'";
			$res=$sql->QueryRow($query);

			$usr = new user_class($res["uid_creat"],$sql,false);
	
			header('Content-Type: text/xml');
			header('Content-Disposition: attachment; filename="'.preg_replace("/ /s","_",$res["titre"]).'.gpx"');
	
			echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
			echo "<gpx     xmlns=\"http://www.topografix.com/GPX/1/1\"\n";
			echo "    xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"\n";
			echo "    creator=\"".$usr->aff("fullname","val")."\"\n";
			echo "    version=\"1.1\"\n";
			echo "    xsi:schemaLocation=\"http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd\">\n";
			echo "    <rte>\n";
			echo "      <name>".$res["titre"]."</name>\n";
	/*
	        <name>LFGC to LFGC</name>
	        <rtept lat="48.553612" lon="7.777500">
	            <name>LFGC</name>
	            <overfly>false</overfly>
	        </rtept>
	        <rtept lat="48.110279" lon="7.359167">
	            <name>LFGA</name>
	            <overfly>false</overfly>
	        </rtept>
	        <rtept lat="48.796944" lon="7.819167">
	            <name>LFSH</name>
	            <overfly>false</overfly>
	        </rtept>
	        <rtept lat="48.553612" lon="7.777500">
	            <name>LFGC</name>
	            <overfly>false</overfly>
	        </rtept>
	*/
			$query="SELECT rte.nom,wpt.description,lat,lon FROM ".$MyOpt["tbl"]."_navroute AS rte LEFT JOIN ".$MyOpt["tbl"]."_navpoints AS wpt ON rte.nom=wpt.nom WHERE rte.idnav='".$id."' ORDER BY ordre";
			$sql->Query($query);
			for($i=0; $i<$sql->rows; $i++)
			{
				$sql->GetRow($i);
				echo "      <rtept lat=\"".$sql->data["lat"]."\" lon=\"".$sql->data["lon"]."\">\n";
				echo "        <name>".$sql->data["nom"]."</name>\n";
				echo "        <overfly>false</overfly>\n";
				echo "      </rtept>\n";
			}
			echo "    </rte>\n";
			echo "</gpx>\n";
	
			exit;
		}

?>

