<?
	if (!is_numeric($nid))
	  {
	  	echo "ID not provided.";
			error_log("ID not provided. : ".$nid);
	  	exit;
	  }

	$q="SELECT uid_creat FROM ".$MyOpt["tbl"]."_navigation WHERE id='".$nid."' LIMIT 1";
	$res=$sql->QueryRow($q);

	if (($res["uid_creat"]==$uid) || (GetDroit("ModifNavigation")))
	  {

			if (is_array($_POST['id']))
			  {
					$i = 1;
					foreach ($_POST['id'] as $wpid)
					{
					    if (is_numeric($wpid))
					      {
							  	$q="UPDATE ".$MyOpt["tbl"]."_navroute SET ordre='".$i."' WHERE id='".$wpid."' AND idnav='".$nid."'";
									$sql->Update($q);
								}
		
					    $i++;
					}
				}
			else
			  {
			  	echo("No data provided.");
			  }
	  }
	else
	  {
	  	echo("Access denied.");
	  }
	exit;
?>