<?
// ---------------------------------------------------------------------------------------------
//   Liste des membres
//     ($Author: miniroot $)
//     ($Date: 2016-04-22 20:48:24 +0200 (ven., 22 avr. 2016) $)
//     ($Revision: 456 $)
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
	if (!GetDroit("AccesMembres")) { FatalError("Accès non autorisé (AccesMembres)"); }

	require_once ("class/document.inc.php");

// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("index.htm"));
	$tmpl_x->assign("path_module","$module/$mod");

// ---- Trombino
	if ($fonc=="trombi")
	  {
		$lstusr=ListActiveUsers($sql,"nom");

		foreach($lstusr as $i=>$id)
		  {
			$usr = new user_class($id,$sql,false);

			$lstdoc=ListDocument($sql,$id,"avatar");
			if (count($lstdoc)>0)
			{
				$doc=new document_class($lstdoc[0],$sql);
				$tmpl_x->assign("aff_avatar",$doc->GenerePath(200,240));
			}
			else
			{
				$tmpl_x->assign("aff_avatar","static/images/none.gif");
			}	
			$tmpl_x->assign("id_membre",$id);

			$tmpl_x->parse("corps.trombino.aff_ligne.aff_colonne");
			$col++;
			if (($col>1) && ($theme=="phone"))
			  {
				$tmpl_x->parse("corps.trombino.aff_ligne");
				$col=0;
			  }
			else if ($col>3)
			  {
				$tmpl_x->parse("corps.trombino.aff_ligne");
				$col=0;
			  }
 		  }
		$tmpl_x->parse("corps.trombino");
	  }
// ---- Liste les membres
	else
	  {
		if (GetDroit("CreeUser"))
		  { $tmpl_x->parse("infos.ajout"); }

		if (!isset($aff))
		{ $aff=""; }
	  
		$lstusr=ListActiveUsers($sql,"std","",($aff=="virtuel") ? "oui" : "non");

		if ($theme=="phone")
		{
			foreach($lstusr as $i=>$id)
			{
				$usr = new user_class($id,$sql);

				$tmpl_x->assign("id_membre",$id);
				$tmpl_x->assign("aff_membre",$usr->aff("fullname"));
				$tmpl_x->assign("tel_membre",$usr->AffTel());
				$tmpl_x->assign("mail_membre",$usr->aff("mail"));

				$lstdoc=ListDocument($sql,$id,"avatar");
				if (count($lstdoc)>0)
				{
					$doc=new document_class($lstdoc[0],$sql);
					$tmpl_x->assign("aff_avatar",$doc->GenerePath(64,64));
				}
				else
				{
					$tmpl_x->assign("aff_avatar","static/images/icn64_membre.png");
				}	

				$tmpl_x->assign("id_membre",$usr->uid);
				$tmpl_x->parse("corps.lst_ligne");
			}
		}
		else
		{
			$tabTitre=array();
			$tabTitre["prenom"]["aff"]="Prénom";
			$tabTitre["prenom"]["width"]=($theme!="phone") ? 150 : 120;
			$tabTitre["nom"]["aff"]="Nom";
			$tabTitre["nom"]["width"]=($theme!="phone") ? 200 : 180;
			$tabTitre["mail"]["aff"]="Mail";
			$tabTitre["mail"]["width"]=280;
			$tabTitre["telephone"]["aff"]="Téléphone";
			$tabTitre["telephone"]["width"]=140;
			$tabTitre["type"]["aff"]="Type";
			$tabTitre["type"]["width"]=120;

			$tabValeur=array();
			foreach($lstusr as $i=>$id)
			  {
				$usr = new user_class($id,$sql);
				$tabValeur[$i]["prenom"]["val"]=$usr->prenom;
				$tabValeur[$i]["prenom"]["aff"]=$usr->aff("prenom");
				$tabValeur[$i]["nom"]["val"]=$usr->nom;
				$tabValeur[$i]["nom"]["aff"]=$usr->aff("nom");
				$tabValeur[$i]["mail"]["val"]=$usr->mail;
				$tabValeur[$i]["mail"]["aff"]=$usr->aff("mail");
				$tabValeur[$i]["telephone"]["val"]=$usr->AffTel();
				$tabValeur[$i]["telephone"]["aff"]=$usr->AffTel();
				$tabValeur[$i]["type"]["val"]=$usr->type;
				$tabValeur[$i]["type"]["aff"]=$usr->aff("type");
			  }

			if ((!isset($order)) || ($order=="")) { $order="nom"; }
			if ((!isset($trie)) || ($trie=="")) { $trie="d"; }

			$tmpl_x->assign("aff_tableau",AfficheTableau($tabValeur,$tabTitre,$order,$trie));
		  }
	}
	
	if (GetDroit("ADM"))
	{
		if ($aff=="virtuel")
		{
			$tmpl_x->assign("aff_virtuel","class='pageTitleSelected'");
		}
		else
		{
			$tmpl_x->assign("aff_normal","class='pageTitleSelected'");
		}
		$tmpl_x->parse("infos.aff_virtuel");
	}
		

// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");



?>
