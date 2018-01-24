<?h
// ---------------------------------------------------------------------------------------------
//   Script de sauvegarde d'une r�servation
//     ($Author: miniroot $)
//     ($Date: 2016-02-14 23:17:30 +0100 (dim., 14 févr. 2016) $)
//     ($Revision: 445 $)
// ---------------------------------------------------------------------------------------------
//   Variables  : 
// ---------------------------------------------------------------------------------------------
/*
    SoceIt v2.0
    Copyright (C) 2005 Matthieu Isorez

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
// ---- Initialisation des variables

//	$tmpl_x->assign("form_checktime",$_SESSION['checkpost']);
	require_once ("class/reservation.inc.php");
	require_once ("class/echeance.inc.php");

  	$ok=1;
	$msg_err="";

//echo "Mettre le test de r�sa ici";

// --- Charge la r�servation
	if (!is_numeric($id))
	  { $id=0; }

	$resa["resa"]=new resa_class($id,$sql);

// ---- V�rifie les infos
	if (($fonc=="Enregistrer") || ($fonc=="Devis masse"))
	  {
		$ok=1;

		if ($form_uid_pilote>0)
		{
			$resa["pilote"]=new user_class($form_uid_pilote,$sql,false,true);
		}
		else
		{
			$resa["pilote"]=new user_class($resa["resa"]->uid_pilote,$sql,false,true);
		}

		if ($form_uid_instructeur>0)
		{
			$resa["instructeur"]=new user_class($form_uid_instructeur,$sql,false,true);
		}
		else
		{
			$resa["instructeur"]=new user_class($resa["resa"]->uid_instructeur,$sql,false,true);
		}

		$s=$resa["pilote"]->CalcSolde();

		if ( $s < -$resa["pilote"]->data["decouvert"])
		{
		  	$msg_err.="<u>Le compte du pilote est NEGATIF ($s �)</u>.<br />";
		  	$msg_err.="&nbsp;&nbsp;&nbsp;&nbsp;*&nbsp;Appeller le tr�sorier pour l'autorisation d'un d�couvert.<br />";
			$ok=4;
		}

		if ($resa["resa"]->edite=='non')
		{
		  	$msg_err.="<u>R�servation d�j� saisie en compta</u>.<br />";
		  	$msg_err.="&nbsp;&nbsp;&nbsp;&nbsp;*&nbsp;Il n'est plus possible de modifier cette r�servation car elle a d�j� �t� saisie en compta.<br />";
			$ok=3;
		}

		// V�rifie si le pilote est lach� sur l'avion
		if ((!$resa["pilote"]->CheckLache($form_uid_ress)) && ($form_uid_instructeur==0))
		{
		  	$msg_err.="<u>R�servation impossible</u>.<br />";
		  	$msg_err.="&nbsp;&nbsp;&nbsp;&nbsp;*&nbsp;Le pilote s�lectionn� n'est pas lach� sur cet avion.<br />";
		  	$msg_err.="&nbsp;&nbsp;&nbsp;&nbsp;*&nbsp;Il n'est pas possible de r�server sans instructeur.<br />";
			$ok=3;
		}

		// V�rifie si le pilote est autoris�
		if (($resa["pilote"]->data["type"]=="invite") || ($resa["pilote"]->data["type"]=="membre"))
		{ 
		  	$msg_err.="<u>R�servation impossible</u>.<br />";
			$msg_err.="&nbsp;&nbsp;&nbsp;&nbsp;*&nbsp;Le pilote s�lectionn� n'a pas le droit d'effectuer de r�servation d'avion<BR>";
			$ok=3;
		}

		if ($resa["resa"]->edite!='non')
		  {
		  	if ($ok==1)
		  	  {
				$resa["resa"]->description=$form_description;
				$resa["resa"]->uid_pilote=$form_uid_pilote;
				$resa["resa"]->uid_debite=$form_uid_debite;
				$resa["resa"]->uid_instructeur=$form_uid_instructeur;
				$resa["resa"]->uid_ressource=$form_uid_ress;
				$resa["resa"]->destination=$form_destination;
				$resa["resa"]->nbpersonne=$form_nbpersonne;
				$resa["resa"]->invite=$form_invite;
				$resa["resa"]->accept=$form_accept;
				$resa["resa"]->tpsestime=$form_tpsestime;
				$resa["resa"]->dte_deb="$form_dte_deb $form_hor_deb";
				$resa["resa"]->dte_fin="$form_dte_fin $form_hor_fin";
				$resa["resa"]->tpsreel=$form_tpsreel;
				$resa["resa"]->horadeb=$form_horadeb;
				$resa["resa"]->horafin=$form_horafin;
				$resa["resa"]->potentielh=$form_potentielh;
				$resa["resa"]->potentielm=$form_potentielm;
			  }


			// On peut mettre � jour les donn�es de vols m�me si on est en n�gatif.
			if (($id>0) && ($ok==4) && (($form_tpsreel!="") || ($form_horadeb!="") || ($form_horafin!="")))
			  {
				$ok=1;

				$resa["resa"]->dte_deb=sql2date($resa["resa"]->dte_deb);
				$resa["resa"]->dte_fin=sql2date($resa["resa"]->dte_fin);

				$resa["resa"]->tpsreel=$form_tpsreel;
				$resa["resa"]->horadeb=$form_horadeb;
				$resa["resa"]->horafin=$form_horafin;
			  }
		  }
	  }

// ---- Supprime la r�servation
	else if (($fonc=="delete") && ($id>0))
	  {
		$ok=0;
		$resa["resa"]=new resa_class($id,$sql);
		$ress=$resa["resa"]->uid_ressource;
		$resa["resa"]->Delete();
		$affrub="index";
	  }
	else
	  {
	  	$ok=3;
	  }



// ---- Enregistre
	$affrub="index";
	$jour=$resa["resa"]->dte_deb;
	$msg_err2="";
	if (($ok==1) && (!isset($_SESSION['tab_checkpost'][$checktime])))
	  {
		$msg_err2.=$resa["resa"]->Save();

		if (($id==0) && ($resa["resa"]->invite=='oui'))
		{
			$resa["pilote"]=new user_class($resa["resa"]->uid_pilote,$sql);

			$query="INSERT INTO ".$MyOpt["tbl"]."_actualites SET ";
			$query.="titre='Recherche passager(s)',";
			$query.="message='".addslashes("Bonjour,\n\nIl me reste des places dans mon vol du ".sql2date($resa["resa"]->dte_deb).". Faites moi savoir si cela vous interresse.\n\n".$resa["pilote"]->Aff("fullname","val"))."',";
			$query.="uid_creat='".$resa["resa"]->uid_pilote."',";
			$query.="dte_creat='".now()."',";
			$query.="uid_modif='".$resa["resa"]->uid_pilote."',";
			$query.="dte_modif='".now()."'";

			$id=$sql->Insert($query);
		}
		
		if ($id==0)
		  {	$id=$resa["resa"]->id; }

		if (($msg_err2=="") && ($msg_err==""))
		  {
			$_SESSION['tab_checkpost'][$checktime]=$checktime;
			$affrub="index";
			$ress=$form_uid_ress;
			$ok=0;
		  }
		else
		  {
		  	$msg_err.=$msg_err2;
		  	$ok=3;
		  }
	  }
	if ($fonc=="Annuler")  	
	  {
		$ok=0;
		$affrub="index";
		$ress=$form_uid_ress;
	  }

	if ($fonc=="Devis masse")  	
	  {
		$ok=0;
		$mod="aviation";
		$affrub="centrage";
	  }

	if ($ok>1)
	  {
	  	$affrub="reservation";
	  }

?>
