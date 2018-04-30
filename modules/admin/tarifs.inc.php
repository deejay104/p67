<?
// ---------------------------------------------------------------------------------------------
//   Saisie des mouvements
//     ($Author: miniroot $)
//     ($Date: 2016-02-14 23:17:30 +0100 (dim., 14 fÃ©vr. 2016) $)
//     ($Revision: 445 $)
// ---------------------------------------------------------------------------------------------
//   Variables  : 
// ---------------------------------------------------------------------------------------------
/*
    SoceIt v1.0
    Copyright (C) 2005 Matthieu Isorez

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
	$tmpl_x = new XTemplate (MyRep("tarifs.htm"));
	$tmpl_x->assign("path_module","$module/$mod");

// ---- Vérifie les variables
	if (!GetDroit("AccesConfigTarifs")) { FatalError("Accès non autorisé (AccesConfigTarifs)"); }

	$tmpl_x->assign("form_checktime",$_SESSION['checkpost']);

// ---- Affiche le menu
	$aff_menu="";
	require_once("modules/".$mod."/menu.inc.php");
	$tmpl_x->assign("aff_menu",$aff_menu);

// ---- Enregistre les tarifs
	if (($fonc=="Enregistrer") && (!isset($_SESSION['tab_checkpost'][$checktime])))
	  {
		foreach($tarif_pilote as $i=>$t)
		{
			if ($tarif_code[$i]!="")
			{
				$t=array(
					"pilote"=>(is_numeric($tarif_pilote[$i])) ? $tarif_pilote[$i] : 0,
					"instructeur"=>(is_numeric($tarif_instructeur[$i])) ? $tarif_instructeur[$i] : 0,
					"reduction"=>(is_numeric($tarif_reduction[$i])) ? $tarif_reduction[$i] : 0,
					"code"=>substr($tarif_code[$i],0,2),
					"nom"=>substr($tarif_nom[$i],0,20),
					"reservation"=>substr($tarif_reservation[$i],0,20),
					"defaut_pil"=>'non',
					"defaut_ins"=>'non'
				);
				if ($i==0)
				{
					$t["ress_id"]=$idavion;
				}
				$sql->Edit("tarifs",$MyOpt["tbl"]."_tarifs",$i,$t);
			}
		}

		if (is_array($tarif_defaut_pil))
		{
			foreach($tarif_defaut_pil as $i=>$t)
			  {
				$query="UPDATE ".$MyOpt["tbl"]."_tarifs SET ";
				$query.="defaut_pil='oui' ";
				$query.="WHERE id=$t";
				$sql->Update($query);
			  }
		}

		if (is_array($tarif_defaut_ins))
		{
			foreach($tarif_defaut_ins as $i=>$t)
			{
				$query="UPDATE ".$MyOpt["tbl"]."_tarifs SET ";
				$query.="defaut_ins='oui' ";
				$query.="WHERE id=$t";
				$sql->Update($query);

			}
		}
	}
// ---- Supprime un tarif
	if (($fonc=="delete") && ($id>0))
	{
		$query="DELETE FROM ".$MyOpt["tbl"]."_tarifs WHERE id='$id'";
		$sql->Delete($query);
	}


// ---- Affiche les modules
	if (GetModule("aviation"))
	  {  	$tmpl_x->parse("corps.vols"); }

// ---- Affiche la liste des avions
	$lst=ListeRessources($sql,array("oui","off"));

	foreach($lst as $i=>$id)
	  {
		$ress=new ress_class($id,$sql);

		$tab_avions[$id]=$ress->data;
		$tmpl_x->assign("id_avion", $id);
	  	$tmpl_x->assign("sel_avion", "");
		$tmpl_x->assign("nom_avion", $ress->Aff("immatriculation","val"));
		$tmpl_x->parse("corps.aff_tarifs.lst_avion");
	  }

// ---- Affiche la page demandée
	$query = "SELECT tarifs.*,ressources.id as idavion,ressources.immatriculation FROM ".$MyOpt["tbl"]."_tarifs AS tarifs LEFT JOIN ".$MyOpt["tbl"]."_ressources AS ressources ON tarifs.ress_id=ressources.id WHERE ressources.actif='oui' ORDER BY ressources.immatriculation,tarifs.nom";
	$sql->Query($query);
	$ress="";
	for($i=0; $i<$sql->rows; $i++)
	  { 
		$sql->GetRow($i);

		$tmpl_x->assign("id_tarifs", $sql->data["id"]);
		$tmpl_x->assign("id_avion", $sql->data["idavion"]);
		$tmpl_x->assign("tarif_immat", strtoupper($sql->data["immatriculation"]));
		$tmpl_x->assign("tarif_code", $sql->data["code"]);
		$tmpl_x->assign("tarif_nom", $sql->data["nom"]);
		$tmpl_x->assign("tarif_reservation", $sql->data["reservation"]);
		$tmpl_x->assign("tarif_pilote", $sql->data["pilote"]);
		$tmpl_x->assign("tarif_instructeur", $sql->data["instructeur"]);
		$tmpl_x->assign("tarif_reduction", $sql->data["reduction"]);
		$tmpl_x->assign("tarif_defautpil_selected", ($sql->data["defaut_pil"]=="oui") ? "checked" : "");
		$tmpl_x->assign("tarif_defautins_selected", ($sql->data["defaut_ins"]=="oui") ? "checked" : "");

		if ($ress!=strtoupper($sql->data["immatriculation"]))
		  {
	  		$tmpl_x->parse("corps.aff_tarifs.lst_tarifs.lst_espace");
	  		$ress=strtoupper($sql->data["immatriculation"]);
	  	  }

	  	$tmpl_x->parse("corps.aff_tarifs.lst_tarifs");
	  }
	$tmpl_x->assign("form_page", "tarifs");
  $tmpl_x->parse("corps.aff_tarifs");

// ---- Affecte les variables d'affichage
	if (GetModule("aviation"))
	  {  	$tmpl_x->parse("infos.vols"); }

	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");

?>
