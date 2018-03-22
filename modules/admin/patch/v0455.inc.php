<?
	
if (file_exists("config/role.inc.php")
{
	$query="TRUNCATE ".$MyOpt["tbl"]."_roles";
	$sql->Update($query);

	require_once ("config/roles.inc.php");

	foreach($Droits as $grp=>$tr)
	{
		foreach($tr as $role=>$d)
		{
			$query="INSERT INTO ".$MyOpt["tbl"]."_roles SET groupe='".$grp."',role='".$role."'";
			$sql->Insert($query);
		}

	}
}
	
?>