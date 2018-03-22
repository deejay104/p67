<?
// ---------------------------------------------------------------------------------------------
//   Facturation des abonnements sur les comptes
// ---------------------------------------------------------------------------------------------
//   Variables  :
// ---------------------------------------------------------------------------------------------
/*
    SoceIt v2.2 ($Revision: 421 $)
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
	require_once ("class/abonnement.inc.php");

// ---- Variables
	$dte=date("Y-m-d");

	$txt="";

// ---- Charge la config  
	if (!file_exists("config/config.inc.php"))
	  { myPrint("Fichier de configuration introuvable","Il manque le fichier de configuration 'config/config.inc.php'."); exit;}

// ---- Lit les paramètres

	if (file_exists("temp/abo.txt"))
	  { $t=filemtime("temp/abo.txt"); }
	else
	  { $t=0; }

	if ((time()-$t)<20*3600)
	  {
		myPrint("Ce script ne peut s'exécuter qu'une fois par jour.");
		echo "Ce script ne peut s'exécuter qu'une fois par jour.";
	  }
	else
	  {
		if (!file_exists("temp/abo.txt"))
		  {
		  	$r=fopen("temp/abo.txt","w");
		  	fclose($r);
		  }
		touch("temp/abo.txt");
	
		myPrint("Script pour la facturation des abonnements.");
	
	
		$query ="SELECT usr.prenom, usr.nom, usr.idcpt, abo.id, abo.uid, abo.abonum, abo.jour_num, abo.jour_sem, ligne.mouvid,ligne.montant,mvt.description,mvt.compte FROM ".$MyOpt["tbl"]."_abonnement AS abo ";
		$query.="LEFT JOIN ".$MyOpt["tbl"]."_abo_ligne AS ligne ON abo.abonum=ligne.abonum ";
		$query.="LEFT JOIN ".$MyOpt["tbl"]."_mouvement AS mvt ON ligne.mouvid=mvt.id ";
		$query.="LEFT JOIN ".$MyOpt["tbl"]."_utilisateurs AS usr ON abo.uid=usr.id ";
		$query.="WHERE abo.actif='oui' AND usr.actif='oui' AND abo.dtedeb<='$dte' AND abo.dtefin>='$dte'";
		$sql->Query($query);

		$tabValeur=array();
		for($i=0; $i<$sql->rows; $i++)
		  {
			$sql->GetRow($i);
			$tabValeur[$i]=$sql->data;
		  }
		
		foreach($tabValeur as $i=>$d)
		  {
			if ( ( ($d["jour_num"]==date("j")) || (($d["jour_num"]==0) && ($d["jour_sem"]==date("w"))) ) || ($force=="yes") )
			{
				$val=$d["montant"];
		  		$query ="INSERT ".$MyOpt["tbl"]."_compte SET ";
		  		$query.="uid='".$d["idcpt"]."', ";
		  		$query.="tiers='".$MyOpt["uid_club"]."', ";
		  		$query.="montant='".(-$val)."', ";
		  		$query.="mouvement='".addslashes($d["description"])."', ";
		  		$query.="commentaire='Abonnement ".strtoupper($tabMois[date("n")])." (".$d["abonum"].")', ";
		  		$query.="date_valeur='".$dte."', ";
		  		$query.="dte='".date("Ym",strtotime($dte))."', ";
		  		$query.="compte='".$d["compte"]."', ";
		  		$query.="uid_creat=0, date_creat='".now()."'";
		  		$sql->Insert($query);

		  		$query ="INSERT ".$MyOpt["tbl"]."_compte SET ";
		  		$query.="uid='".$MyOpt["uid_club"]."', ";
		  		$query.="tiers='".$d["idcpt"]."', ";
		  		$query.="montant='".$val."', ";
		  		$query.="mouvement='".addslashes($d["description"])."', ";
		  		$query.="commentaire='Abonnement ".strtoupper($tabMois[date("n")])." (".$d["abonum"].")', ";
		  		$query.="date_valeur='".$dte."', ";
		  		$query.="dte='".date("Ym",strtotime($dte))."', ";
		  		$query.="compte='".$d["compte"]."', ";
		  		$query.="uid_creat=0, date_creat='".now()."'";
				$sql->Insert($query);
		
				myPrint($d["prenom"]." ".$d["nom"]." (".$d["uid"].") -> Mvt:".$d["description"]." Description:".$d["abonum"]." -".$d["montant"]);
				myPrint("Club (".$MyOpt["uid_club"].") -> Mvt:".$d["description"]." Description:".$d["abonum"]." ".$d["montant"]);
			}
		  }
	  }


	if ($aff=="oui")
	{
		$corps=$gl_myprint_txt;	
	}

?>
