<?
// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("utildonnees.htm"));
	$tmpl_x->assign("path_module","$module/$mod");

// ---- Vérifie le droit d'accès
	if (!GetDroit("AccesUtilisateurDonnees")) { FatalError("Accès non autorisé (AccesEcheances)"); }

// ---- Sauvegarde
	if (($fonc=="Enregistrer") && (GetDroit("ModifUtilDonnees")))
	{
		if (is_array($form_nom))
		{
			foreach($form_nom as $id=>$n)
			{
				if ($id>0)
				{
					$q="UPDATE ".$MyOpt["tbl"]."_utildonneesdef SET nom='".$n."', type='".$form_type[$id]."' WHERE id='".$id."'";
					$sql->Update($q);
				}
				else if ($n!="")
				{
					$q="INSERT ".$MyOpt["tbl"]."_utildonneesdef SET nom='".$n."', type='".$form_type[$id]."'";
					$sql->Insert($q);
				}

			}
		}
	}
	
// ---- Supprime
	if (($fonc=="delete") && (GetDroit("ModifUtilDonnees")))
	{
		if ($id>0)
		{
			$q="UPDATE ".$MyOpt["tbl"]."_utildonneesdef SET actif='non', type='".$form_type[$id]."' WHERE id='".$id."'";
			$sql->Update($q);
		}
	}

// ---- Charge les définitions
	$query="SELECT * FROM ".$MyOpt["tbl"]."_utildonneesdef WHERE actif='oui' ORDER BY ordre";
	$sql->Query($query);

	for($i=0; $i<$sql->rows; $i++)
	{
		$sql->GetRow($i);
		$tmpl_x->assign("form_id",$sql->data["id"]);
		$tmpl_x->assign("form_ordre",$sql->data["ordre"]);
		$tmpl_x->assign("form_nom",$sql->data["nom"]);
		$tmpl_x->assign("select_type_".$sql->data["type"],"selected");
		$tmpl_x->parse("corps.lst_donnees");
		$tmpl_x->parse("corps.lst_sort");
	}
	
// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");

	
?>