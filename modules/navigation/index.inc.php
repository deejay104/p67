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
	$tmpl_x = new XTemplate (MyRep("index.htm"));
	$tmpl_x->assign("path_module","$module/$mod");

// ---- Affiche la liste

// ---- Liste des ressources
	$tabTitre=array();
	$tabTitre["titre"]["aff"]="Titre";
	$tabTitre["titre"]["width"]=250;



	$query="SELECT id,titre FROM ".$MyOpt["tbl"]."_navigation ORDER BY titre";
	$sql->Query($query);

	$tabValeur=array();

	for($i=0; $i<$sql->rows; $i++)
		{
			$sql->GetRow($i);
			$tabValeur[$i]["titre"]["val"]=$sql->data["titre"];
			$tabValeur[$i]["titre"]["aff"]="<a href='index.php?mod=navigation&rub=detail&id=".$sql->data["id"]."'>".$sql->data["titre"]."</a>";
	  }

	if ($order=="") { $order="nom"; }
	if ($trie=="") { $trie="d"; }

	$tmpl_x->assign("aff_tableau",AfficheTableau($tabValeur,$tabTitre,$order,$trie));

// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");



?>
