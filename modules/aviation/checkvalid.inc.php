<?
// ---------------------------------------------------------------------------------------------
//   Distribution des mails de la liste de diffusion
// ---------------------------------------------------------------------------------------------
//   Variables  :
// ---------------------------------------------------------------------------------------------
/*
    SoceIt v2.2 ($Revision: 385 $)
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

// ---- Lit les param�tres
//	$id=$HTTP_GET_VARS["id"];
exit;
	if (file_exists("temp/check.txt"))
	  { $t=filemtime("temp/check.txt"); }
	else
	  { $t=0; }

	if ((time()-$t)<7*24*3600)
	  {
	  	$txt.=myPrint("Ce script ne peut s'ex�cuter que tous les 7 jours.");
	  }
	else
	  {
		if (!file_exists("temp/check.txt"))
		  {
		  	$r=fopen("temp/check.txt","w");
		  	fclose($r);
		  }
		touch("temp/check.txt");
	
		$txt.=myPrint("Script pour mail de rappel.");

// ---- Mail du pr�sident
		$query="SELECT p67_utilisateurs.mail FROM p67_utilisateurs WHERE droits LIKE '%PRE%' AND actif='oui'";
		$sql->Query($query);
		
		$tabPre=array();
		for($i=0; $i<$sql->rows; $i++)
		{ 
			$sql->GetRow($i);
		
			$tabPre[$i]=$sql->data["mail"];
		}
		$mailpre=$tabPre[0];

		$txt.=myPrint("Pr�sident : '$mailpre'");

// ---- Mail du tr�sorier
		$query="SELECT p67_utilisateurs.mail FROM p67_utilisateurs WHERE droits LIKE '%TRE%' AND actif='oui'";
		$sql->Query($query);
		
		$tabTre=array();
		for($i=0; $i<$sql->rows; $i++)
		{ 
			$sql->GetRow($i);
		
			$tabTre[$i]=$sql->data["mail"];
		}
		$mailtre=$tabTre[0];

		$txt.=myPrint("Tr�sorier : '$mailtre'");

// ---- Liste les comptes actifs

		$query="SELECT usr.id, usr.nom, usr.prenom, usr.mail, usr.dte_licence, usr.dte_medicale, usr.decouvert, usr.type, SUM(cpt.montant) AS total FROM p67_utilisateurs AS usr, p67_compte AS cpt WHERE usr.id = cpt.uid AND usr.actif<>'non' AND usr.virtuel='non' GROUP BY usr.id";
		$sql->Query($query);
		for($i=0; $i<$sql->rows; $i++)
		  { 
			$sql->GetRow($i);
	
			$txt.=myPrint($sql->data["prenom"]." ".$sql->data["nom"]." <".$sql->data["mail"]."> ");
	
			if ($sql->data["total"]<-$sql->data["decouvert"])
		  {
			  	$txt.=myPrint("[".$sql->data["total"]."] ");
	
				$tmail=file("config/mail.decouvert.txt");
	
				$mail = '';
				foreach($tmail as $ligne)
				  { $mail.=$ligne; }
	
				$mail=nl2br(str_replace("{solde}",$sql->data["total"],$mail));
	
				$ret=MyMail($mailtre,$sql->data["mail"],$tabTre,"[".$MyOpt["site_title"]."] : Ton compte est � d�couvert",$mail,"");
		  }
	
			if ( ($sql->data["dte_licence"]!="0000-00-00") && (($sql->data["dte_licence"]=='pilote') || ($sql->data["dte_licence"]=='eleve') || ($sql->data["dte_licence"]=='instructeur')) )
		  {
				if (date_diff_txt($sql->data["dte_licence"],date("Y-m-d"))>0)
			  {
					$txt.=myPrint("[Licence out] ");
	
					$tmail=file("config/mail.dteechue.txt");
					$mail = '';
					foreach($tmail as $ligne)
					  { $mail.=$ligne; }
					$mail=nl2br(str_replace("{type}","ta licence",$mail));
	
					$ret=MyMail($mailpre,$sql->data["mail"],$tabPre,"[".$MyOpt["site_title"]."] : Validit� de ta licence �chue",$mail,"");
			  }
				else if ((date_diff_txt($sql->data["dte_licence"],date("Y-m-d"))>-31*24*3600) && (date_diff_txt($sql->data["dte_licence"],date("Y-m-d"))<-23*24*3600))
			  {
					$txt.=myPrint("[Licence] ");
	
					$tmail=file("config/mail.dtevalide.txt");
					$mail = '';
					foreach($tmail as $ligne)
					  { $mail.=$ligne; }
					$mail=str_replace("{type}","ta licence",$mail);
	
					$ret=MyMail($mailpre,$sql->data["mail"],$tabPre,"[".$MyOpt["site_title"]."] : Validit� de ta licence p�rim�e dans le mois",$mail,"");
			  }
		  }
		
			if ( ($sql->data["dte_medicale"]!="0000-00-00") && (($sql->data["dte_licence"]=='pilote') || ($sql->data["dte_licence"]=='eleve') || ($sql->data["dte_licence"]=='instructeur')) )
			  {
				if (date_diff_txt($sql->data["dte_medicale"],date("Y-m-d"))>0)
				  {
					$txt.=myPrint("[VM out] ");
	
					$tmail=file("config/mail.dteechue.txt");
					$mail = '';
					foreach($tmail as $ligne)
					  { $mail.=$ligne; }
					$mail=str_replace("{type}","ta visite m�dicale",$mail);
	
					$ret=MyMail($mailpre,$sql->data["mail"],$tabPre,"[".$MyOpt["site_title"]."] : Validit� de ta visite m�dicale �chue",$mail,"");
				  }
				else if ((date_diff_txt($sql->data["dte_medicale"],date("Y-m-d"))>-30*24*3600) && (date_diff_txt($sql->data["dte_medicale"],date("Y-m-d"))<-23*24*3600))
				  {
					$txt.=myPrint("[VM] ");
	
					$tmail=file("config/mail.dtevalide.txt");
					$mail = '';
					foreach($tmail as $ligne)
					  { $mail.=$ligne; }
					$mail=str_replace("{type}","ta visite m�dicale",$mail);
	
					$ret=MyMail($mailpre,$sql->data["mail"],$tabPre,"[".$MyOpt["site_title"]."] : Validit� de ta visite m�dicale p�rim�e dans le mois",$mail,"");
				  }
			  }
	
			$txt.=myPrint("");
			$txt.=myPrint("");
		  }
	  }

	$corps=$txt;
?>
