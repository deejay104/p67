<?
// ---------------------------------------------------------------------------------------------
//   Actualit�s
//   
// ---------------------------------------------------------------------------------------------
//   Variables  :
// ---------------------------------------------------------------------------------------------
/*
    SoceIt v2.10
    Copyright (C) 2016 Matthieu Isorez

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

    ($Author: miniroot $)
    ($Date: 2016-02-14 23:17:30 +0100 (dim., 14 févr. 2016) $)
    ($Revision: 445 $)
*/
?>

<?
// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("index.htm"));

	$tmpl_x->assign("site_title", $MyOpt["site_title"]);
	$tmpl_x->assign("form_checktime",$_SESSION['checkpost']);
	$tmpl_x->assign("path_module","$module/$mod");

// ---- Enregistre le post
	$txtnewmsg="Ecrivez votre message...";


	if (!is_numeric($id))
	  { $id=0; }

	if ( ($fonc=="Poster") && (!isset($_SESSION['tab_checkpost'][$checktime])) )
		{
			$_SESSION['tab_checkpost'][$checktime]=$checktime;
	
			if ($form_message!=$txtnewmsg)
				{
					if ($id>0)
					  {
		
							$query="SELECT titre,message FROM `".$MyOpt["tbl"]."_actualites` WHERE id='$id'";
							$res = $sql->QueryRow($query);
		
							if ( (GetDroit("ModifActualite")) || ( ($uid==$res["uid_creat"]) && (time()-strtotime($d["dte_creat"])<3600) ) )
							  {
									$query="UPDATE ".$MyOpt["tbl"]."_actualites SET titre='".addslashes($form_titre)."',message='".addslashes($form_message)."',uid_modif='$uid',dte_modif=NOW() WHERE id='$id'";
									$sql->Update($query);
							  }
						}
					else
						{
							$query="INSERT INTO ".$MyOpt["tbl"]."_actualites (titre,message,uid_creat,dte_creat,uid_modif,dte_modif) VALUES ('".addslashes($form_titre)."','".addslashes($form_message)."','$uid',NOW(),'$uid',NOW())";
							$id=$sql->Insert($query);
						}
					$tmpl_x->assign("aff_id", $id);
					$tmpl_x->parse("corps.aff_sendmail");		
					$id=0;
				}
		}

// ---- Supprime le post
	if ( ($fonc=="supprimer") && ($id>0) )
	  {
			$query="DELETE FROM ".$MyOpt["tbl"]."_actualites WHERE id='$id'";
			$sql->Delete($query);
			$id=0;
		}

// ---- Affichages du menu
	foreach($MyOpt["menu"] as $menu=>$droit) {
		if ((($droit=="") || (GetDroit($droit))) && ($droit!="-"))
		  { $tmpl_x->parse("corps.menu_".$menu); }
	}


// ---- Informations personnelles

	if (GetModule("aviation"))
	{
		// Compte
		$tmpl_x->assign("solde", $myuser->AffSolde());
	
	 	$tmpl_x->assign("dte_licence", $myuser->aff("dte_licence"));
		$tmpl_x->assign("dte_medicale", $myuser->aff("dte_medicale"));
		$tmpl_x->assign("dte_medicale", $myuser->aff("dte_medicale"));
		$tmpl_x->assign("nb_vols", $myuser->NombreVols("3"));
	
		$tmpl_x->parse("corps.mod_aviation_detail");		
	}



// ---- Derniers message des forums

  $query = "SELECT COUNT(forums.id) AS nb FROM ".$MyOpt["tbl"]."_forums AS forums LEFT JOIN ".$MyOpt["tbl"]."_forums_lus AS forums_nonlus ON forums_nonlus.forum_usr=$uid AND forums.id=forums_nonlus.forum_msg WHERE forums_nonlus.forum_msg IS NULL";
	$res=$sql->QueryRow($query);
	$tmpl_x->assign("nb_nonlus",(($res["nb"]>1) ? $res["nb"]." messages" : (($res["nb"]==1) ? $res["nb"]." message" : "Pas de nouveau message")));
	$tmpl_x->assign("color_nonlus",($res["nb"]>0) ? "red" : "black");


// ---- Prochaine manips

	$query = "SELECT p67_manips.* FROM p67_manips WHERE p67_manips.dte_manip>NOW() ORDER BY dte_manip LIMIT 1";
	$res = $sql->QueryRow($query);

	if ($res["id"]>0)
	  {
	  	$res["creat"]=new user_class($res["uid_creat"],$sql,false,false);

			$tmpl_x->assign("manip_id", $res["id"]);
			$tmpl_x->assign("manip_titre", $res["titre"]);
			$tmpl_x->assign("manip_date", sql2date($res["dte_manip"]));
			$tmpl_x->assign("manip_creat",$res["creat"]->Aff("prenom")." ".$res["creat"]->Aff("nom"));
		
			$msg.= preg_replace("/<\/?SCRIPT[^>]*>/i","",nl2br($res["comment"]))."<br />";
			//$msg.= "<p align=right><a href=\"manips.php?rub=detail&id=".$res["id"]."\">-Voir les participants-</a></p>";
		
			$tmpl_x->assign("manip_txt", $msg);	

			$tmpl_x->parse("corps.aff_manips");
	}



// ---- Derniers documents

	if ($id>0)
	  {
			$query="SELECT titre,message FROM `".$MyOpt["tbl"]."_actualites` WHERE id='$id'";
			$res = $sql->QueryRow($query);
			$tmpl_x->assign("news_title", $res["titre"]);
			$tmpl_x->assign("news_message", $res["message"]);
			$tmpl_x->assign("new_color", "000000");	
		}
	else
	  {
			$tmpl_x->assign("news_title", "Nouvelle actualit�");
			$tmpl_x->assign("news_message", $txtnewmsg);
			$tmpl_x->assign("new_color", "bbbbbb");	
	  }

	$tmpl_x->assign("news_title_clear", "Nouvelle actualit�");
	$tmpl_x->assign("news_message_clear", $txtnewmsg);
	$tmpl_x->assign("form_id", $id);


// ---- Actualit�s
	if ( (!is_numeric($limit)) || ($limit==0) )
	  { $limit=10; }
	$tmpl_x->assign("aff_limit", $limit+5);	

	$q="";
	if ($search!="")
	  {
	  	$q=" AND (titre LIKE '%".$search."%' OR message LIKE '%".$search."%') ";
			$tmpl_x->assign("aff_search", $search);
  	
	  }

	$query="SELECT * FROM `".$MyOpt["tbl"]."_actualites` WHERE actif='oui' $q ORDER BY dte_creat DESC LIMIT 0,$limit";
	$sql->Query($query);
	$news=array();
	for($i=0; $i<$sql->rows; $i++)
	  { 
			$sql->GetRow($i);
			$news[$i]=$sql->data;
	  }

	$tmpl_x->assign("msg_lastid", $sql->data["id"]);	

	$idprev=0;
	foreach($news as $id=>$d)
	  {
	  	$resusr=new user_class($d["uid_creat"],$sql,false,false);

			$txt=nl2br(htmlentities($d["message"],ENT_HTML5,"ISO-8859-1"));
			$txt=preg_replace("/((http|https|ftp):\/\/[^ |<]*)/si","<a href='$1' target='_blank'>$1</a>",$txt);
			$txt=preg_replace("/ (www\.[^ |\/]*)/si","<a href='http://$1' target='_blank'>$1</a>",$txt);

			$tmpl_x->assign("msg_id", $d["id"]);	
			$tmpl_x->assign("msg_titre", $d["titre"]);	
			$tmpl_x->assign("msg_message", $txt);	
			$tmpl_x->assign("msg_autheur", $resusr->Aff("fullname"));	
			$tmpl_x->assign("msg_date", DisplayDate($d["dte_creat"]));	

			$tmpl_x->assign("msg_idprev", $idprev);	
			$idprev=$d["id"];

			if (GetDroit("SupprimeActualite"))
			  {
						$tmpl_x->parse("corps.aff_message.icn_supprimer");
				}
			if ( (($uid==$d["uid_creat"]) && (time()-strtotime($d["dte_creat"])<3600)) || (GetDroit("ModifActualite")) )
			  {
						$tmpl_x->parse("corps.aff_message.icn_modifier");
				}
			$tmpl_x->parse("corps.aff_message");
		}

  	


// ---- Affecte les variables d'affichage

	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");

?>