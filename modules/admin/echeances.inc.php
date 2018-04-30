<?
// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("echeances.htm"));
	$tmpl_x->assign("path_module","$module/$mod");

// ---- Vérifie le droit d'accès
	if (!GetDroit("AccesConfigEcheances")) { FatalError("Accès non autorisé (AccesConfigEcheances)"); }

// ---- Affiche le menu
	$aff_menu="";
	require_once("modules/".$mod."/menu.inc.php");
	$tmpl_x->assign("aff_menu",$aff_menu);

// ---- Sauvegarde
	if ($fonc=="Enregistrer")
	{
		foreach($form_description as $id=>$d)
		{
			if(trim($d)!="")
			{
				$t=array(
					"description"=>$d,
					"poste"=>$form_poste[$id],
					"droit"=>$form_droit[$id],
					"resa"=>$form_resa[$id],
					"multi"=>$form_multi[$id],
					"cout"=>$form_cout[$id],
					"notif"=>$form_notif[$id],
					"delai"=>$form_delai[$id]
				);
				$sql->Edit("echeance",$MyOpt["tbl"]."_echeancetype",$id,$t);
			}
		}
	}
// ---- Supprime une échéance
	if (($fonc=="delete") && ($id>0))
	{
		$sql->Edit("echeance",$MyOpt["tbl"]."_echeancetype",$id,array("actif"=>"non"));		
	}

// ---- Liste des postes
	$query = "SELECT * FROM ".$MyOpt["tbl"]."_mouvement WHERE actif='oui' ORDER BY ordre,description";
	$sql->Query($query);
	$tabposte=array();
	for($i=0; $i<$sql->rows; $i++)
	{ 
		$sql->GetRow($i);
		$tabposte[$sql->data["id"]]=$sql->data;
	}

// ---- Liste des groupes
	$query = "SELECT groupe FROM ".$MyOpt["tbl"]."_groupe ORDER BY description";
	$sql->Query($query);
	$tabgrp=array();
	for($i=0; $i<$sql->rows; $i++)
	{ 
		$sql->GetRow($i);
		$tabgrp[$sql->data["groupe"]]=$sql->data["groupe"];
	}
	
// ---- Affiche les types d'échéance
	$query="SELECT * FROM ".$MyOpt["tbl"]."_echeancetype ORDER BY description";
	$sql->Query($query);

	for($i=0; $i<$sql->rows; $i++)
	{
		$sql->GetRow($i);
		$tmpl_x->assign("form_id",$sql->data["id"]);
		$tmpl_x->assign("form_description",$sql->data["description"]);
		$tmpl_x->assign("form_droit",$sql->data["droit"]);
		$tmpl_x->assign("form_cout",$sql->data["cout"]);
		$tmpl_x->assign("form_delai",$sql->data["delai"]);

		$tmpl_x->assign("select_resa_instructeur","");
		$tmpl_x->assign("select_resa_obligatoire","");
		$tmpl_x->assign("select_resa_facultatif","");
		$tmpl_x->assign("select_multi_oui","");
		$tmpl_x->assign("select_multi_non","");

		$tmpl_x->assign("select_notif_oui","");
		$tmpl_x->assign("select_notif_non","");

		$tmpl_x->assign("select_resa_".$sql->data["resa"],"selected");
		$tmpl_x->assign("select_multi_".$sql->data["multi"],"selected");
		$tmpl_x->assign("select_notif_".$sql->data["notif"],"selected");

		foreach($tabposte as $id=>$d)
		{
			$tmpl_x->assign("form_poste",$d["description"]);
			$tmpl_x->assign("form_posteid",$id);
			$tmpl_x->assign("select_poste",($sql->data["poste"]==$id) ? "selected" : "");
			
			$tmpl_x->parse("corps.lst_echeance.lst_poste");
		}

		foreach($tabgrp as $grp=>$d)
		{
			$tmpl_x->assign("form_groupe",$grp);
			$tmpl_x->assign("select_groupe",($sql->data["droit"]==$grp) ? "selected" : "");
			
			$tmpl_x->parse("corps.lst_echeance.lst_groupe");
		}
		
		$tmpl_x->parse("corps.lst_echeance");
	}

	foreach($tabposte as $id=>$d)
	{
		$tmpl_x->assign("form_poste",$d["description"]);
		$tmpl_x->assign("form_posteid",$id);
		$tmpl_x->assign("select_poste",($sql->data["poste"]==$id) ? "selected" : "");
		
		$tmpl_x->parse("corps.lst_poste");
	}
	
// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");

?>