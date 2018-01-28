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
	$query="SELECT p67_utilisateurs.mail FROM p67_utilisateurs WHERE droits LIKE '%TRE%' AND actif='oui'";
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

	foreach($lstusr as $i=>$id)
	{
		$usr = new user_class($id,$sql,false,true);
		$ret=true;
		$m=$usr->CalcSolde();
		if (($m<-$usr->data["decouvert"]) && ($usr->mail!="") && ($usr->virtuel=="non"))
		{
			myPrint($usr->fullname." - Solde: ".$m);

			$mail ="Bonjour,\n\n";
			$mail.="Sauf erreur de notre part, le solde ton compte est de ".$m." €.\n";
			$mail.="Conformément à notre règlement intérieur et afin de préserver la santé financière de notre association, je te demande de faire le nécessaire au plus vite afin de régulariser ta situation. Dans un souci de rapidité, nous te demandons de privilégier dans le mesure du possible un règlement par virement bancaire sans oublier de m'en informer.\n\n";
			$mail.="Nous comptons sur toi.\n\n";
			$mail.="A bientôt\n\n";
			$mail.="Le Trésorier";
			$mail=nl2br(htmlentities($mail,ENT_COMPAT,'ISO-8859-1'));

			$ret=MyMail($mailtre,$usr->mail,$tabTre,"[".$MyOpt["site_title"]."] Compte à découvert",$mail,"");
		}
		if (!$ret)
		{
			$res="ERREUR";
		}
	}

	myPrint($res);
?>