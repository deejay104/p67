<?
	
	$query="SELECT id FROM `".$MyOpt["tbl"]."_droits`";
	$sql->Query($query);
	
	if ($sql->rows==0)
	{
		$query="SELECT id,droits FROM `".$MyOpt["tbl"]."_utilisateurs` WHERE actif='oui'";
		$sql->Query($query);
		$tusr=array();
		for($i=0; $i<$sql->rows; $i++)
		{ 
			$sql->GetRow($i);
			if ($sql->data["droits"]!="")
			{
				$tusr[$sql->data["id"]]=$sql->data["droits"];
			}
		}

		foreach($tusr as $id=>$grp)
		{
			$trole=preg_split("/,/",$grp);
			foreach ($trole as $grp)
		  	{
				$query ="INSERT INTO ".$MyOpt["tbl"]."_droits (`groupe` ,`uid`) ";
				$query.="VALUES ('$grp' , '$id')";
				$sql->Insert($query);
		  	}
		}
	}
?>