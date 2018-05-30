<?

	$query="SELECT id,type FROM ".$MyOpt["tbl"]."_utilisateurs WHERE virtuel='non' AND actif='oui'";
	$sql->Query($query);
	
	$tabUser=array();
	for($i=0; $i<$sql->rows; $i++)
	{ 
		$sql->GetRow($i);
		if ($sql->data["type"]=="pilote")
		{
			$tabUser[$sql->data["id"]]="PIL";
		}
		else if ($sql->data["type"]=="instructeur")
		{
			$tabUser[$sql->data["id"]]="INS";
		}
		else if ($sql->data["type"]=="eleve")
		{
			$tabUser[$sql->data["id"]]="ELE";
		}
		else if ($sql->data["type"]=="membre")
		{
			$tabUser[$sql->data["id"]]="MEM";
		}
		else if ($sql->data["type"]=="invite")
		{
			$tabUser[$sql->data["id"]]="INV";
		}
	}
	
	foreach($tabUser as $id=>$grp)
	{
		$query="UPDATE ".$MyOpt["tbl"]."_utilisateurs SET groupe='".$grp."' WHERE id=".$id;
		$sql->Update($query);
		$query="DELETE FROM ".$MyOpt["tbl"]."_droits WHERE groupe='".$grp."' AND uid=".$id;
		$sql->Delete($query);
		$query="INSERT INTO ".$MyOpt["tbl"]."_droits SET groupe='".$grp."', uid=".$id.", uid_creat=0, dte_creat='".now()."'";
		$sql->Delete($query);
	}
?>