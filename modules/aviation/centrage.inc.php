<?
// ---------------------------------------------------------------------------------------------
//   Page de devis de masse et centrage
//     ($Author: miniroot $)
//     ($Date: 2016-02-14 23:17:30 +0100 (dim., 14 fÃ©vr. 2016) $)
//     ($Revision: 445 $)
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
// ---- Vérifie si l'on veut quitter la page
	if ($fonc=="Retour")  	
	  {
		$mod="reservations";
		$affrub="reservation";
	  }

// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("centrage.htm"));
	$tmpl_x->assign("path_module","$module/$mod");

// ---- Initialisation des variables
	$tmpl_x->assign("form_checktime",$_SESSION['checkpost']);


// ---- Vérifie les variables
	if (!is_numeric($id)) { FatalError("Les paramètres de la page sont incorrectes."); }


// ---- Affiche les valeurs enregistrée pour la page

	$tmpl_x->assign("id", $id);

	// Récupère les informations sur le vol
	$query = "SELECT * FROM ".$MyOpt["tbl"]."_calendrier WHERE id='$id'";
	$res=$sql->QueryRow($query);

	// Charge les données de l'avion
	$query = "SELECT * FROM ".$MyOpt["tbl"]."_ressources WHERE id='".$res["uid_avion"]."'";

	$resavion=$sql->QueryRow($query);
	$data=$resavion["centrage"];

	// Décode les données de l'avion
	$parser = xml_parser_create();
	xml_parser_set_option($parser,XML_OPTION_CASE_FOLDING,0);
	xml_parser_set_option($parser,XML_OPTION_SKIP_WHITE,1);
	xml_parse_into_struct($parser,$data,$values,$tags);
	xml_parser_free($parser);

	$tabplace=array();

	// boucle à travers les structures
	foreach ($tags as $key=>$val)
	  {
		if ($key == "place")
		  {
			$ranges = $val;
			// each contiguous pair of array entries are the
			// lower and upper range for each molecule definition
			for ($i=0; $i < count($ranges); $i+=2)
			  {
				$offset = $ranges[$i] + 1;
				$len = $ranges[$i + 1] - $offset;
				$t = parsePlace(array_slice($values, $offset, $len));
				$tabplace[$t["id"]]=$t;
			  }
		  }
		else
		  {
			continue;
		  }
	  }

	function parsePlace($mvalues)
	  {
		for ($i=0; $i < count($mvalues); $i++)
		$t[$mvalues[$i]["tag"]] = $mvalues[$i]["value"];
		return $t;
	  }

	// Récupère la liste des passagers
	$query = "SELECT * FROM ".$MyOpt["tbl"]."_masses WHERE uid_vol='$id'";
	$sql->Query($query);
	for($i=0; $i<$sql->rows; $i++)
	  { 
		$sql->GetRow($i);
		$tabplace[$sql->data["uid_place"]]["idpilote"]=$sql->data["uid_pilote"];
		$tabplace[$sql->data["uid_place"]]["poids"]=$sql->data["poids"];
		$tabplace[$sql->data["uid_place"]]["idenr"]=$sql->data["id"];
	  }

	// Met à jour avec les nouvelles infos
	if (is_array($form_passager_poids))
	  {
		foreach($form_passager_poids as $k=>$v)
		  {
			$tabplace[$k]["poids"]=$v;
		  }
	  }

	if ($maj>0)
	  {
		$tabplace[$maj]["poids"]="";
		if ($form_passager_pilote[$maj]==65535)
		  {
			$tabplace[$maj]["poids"]="75";
			$tabplace[$maj]["idpilote"]="65535";
		  }
		if ($form_passager_pilote[$maj]==0)
		  {
			$tabplace[$maj]["idpilote"]="0";
		  }
	  }

	// Affiche la liste des places de l'avion
	$tot=0;
	foreach($tabplace as $k=>$tv)
	  {
		$tmpl_x->assign("passager_id", $k);
		$tmpl_x->assign("passager_txt", $tv["name"]);

		$tmpl_x->reset("corps.lst_pilote");
		$tmpl_x->assign("passager_unite", $MyOpt["unitPoids"]);
		$coef=($tv["coef"]>0) ? $tv["coef"] : 1;


		// Liste des pilotes
		if ( ($tv["type"]=="pilote") || ($tv["type"]=="copilote") || ($tv["type"]=="passager") )
		  {
			$tmpl_x->assign("chk_pax", "");
			if ($tv["idpilote"]==65535)
			  {
				$tabplace[$k]["idpilote"]=65535;
				$tmpl_x->assign("chk_pax", "selected");
			  }

			$lst=ListActiveUsers($sql,"prenom,nom","!membre,!invite");

			foreach($lst as $i=>$tmpuid)
			{
				// $sql->GetRow($i);
				$resusr=new user_class($tmpuid,$sql,false,true);
				$tmpl_x->assign("uid_pilote", $resusr->uid);
				$tmpl_x->assign("nom_pilote", $resusr->Aff("fullname","val"));
				
				// $tmpl_x->assign("uid_pilote", $sql->data["id"]);
				// $tmpl_x->assign("nom_pilote", AffInfo($sql->data["prenom"],"prenom")." ".AffInfo($sql->data["nom"],"nom"));

				if ($form_passager_pilote[$k]==$resusr->uid)
				{
					$tmpl_x->assign("chk_pilote", "selected");
					if ($tv["poids"]=="")
					  {
						$tabplace[$k]["poids"]=$resusr->data["poids"];
					  }
					$tabplace[$k]["idpilote"]=$resusr->uid;
				}
				else if ( ( (($res["uid_pilote"]==$resusr->uid) && ($tv["type"]=="pilote"))
				       || ($tv["idpilote"]==$resusr->uid)
				       || (($res["uid_instructeur"]==$resusr->uid) && ($tv["type"]=="copilote") && ($tv["idpilote"]==0)) )
				       && ($form_passager_pilote[$k]=="")
				   )
				{
					$tmpl_x->assign("chk_pilote", "selected");
					if ($tv["poids"]=="")
					  {
						$tabplace[$k]["poids"]=$resusr->data["poids"];
					  }
					$tabplace[$k]["idpilote"]=$resusr->uid;
				}
				else
				{
					$tmpl_x->assign("chk_pilote", "");
				}


				$tmpl_x->parse("corps.lst_passager.aff_pilote.lst_pilote");
			  }
	
			$tmpl_x->parse("corps.lst_passager.aff_pilote");
		  }
		else if ($tv["type"]=="essence")
		  {
			$tmpl_x->assign("passager_unite", $MyOpt["unitVol"]." (=".round($tabplace[$k]["poids"]*$coef,0)." ".$MyOpt["unitPoids"].")");
		  }
		$tmpl_x->assign("passager_poids", $tabplace[$k]["poids"]);
		$tot=$tot+round($tabplace[$k]["poids"]*$coef,0);

		$tmpl_x->parse("corps.lst_passager");
	  }

	$tmpl_x->assign("masse_totale", $tot);

	if ($tot<=$resavion["massemax"])
	  {
		$tmpl_x->assign("masse_max", "$unitPoids <font color=\"green\"> &lt; ".$resavion["massemax"]." ".$MyOpt["unitPoids"]."</font>");
	  }
	else
	  {
		$tmpl_x->assign("masse_max", "$unitPoids <font color=\"red\"> &gt; ".$resavion["massemax"]." ".$MyOpt["unitPoids"]."</font>");
	  }


// ---- Enregistre les données


	foreach($tabplace as $k=>$v)
	  {
		if ($v["idenr"]>0)
		  {
		  	$query="UPDATE ".$MyOpt["tbl"]."_masses SET uid_vol='$id', uid_pilote='".$v["idpilote"]."', uid_place=$k, poids='".$v["poids"]."', uid_modif='$uid', dte_modif='".now()."' WHERE id='".$v["idenr"]."'";
			$sql->Update($query);
		  	//echo $query."<br>\n";
		  }
		else if ($v["poids"]>0)
		  {
		  	if (!is_numeric($v["idpilote"]))
		  	  { $v["idpilote"]=0; }
		  	$query="INSERT INTO ".$MyOpt["tbl"]."_masses SET uid_vol='$id', uid_pilote='".$v["idpilote"]."', uid_place='$k', poids='".$v["poids"]."', uid_creat='$uid', dte_creat='".now()."', uid_modif='$uid', dte_modif='".now()."'";
			$sql->Insert($query);
		  	//echo $query."<br>\n";
		  }
	  }

// ---- Affecte les variables d'affichage
	if ($form!="Retour")
	  {
		$tmpl_x->parse("icone");
		$icone=$tmpl_x->text("icone");
		$tmpl_x->parse("infos");
		$infos=$tmpl_x->text("infos");
		$tmpl_x->parse("corps");
		$corps=$tmpl_x->text("corps");
	  }
?>
