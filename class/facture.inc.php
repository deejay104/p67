<?
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

    ($Author: miniroot $)
    ($Date: 2012-07-31 22:04:07 +0200 (mar., 31 juil. 2012) $)
    ($Revision: 388 $)
*/

// Class Facture

// Liste des mois
$tabMois[1]="janvier";
$tabMois[2]="février";
$tabMois[3]="mars";
$tabMois[4]="avril";
$tabMois[5]="mai";
$tabMois[6]="juin";
$tabMois[7]="juillet";
$tabMois[8]="aout";
$tabMois[9]="septembre";
$tabMois[10]="octobre";
$tabMois[11]="novembre";
$tabMois[12]="décembre";

// Liste des jours de la semaine
$tabJour["0"]="Dimanche";
$tabJour["1"]="Lundi";
$tabJour["2"]="Mardi";
$tabJour["3"]="Mercredi";
$tabJour["4"]="Jeudi";
$tabJour["5"]="Vendredi";
$tabJour["6"]="Samedi";

class facture_class{

 	# Constructor
	function __construct($id="",$sql){
		global $MyOpt;
		global $gl_uid;

		$this->sql=$sql;
		$this->tbl=$MyOpt["tbl"];

		$this->id="";
		$this->uid="";
		$this->dte="";
		$this->dteid="";
		$this->total="";
		$this->paid="";
		$this->email="";
		$this->comment="";
		$this->lignes=array();
		$this->dte_creat="";
		$this->uid_creat="";

		$clubusr = new user_class($MyOpt["uid_club"],$sql);

		if ($id>0)
		  {
			$this->load($id);
		  }
	}

	# Charge facture
	function load($id){
		$this->id=$id;
		$sql=$this->sql;
		$query = "SELECT * FROM ".$this->tbl."_factures WHERE id='$id'";
		$res = $sql->QueryRow($query);

		// Charge les variables
		$this->uid=$res["uid"];
		$this->dte=$res["dte"];
		$this->dteid=$res["dteid"];
		$this->total=$res["total"];
		$this->paid=$res["paid"];
		$this->email=$res["email"];
		$this->comment=$res["comment"];

	}

	function save() {
		$sql=$this->sql;
		
		if ($this->id=="")
		{
			$query = "SELECT MAX(id) AS id FROM ".$this->tbl."_factures WHERE id>'".date("Y")."00000'";
			$res=$sql->QueryRow($query);
	
			if ($res["id"]=="")
			  {
				$this->id=date("Y")."00001";
			  }		  	
			else
			  {
				$this->id=$res["id"]+1;
			  }
		}

		if (is_array($this->lignes))
		{
			foreach($this->lignes as $i=>$val)
			  {
		  	  	$query="UPDATE ".$this->tbl."_compte SET facture='".$this->id."' WHERE id='".$val["id"]."'";
		  	  	$sql->Update($query);
		  	  	$total=$total+$val["montant"];
			  }

			$this->total=$total;
		}

 	  	$query="INSERT INTO ".$this->tbl."_factures SET id='".$this->id."', uid='".$this->uid."', total='".$this->total."', dte='".now()."', dteid='".date("Ym")."', comment='".$this->comment."'";
		$sql->Insert($query);
	}

	function Restant() {
		global $MyOpt;

		if ($this->id=="")
		{
			return 0;
		}
		
		$sql=$this->sql;
		$query = "SELECT SUM(".$this->tbl."_compte.montant) AS paye FROM ".$this->tbl."_compte WHERE uid='".$this->uid."' AND rembfact='".$this->id."'";
		$res=$sql->QueryRow($query);

		return round($this->total-$res["paye"],2);
	}

	function Paye () {
		$sql=$this->sql;
		$this->paid="Y";
  		$query="UPDATE ".$this->tbl."_factures SET paid='Y' WHERE id='".$this->id."'";
		$sql->Update($query);
	}

	function NonPaye () {
		$sql=$this->sql;
		$this->paid="N";
  		$query="UPDATE ".$this->tbl."_factures SET paid='N' WHERE id='".$this->id."'";
		$sql->Update($query);
	}

	function updateTotal () {
		$sql=$this->sql;
  		$query="UPDATE ".$this->tbl."_factures SET total='".$this->total."' WHERE id='".$this->id."'";
		$sql->Update($query);
	}

	function ChargeLignes() {
		global $MyOpt;
		$sql=$this->sql;

		$query = "SELECT ".$this->tbl."_compte.* FROM ".$this->tbl."_compte WHERE ".$this->tbl."_compte.uid='".$this->uid."' AND ".$this->tbl."_compte.tiers='".$MyOpt["uid_club"]."' AND ".$this->tbl."_compte.facture='".$this->id."' ORDER BY date_valeur, id";
		$query = "SELECT ".$this->tbl."_compte.* FROM ".$this->tbl."_compte WHERE uid='".$this->uid."' AND facture='".$this->id."' ORDER BY date_valeur, id";
		$sql->Query($query);
		$total=0;
		for($i=0; $i<$sql->rows; $i++)
		  { 
			$sql->GetRow($i);
			$this->lignes[$i]=$sql->data;
			$total=$total+$sql->data["montant"];
		  }

		$this->total=-$total;
		
	}

	function ChargeReglements() {
		global $MyOpt;
		
		$sql=$this->sql;
		$query = "SELECT * FROM ".$this->tbl."_compte WHERE uid='".$this->uid."' AND rembfact='".$this->id."' ORDER BY date_valeur, id";
		$sql->Query($query);
		$total=0;
		for($i=0; $i<$sql->rows; $i++)
		  { 
			$sql->GetRow($i);
			$this->reglements[$i]=$sql->data;
			$total=$total+$sql->data["montant"];
		  }
		$this->totalpaye=$total;
	}

	function FacturePDF($mode="I") {
		global $MyOpt,$tabJour,$tabMois;
		
		require_once('external/tcpdf/config/lang/eng.php');
		require_once('external/tcpdf/tcpdf.php');

		$sql=$this->sql;

		$tmpl_pdf = new XTemplate (MyRep("facture.htm"));
	
	// ---- Initialise les variables
		$tmpl_pdf->assign("form_checktime",$_SESSION['checkpost']);
	
	// ---- Affiche la facture demandée
		$tmpl_pdf->assign("id_facture",$this->id);
		if (file_exists("custom/".$MyOpt["site_logo"]))
		{
			$tmpl_pdf->assign("titre_logo", "custom/".$MyOpt["site_logo"]);
		}
		else
		{
			$tmpl_pdf->assign("titre_logo", "static/images/logo.png");
		}
	
		$clubusr = new user_class($MyOpt["uid_club"],$sql);
		$tmpl_pdf->assign("club_nom_compte", preg_replace("/-/"," ",$clubusr->Aff("prenom","val"))." ".$clubusr->Aff("nom","val"));
		$tmpl_pdf->assign("club_addr1", $clubusr->Aff("adresse1"));
		$tmpl_pdf->assign("club_addr2", $clubusr->Aff("adresse2").(($clubusr->data["adresse2"]!="") ? $clubusr->Aff("adresse2") : "" ));
		$tmpl_pdf->assign("club_ville", $clubusr->Aff("ville"));
		$tmpl_pdf->assign("club_ville2", ucwords($clubusr->Aff("ville")));
	
		$tmpl_pdf->assign("club_codepostal", $clubusr->Aff("codepostal"));
		$tmpl_pdf->assign("club_tel_fixe", $clubusr->Aff("tel_fixe"));
		$tmpl_pdf->assign("club_mail", $clubusr->Aff("mail","val"));
	
		// Nom de l'utilisateur
		$cptusr=new user_class($this->uid,$sql);
		$tmpl_pdf->assign("nom_compte", $cptusr->fullname);
		$tmpl_pdf->assign("addr1", $cptusr->Aff("adresse1"));
		$tmpl_pdf->assign("addr2", $cptusr->Aff("adresse2").(($cptusr->data["adresse2"]!="") ? $cptusr->Aff("adresse2") : "" ));
		$tmpl_pdf->assign("ville", $cptusr->Aff("ville"));
		$tmpl_pdf->assign("codepostal", $cptusr->Aff("codepostal"));
	
		// Définition des variables
		$myColor[0]="F0F0F0";
		$myColor[1]="F7F7F7";
	
		$tmpl_pdf->assign("titre_facture",$this->comment);
	
		$dte=strtotime($this->dte);
		$tmpl_pdf->assign("date_facture1",$tabJour[date("w",$dte)]." ".date("j",$dte)." ".$tabMois[date("n",$dte)]." ".date("Y",$dte));
		$tmpl_pdf->assign("date_facture2",date("j",$dte)." ".$tabMois[date("n",$dte)]." ".date("Y",$dte));
	
	// ---- Affiche les lignes de factures
		if (is_array($this->lignes)) {
			foreach($this->lignes as $i=>$v)
			  {
				$tmpl_pdf->assign("id_ligne", $v["id"]);
				$tmpl_pdf->assign("designation_ligne", htmlentities($v["mouvement"],ENT_HTML5,"ISO-8859-1"));
				$tmpl_pdf->assign("comment_ligne", htmlentities($v["commentaire"],ENT_HTML5,"ISO-8859-1"));
				$tmpl_pdf->assign("date_ligne", date("d/m/Y",strtotime($v["date_valeur"])));
				$tmpl_pdf->assign("montant_ligne", AffMontant(round(-$v["montant"],2)));
		
				$tmpl_pdf->parse("corps.lst_ligne");
			  }
		}	
		$tmpl_pdf->assign("texte_paiement",nl2br($MyOpt["texte_paiement"]));
		$tmpl_pdf->assign("total_facture",AffMontant(round($this->total,2)));

	// ---- Affiche les lignes de reglements
		if (is_array($this->reglements)) {
			foreach($this->reglements as $i=>$v)
			  {
				$tmpl_pdf->assign("id_ligne", $v["id"]);
				$tmpl_pdf->assign("designation_ligne", htmlentities($v["mouvement"],ENT_HTML5,"ISO-8859-1"));
				$tmpl_pdf->assign("comment_ligne", htmlentities($v["commentaire"],ENT_HTML5,"ISO-8859-1"));
				$tmpl_pdf->assign("date_ligne", date("d/m/Y",strtotime($v["date_valeur"])));
				$tmpl_pdf->assign("montant_ligne", htmlentities(AffMontant(round($v["montant"],2)),ENT_HTML5,"ISO-8859-1"));
		
				$tmpl_pdf->parse("corps.aff_reglement.lst_reglement");
			  }
			$tmpl_pdf->parse("corps.aff_reglement");
		}		
		$tmpl_pdf->assign("total_restant",AffMontant(round($this->restant(),2)));
	
	
	// ---- Affecte les variables d'affichage
		require_once('external/tcpdf/config/lang/eng.php');
		require_once('external/tcpdf/tcpdf.php');
	
		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, false, 'ISO-8859-1', false);
		//$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'ISO-8859-1', false);
		
		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor($MyOpt["site_title"]);
		$pdf->SetTitle('Facture '.$MyOpt["site_title"]);
		$pdf->SetSubject('Facture');
		$pdf->SetKeywords('Facture');
		
		// set default header data
		$pdf->SetHeaderData("", "", "", "");
		
		// set header and footer fonts
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
				
		//set margins
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		
		
		// ---------------------------------------------------------
				
		// Set font
		// dejavusans is a UTF-8 Unicode font, if you only need to
		// print standard ASCII chars, you can use core fonts like
		// helvetica or times to reduce file size.
		$pdf->SetFont('helvetica', '', 14, '', true);
		
		// Add a page
		// This method has several options, check the source code documentation for more information.
		$pdf->AddPage();
	
		$tmpl_pdf->parse("corps");
		$html="";
		$html = &$tmpl_pdf->text("corps");
	
		// output the HTML content
		$pdf->writeHTML($html, true, false, true, false, '');
	
		// reset pointer to the last page
		$pdf->lastPage();
		
		// ---------------------------------------------------------
		
		//Close and output PDF document
		if ($mode=="")
		 { $mode="I"; }
	
		// $corps=$pdf->Output('Facture.pdf', $mode);
	
		//============================================================+
		// END OF FILE                                                 
		//============================================================+
		$ret=$pdf->Output('Facture.pdf', $mode);
		unset($pdf);
		unset($tmpl_pdf);
		return $ret;
	}
}

function ListeFactures($sql,$usr,$deb,$nb,$order,$way="",$dte="")
{ global $MyOpt,$MyOpt;

	$query = "SELECT ".$MyOpt["tbl"]."_factures.id FROM ".$MyOpt["tbl"]."_factures WHERE ".(($usr>0) ? $MyOpt["tbl"]."_factures.uid=$usr" : "1=1")." ".((($dte>0)) ? "AND dteid='$dte'" : "")." ORDER BY $order $way LIMIT $deb,$nb";
	$sql->Query($query);
	$col=50;

	$t=array();
	for($i=0; $i<$sql->rows; $i++)
	  { 
		$sql->GetRow($i);
		$t[$i]=$sql->data["id"];
	  }

	return $t;
}


function NbFactures($sql,$usr,$dte="")
{ global $MyOpt;
	$query = "SELECT COUNT(*) AS nb FROM ".$MyOpt["tbl"]."_factures WHERE ".(($usr>0) ? $MyOpt["tbl"]."_factures.uid=$usr" : "1=1").((($dte>0)) ? " AND dteid='$dte'" : "");
	$res=$sql->QueryRow($query);
	return $res["nb"];
}

?>
