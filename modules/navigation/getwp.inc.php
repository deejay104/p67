<?
// ---- Refuse l'accès en direct
	if ((!isset($token)) || ($token==""))
	  { header("HTTP/1.0 401 Unauthorized"); exit; }

// ---- 
	$result=array();

	$query="SELECT nom,description FROM ".$MyOpt["tbl"]."_navpoints WHERE (nom LIKE '%".$_REQUEST["term"]."%' OR description LIKE '%".$_REQUEST["term"]."%') ".(($_REQUEST["type"]!="") ? " AND icone='".addslashes($_REQUEST["type"])."'" : "")." LIMIT 0,20";
	$sql->Query($query);
	for($i=0; $i<$sql->rows; $i++)
	{
		$sql->GetRow($i);
//		array_push($result,array(strtoupper($sql->data["nom"]),strtoupper($sql->data["nom"]." - ".$sql->data["description"])));
		$r=array();
		$r["value"]=strtoupper($sql->data["nom"]);
		$r["label"]=$sql->data["nom"]." : ".$sql->data["description"];
		
		array_push($result,$r);
	}
	
	echo json_encode($result);
?>