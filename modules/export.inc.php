<?
	$tabExport=array();

//SELECT dte, COUNT(DISTINCT uid) FROM p67_presence WHERE dte LIKE '2012%' GROUP BY dte
		
	if (($list=="presence") && (is_numeric($dte)))
	  {
	  	$dtey=substr($dte,0,4);
	  	if (!is_numeric($dtey))
	  	  { $dtey="0"; }
		$query ="SELECT presence.dte, usr.prenom, usr.nom, CONCAT(UPPER(usr.nom),' ',usr.prenom) AS membre, usr.num_caf, presence.type AS typeid, type.nom AS type, type.libelle, presence.zone, presence.regime, presence.age, presence.handicap, SUM(tpspaye) AS tpspaye, SUM(tpsreel) AS tpsreel ";
		$query.="FROM ".$MyOpt["tbl"]."_presence AS presence ";
		$query.="LEFT JOIN ".$MyOpt["tbl"]."_utilisateurs AS usr ON presence.uid=usr.id ";
		$query.="LEFT JOIN ".$MyOpt["tbl"]."_type AS type ON presence.type=type.id ";
		$query.="WHERE presence.dte like '".$dtey."%' GROUP BY presence.dte,presence.uid,presence.type";

		$sql->Query($query);
		for($i=0; $i<$sql->rows; $i++)
		  {
			$sql->GetRow($i);
			$tabExport[$i]=ConvertToArray($sql->data);
			//$tabExport[$i]["membres"]=$sql->data["prenom"]." ".$sql->data["nom"];
		  }


	  }
	else if (($list=="facturation") && (is_numeric($dte)))
	  {
	  	$dtey=substr($dte,0,4);
	  	if (!is_numeric($dtey))
	  	  { $dtey="0"; }
		$query ="SELECT cpt.dte,usr.prenom,usr.nom,CONCAT(UPPER(usr.nom),' ',usr.prenom) AS membre, usr.num_caf, usr.zone, usr.regime, FLOOR(DATEDIFF('".($dte-1)."-12-31',dte_naissance)/365) AS age, cpt.compte, SUM(cpt.montant) AS montant ";
		$query.="FROM ".$MyOpt["tbl"]."_compte AS cpt ";
		$query.="LEFT JOIN ".$MyOpt["tbl"]."_utilisateurs AS usr ON cpt.tiers=usr.id ";
		$query.="WHERE cpt.uid=".$MyOpt["uid_club"]." AND cpt.mouvement<>'Réglement' AND cpt.dte like '".$dtey."%' ";
		$query.="GROUP BY cpt.dte,cpt.tiers, cpt.compte ";
		$query.="ORDER BY cpt.dte,membre,cpt.compte";

		$sql->Query($query);
		for($i=0; $i<$sql->rows; $i++)
		  {
			$sql->GetRow($i);
			$tabExport[$i]=ConvertToArray($sql->data);
			//$tabExport[$i]["membres"]=$sql->data["prenom"]." ".$sql->data["nom"];
		  }


	  }

	else if ($list=="famille")
	  {
		$lstusr=ListActiveUsers($sql,"","enfant");

		$tabValeur=array();
		foreach($lstusr as $i=>$id)
		  {
			$usr = new user_class($id,$sql,false);

			if (GetModule("aviation"))
			  { $usr->LoadLache(); }
			if (GetModule("creche"))
			  { $usr->LoadEnfants(); }		

			foreach($usr->data as $key=>$v)
			  {
			  	$val=$usr->Aff($key,"val");
			  	if ($val!="******")
				  { $tabExport[$i][$key]=$val; }
			  }
		  }
	  }
	else
	  {
		FatalError( "L'export demandé n'existe pas.");
	  }


	if (count($tabExport)>0)
	  {
		header("Content-Type: application/vnd.ms-excel;");
		header('Content-Disposition: filename="export.csv";');

	  	foreach($tabExport[0] as $key=>$val)
	  	  {
	  	  	echo $key.";";
	  	  }
		echo "\n";
		
		foreach($tabExport as $i=>$txt)
		  {
		  	foreach($txt as $key=>$val)
		  	  {
				$v=preg_replace("/\n/"," ",$val);
				$v=preg_replace("/\r/","",$v);
				$v=preg_replace("/;/",",",$v);

		  	  	echo $v.";";
		  	  }
			echo "\n";
		  }
		exit;
	  }
	else
	  {
		FatalError( "La liste est vide.");
	  }


function ConvertToArray($tab)
  {
  	$ret=array();
  	foreach($tab as $k => $v)
  	  {
  	  	if (!is_numeric($k))
  	  	  {
  	  	  	$ret[$k]=$v;
  	  	  }
  	  }
  	return $ret;
  }
?>