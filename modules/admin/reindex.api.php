<?
// ---- Refuse l'accès en direct
	if ((!isset($token)) || ($token==""))
	  { header("HTTP/1.0 401 Unauthorized"); exit; }

// ---- 
	$ret=array();
	$ret["result"]="OK";
	$ret["data"]="";

	$sql->show=true;

function AjoutLog($txt)
{
	return htmlentities($txt)."<br />";
}	


// ---- Charge la structure des tables en base
	$ret["data"].=AjoutLog("Suppression des index");

	$tabProd=array();
	$q="SHOW TABLES;";
	$sql->Query($q);
	for($i=0; $i<$sql->rows; $i++)
	{
		$sql->GetRow($i);
		$tabProd[$sql->data["Tables_in_".$db]]=array();
	}
	
	foreach($tabProd as $tab=>$t)
	{
		$q="DESCRIBE ".$tab.";";
		$sql->Query($q);
		for($i=0; $i<$sql->rows; $i++)
		{
			$sql->GetRow($i);
			$tabProd[$tab][$sql->data["Field"]]["Type"]=$sql->data["Type"];
			if ($sql->data["Default"]!="")
			{
				$tabProd[$tab][$sql->data["Field"]]["Default"] = $sql->data["Default"];
			}
		}
		$q="SHOW INDEX FROM ".$tab.";";
		$sql->Query($q);
		for($i=0; $i<$sql->rows; $i++)
		{
			$sql->GetRow($i);
			$tabProd[$tab][$sql->data["Column_name"]]["Index"]=($sql->data["Key_name"]=="PRIMARY") ? "PRIMARY" : $sql->data["Key_name"];
		}
	}

	foreach($tabProd as $tab=>$fields)
	{
		foreach($fields as $field=>$d)
		{
			if ( (isset($tabProd[$tab][$field]["Index"])) && ($tabProd[$tab][$field]["Index"]!="PRIMARY") )
			{
				$q="ALTER TABLE `".$tab."` DROP INDEX `".$tabProd[$tab][$field]["Index"]."`";
				$res=$sql->Update($q);
				if ($res==-1)
				{
					$ret["result"]="NOK";
					$ret["data"].=AjoutLog(" ! Erreur suppression index ".$MyOpt["tbl"]."_".$tab.":".$field);	
				}
				else
				{
					$ret["data"].=AjoutLog(" - Suppresion Index ".$MyOpt["tbl"]."_".$tab.":".$field);	
				}
			}
		}
	}
	
// ---- Renvoie le log
	echo json_encode($ret);
  
?>