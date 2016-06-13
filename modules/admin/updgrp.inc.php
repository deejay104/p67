<?
	if (!isset($grp))
	  {
	  	echo "GRP not provided.";
			error_log("GRP not provided.");
	  	exit;
	  }
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
			  	echo("No data provided.");
			  }
	  }
	else
	  {
	  	echo("Access denied.");
	  }
	exit;
?>