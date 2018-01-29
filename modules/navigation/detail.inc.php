<?
// ---------------------------------------------------------------------------------------------
//   Navigation
//     ($Author: miniroot $)
// ---------------------------------------------------------------------------------------------
//   Variables  : 
// ---------------------------------------------------------------------------------------------
/*
    SoceIt v2.0
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
*/
?>

<?
// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("detail.htm"));
	$tmpl_x->assign("path_module","$module/$mod");
	$tmpl_x->assign("form_checktime",$_SESSION['checkpost']);

// ---- Ajout du point
	if (!is_numeric($id))
	  { $id=0; }
	  
	if (($form_route!="") && ($id>0) && (!isset($_SESSION['tab_checkpost'][$checktime])))
	{

	  	$q="SELECT MAX(ordre) AS max FROM ".$MyOpt["tbl"]."_navroute WHERE idnav='".$id."' LIMIT 1";
	  	$res=$sql->QueryRow($q);
			
		$query="INSERT INTO ".$MyOpt["tbl"]."_navroute SET idnav='".$id."', nom='".strtoupper($form_route)."', ordre='".($res["max"]+1)."'";
		$sql->Insert($query);

		$_SESSION['tab_checkpost'][$checktime]=$checktime;
	}

	if (($fonc=="Mettre à jour") && ($id>0) )
	{
	  	$q="SELECT uid_creat FROM ".$MyOpt["tbl"]."_navigation WHERE id='".$id."' LIMIT 1";
	  	$res=$sql->QueryRow($q);
  	
	  	if (($res["uid_creat"]==$uid) || (GetDroit("ModifNavigation")))
	  	{
		  	$q="UPDATE ".$MyOpt["tbl"]."_navigation SET titre='".$form_titre."',vitesse='".$form_vitesse."',vitvent='".$form_vitvent."',dirvent='".$form_dirvent."',uid_modif='".$uid."',dte_modif='".now()."' WHERE id='".$id."'";
		  	$sql->Update($q);
		}
	}
	else if (($fonc=="Créer") && ($id==0) && (!isset($_SESSION['tab_checkpost'][$checktime])))
	{
			$q="INSERT INTO ".$MyOpt["tbl"]."_navigation SET titre='".$form_titre."',vitesse='".$form_vitesse."',vitvent='".$form_vitvent."',dirvent='".$form_dirvent."',uid_creat='".$uid."',dte_creat='".now()."',uid_modif='".$uid."',dte_modif='".now()."'";
			$id=$sql->Insert($q);
			$_SESSION['tab_checkpost'][$checktime]=$checktime;
	}
	else if (($fonc=="supprimer") && ($idpoint>0))
	{
	  	$q="DELETE FROM ".$MyOpt["tbl"]."_navroute WHERE id='".$idpoint."'";
	  	$sql->Delete($q);
	}

echo $q."'".$fonc."' '".$id."'";

// ---- Affiche la route
	$tmpl_x->assign("form_id",$id);

	$query="SELECT titre,vitesse,dirvent,vitvent,uid_creat FROM ".$MyOpt["tbl"]."_navigation WHERE id='".$id."'";
	$res=$sql->QueryRow($query);
	$tmpl_x->assign("form_titre",$res["titre"]);
	$tmpl_x->assign("form_vitesse",$res["vitesse"]);
	$tmpl_x->assign("form_vitvent",$res["vitvent"]);
	$tmpl_x->assign("form_dirvent",$res["dirvent"]);

	$query="SELECT rte.id,rte.nom,wpt.description,wpt.lon,wpt.lat FROM ".$MyOpt["tbl"]."_navroute AS rte LEFT JOIN ".$MyOpt["tbl"]."_navpoints AS wpt ON rte.nom=wpt.nom WHERE rte.idnav='".$id."' ORDER BY ordre";
	$sql->Query($query);
	$tabPoints=array();
	for($i=0; $i<$sql->rows; $i++)
	{
		$sql->GetRow($i);
		$tabPoints[$i]["id"]=$sql->data["id"];
		$tabPoints[$i]["nom"]=$sql->data["nom"];
		$tabPoints[$i]["description"]=$sql->data["description"];
		$tabPoints[$i]["lat1"]=$sql->data["lat"];
		$tabPoints[$i]["lon1"]=$sql->data["lon"];
		$tabPoints[$i+1]["lat2"]=$sql->data["lat"];
		$tabPoints[$i+1]["lon2"]=$sql->data["lon"];
	}

	$totdis=0;
	$tottsv=0;

	foreach ($tabPoints as $i=>$p)
	  {
			$tmpl_x->assign("aff_id",$p["id"]);
			$tmpl_x->assign("aff_nom",$p["nom"]);
			$tmpl_x->assign("aff_description",$p["description"]);
	
			if (($p["lat1"]!="") && ($p["lat2"]!=""))
			  {
					$vp=$res["vitesse"];
					$vw=$res["vitvent"];

					$d=round(getDistance($p["lat1"],$p["lon1"],$p["lat2"],$p["lon2"],"N"),0);
					$rv=round(getBearing($p["lat2"],$p["lon2"],$p["lat1"],$p["lon1"]),0);
					$t=round($d*60/$res["vitesse"],0);

					$a=deg2rad($res["dirvent"]-$rv);

					$vd = $vw*cos($a);
					$vt = $vw*sin($a);
					$x=($vw/$vp)*sin($a);

					$vs = ($vp*cos($x)) - $vd;
					$tr=round($d*60/$vs,0);

					$tmpl_x->assign("aff_distance",$d);
					$tmpl_x->assign("aff_rm",$rv."°");
					$tmpl_x->assign("aff_tsv",$t);
					$tmpl_x->assign("aff_cap",$rv+round(rad2deg($x),0)."°");
					$tmpl_x->assign("aff_tps",$tr);

					$totdis=$totdis+$d;
					$tottsv=$tottsv+$t;
					$tottps=$tottps+$tr;

					
				}
	
			if ($p["lat1"]!="")
			  {
					$tmpl_x->parse("corps.lst_point");
				}
		}

		$tmpl_x->assign("tot_distance",$totdis);
		$tmpl_x->assign("tot_tsv",AffHeures($tottsv));
		$tmpl_x->assign("tot_tps",AffHeures($tottps));


	if ( ($id==0) && (GetDroit("CreeNavigation")) )
	  {
			$tmpl_x->parse("corps.aff_creer");
		}
	else if (($res["uid_creat"]==$uid) || (GetDroit("ModifNavigation")))
	  {
			$tmpl_x->parse("corps.aff_update");
		}
	if ( (($res["uid_creat"]==$uid) || (GetDroit("ModifNavigation"))) && ($id>0))
	  {
			$tmpl_x->parse("corps.aff_sortable");
			$tmpl_x->parse("corps.aff_ajout");
		}

// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");


?>
