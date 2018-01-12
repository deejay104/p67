<?
require_once ("class/user.inc.php");

class compte_class{

 	# Constructor
	function __construct($id="",$sql)
	{
		global $MyOpt;
		global $gl_uid;

		$this->sql=$sql;
		$this->tbl=$MyOpt["tbl"];
		$this->myuid=$gl_uid;

		$this->id=0;

		$this->deb=0;
		$this->cre=0;
		$this->ventilation=array();
		$this->montant=0;
		$this->poste=0;
		$this->commentaire="";
		$this->date_valeur=date("Y-m-d H:i:s");
		$this->compte="";
		$this->status="brouillon";
		$this->uid_creat=$gl_uid;
		$this->date_creat=now();
		
		if ($id>0)
		{
			$this->load($id);
		}
	}

	# Load document
	function load($id)
	{
		$this->id=$id;
		$sql=$this->sql;
		$query = "SELECT * FROM ".$this->tbl."_comptetemp WHERE id='$id'";
		$res = $sql->QueryRow($query);

		$this->poste=$res["poste"];
		$this->commentaire=$res["commentaire"];
		$this->montant=$res["montant"];
		$this->ventilation=$res["ventilation"];
		$this->date_valeur=$res["date_valeur"];
		$this->dte=date("Ym",strtotime($this->date_valeur));
		$this->status=$res["status"];

		$this->mvt=json_decode($this->ventilation,true);
	}
	
	function generate($uid,$poste,$txt,$dte,$montant,$ventilation)
	{
		global $MyOpt;

		$sql=$this->sql;
		$query="SELECT * FROM ".$this->tbl."_mouvement WHERE id='".$poste."'";
		$res=$sql->QueryRow($query);

		$deb=0;
		if ($res["debiteur"]=="B")
		  { $deb=$MyOpt["uid_banque"]; }
		else if ($res["debiteur"]=="C")
		  { $deb=$MyOpt["uid_club"]; }
		else if ($res["debiteur"]>0)
		  { $deb=$res["debiteur"]; }
		else if ($uid=="*")
		  { $deb=0; }
		else
		  { $deb=$uid; }
		$this->deb=$deb;

		$cre=0;
		if ($res["crediteur"]=="B")
		  { $cre=$MyOpt["uid_banque"]; }
		else if ($res["crediteur"]=="C")
		  { $cre=$MyOpt["uid_club"]; }
		else if ($res["crediteur"]>0)
		  { $cre=$res["crediteur"]; }
		else if ($uid=="*")
		  { $cre=0; }
		else
		  { $cre=$uid; }
		$this->cre=$cre;

		$this->mvt[1]["uid"]=$deb;
		$this->mvt[1]["tiers"]=$cre;
		$this->mvt[1]["montant"]=-$montant;
		$this->mvt[1]["poste"]=$poste;

		$this->mvt[2]["uid"]=$cre;
		$this->mvt[2]["tiers"]=$deb;
		$this->mvt[2]["montant"]=$montant;
		$this->mvt[2]["poste"]=$poste;
		
		$this->poste=$poste;
		$this->commentaire=$txt;
		$this->montant=$montant;
		$this->date_valeur=$dte;
		$this->dte=date("Ym",strtotime($dte));
		$this->ventilation=json_encode($this->mvt);
	}

	function Save()
	{
		$sql=$this->sql;
		if ($this->id==0)
		{
			$query="INSERT INTO ".$this->tbl."_comptetemp SET deb='".$this->deb."', cre='".$this->cre."', ventilation='".$this->ventilation."',montant='".$this->montant."', poste='".$this->poste."', commentaire='".addslashes($this->commentaire)."', date_valeur='".$this->date_valeur."', compte='".$this->compte."', status='".$this->status."', uid_creat='".$this->myuid."',date_creat='".now()."'";
			$this->id=$sql->Insert($query);
		}
		else
		{
			$query="UPDATE ".$this->tbl."_comptetemp SET deb='".$this->deb."', cre='".$this->cre."', ventilation='".$this->ventilation."',montant='".$this->montant."', poste='".$this->poste."', commentaire='".addslashes($this->commentaire)."', date_valeur='".$this->date_valeur."', compte='".$this->compte."', status='".$this->status."', uid_creat='".$this->myuid."',date_creat='".now()."' WHERE id='".$this->id."'";
			$sql->Update($query);
		}
	}

	function Debite()
	{
		$this->erreur="";
		if ($this->status!="brouillon")
		{
			$this->erreur="Cette transaction a déjà été débitée<br>";
			return 0;
		}
		
		// Débite le mouvement sur les comptes
		$sql=$this->sql;

		$this->nbmvt=0;
		$totmnt=0;
		foreach($this->mvt as $i=>$m)
		{
			$query="SELECT description FROM ".$this->tbl."_mouvement WHERE id='".$m["poste"]."'";
			$res=$sql->QueryRow($query);

			$query ="INSERT ".$this->tbl."_compte SET ";
			$query.="mid='".$this->id."', ";
			$query.="uid='".$m["uid"]."', ";
			$query.="tiers='".$m["tiers"]."', ";
			$query.="montant='".$m["montant"]."', ";
			$query.="mouvement='".addslashes($res["description"])."', ";
			$query.="commentaire='".addslashes($this->commentaire)."', ";
			$query.="facture='".(($this->facture=="") ? "NOFAC" : "")."', ";
			$query.="date_valeur='".$this->date_valeur."', ";
			$query.="dte='".date("Ym",strtotime($this->date_valeur))."', ";
			$query.="compte='".$this->compte."', ";
			$query.="uid_creat=".$this->uid_creat.", date_creat='".now()."'";
			// echo "$query<BR>";
			$sql->Insert($query);
			$this->nbmvt++;
			$totmnt=$totmnt+$form_montant[$k];
		}

		$this->status="debite";
	  	$query="UPDATE ".$this->tbl."_comptetemp SET status='debite', uid_creat='".$this->myuid."',date_creat='".now()."' WHERE id='".$this->id."'";
		$sql->Update($query);
		
		if ($totmnt<>0)
		{
			$this->erreur="La somme totale des montants n'est pas nulle.<br />";
		}
		return $this->nbmvt;
	}

	function Annule()
	{
		$sql=$this->sql;
	  	$query="UPDATE ".$this->tbl."_comptetemp SET status='annule', uid_creat='".$this->myuid."',date_creat='".now()."' WHERE id='".$this->id."'";
		$sql->Update($query);
	}
	
	function Affiche()
	{
		$sql=$this->sql;
		$txt ="<input type='hidden' name='form_mid[".$this->id."]' value='ok'>";
		$txt.="<table class='tableauCompte'>";

		$tabcol=array();
		$tabcol[0]="fafafa";
		$tabcol[1]="ffffff";
		$c=0;
		
		foreach ($this->mvt as $i=>$d)
		{
			$query="SELECT description FROM ".$this->tbl."_mouvement WHERE id='".$d["poste"]."'";
			$res=$sql->QueryRow($query);

			$deb = new user_class($d["uid"],$sql,false);

			// $txt.="<tr style='background-color: #fafafa;'>";
			$txt.="<tr style='background-color: #".$tabcol[$c].";'>";
			$txt.="<td width='120'>".sql2date($this->date_valeur)."</td>";
			$txt.="<td width='350'>".$res["description"]."</td>";
			$txt.="<td width='350'>".$this->commentaire."</td>";
			$txt.="<td width='200'>".$deb->fullname."</td>";
			$txt.="<td width='100' style='border-left:1px solid black; text-align:right; padding-right:10px;'>".AffMontant($d["montant"])."</td>";
			$txt.="</tr>";
			$c=1-$c;
		// $txt.="<tr>";
		// $txt.="<td>".sql2date($this->date_valeur)."</td>";
		// $txt.="<td>".$res["description"]."</td>";
		// $txt.="<td>".$this->commentaire."</td>";
		// $txt.="<td>".$cre->fullname."</td>";
		// $txt.="<td style='border-left:1px solid black; text-align:right; padding-right:10px;'>".AffMontant($this->montant)."</td>";
		// $txt.="</tr>";
		}
		$txt.="</table>";

		return $txt;
	}

	
	function AfficheEntete()
	{
		$txt ="<table>";
		$txt.="<tr style='height:30px; border-bottom:1px solid black; background-color:#cccccc; text-decoration: bold;'>";
		$txt.="<th width=120>Date</th>";
		$txt.="<th width=350>Poste</th>";
		$txt.="<th width=350>Commentaire</th>";
		$txt.="<th width=200>Membre</th>";
		$txt.="<th width=100 style='border-left:1px solid black; text-align:right; padding-right:10px;'>Montant</th>";
		$txt.="</tr>";
		$txt.="</table>";

		return $txt;
	}
}
?>