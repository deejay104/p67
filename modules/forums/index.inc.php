<?
// ---------------------------------------------------------------------------------------------
//   Page d'accueil des forums
//   
// ---------------------------------------------------------------------------------------------
//   Variables  : $fonc - fonction à exécuter
//		  $critere - liste des criteres de recherche
// ---------------------------------------------------------------------------------------------
/*
    SoceIt v2.0
    Copyright (C) 2008 Matthieu Isorez

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
	$tmpl_x = new XTemplate (MyRep("index.htm"));
	$tmpl_x->assign("path_module","$module/$mod");

// ---- Fonc = marquer
//	 On marque tous les messages comme lus
	if (isset($fonc) && ($fonc=='marquer')) 
	  {
  	 	$query = "DELETE FROM ".$MyOpt["tbl"]."_forums_lus WHERE forum_usr=$uid";
			$sql->Delete($query);
	
			$query = "SELECT id FROM ".$MyOpt["tbl"]."_forums";
			$sql->Query($query);
	
			$tablus=array();
			for($i=0; $i<$sql->rows; $i++)
			  {
				$sql->GetRow($i);
				$tablus[$i]=$sql->data["id"];
			  }
	
			foreach ($tablus as $id)
			  {
				$query ="INSERT INTO ".$MyOpt["tbl"]."_forums_lus SET forum_msg=$id, forum_usr=$uid, forum_date='".now()."'"; 
				$sql->Insert($query);   
			  }
	  }

// ---- Fonc = Delete
	if (isset($fonc) && ($fonc=='delete') && ($fid>0)) 
	  {
  	  $query = "UPDATE ".$MyOpt["tbl"]."_forums SET actif='non' WHERE id='$fid' OR fid='$fid'";
			$sql->Delete($query);
	  }

// ---- Initialisation des variables
	$tmpl_x->assign("pageIndex", $pageIndex);
	$tmpl_x->assign("color",$color);
	$tmpl_x->assign("color2",$color2);

	$tmpl_x->assign("critere", $critere);


// ---- Affiche les bouttons
	if (GetDroit("CreeForum"))
	  {
			$tmpl_x->parse("infos.nouveau");
	  }


// ---- Récupère la liste des forums
	$sqlb = new mysql_class($mysqluser, $mysqlpassword, $hostname, $db, $port);

	$query ="SELECT id, fid, titre, message AS corps, fil, droit_r AS droit ";
	$query.="FROM ".$MyOpt["tbl"]."_forums ";
	$query.="WHERE fil=0 AND actif='oui' ORDER BY titre";
	$sql->Query($query);

	for($i=0; $i<$sql->rows; $i++)
	  {
		$sql->GetRow($i);

		// Récupère le nombre de message
		$query="SELECT COUNT(*) AS nb FROM ".$MyOpt["tbl"]."_forums AS forums WHERE forums.fid=".$sql->data["id"];
		$res=$sqlb->QueryRow($query);
	
		$tmpl_x->assign("numsforum", $sql->data["id"]);
		$tmpl_x->assign("titreforum", $sql->data["titre"]);
		$tmpl_x->assign("intitule", $sql->data["corps"]);
		$tmpl_x->assign("nbmessages", $res["nb"]);

		// Gestion des forums
		if (GetDroit("ModifForum"))
		  {
				$tmpl_x->parse("corps.nomforum.editforum");
		  }

	  	if ( (GetDroit($sql->data["droit"])) || ($sql->data["droit"]=="ALL") )
	  	{
			$query = "SELECT forums.id AS id, forums.fid AS fid FROM ".$MyOpt["tbl"]."_forums AS forums LEFT JOIN ".$MyOpt["tbl"]."_forums_lus AS forums_lus ON forums.id=forums_lus.forum_msg AND forums_lus.forum_usr=$uid WHERE forums_lus.forum_msg IS NULL AND forums.fid=".$sql->data["id"]." AND forums.actif='oui'";
			$sqlb->Query($query);
			if ($sqlb->rows>0)
			{
				$tabmsg=array();
				for($ii=0; $ii<$sqlb->rows; $ii++)
				  {
					$sqlb->GetRow($ii);
					$tabmsg[$ii]=$sqlb->data["id"];
				  }

				foreach ($tabmsg as $id)
				  {
					$query="SELECT forum.id AS id, forum.fid AS fid, forum.uid_creat, forum.dte_creat AS date, forum.titre AS titre FROM ".$MyOpt["tbl"]."_forums AS forum WHERE forum.id=".$id;
					$res=$sqlb->QueryRow($query);
				
					$tmpl_x->assign("id_forum", $res["fid"]);
					$tmpl_x->assign("id_msg", $id);
					$usr = new user_class($res["uid_creat"],$sqlb,false);

					$tmpl_x->assign("usertxt", $usr->fullname);
					$tmpl_x->assign("titremsg", htmlentities($res["titre"]));
					$tmpl_x->assign("datemsg", DisplayDate($res["date"]));
				
					$tmpl_x->parse("corps.nomforum.msgnonlu");
				  }

			}
			else
			{
				$tmpl_x->parse("corps.nomforum.msgnonlu2");
			}

			$tmpl_x->parse("corps.nomforum");
		}
	}

// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");

?>