<?
// ---------------------------------------------------------------------------------------------
//   Batch de notification 
// ---------------------------------------------------------------------------------------------
?>
<?
	if ($gl_mode!="batch")
	  { FatalError("Acces refuse","Ne peut etre execute qu'en arriere plan"); }

  	require_once ("class/echeance.inc.php");

// ---- Mail du président
	$query="SELECT mail FROM ".$MyOpt["tbl"]."_utilisateurs WHERE droits LIKE '%PRE%' AND actif='oui'";
	$sql->Query($query);
	
	$tabPre=array();
	for($i=0; $i<$sql->rows; $i++)
	{ 
		$sql->GetRow($i);
	
		$tabPre[$i]=$sql->data["mail"];
	}

	if (isset($tabPre[0]))
	{
		$mailpre=$tabPre[0];
	}
	else
	{
		FatalError("Erreur","Impossible de trouver le mail du president");
	}
	myPrint("President : '$mailpre'");

// ---- Liste les comptes actifs
	$query="SELECT * FROM ".$MyOpt["tbl"]."_echeancetype ORDER BY description";
	$sql->Query($query);

	$lsttype=array();
	for($i=0; $i<$sql->rows; $i++)
	{
		$sql->GetRow($i);
		$lsttype[$sql->data["id"]]=$sql->data;
	}
	
	$gl_res="OK";
	
	foreach($lsttype as $id=>$d)
	{
		myPrint("* ".$d["description"]);

		$delai=$d["delai"];
		
		if ($d["notif"]=="oui")
		{
			$lstdte=array();
			$lstdte=ListeEcheanceType($sql,$id);
			foreach($lstdte as $i=>$did)
			{
				$dte = new echeance_class($did,$sql,0);
				$usr = new user_class($dte->uid,$sql,false);
				$ret=true;

				if (date_diff_txt($dte->Val(),date("Y-m-d"))>0)
				{
					myPrint($usr->fullname." - ".$dte->description." echue");

					$tabvar=array();
					$tabvar["description"]=$dte->description;
					$tabvar["type"]="est échue depuis le";
					$tabvar["date"]=sql2date($dte->Val());
					
					SendMailFromFile($mailpre,$usr->mail,$tabPre,"[".$MyOpt["site_title"]."] : ".$dte->description." échue",$tabvar,"echeances");
				}
				else if (date_diff_txt($dte->Val(),date("Y-m-d"))>-$delai*24*3600)
				{
					myPrint($usr->fullname." - ".$dte->description." expire dans moins de ".$delai." jours");

					$tabvar=array();
					$tabvar["description"]=$dte->description;
					$tabvar["type"]="est échue depuis le";
					$tabvar["date"]=sql2date($dte->Val());
					
					SendMailFromFile($mailpre,$usr->mail,$tabPre,"[".$MyOpt["site_title"]."] : ".$dte->description." arrive à échéance le ".sql2date($dte->Val()),$tabvar,"echeances");
				}
				if (!$ret)
				{
					$gl_res="ERREUR";
				}
			}
		}
	}
	
	
	myPrint($gl_res);
?>