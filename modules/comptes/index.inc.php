<?
// ---------------------------------------------------------------------------------------------
//   Visualisation des comptes
//     (@Revision: $Id$ )
// ---------------------------------------------------------------------------------------------
//   Variables  : $id - numéro du compte
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
// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("index.htm"));
	$tmpl_x->assign("path_module","$module/$mod");

// ---- Initialise les variables
	$tmpl_x->assign("form_checktime",$_SESSION['checkpost']);


// ---- Liste des comptes
	if (!isset($id))
	  { $id=$myuser->idcpt; }

	if ((GetDroit("ListeComptes")) && ($liste==""))
	  {
			$lst=ListActiveUsers($sql,"std",$MyOpt["restrict"]["comptes"],"");
		
			foreach($lst as $i=>$tmpuid)
			  {
			  	$resusr=new user_class($tmpuid,$sql);
	
					$tmpl_x->assign("id_compte", $resusr->data["id"]);
					$tmpl_x->assign("chk_compte", ($resusr->data["id"]==$id) ? "selected" : "") ;
					$tmpl_x->assign("nom_compte", $resusr->fullname);
					$tmpl_x->parse("corps.compte.lst_compte");
			}
			$tmpl_x->parse("infos.liste_compte");
			$tmpl_x->parse("corps.compte");

	  }
	else
	  {
		if (GetModule("creche"))
		  {
		  	$ok=0;
		  	$myuser->LoadEnfants();
			$tmpl_x->assign("id_compte", $myuser->uid);
			$tmpl_x->assign("chk_compte", ($myuser->uid==$id) ? "selected" : "") ;
			$tmpl_x->assign("nom_compte", $myuser->fullname);
			$tmpl_x->parse("corps.compte.lst_compte");
			if ($myuser->uid==$id)
			  { $ok=1; }

	  	  	foreach($myuser->data["enfant"] as $enfant)
	  	  	  {
	  	  		if ($enfant["id"]>0)
	  	  		{
					if ($enfant["id"]==$id)
					  { $ok=1; }
					$tmpl_x->assign("id_compte", $enfant["id"]);
					$tmpl_x->assign("chk_compte", ($enfant["id"]==$id) ? "selected" : "") ;
					$tmpl_x->assign("nom_compte", $enfant["usr"]->fullname);
					$tmpl_x->parse("corps.compte.lst_compte");
				}
			}
			$tmpl_x->parse("corps.compte");
			
			if ($ok==0)
			  { $id=$uid; }
		  }
		else
		  {
	  		$id=$uid;
	  	  }
	  }

	$cptusr=new user_class($id,$sql);

// ---- Affiche le compte demandé
	//$query = "SELECT p67_utilisateurs.id, p67_utilisateurs.nom, p67_utilisateurs.prenom FROM p67_utilisateurs WHERE p67_utilisateurs.actif='oui' AND p67_utilisateurs.id=$id ORDER BY prenom,nom";
	//$tabuser[$id]=$sql->QueryRow($query);

	// Nom de l'utilisateur
	$tmpl_x->assign("nom_compte", $cptusr->Aff("prenom")." ".$cptusr->Aff("nom"));

	// Calcul le solde du compte
	$solde=$cptusr->CalcSolde();

	// Définition des variables
	$myColor[50]="F0F0F0";
	$myColor[60]="F7F7F7";
	if (!is_numeric($ts))
	  { $ts = 0; }

	// Entete du tableau d'affichage
	$tabTitre=array();
	$tabTitre["date"]["aff"]="Date";
	$tabTitre["date"]["width"]=85;
	if ($theme!="phone")
	  {
		$tabTitre["mvt"]["aff"]="Mouvement";
		$tabTitre["mvt"]["width"]=350;
		$tabTitre["rem"]["aff"]="Commentaire";
		$tabTitre["rem"]["width"]=300;
	  }
	else
	  {
		$tabTitre["rem"]["aff"]="Commentaire";
		$tabTitre["rem"]["width"]=250;
	  }
	$tabTitre["line"]["aff"]="<line>";
	$tabTitre["line"]["width"]=1;
	$tabTitre["tot"]["aff"]="&nbsp;&nbsp;Montant";
	$tabTitre["tot"]["width"]=80;
	if ($trie=="")
	  {
		$tabTitre["solde"]["aff"]="&nbsp;&nbsp;Solde Cpt";
		$tabTitre["solde"]["width"]=80;
	  }
	if ($theme!="phone")
	  {
		$tabTitre["releve"]["aff"]="&nbsp;";
		$tabTitre["releve"]["width"]=40;
	  }

	$tabValeur=array();

	$query = "SELECT p67_compte.* FROM p67_compte WHERE p67_compte.uid=$id ORDER BY date_valeur DESC, id DESC";
	$sql->Query($query);
	$total=$solde;
	$col=50;
	for($i=0; $i<$sql->rows; $i++)
	  { 
		$sql->GetRow($i);

		$afftotal=round($total,2);

		$tabValeur[$i]["date"]["val"]=CompleteTxt($i,"20","0");
		$tabValeur[$i]["date"]["aff"]=date("d/m/Y",strtotime($sql->data["date_valeur"]));
		$tabValeur[$i]["mvt"]["val"]=$sql->data["mouvement"];
		$tabValeur[$i]["rem"]["val"]=$sql->data["commentaire"];
		$tabValeur[$i]["line"]["val"]="<line>";
		$tabValeur[$i]["tot"]["val"]=$sql->data["montant"];
		$tabValeur[$i]["tot"]["align"]="right";
		$tabValeur[$i]["tot"]["aff"]=AffMontant($sql->data["montant"])."&nbsp;&nbsp;";

		if ($trie=="")
		  {
			$tabValeur[$i]["solde"]["val"]=(($afftotal==0) ? "0" : $afftotal);
			$tabValeur[$i]["solde"]["align"]="right";
			$tabValeur[$i]["solde"]["aff"]=(($afftotal==0) ? "0,00 ".$MyOpt["devise"] : AffMontant($afftotal))."&nbsp;&nbsp;";
		  }
		$tabValeur[$i]["releve"]["val"]=$sql->data["pointe"];

		$total=$total-$sql->data["montant"];
	  }

	if ($order=="") { $order="date"; }
//		if ($trie=="") { $trie="i"; }
	$tmpl_x->assign("aff_tableau",AfficheTableau($tabValeur,$tabTitre,$order,$trie,$url="id=$id",$ts,50));


	$query = "SELECT SUM(p67_compte.montant) AS total FROM p67_compte WHERE p67_compte.uid=$id";
	$res=$sql->QueryRow($query);
	$tmpl_x->assign("tot_montant", ($res["total"]=="") ? "0" : $res["total"]);

	// Total d'heures
	$tmpl_x->assign("nb_heure", $cptusr->AffNbHeuresVol());

	// ---- Total d'heures 12 derniers mois
	$tmpl_x->assign("nb_heure_deran", $cptusr->AffNbHeures12mois());

	// Total d'heures année courante
	$tmpl_x->assign("nb_heure_an", $cptusr->AffNbHeuresAn());

	// Affiche le résultat
	if ($MyOpt["module"]["aviation"]=="on")
	  { $tmpl_x->parse("corps.nbheures"); }

// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");

?>
