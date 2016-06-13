<?
// ---------------------------------------------------------------------------------------------
//   Liste des membres
//     ($Author: miniroot $)
//     ($Date: 2012-11-26 21:01:41 +0100 (lun., 26 nov. 2012) $)
//     ($Revision: 413 $)
// ---------------------------------------------------------------------------------------------
//   Variables  : 
// ---------------------------------------------------------------------------------------------
/*
    SoceIt v2.0
    Copyright (C) 2007 Matthieu Isorez

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
	if (!GetDroit("AccesExport")) { FatalError("Accès non autorisé (AccesExport)"); }


// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("index.htm"));
	$tmpl_x->assign("path_module","$module/$mod");


	$tabTitre=array();
	$tabTitre["nom"]["aff"]="Nom";
	$tabTitre["nom"]["width"]=150;
	$tabTitre["description"]["aff"]="Description";
	$tabTitre["description"]["width"]=200;

	$query="SELECT * FROM ".$MyOpt["tbl"]."_export";
        $sql->Query($query);
        for($i=0; $i<$sql->rows; $i++)
          { 
	        $sql->GetRow($i);

		$tabValeur[$i]["nom"]["val"]=$sql->data["nom"];
		$tabValeur[$i]["nom"]["aff"]="<a href='index.php?mod=export&rub=detail&id=".$sql->data["id"]."'>".$sql->data["nom"]."</a>";
		$tabValeur[$i]["description"]["val"]=$sql->data["description"];
		$tabValeur[$i]["description"]["aff"]=$sql->data["description"];
	  }

	$tmpl_x->assign("aff_tableau",AfficheTableau($tabValeur,$tabTitre,$order,$trie));

// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=&$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=&$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=&$tmpl_x->text("corps");



?>
