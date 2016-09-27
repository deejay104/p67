<?
// ---------------------------------------------------------------------------------------------
//   Facturation des abonnements sur les comptes
// ---------------------------------------------------------------------------------------------
//   Variables  :
// ---------------------------------------------------------------------------------------------
/*
    SoceIt v2.2 ($Revision: 404 $)
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
	myPrint("Script pour la notification des vols non cloturés.");
	$OB="----=_OuterBoundary_000";

	if (file_exists("temp/vols.txt"))
	  { $t=filemtime("temp/vols.txt"); }
	else
	  { $t=0; }

	if ((time()-$t)<5*60*0)
	  {
		myPrint("Ce script ne peut s'exécuter que toures les 5 minutes.");
	  }
	else
	  {
		if (!file_exists("temp/vols.txt"))
		  {
		  	$r=fopen("temp/vols.txt","w");
		  	fclose($r);
		  }
		touch("temp/vols.txt");


// ---- Lit les factures
		$query ="SELECT * FROM ".$MyOpt["tbl"]."_calendrier WHERE dte_fin<='".now()."'-".(($MyOpt["tempscloture"]>0) ? $MyOpt["tempscloture"] : "30")."*60 AND horafin=0";
	
	//	if ($gl_mode!="batch")
	//	  { $query.="LIMIT 0,20"; }
	
		$sql->Query($query);
	
		$tabVol=array();
		for($i=0; $i<$sql->rows; $i++)
		  {
			$sql->GetRow($i);
			$tabVol[$i]=$sql->data;
		  }
	
		foreach($tabVol as $i=>$val)
		{
			myPrint("[".$val["id"]."] ".$val["dte_deb"]." ".$val["dte_fin"]);
	
			$tmail=file("config/mail.vols.txt");
	
			$t = '';
			foreach($tmail as $ligne)
			  { $t.=$ligne; }
	
			$usr=new user_class($val["uid_pilote"],$sql);
	
			$t=str_replace("{dte_deb}",sql2date($val["dte_deb"],"jour"),$t);
			$t=str_replace("{dte_deb_heure}",sql2date($val["dte_deb"],"heure"),$t);
			$t=str_replace("{dte_fin}",sql2date($val["dte_fin"],"jour"),$t);
			$t=str_replace("{dte_fin_heure}",sql2date($val["dte_fin"],"heure"),$t);
			$t=str_replace("{tempscloture}",$MyOpt["tempscloture"],$t);
			$t=str_replace("{site_title}",$MyOpt["site_title"],$t);
			$mail="";
	
			$mail.="\n--$OB\n";
			$mail.="Content-Type: text/plain;\n\tcharset=\"iso-8859-1\"\n";
			$mail.="Content-Transfer-Encoding: quoted-printable\n\n";
			$mail.=$t."\n\n";;
	
			$mail.="\n--$OB--\n";
	
			$headers ="MIME-Version: 1.0\r\n"; 
			$headers.="From: $from\r\n"; 
			$headers.="Reply-To: $from\r\n";
		    	$headers.="X-Mailer: PHP/" . phpversion() ."\r\n";
			$headers.="Content-Type: multipart/mixed;\n\t boundary=\"".$OB."\"\r\n";

			MyMail($from,$usr->mail,$from,"[".$MyOpt["site_title"]."] : Vol de ".$usr->fullname." non cloturé dans les temps",$mail,$headers);
	
			unset($mail);
		  }


	}

?>
