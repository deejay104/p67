<?
// ---------------------------------------------------------------------------------------------
//   Saisie des consommation
//     ($Author: miniroot $)
//     ($Date: 2016-02-14 23:17:30 +0100 (dim., 14 fÃ©vr. 2016) $)
//     ($Revision: 445 $)
// ---------------------------------------------------------------------------------------------
//   Variables  : 
// ---------------------------------------------------------------------------------------------
/*
    SoceIt v2.0
    Copyright (C) 2006 Matthieu Isorez

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
// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep($rep_tmpl,"conso.htm"));

// ---- Vérifie les variables
	if (!GetDroit("ESC")) { FatalError("Accès non authorisé"); }

	$tmpl_x->assign("form_checktime",$_SESSION['checkpost']);

// ---- Enregistre le mouvement
	if (($fonc=="Enregistrer") && (!isset($_SESSION['tab_checkpost'][$checktime])))
	  {
		foreach($form_qte as $k=>$v)
		  {
			if ($form_id[$k]>0)
			  {
			  	$query="UPDATE p67_conso SET idvol='".$form_vid[$k]."', idavion='$idavion', quantite='".$form_qte[$k]."', prix='".$form_prix[$k]."', tiers='".$form_tiers[$k]."', uid_modif=$uid, dte_modif=NOW() ";
			  	$query.="WHERE id='".$form_id[$k]."'";
				$sql->Update($query);
			  }
			else if ($form_qte[$k]>0)
			  {
			  	$query="INSERT INTO p67_conso SET idvol='".$form_vid[$k]."', idavion='$idavion', quantite='".$form_qte[$k]."', prix='".$form_prix[$k]."', tiers='".$form_tiers[$k]."', uid_creat=$uid, dte_creat=NOW(), uid_modif=$uid, dte_modif=NOW() ";
				$sql->Insert($query);
			  }
		  }
	  }


// ---- Affiche la page demandée
	$query = "SELECT id,immatriculation FROM p67_avion WHERE actif='oui' ORDER BY immatriculation";
	$sql->Query($query);
	for($i=0; $i<$sql->rows; $i++)
	  { 
		$sql->GetRow($i);
		$tab_avions[$sql->data["id"]]=$sql->data;
		$tmpl_x->assign("id_avion", $sql->data["id"]);
		if ($sql->data["id"]==$idavion)
		  {
			$tmpl_x->assign("sel_avion", "selected");
			$tmpl_x->assign("nom_avion", $sql->data["immatriculation"]." *");
		  }
		else
		  {
		  	$tmpl_x->assign("sel_avion", "");
			$tmpl_x->assign("nom_avion", $sql->data["immatriculation"]);
		  }
		$tmpl_x->parse("corps.aff_vols.lst_avion");
	  }

	if ((!isset($idavion)) || (!is_numeric($idavion)))
	  { $t=current($tab_avions); $idavion=$t["id"]; }

	$dte='2006-04-16';
	$query = "SELECT p67_calendrier.id AS vid, p67_calendrier.dte_deb AS dte_deb, p67_avion.id AS aid, p67_avion.immatriculation AS immatriculation, pilote.nom AS pnom, pilote.prenom AS pprenom, pilote.id AS puid, instructeur.nom AS inom, instructeur.prenom AS iprenom, instructeur.id AS iuid, conso.quantite AS quantite, conso.prix AS prix, conso.tiers AS tiers, conso.id AS id ";
	$query.= "FROM p67_calendrier, p67_avion, p67_utilisateurs AS pilote LEFT JOIN p67_utilisateurs AS instructeur ON p67_calendrier.uid_instructeur = instructeur.id  LEFT JOIN p67_conso AS conso ON p67_calendrier.id = conso.idvol ";
	$query.= "WHERE p67_calendrier.uid_avion = p67_avion.id AND p67_calendrier.uid_pilote = pilote.id AND dte_deb>='$dte' AND dte_deb<NOW() AND p67_calendrier.actif='oui' AND p67_calendrier.prix<>0 AND p67_avion.id='$idavion' ORDER BY p67_calendrier.dte_deb";
	
	$sql->Query($query);
	for($i=0; $i<$sql->rows; $i++)
	  { 
		$sql->GetRow($i);
	  	$tmpl_x->assign("date_vols", sql2date(eregi_replace("^([0-9]*-[0-9]*-[0-9]*)[^$]*$","\\1",$sql->data["dte_deb"])));

		$p=($sql->data["pprenom"]!="") ? AffInfo($sql->data["pprenom"],"prenom")."&nbsp;" : "";
		$p.=AffInfo($sql->data["pnom"],"nom");
		$p.=($sql->data["inom"]!="") ? " / ".AffInfo($sql->data["iprenom"],"prenom")."&nbsp;".AffInfo($sql->data["inom"],"nom") : "";
		$tmpl_x->assign("pilote_vols", $p);

		$tmpl_x->assign("fid", $i);
		$tmpl_x->assign("id_vols", $sql->data["id"]);
		$tmpl_x->assign("vid_vols", $sql->data["vid"]);

		$tmpl_x->assign("qte_vols", $sql->data["quantite"]);
		$tmpl_x->assign("prix_vols", $sql->data["prix"]);
		$tmpl_x->assign("tiers_vols", $sql->data["tiers"]);

		$tmpl_x->parse("corps.aff_vols.lst_vols");
	  }
	
	$tmpl_x->parse("corps.aff_vols");


// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");

?>
