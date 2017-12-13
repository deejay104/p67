<?
// ---------------------------------------------------------------------------------------------
//   Liste des membres
//     ($Author: miniroot $)
//     ($Date: 2014-09-16 21:09:58 +0200 (mar., 16 sept. 2014) $)
//     ($Revision: 435 $)
// ---------------------------------------------------------------------------------------------
//   Variables  : 
// ---------------------------------------------------------------------------------------------
/*
    SoceIt v2.2
    Copyright (C) 2012 Matthieu Isorez

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
	if (!GetDroit("AccesFamilles")) { FatalError("Accès non autorisé (AccesFamilles)"); }

// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("familles.htm"));
	$tmpl_x->assign("path_module","$module/$mod");

	if (GetDroit("ModifFamilleCree"))
	  { $tmpl_x->parse("infos.ajout"); }
	
	if (GetDroit("AccesAbonnements"))
	  { $tmpl_x->parse("infos.abonnements"); }
	
	$tabTitre=array();
	$tabTitre["prenom"]["aff"]="Prénom";
	$tabTitre["prenom"]["width"]=150;
	$tabTitre["nom"]["aff"]="Nom";
	$tabTitre["nom"]["width"]=200;
	if ($theme!="phone")
	  {
		$tabTitre["mail"]["aff"]="Mail";
		$tabTitre["mail"]["width"]=280;
	  }
	$tabTitre["telephone"]["aff"]="Téléphone";
	$tabTitre["telephone"]["width"]=120;
	if ($theme!="phone")
	  {
		$tabTitre["zone"]["aff"]="Zone";
		$tabTitre["zone"]["width"]=160;
	  }

	$lstusr=ListActiveUsers($sql,"",$MyOpt["restrict"]["famille"]);

	$tabValeur=array();
	foreach($lstusr as $i=>$id)
	  {
		$usr = new user_class($id,$sql,false);
		$tabValeur[$i]["prenom"]["val"]=$usr->prenom;
		$tabValeur[$i]["prenom"]["aff"]="<A href='membres.php?rub=famille&id=$id'>".$usr->aff("prenom","val")."</a>";
		$tabValeur[$i]["nom"]["val"]=$usr->nom;
		$tabValeur[$i]["nom"]["aff"]="<A href='membres.php?rub=famille&id=$id'>".$usr->aff("nom","val")."</a>";
		$tabValeur[$i]["mail"]["val"]=$usr->mail;
		$tabValeur[$i]["mail"]["aff"]=$usr->aff("mail");
		$tabValeur[$i]["telephone"]["val"]=$usr->AffTel();
		$tabValeur[$i]["telephone"]["aff"]=$usr->AffTel();
		$tabValeur[$i]["zone"]["val"]=$usr->aff("zone");
		$tabValeur[$i]["zone"]["aff"]=$usr->aff("zone");
	  }

	if ($order=="") { $order="nom"; }
	if ($trie=="") { $trie="d"; }

	$tmpl_x->assign("aff_tableau",AfficheTableau($tabValeur,$tabTitre,$order,$trie));

	if (GetDroit("AccesFamille"))
	  { $tmpl_x->parse("infos.exporter"); }
	
// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=&$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=&$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=&$tmpl_x->text("corps");



?>
