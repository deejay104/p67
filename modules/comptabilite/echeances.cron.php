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

// ---- Mail du trésorier
	$query="SELECT mail FROM ".$MyOpt["tbl"]."_utilisateurs WHERE droits LIKE '%TRE%' AND actif='oui'";
	$sql->Query($query);
	
	$tabTre=array();
	for($i=0; $i<$sql->rows; $i++)
	{ 
		$sql->GetRow($i);
	
		$tabTre[$i]=$sql->data["mail"];
	}
	$mailtre=$tabTre[0];

	myPrint("Tresorier : '$mailtre'");

// ---- Liste les comptes actifs
	$query="SELECT * FROM ".$MyOpt["tbl"]."_echeancetype ORDER BY description";
	$sql->Query($query);

	$lsttype=array();
	for($i=0; $i<$sql->rows; $i++)
	{
		$sql->GetRow($i);
		$lsttype[$sql->data["id"]]=$sql->data;
	}
	
	$res="OK";
	
	foreach($lsttype as $id=>$d)
	{
		myPrint("* ".$d["description"]);

		$x=$d["delai"];
		
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

					$mail ="Bonjour,\n\n";
					$mail.="L'échéance ".$dte->description." est échue depuis le ".sql2date($dte->Val())."\n";
					$mail.="Je t'invite à faire le nécessaire pour la renouveler sans oublier de m'envoyer une copie pour mise à jour de ton profil sur le site.\n\n";
					$mail.="A bientôt\n\n";
					// $mail.=$usr->aff("prenom")."\n";
					$mail.="Le Président";
					$mail=nl2br(htmlentities($mail,ENT_COMPAT,'ISO-8859-1'));

					$ret=MyMail($mailpre,$usr->mail,$tabPre,"[".$MyOpt["site_title"]."] : ".$dte->description." échue",$mail,"");
				}
				else if (date_diff_txt($dte->Val(),date("Y-m-d"))>-$x*24*3600)
				{
					myPrint($usr->fullname." - ".$dte->description." expire dans moins de ".$x." jours");

					$mail ="Bonjour,\n\n";
					$mail.="L'échéance ".$dte->description." arrive à son terme le ".sql2date($dte->Val())."\n";
					$mail.="Je t'invite à faire le nécessaire pour la renouveler sans oublier de m'envoyer une copie pour mise à jour de ton profil sur le site.\n\n";
					$mail.="A bientôt\n\n";
					// $mail.=$usr->aff("prenom")."\n";
					$mail.="Le Président";
					$mail=nl2br(htmlentities($mail,ENT_COMPAT,'ISO-8859-1'));

					$ret=MyMail($mailpre,$usr->mail,$tabPre,"[".$MyOpt["site_title"]."] : ".$dte->description." arrive à échéance le ".sql2date($dte->Val()),$mail,"");
				}
				if (!$ret)
				{
					$res="ERREUR";
				}
			}
		}
	}
	
	myPrint($res);
?>