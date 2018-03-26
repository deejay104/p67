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

	require_once ("class/compte.inc.php");

// ---- Liste des comptes
	if (!isset($id))
	  { $id=$myuser->idcpt; }

	// if ((GetDroit("ListeComptes")) && ($liste==""))
	if (GetDroit("ListeComptes"))
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
	if ((!isset($order)) || ($order==""))
	{ $order="date_valeur"; }

	if (!isset($trie))
	{ $trie=""; }


	// Nom de l'utilisateur
	$tmpl_x->assign("nom_compte", $cptusr->Aff("prenom")." ".$cptusr->Aff("nom"));

	// Définition des variables
	$myColor[50]="F0F0F0";
	$myColor[60]="F7F7F7";
	if ((!isset($ts)) || (!is_numeric($ts)))
	  { $ts = 0; }

	// Entete du tableau d'affichage
	$tabTitre=array();
	$tabTitre["date_valeur"]["aff"]="Date";
	$tabTitre["date_valeur"]["width"]=100;
	if ($theme!="phone")
	  {
		$tabTitre["mouvement"]["aff"]="Mouvement";
		$tabTitre["mouvement"]["width"]=350;
		$tabTitre["commentaire"]["aff"]="Commentaire";
		$tabTitre["commentaire"]["width"]=400;
	  }
	else
	  {
		$tabTitre["commentaire"]["aff"]="Commentaire";
		$tabTitre["commentaire"]["width"]=250;
	  }
	$tabTitre["line"]["aff"]="<line>";
	$tabTitre["line"]["width"]=1;
	$tabTitre["montant"]["aff"]="Montant";
	$tabTitre["montant"]["width"]=100;
	if ((GetDroit("AfficheSignatureCompte")) && ($theme!="phone"))
	{
		$tabTitre["signature"]["aff"]="";
		$tabTitre["signature"]["width"]=20;
	}
	if ($trie=="")
	{
		$tabTitre["solde"]["aff"]="Solde";
		$tabTitre["solde"]["width"]=110;
	}
	if ($theme!="phone")
	{
		$tabTitre["releve"]["aff"]="&nbsp;";
		$tabTitre["releve"]["width"]=40;
	}
	

	
	$tabValeur=array();
	$tl=50;

	// Affiche le solde du compte
	$tmpl_x->assign("solde_compte", AffMontant($cptusr->CalcSolde()));
	
	
	// Calcul le nombre ligne totale
	$query = "SELECT COUNT(*) AS nb FROM ".$MyOpt["tbl"]."_compte WHERE ".$MyOpt["tbl"]."_compte.uid=$id";
	$res=$sql->QueryRow($query);
	$totligne=$res["nb"];

	// Calcul le solde du compte au début de l'affichage
	$query = "SELECT SUM(lignes.montant) AS solde FROM (SELECT montant FROM ".$MyOpt["tbl"]."_compte WHERE ".$MyOpt["tbl"]."_compte.uid=$id ORDER BY $order ".((($trie=="i") || ($trie=="")) ? "DESC" : "").", id DESC LIMIT $ts,$totligne) AS lignes";
	$res=$sql->QueryRow($query);
	$solde=$res["solde"];
	
	// Affiche les lignes
	$query = "SELECT * FROM ".$MyOpt["tbl"]."_compte WHERE ".$MyOpt["tbl"]."_compte.uid=$id ORDER BY $order ".((($trie=="i") || ($trie=="")) ? "DESC" : "").", id DESC LIMIT $ts,$tl";
	$sql->Query($query);
	$col=50;
	for($i=0; $i<$sql->rows; $i++)
	{ 
		$sql->GetRow($i);

		$tabValeur[$i]["lid"]["val"]=$sql->data["id"];
		$tabValeur[$i]["date_valeur"]["val"]=CompleteTxt($i,"20","0");
		$tabValeur[$i]["date_valeur"]["aff"]=date("d/m/Y",strtotime($sql->data["date_valeur"]));
		$tabValeur[$i]["mid"]["val"]=$sql->data["mid"];
		$tabValeur[$i]["date_creat"]["val"]=$sql->data["date_creat"];
		$tabValeur[$i]["mouvement"]["val"]=$sql->data["mouvement"];
		$tabValeur[$i]["commentaire"]["val"]=$sql->data["commentaire"];
		$tabValeur[$i]["line"]["val"]="<line>";
		$tabValeur[$i]["montant"]["val"]=$sql->data["montant"];
		$tabValeur[$i]["montant"]["align"]="right";
		$tabValeur[$i]["montant"]["aff"]=AffMontant($sql->data["montant"])."&nbsp;&nbsp;";

		if ((!isset($trie)) || ($trie==""))
		  {
			$afftotal=round($solde,2);
			$tabValeur[$i]["solde"]["val"]=(($afftotal==0) ? "0" : $afftotal);
			$tabValeur[$i]["solde"]["align"]="right";
			$tabValeur[$i]["solde"]["aff"]=(($afftotal==0) ? "0,00 ".$MyOpt["devise"] : AffMontant($afftotal))."&nbsp;&nbsp;";
			$solde=$solde-$sql->data["montant"];
		  }
		$tabValeur[$i]["releve"]["val"]=$sql->data["pointe"];

	}

	if (GetDroit("AfficheDetailMouvement"))
	{
		foreach($tabValeur as $i=>$d)
		{
			$tabValeur[$i]["date_valeur"]["aff"]="<a title='Créé le ".sql2date($tabValeur[$i]["date_creat"]["val"])."'>".$tabValeur[$i]["date_valeur"]["aff"]."</a>";
			$tabValeur[$i]["mouvement"]["aff"]="<a title='".AfficheDetailMouvement($id,$d["mid"]["val"])."'>".$tabValeur[$i]["mouvement"]["val"]."</a>";
		}
	}

	if ((GetDroit("AfficheSignatureCompte")) && ($theme!="phone"))
	{
		foreach($tabValeur as $i=>$d)
		{
			$tabValeur[$i]["signature"]["val"]=AfficheSignatureCompte($d["lid"]["val"]);
			$tabValeur[$i]["signature"]["aff"]=($tabValeur[$i]["signature"]["val"]=="ok") ? "<a title='Signature de la transaction confirmée'><img src='static/images/icn16_signed.png' /></a>" : "<a title='Transaction potentiellement altérée'><img src='static/images/icn16_warning.png' /></a>";
		}
	}
	
	if ($order=="") { $order="date"; }
	$tmpl_x->assign("aff_tableau",AfficheTableauFiltre($tabValeur,$tabTitre,$order,$trie,$url="id=$id",$ts,$tl,$totligne));

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
