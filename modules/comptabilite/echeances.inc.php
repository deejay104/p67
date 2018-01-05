<?
// ---------------------------------------------------------------------------------------------
//   Saisie des mouvements
// ---------------------------------------------------------------------------------------------
//   Variables  : 
// ---------------------------------------------------------------------------------------------
/*
    SoceIt v2.2 ($Revision: 445 $)
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
// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("echeances.htm"));
	$tmpl_x->assign("path_module","$module/$mod");

// ---- Vérifie les variables
	if (!GetDroit("AccesPageEcheances")) { FatalError("Accès non autorisé"); }

	if (!is_numeric($form_id))
	{
		$form_id=0;
	}
	require_once ("class/echeance.inc.php");

	$tmpl_x->assign("form_checktime",$_SESSION['checkpost']);

// ---- Affiche les types d'échéance
	$query="SELECT * FROM ".$MyOpt["tbl"]."_echeancetype ORDER BY description";
	$sql->Query($query);

	$form_poste=0;
	$form_description="";
	for($i=0; $i<$sql->rows; $i++)
	{
		$sql->GetRow($i);
		$tmpl_x->assign("form_echeanceid",$sql->data["id"]);
		$tmpl_x->assign("form_echeance",$sql->data["description"]);
		$tmpl_x->assign("select_echeance",($sql->data["id"]==$form_id) ? "selected" : "");

		if ( ($sql->data["id"]==$form_id) && ($sql->data["poste"]>0))
		{
			$form_poste=$sql->data["poste"];
			$form_commentaire=$sql->data["description"];

			$tmpl_x->assign("form_cout",$sql->data["cout"]);
			if ($fonc!="Débiter")
			{
				$tmpl_x->parse("corps.aff_debite");
			}
		}
		$tmpl_x->parse("corps.lst_echeance");
	}
	$tmpl_x->assign("form_id",$form_id);

// ---- Valide le débit des échéances
	$save=false;

	if ($fonc=="Débiter")
	{
		$tmpl_x->assign("form_cout",$form_cout);
		$tmpl_x->assign("form_date",$form_date);

			$query="SELECT * FROM ".$MyOpt["tbl"]."_mouvement WHERE id='".$form_poste."'";
		$res=$sql->QueryRow($query);
		foreach ($form_debite as $id=>$d)
		{
			$usr = new user_class($id,$sql,false);
			
			$tmpl_x->assign("enr_uid",$id);
			$tmpl_x->assign("enr_date",date("d/m/Y"));
			$tmpl_x->assign("enr_poste",$res["description"]);
			$tmpl_x->assign("enr_commentaire",$form_commentaire." jusqu'au ".sql2date($form_date));
			$tmpl_x->assign("enr_membre",$usr->fullname);
			$tmpl_x->assign("enr_cout",AffMontant(-$form_cout));
			$tmpl_x->parse("corps.aff_visualisation.lst_visualisation");
		}
		$tmpl_x->parse("corps.aff_visualisation");
		$save=true;
	}

// ---- Enregistre le débit des échéances
	if ($fonc=="Valider")
	{
		$query="SELECT * FROM ".$MyOpt["tbl"]."_mouvement WHERE id='".$form_poste."'";
		$res=$sql->QueryRow($query);
		foreach ($form_debite as $id=>$d)
		{
			echo "Echeance ".$form_id." pour ".$id." jusqu'au ".$form_date."<br>";
			echo "Débite ".$form_id." de ".$form_cout." poste ".$form_poste."<br>";
		}
	}
	
// ---- Liste des échéances
	if ($save==false)
	{
		$tabTitre=array();
		$tabTitre["prenom"]["aff"]="Prénom";
		$tabTitre["prenom"]["width"]=150;
		$tabTitre["nom"]["aff"]="Nom";
		$tabTitre["nom"]["width"]=250;
		$tabTitre["echeance"]["aff"]="Echéance";
		$tabTitre["echeance"]["width"]=350;
		$tabTitre["debiter"]["aff"]="<input type='checkbox' id='form_debite' OnClick='selectAll();'> Débiter";
		$tabTitre["debiter"]["width"]=100;

		if ($form_id>0)
		{
			$lstusr=ListeMembresEcheance($sql,$form_id);
		}

		$tabValeur=array();
		foreach($lstusr as $i=>$id)
		{
			$usr = new user_class($id,$sql,false);
			$dte = new echeance_class(0,$sql,$id);
			$dte->loadtype($form_id);

			$tabValeur[$i]["prenom"]["val"]=$usr->prenom;
			$tabValeur[$i]["prenom"]["aff"]=$usr->aff("prenom");
			$tabValeur[$i]["nom"]["val"]=$usr->nom;
			$tabValeur[$i]["nom"]["aff"]=$usr->aff("nom");
			$tabValeur[$i]["echeance"]["val"]=$dte->Val();
			$tabValeur[$i]["echeance"]["aff"]=$dte->Affiche();
			$tabValeur[$i]["debiter"]["aff"]="<input type='checkbox' id='form_debite_".$id."' name='form_debite[".$id."]'>";

			$tmpl_x->assign("form_uid",$id);
			$tmpl_x->parse("corps.lst_checkbox");
		}

		if ($order=="") { $order="nom"; }
		if ($trie=="") { $trie="d"; }

		$tmpl_x->assign("aff_tableau",AfficheTableau($tabValeur,$tabTitre,$order,$trie,"form_id=".$form_id));
	}

	
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