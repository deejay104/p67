<?
// ---------------------------------------------------------------------------------------------
//   Notification de nouvelles factures
// ---------------------------------------------------------------------------------------------
//   Variables  :
// ---------------------------------------------------------------------------------------------
/*
    SoceIt v2.2 ($Revision: 420 $)
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
  	require_once ("class/facture.inc.php");

	set_time_limit(0);

// ---- Variables
	$dte=date("Y-m-d");

	$txt="";

// ---- Charge la config  
	if (!file_exists("config/config.inc.php"))
	  { myPrint("Fichier de configuration introuvable","Il manque le fichier de configuration 'config/config.inc.php'."); exit; }

// ---- Récupère l'adresse email de l'émetteur
	$clubusr = new user_class($MyOpt["uid_club"],$sql);
	$from=$clubusr->data["mail"];


// ---- Définit les variables
	myPrint("Script pour la notification de nouvelle facture.");

// ---- Lit les factures
	$query ="SELECT factures.*, utilisateurs.id AS myid, utilisateurs.prenom, utilisateurs.nom, utilisateurs.mail FROM ".$MyOpt["tbl"]."_factures AS factures LEFT JOIN ".$MyOpt["tbl"]."_utilisateurs AS utilisateurs ON factures.uid=utilisateurs.id WHERE factures.email='N' ";
//$query.=" AND utilisateurs.id=162 ";

	if ($gl_mode!="batch")
	  { $query.="LIMIT 0,20"; }

	$sql->Query($query);

	$tabFac=array();
	for($i=0; $i<$sql->rows; $i++)
	  {
		$sql->GetRow($i);
		$tabFac[$i]=$sql->data;
	  }

	foreach($tabFac as $i=>$val)
	{
		myPrint("[".$val["id"]."] ".$val["prenom"]." ".$val["nom"]);

		$tmail=file("config/mail.facture.txt");

		$t = '';
		foreach($tmail as $ligne)
		  { $mail.=$ligne; }

		$mail=str_replace("{facnum}",$val["id"],nl2br($mail));
		$mail=str_replace("{facdate}",$tabMois[date("n",strtotime($val["dte"]))]." ".date("Y",strtotime($val["dte"])),$mail);

		// Charge la facture en PDF
		$fac = new facture_class($val["id"],$sql);
		$fac->ChargeLignes();
		$fac->ChargeReglements();
		$attach=array();
		$attach[0]["nom"]="facture.pdf";
		$attach[0]["type"]="text";
		$attach[0]["data"]=$fac->FacturePDF("S");

		MyMail($from,$val["mail"],"","[".$MyOpt["site_title"]."] : Relance facture ".$val["id"]." pour ".$tabMois[date("n",strtotime($val["dte"]))]." ".date("Y",strtotime($val["dte"])),$mail,"",$attach);

		unset($mail);
		unset($attach);

		$query ="UPDATE ".$MyOpt["tbl"]."_factures AS factures SET factures.email='Y' WHERE factures.id='".$val["id"]."' ";
		$sql->Query($query);
		
	}




?>
