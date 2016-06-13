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
	$tmpl_x = new XTemplate (MyRep("import.htm"));
	$tmpl_x->assign("path_module","$module/$mod");
	$tmpl_x->assign("form_checktime",$_SESSION['checkpost']);

// ---- Import des waypoint
	if (($fonc=="Importer") && (GetDroit("ModifWaypoint")) && (!isset($_SESSION['tab_checkpost'][$checktime])))
//	if ($fonc=="Importer")
	  {
			//Charge le GPX
			$data = implode("",file($_FILES["form_gpx"]["tmp_name"]));

			$wpt = new SimpleXMLElement($data);

			$upd=0;
			$ins=0;
			foreach($wpt->wpt as $i=>$d)
			  {
			  	$q="SELECT nom FROM ".$MyOpt["tbl"]."_navpoints WHERE nom='".strtoupper($d->name)."' LIMIT 1";
			  	$res=$sql->QueryRow($q);
					if ($res["nom"]!="")
					  {
					  	$q="UPDATE ".$MyOpt["tbl"]."_navpoints SET description='".addslashes($d->cmt)."',lat='".$d["lat"]."',lon='".$d["lon"]."',icone='".$form_icone."' WHERE nom='".strtoupper($d->name)."'";
							$sql->Update($q);
							$upd=$upd+1;
						}
					else
						{
					  	$q="INSERT INTO ".$MyOpt["tbl"]."_navpoints SET nom='".strtoupper($d->name)."', description='".addslashes($d->cmt)."',lat='".$d["lat"]."',lon='".$d["lon"]."',icone='".$form_icone."'";
							$sql->Insert($q);
							$ins=$ins+1;
						}
				}

			$tmpl_x->assign("aff_resultat","Insert:".$ins." Update:".$upd);
			$_SESSION['tab_checkpost'][$checktime]=$checktime;
		
		}

// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");



?>
