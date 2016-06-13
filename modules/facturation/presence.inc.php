<?
// ---------------------------------------------------------------------------------------------
//   Saisie des présences
// ---------------------------------------------------------------------------------------------
//   Variables  : 
//	$dte - Jour à traiter
// ---------------------------------------------------------------------------------------------
/*
    SoceIt v2.2 ($Revision: 456 $)
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
	if (!GetDroit("AccesPresence")) { FatalError("Accès non autorisé (AccesPresence)"); }

	require_once ("class/abonnement.inc.php");
// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("presence.htm"));
	$tmpl_x->assign("path_module","$module/$mod");


// Export
// SELECT dte,TYPE , zone, SUM( tpspaye ) , SUM( tpsreel ) FROM `".$MyOpt["tbl"]."_presence` GROUP BY dte,TYPE , zone

// ---- Rempli le tableau des plages horaires
	$query="SELECT * FROM ".$MyOpt["tbl"]."_plage ORDER BY deb";
	$sql->Query($query);
	$tabPlage=array();

	for($i=0; $i<$sql->rows; $i++)
	  {
		$sql->GetRow($i);

		$tabPlage[$sql->data["plage"]]["nom"]=$sql->data["titre"];
		$tabPlage[$sql->data["plage"]]["deb"]=$sql->data["deb"];
		$tabPlage[$sql->data["plage"]]["fin"]=$sql->data["fin"];
		$tabPlage[$sql->data["plage"]]["jour"][$sql->data["jour"]]=1;
	  }


// ---- Enregistre

	if (($fonc=="Enregistrer") || ($fonc=="Suivant"))
	  {
		$query="DELETE FROM ".$MyOpt["tbl"]."_presence WHERE dtedeb>='$dte 00:00:00' AND dtefin<='$dte 23:59:59'";
		$sql->Delete($query);

		if (count($form_presence)>0)
		  {
			foreach($form_presence as $pid=>$ttype)
			  {
			  	$puid=substr($pid,1,strlen($pid)-1);
			  	$type=substr($ttype,2,strlen($ttype)-2);
				$usr = new user_class($puid,$sql);


				if (substr($ttype,0,2)!="00")
				  {
					$p=substr($pid,0,1);

					$st=$tabPlage[$p]["deb"];
					$et=$tabPlage[$p]["fin"];

// Convertir $st et $et en heure de la journée

					$query ="INSERT ".$MyOpt["tbl"]."_presence (uid,dte,dtedeb,dtefin,type,zone,regime,tpspaye,tpsreel,age,handicap) ";
					$query.="VALUES ('$puid','".date("Ym",strtotime($dte))."','$dte ".$st.":00','$dte ".$et.":00','".$type."','".$usr->zone."','".$usr->data["regime"]."','".($et-$st)."','".(substr($ttype,1,1)*($et-$st))."','".$usr->CalcAge($dte)."','".$usr->data["handicap"]."')";
					$sql->Insert($query);

				  }
			  }
		  }

	  }


	if ($fonc=="Suivant")
	  {
	  	$dte=CalcNextDay($dte,1);
	  }

// ---- Type du jour

	if (($dte=="") || (!preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/",$dte)))
	  { $dte=date("Y-m-d"); }

	$todayNum=date("w",strtotime($dte));

	$query="SELECT id FROM ".$MyOpt["tbl"]."_vacances WHERE dtedeb<='$dte' AND dtefin>='$dte'";
	$res=$sql->QueryRow($query);

	if (($res["id"]>0) && ($MyOpt["tabPresenceJour"][$todayNum]!=""))
	  {
		$todayNum=7;
	  }

	$todayType=$MyOpt["tabPresenceJour"][$todayNum];


// ---- Liste des familles
	$tabTitre=array();
	$tabTitre["prenom"]["aff"]="Prénom";
	$tabTitre["prenom"]["width"]=150;
	$tabTitre["nom"]["aff"]="Nom";
	$tabTitre["nom"]["width"]=150;

	$tabRTitre=array();

	// Charger la table des présences
	$query="SELECT * FROM ".$MyOpt["tbl"]."_presence WHERE dtedeb>='$dte 00:00:00' AND dtefin<='$dte 23:59:59'";
	$sql->Query($query);
	$tabPresence=array();
	for($i=0; $i<$sql->rows; $i++)
	  {
		$sql->GetRow($i);
		$tabPresence[$sql->data["uid"]][$sql->data["type"]]=$sql->data["tpsreel"];
	  }


	// Charge la liste des utilisateurs
	$lstusr=ListActiveUsers($sql,"","enfant");


	$tabValeur=array();
	$nb=0;
	foreach($lstusr as $i=>$id)
	  {
		$usr = new user_class($id,$sql,false);
		$tabValeur[$i]["prenom"]["val"]=$usr->prenom;
		$tabValeur[$i]["prenom"]["aff"]="<A href='membres.php?rub=detail&id=$id'>".$usr->aff("prenom")."</a>";
		$tabValeur[$i]["nom"]["val"]=$usr->nom;
		$tabValeur[$i]["nom"]["aff"]="<A href='membres.php?rub=detail&id=$id'>".$usr->aff("nom")."</a>";

		// Recherche des abonnements actifs (via fonction de la classe)
		// Résultat = liste des mouvements dans un tableau 
		//   $tabAbo[$i]["abo"]="123456A";
		//   $tabAbo[$i]["mouvid"]="xxx";
		//   $tabAbo[$i]["jx"]="X";
		
		$tabAbo=TodayAbonnement($sql,$dte,$id);

		// Pour chaque ligne d'abonnement cocher la journée, midi ou soir en fonction de la valeur par défaut
		// type J : Journée, A : matin, M : mercredi, P : après-midi, S : soir

		$t=array();
		$tt=array();
		foreach ($tabAbo as $ii=>$d)
		  {
		  	// Voir pour boucler sur les lettres
		  	
		  	//[M,S,A,P,J         ]  [M,S,A,P,J    ]
			$t[$d["j".$todayNum]]=$d["j".$todayNum];			
			$tt[$todayType.$d["j".$todayNum]]=$d["j".$todayNum];

			//if ($d["j".$todayNum]=="J")
			//  { $tt[$todayType."A"]="A"; }
			if (is_array($MyOpt["tabPresencePlage"][$todayType.$d["j".$todayNum]]))
			  {
		  		foreach ($MyOpt["tabPresencePlage"][$todayType.$d["j".$todayNum]] as $k=>$v)
		  	  	  {
		  	  	  	$tt[$todayType.$d["j".$todayNum]]=$d["j".$todayNum];
		  	  	  }
		  	  }

		  }		

		// Gère la présence sur une journée
		if (is_array($MyOpt["tabPresencePlage"][$todayType.$d["j".$todayNum]]))
		  {
		  	foreach ($MyOpt["tabPresencePlage"][$todayType.$d["j".$todayNum]] as $k=>$v)
		  	  {
				if (($t[$d["j".$todayNum]]!="") && ($t[$v]==""))
				  {
					$t[$v]=$t[$d["j".$todayNum]];
				  }


				if (($tt[$todayType.$d["j".$todayNum]]) && ($tt[$todayType.$v]==""))
				  {
				  	$tt[$todayType.$v]=$tt[$todayType.$d["j".$todayNum]];
				  }
			  }

		  }


		// Si une valeur existe dans la table des présences la charger au dessus de ce que donne la ligne d'abonnement
		if (count($tabPresence[$id])>0)
		  {
			foreach ($tabPresence[$id] as $type=>$tps)
			  {
			  	if (($tps>0))
			  	  {
			  		$tt[$type]="1";
			  		$t[substr($type,1,1)]="1";
			  	  }
				else
			  	  {
			  		$tt[$type]="0";
			  		$t[substr($type,1,1)]="0";
			  	  }
			  }
		  }

		// Liste toutes les plages horaires
		foreach($tt as $hor=>$v)
		  {
		  	$h=substr($hor,1,1);
			$tabValeur[$i][$h]["val"]=$todayType.(($t[$h]=="") ? "0" : $t[$h]);
			$tabValeur[$i][$h]["aff"]="<input type=\"hidden\" name=\"form_presence[".$h.$id."]\" value=\"".(($t[$h]=="") ? "0" : "1")."0".$todayType.$h."\" />";
			$tabValeur[$i][$h]["aff"].="<input type=\"checkbox\" name=\"form_presence[".$h.$id."]\" value=\"".(($t[$h]=="") ? "0" : "1")."1".$todayType.$h."\" ".(($tt[$todayType.$h]=="0") ? "" : "checked")." />";

			if ($h!="")
			  {
			  	$tabRTitre[$h]=1;
			  }
		  }
	  }


	// Liste les colonnes


	$nb=0;
	foreach($tabPlage as $i=>$v)
	  {
		if (((!is_array($v["jour"])) || ($v["jour"][$todayType]==1)) && ($tabRTitre[$i]>0) )
		  {
			$tabTitre[$i]["aff"]=$v["nom"];
			$nb=$nb+1;
		  }
	  }

	foreach($tabPlage as $i=>$v)
	  {
		if (((!is_array($v["jour"])) || ($v["jour"][$todayType]==1)) && ($tabRTitre[$i]>0) )
		  {
			$tabTitre[$i]["width"]=floor(400/$nb);		
		  }
	  }


	// Défini l'ordre
	if ($order=="") { $order="nom"; }
	if ($trie=="") { $trie="d"; }

	$tmpl_x->assign("aff_tableau",AfficheTableau($tabValeur,$tabTitre,$order,$trie,"dte=$dte"));

	$tmpl_x->assign("dte",$dte);
	$tmpl_x->assign("dtey",date("Y",strtotime($dte)));
	$tmpl_x->assign("dtem",date("m",strtotime($dte)));
	$tmpl_x->assign("month",date("Ym"));
	$tmpl_x->assign("dte_txt",$tabJour[date("w",strtotime($dte))]." ".sql2date($dte));
	
	$tmpl_x->assign("dte_prec",CalcNextDay($dte,-1));
	$tmpl_x->assign("dte_suiv",CalcNextDay($dte,1));

// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=&$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=&$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=&$tmpl_x->text("corps");

function CalcNextDay($dte,$p)
  { global $MyOpt;
	$d="";
	$d1=strtotime($dte);
	while($MyOpt["tabPresenceJour"][$d]=="")
	  {
		$d1=$d1+$p*3600*24;
		$d=date("w",$d1);
	  }
	return date("Y-m-d",$d1);
  }
?>
