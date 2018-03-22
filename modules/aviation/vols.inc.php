<?
// ---------------------------------------------------------------------------------------------
//   Suivi des heures de vol
//     ($Author: miniroot $)
//     ($Date: 2016-04-22 20:48:24 +0200 (ven., 22 avr. 2016) $)
//     ($Revision: 456 $)
// ---------------------------------------------------------------------------------------------
//   Variables  : 
// ---------------------------------------------------------------------------------------------
/*
    SoceIt v2.0
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
	require_once ("class/reservation.inc.php");


// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("vols.htm"));
	$tmpl_x->assign("path_module","$module/$mod");

// ---- Vérifie les variables
	$tmpl_x->assign("form_checktime",$_SESSION['checkpost']);

// ---- Vérification des paramètres
	if ((GetDroit("ListeVols")) && ($liste==""))
  {
		if (!isset($id))
		  { $id=$uid; }

		$lstusr=ListActiveUsers($sql,"prenom");

		foreach($lstusr as $i=>$tid)
		{ 
			$tmpusr=new user_class($tid,$sql);
			$tmpl_x->assign("id_user", $tid);
			$tmpl_x->assign("chk_user", ($tid==$id) ? "selected" : "") ;
			$tmpl_x->assign("nom_user", $tmpusr->fullname);
			$tmpl_x->parse("corps.listeVols.lst_user");
		}
		$tmpl_x->parse("corps.listeVols");
  }
	else
  {
  	$id=$uid;
  }

	if (GetDroit("AccesSuiviVols"))
	{
		$tmpl_x->parse("infos.suiviVols");
	}

	$tabTitre=array();
	$tabTitre["dte_deb"]["aff"]="Date";
	$tabTitre["dte_deb"]["width"]=($theme=="phone") ? 120 : 220 ;
	$tabTitre["immat"]["aff"]="Immat";
	$tabTitre["immat"]["width"]=75;
	$tabTitre["tpsreel"]["aff"]="Bloc";
	$tabTitre["tpsreel"]["width"]=60;
	$tabTitre["temps"]["aff"]="Temps";
	$tabTitre["temps"]["width"]=60;
	$tabTitre["cout"]["aff"]="Cout";
	$tabTitre["cout"]["width"]=80;
	if ($theme!="phone")
	  {
		$tabTitre["instructeur"]["aff"]="Instructeur";
		$tabTitre["instructeur"]["width"]=270;
	  }

// ---- Chargement des données
	if ($order=="") { $order="dte_deb"; }
	if ($trie=="") { $trie="i"; }
	if (!is_numeric($ts))
	  { $ts = 0; }
	$tl=40;
	$lstresa=ListReservationVols($sql,$id,$order,$trie,$ts,$tl);
	$usr=new user_class($id,$sql);
	$tmpl_x->assign("username",$usr->Aff("prenom")." ".$usr->Aff("nom"));

	$totligne=ListReservationNbLignes($sql,$id);
	
	$tabresa=array();
	foreach($lstresa as $i=>$rid)
	{
		$resa = new resa_class($rid,$sql,false);
		$ress = new ress_class($resa->uid_ressource,$sql);
		$usrinst = new user_class($resa->uid_instructeur,$sql);

		$t1=sql2date($resa->dte_deb,"jour");
		$t2=sql2date($resa->dte_fin,"jour");

		if ($t1!=$t2)
		  { $dte=$t1." - ".$t2; }
		else if ((sql2time($resa->dte_deb)!="00:00:00") && ($theme!="phone"))
		  { $dte=$t1." (".sql2time($resa->dte_deb,"nosec")." à ".sql2time($resa->dte_fin,"nosec").")"; }
		else if  ($theme!="phone")
		  { $dte=$t1." (N/A)"; }
		else
		  { $dte=$t1; }

		$tabValeur[$i]["dte_deb"]["val"]=strtotime($resa->dte_deb);
		$tabValeur[$i]["dte_deb"]["aff"]="<a href='index.php?mod=reservations&rub=reservation&id=$rid'>".$dte."</a>";
		$tabValeur[$i]["immat"]["val"]=$ress->nom;
		$tabValeur[$i]["immat"]["aff"]="<a href='index.php?mod=reservations&rub=reservation&id=$rid'>".$ress->immatriculation."</a>";
		$tabValeur[$i]["tpsreel"]["val"]=$resa->tpsreel;
		$tabValeur[$i]["tpsreel"]["aff"]=$resa->AffTempsReel();
		$tabValeur[$i]["temps"]["val"]=$resa->temps;
		$tabValeur[$i]["temps"]["aff"]=$resa->AffTemps();
		$tabValeur[$i]["cout"]["val"]=$resa->prix;
		$tabValeur[$i]["cout"]["aff"]=$resa->AffPrix()."&nbsp;&nbsp;";
		$tabValeur[$i]["cout"]["align"]="right";
		if ($id==$resa->uid_instructeur)
		{
		  	$usrinst = new user_class($resa->uid_pilote,$sql);
			$tabValeur[$i]["instructeur"]["val"]="Avec : ".$usrinst->Aff("fullname");
		}
		else
		{
			$tabValeur[$i]["instructeur"]["val"]=$usrinst->Aff("fullname");
		}

	}

// ---- Affiche le tableau
	$tmpl_x->assign("tab_liste",AfficheTableauFiltre($tabValeur,$tabTitre,$order,$trie,$url="id=$id",$ts,$tl,$totligne));


// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");

?>
