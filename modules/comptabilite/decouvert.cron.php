<?
// ---------------------------------------------------------------------------------------------
//   Batch de notification 
// ---------------------------------------------------------------------------------------------
?>
<?
	if ($gl_mode!="batch")
	  { FatalError("Acces refuse","Ne peut etre execute qu'en arriere plan"); }

  	require_once ("class/echeance.inc.php");

// ---- Mail du tr�sorier
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
			$mail.="Sauf erreur de notre part, le solde ton compte est de ".$m." �.\n";
			$mail.="Conform�ment � notre r�glement int�rieur et afin de pr�server la sant� financi�re de notre association, je te demande de faire le n�cessaire au plus vite afin de r�gulariser ta situation. Dans un souci de rapidit�, nous te demandons de privil�gier dans le mesure du possible un r�glement par virement bancaire sans oublier de m'en informer.\n\n";
			$mail.="Nous comptons sur toi.\n\n";
			$mail.="A bient�t\n\n";
			$mail.="Le Tr�sorier";
			$mail=nl2br(htmlentities($mail,ENT_COMPAT,'ISO-8859-1'));

			$ret=MyMail($mailtre,$usr->mail,$tabTre,"[".$MyOpt["site_title"]."] Compte � d�couvert",$mail,"");
		}
		if (!$ret)
		{
			$res="ERREUR";
		}
	}

	myPrint($res);
?>