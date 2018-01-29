<?
// ---------------------------------------------------------------------------------------------
//   Batch de notification 
// ---------------------------------------------------------------------------------------------
?>
<?
	if ($gl_mode!="batch")
	  { FatalError("Acces refuse","Ne peut etre execute qu'en arriere plan"); }

	myPrint("Notification des actualités");

// ---- Récupère les actualités pour lesquels le mail n'a pas été envoyé
	$query="SELECT * FROM `".$MyOpt["tbl"]."_actualites` WHERE mail='non' AND actif='oui'";
	$sql->Query($query);
	
	$tabActu=array();
	for($i=0; $i<$sql->rows; $i++)
	{ 
		$sql->GetRow($i);
	
		$tabActu[$sql->data["id"]]=$sql->data;
	}

// ---- On récupère la liste des membres
	$lst=ListActiveMails($sql);

// ---- traite toutes les actus
	$ret=true;

	foreach ($tabActu as $id=>$d)
	{
		myPrint($d["titre"]);
		
		$auth = new user_class($d["uid_creat"],$sql,false);
		$from["mail"]=$auth->mail;
		$from["name"]=$auth->fullname;

		$txt=nl2br(htmlentities( utf8_encode($d["message"]) ));
		$txt.="<br /><br />-Email envoyé à partir du site ".$MyOpt["site_title"]."-";

		// Envoie du message aux membres
		$dest="";
		foreach($lst as $i=>$uid)
		{
			// Et on envoie un mail à chacune des personnes de la liste
			$usr = new user_class($uid,$sql,false);
			
			if ($usr->mail!="")
			{
				if (!MyMail($from,$usr->mail,array(),$d["titre"],$txt))
				{
					myPrint($usr->mail." NOK");
					$dest.=$usr->mail." NOK, ";
					$ret=false;
				}
				else
				{
					myPrint($usr->mail." OK");
				}

			}
		}

		MyMail($from,"matthieu@les-mnms.net",array(),$d["titre"],"**".$dest);

		$query="UPDATE ".$MyOpt["tbl"]."_actualites SET mail='oui',dte_mail='".now()."' WHERE id='$id'";
		$sql->Update($query);
	}

	if ($ret)
	{
		$gl_res="OK";
	}
	else
	{
		$gl_res="ERREUR";
	}

?>