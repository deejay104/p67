<?
// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("echeances.htm"));
	$tmpl_x->assign("path_module","$module/$mod");

// ---- Vérifie le droit d'accès
	if (!GetDroit("AccesConfigEcheances")) { FatalError("Accès non autorisé (AccesEcheances)"); }

// ---- Sauvegarde
	if ($fonc=="Enregistrer")
	{
		foreach($form_description as $id=>$d)
		{
			if ($id>0)
			{
				$query="UPDATE ".$MyOpt["tbl"]."_echeancetype SET description='".$d."',droit='".$form_droit[$id]."',resa='".$form_resa[$id]."',multi='".$form_multi[$id]."',cout='".$form_cout[$id]."' WHERE id='".$id."'";
				$sql->Update($query);
			}
			else
			{
				if (trim($d)!="")
				{
					$query="INSERT INTO ".$MyOpt["tbl"]."_echeancetype SET description='".$d."',droit='".$form_droit[$id]."',resa='".$form_resa[$id]."',multi='".$form_multi[$id]."',cout='".$form_cout[$id]."'";
					$sql->Insert($query);
				}
			}
		}
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

		$tmpl_x->assign("select_resa_instructeur","");
		$tmpl_x->assign("select_resa_obligatoire","");
		$tmpl_x->assign("select_resa_facultatif","");
		$tmpl_x->assign("select_multi_oui","");
		$tmpl_x->assign("select_multi_non","");

		$tmpl_x->assign("select_resa_".$sql->data["resa"],"selected");
		$tmpl_x->assign("select_multi_".$sql->data["multi"],"selected");

		$tmpl_x->parse("corps.lst_echeance");
	}
	
// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");

?>