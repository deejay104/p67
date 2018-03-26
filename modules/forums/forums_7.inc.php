<?
// ---------------------------------------------------------------------------------------------
//   Affiche un message du forum
// ---------------------------------------------------------------------------------------------
//   Variables  : $fid - Numéro du forums
//		  $mid - Numéro du message
//		  $critere - Transmet les criteres de recherches s'il y en a
// ---------------------------------------------------------------------------------------------
/*
    Easy-Aero v2.14
    Copyright (C) 2018 Matthieu Isorez

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
?>

<?
	require_once ("class/document.inc.php");
// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("forums_7.htm"));
	$tmpl_x->assign("path_module","$module/$mod");

// ---- Vérifie les variables
	if (!is_numeric($fid))
	  { echo "Erreur dans la variable fid"; exit; }
	if (!is_numeric($mid))
	  { echo "Erreur dans la variable mid"; exit; }



// ---- On marque le message comme lu
	if ($uid>0)
	  {
		$query="SELECT forum_msg AS id FROM ".$MyOpt["tbl"]."_forums_lus WHERE forum_msg=$mid AND forum_usr=$uid";
		$res=$sql->QueryRow($query);
		if ($res["id"]>0)
		  {
				$query ="UPDATE ".$MyOpt["tbl"]."_forums_lus SET forum_date = '".now()."' WHERE forum_msg=$mid AND forum_usr=$uid";
				$sql->Update($query);
		  }
		else
		  {
				$query="INSERT INTO ".$MyOpt["tbl"]."_forums_lus SET forum_msg=$mid, forum_usr=$uid, forum_date='".now()."'";
				$sql->Insert($query);
		  }
	  }

// ---- Récupère les données sur le message
	$query = "SELECT forum.id AS id,";
	$query.= "forum.fid AS fid,";
	$query.= "forum.message AS corps,";
	$query.= "forum.titre AS titre,";
	$query.= "forum.pseudo AS pseudo,";
	$query.= "forum.uid_creat AS uid_creat,";
	$query.= "forum.uid_maj AS uid_maj,";
	$query.= "forum.dte_maj AS date_maj ";
	$query.= "FROM ".$MyOpt["tbl"]."_forums AS forum ";
	$query.= "WHERE forum.id=$mid";
	$res=$sql->QueryRow($query);

	$query="SELECT titre,droit_w AS droit FROM ".$MyOpt["tbl"]."_forums WHERE id=".$res["fid"];
	$resb=$sql->QueryRow($query);

// ---- Initialisation des variables
//	$tmpl_prg->assign("prg_icone","images/icones/forums.gif");
//	$tmpl_prg->assign("prg_titre",(($res["user_buque_txt"] != "") ? $res["user_buque_txt"] : $res["forum_buque"])." : ".htmlentities($res["forum_titre"]));
//	$tmpl_prg->assign("prg_titre",$resb["titre"]);
//	$tmpl_prg->parse("main.haut");


// ---- Initialisation des variables
	$tmpl_x->assign("color",$color);
	$tmpl_x->assign("color2",$color2);

	$tmpl_x->assign("fid", $fid);
	$tmpl_x->assign("mid", $mid);
	$tmpl_x->assign("idmsg", $res["id"]);

// ---- Titre de la page
	$usr = new user_class($res["uid_creat"],$sql,false);
	$tmpl_x->assign("buque", $usr->fullname);
	$tmpl_x->assign("titre", htmlentities($res["titre"],ENT_HTML5,"ISO-8859-1"));
	$tmpl_x->assign("date", DisplayDate($res["date_maj"]));

	$usr = new user_class($res["uid_maj"],$sql,false);
	$tmpl_x->assign("usr_maj", $usr->fullname);


// ---- Affiche les infos
	$tmpl_x->parse("titre");

// ---- Affiche les boutons

	// Boutons de réponse à un message
	if (GetDroit($resb["droit"]))
	  { $tmpl_x->parse("infos.ecrire"); }
	if ((($res["uid_creat"] == $uid) && ($uid>0)) || (GetDroit("ModifMessage")))
	  { $tmpl_x->parse("infos.modifier"); }
	if (GetDroit("SupprimeMessage"))
	  { $tmpl_x->parse("infos.supprimer"); }


// ---- Affiche le corps du message
	if ((!preg_match("/<BR>/i",$res["corps"])) && (!preg_match("/<P>/i",$res["corps"])) && (!preg_match("/<DIV>/i",$res["corps"])) && (!preg_match("/<IMG/i",$res["corps"])) && (!preg_match("/<TABLE/i",$res["corps"])))
	  { $msg = nl2br(htmlentities($res["corps"],ENT_HTML5,"ISO-8859-1")); }
	else
	  { $msg = $res["corps"]; }

	$msg=preg_replace("/<\/?SCRIPT[^>]*>/i","",$msg);

	$msg=preg_replace("/((http|https|ftp):\/\/[^ |<]*)/si","<a href='$1' target='_blank'>$1</a>",$msg);
	$msg=preg_replace("/ (www\.[^ |\/]*)/si","<a href='http://$1' target='_blank'>$1</a>",$msg);

	
// ---- Mets en relief les critères de recherche
	$critere = trim($critere);
	if ($critere!="")
	  {
			$tabcrit=explode(" ",$critere);
			foreach($tabcrit as $crit)
			  { $msg=preg_replace("/".$crit."/si","<span class='forum_Message_Selection'>$crit</span>",$msg); }
		}

	$tmpl_x->assign("msg", $msg);

// ---- Affiche les pièces jointes au message
	$lstdoc=ListDocument($sql,$mid,"forum");
	  	
	if ((is_array($lstdoc)) && (count($lstdoc)>0))
	  {
		foreach($lstdoc as $i=>$did)
		  {
			$doc = new document_class($did,$sql);
			$tmpl_x->assign("form_document",$doc->Affiche());
			$tmpl_x->parse("corps.pieces_jointes.lst_document");
		  }
		  $tmpl_x->parse("corps.pieces_jointes");
	  }

// ---- Affiche les réponses
	$query ="SELECT * ";
	$query.="FROM ".$MyOpt["tbl"]."_forums AS forum ";
	$query.="WHERE forum.fil = $mid";
	$sql->Query($query);

	// Charge les messages
	$rep=array();
	for($i=0; $i<$sql->rows; $i++)
	{
		$sql->GetRow($i);
		$rep[$sql->data["id"]]=$sql->data;
	}
	
	foreach($rep as $i=>$d)
	{
		$usr = new user_class($d["uid_creat"],$sql,false);

		$lstdoc=ListDocument($sql,$d["uid_creat"],"avatar");
		if (count($lstdoc)>0)
		{
			$doc = new document_class($lstdoc[0],$sql);
			$tmpl_x->assign("rep_usrid",$lstdoc[0]);
		}
		else
		{
			$tmpl_x->assign("rep_usrid","-1");
		}				
		$tmpl_x->assign("rep_usr_creat", $usr->fullname);
		$tmpl_x->assign("rep_titre", $d["titre"]);
		$tmpl_x->assign("rep_dte_creat", DisplayDate($d["dte_creat"]));
		$tmpl_x->assign("rep_mid", $d["id"]);
		$tmpl_x->assign("rep_message", $d["message"]);

		$tmpl_x->parse("corps.lst_reponse");  
	}

// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");

?>
