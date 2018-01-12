<?
// ---- Refuse l'accès en direct
	if ((!isset($token)) || ($token==""))
	  { header("HTTP/1.0 401 Unauthorized"); exit; }

// ---- Valide les paramètres
	if (isset($_REQUEST["id"]))
	{
		$id=$_REQUEST["id"];
	}
	if (!is_numeric($id))
	{
	  	$id=0;
	}
	if ($id==0)
	{
		$res=array();
		$res["result"]=utf8_encode("L'id n'est pas défini");
	  	echo json_encode($res);
	  	exit;
	}

// ---- Récupère les informations du post
	$query="SELECT * FROM `".$MyOpt["tbl"]."_actualites` WHERE id='$id'";
	$res=$sql->QueryRow($query);

	if ($res["mail"]=='oui')
	  {
	  	echo "*Le mail a déjà été envoyé";
	  	exit;
	  }

	$auth = new user_class($res["uid_creat"],$sql,false);
	$from["mail"]=$auth->data["mail"];
	$from["name"]=$auth->data["fullname"];

	$txt=nl2br(htmlentities( utf8_encode($res["message"]) ));
	$txt.="<br /><br />-Email envoyé à partir du site ".$MyOpt["site_title"]."-";

// ---- Envoie du message aux membres
	// On récupère la liste
	$lst=ListActiveMails($sql);

	$ret="";
	$dest="";
	$lstfiles=array();
	foreach($lst as $i=>$uid)
	  {
	  	// Et on envoie un mail à chacune des personnes de la liste
			$usr = new user_class($uid,$sql,false);

		
			if ($usr->mail!="")
			{

		  		if (!MyMail($from,$usr->mail,"",$res["titre"],$txt))
		  		{
					$ret.="  ".$usr->mail."<br />";
		  		}

				$dest.=$usr->mail.", ";
			}
	  }

	MyMail($from,"matthieu@les-mnms.net","",$res["titre"],"**".$dest);

	$query="UPDATE ".$MyOpt["tbl"]."_actualites SET mail='oui',dte_mail='".now()."' WHERE id='$id'";
	$sql->Update($query);


	if ($ret=="")
	  {
			echo "OK : Message envoyé";
		}
	else
	  {
			echo "Erreur d'envoi pour les emails suivants :<br />";
			echo $ret;
		}
	exit;

?>