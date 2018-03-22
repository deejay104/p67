<?
	
	$query="SELECT id FROM `".$MyOpt["tbl"]."_echeance`";
	$sql->Query($query);
	
	if ($sql->rows==0)
	{
		// Convertir toutes les dates actuelles en échéances
		// Créé le type d'échéances
		// - Médicale
		// - PPL
		$q[]="INSERT INTO ".$MyOpt["tbl"]."_echeancetype SET id=1,description='SEP', resa='instructeur',multi='non';";
		$q[]="INSERT INTO ".$MyOpt["tbl"]."_echeancetype SET id=2,description='Certificat Médical', resa='instructeur',multi='non';";
		foreach($q as $i=>$query)
		{
			$sql->Update(utf8_decode($query));
		}

		// Liste des users
		//   Pour chacun ajouter les 2 dates si la date éxiste
		$query="SELECT id,dte_licence,dte_medicale FROM `".$MyOpt["tbl"]."_utilisateurs";
		$sql->Query($query);
		$tabEcheance=array();
		for($i=0; $i<$sql->rows; $i++)
		  { 
				$sql->GetRow($i);
				$tabEcheance[$sql->data["id"]][1]=$sql->data["dte_licence"];
				$tabEcheance[$sql->data["id"]][2]=$sql->data["dte_medicale"];
		  }
		foreach ($tabEcheance as $uid=>$d)
		{
			$q="INSERT INTO ".$MyOpt["tbl"]."_echeance SET typeid='1',uid='".$uid."',dte_echeance='".$d[1]."';";
			$sql->Insert($q);
			$q="INSERT INTO ".$MyOpt["tbl"]."_echeance SET typeid='2',uid='".$uid."',dte_echeance='".$d[2]."';";
			$sql->Insert($q);
		}
	}
?>