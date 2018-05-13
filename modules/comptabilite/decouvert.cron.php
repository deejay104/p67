<?
// ---------------------------------------------------------------------------------------------
//   Batch de notification 
// ---------------------------------------------------------------------------------------------
?>
<?
	if ($gl_mode!="batch")
	  { FatalError("Acces refuse","Ne peut etre execute qu'en arriere plan"); }

  	require_once ("class/echeance.inc.php");

// ---- Mail du trésorier
	$query="SELECT mail FROM ".$MyOpt["tbl"]."_utilisateurs WHERE droits LIKE '%TRE%' AND actif='oui'";
	$sql->Query($query);
	
	$tabTre=array();
	for($i=0; $i<$sql->rows; $i++)
	{ 
		$sql->GetRow($i);
	
		$tabTre[$i]=$sql->data["mail"];
	}
	if (isset($tabTre[0]))
	{
		$mailtre=$tabTre[0];
	}
	else
	{
		FatalError("Erreur","Impossible de trouver le mail du tresorier");
	}

	myPrint("Tresorier : '$mailtre'");

// ---- Liste les comptes actifs
	$lstusr=ListActiveUsers($sql,"std",$MyOpt["restrict"]["comptes"],"");
	$gl_res="OK";

	foreach($lstusr as $i=>$id)
	{
		$usr = new user_class($id,$sql,false,true);
		$ret=true;
		$solde=$usr->CalcSolde();
		if (($solde<-$usr->data["decouvert"]) && ($usr->mail!="") && ($usr->virtuel=="non"))
		{
			myPrint($usr->fullname." - Solde: ".$solde);

			$tabvar=array();
			$tabvar["solde"]=$solde;
			
			SendMailFromFile($mailtre,$usr->mail,$tabTre,"[".$MyOpt["site_title"]."] : Compte à découvert",$tabvar,"decouvert");

		}
		if (!$ret)
		{
			$gl_res="ERREUR";
		}
	}

	myPrint($gl_res);
?>