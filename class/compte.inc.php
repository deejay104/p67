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
		$this->facture="NOFAC";
		$this->rembfact="";
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
		$this->compte=$res["compte"];
		$this->facture=$res["facture"];
		$this->rembfact=$res["rembfact"];
		$this->dte=date("Ym",strtotime($this->date_valeur));
		$this->status=$res["status"];

		$this->mvt=json_decode($this->ventilation,true);
	}
	
	function generate($uid,$poste,$txt,$dte,$montant,$ventilation,$facture="NOFAC",$rembfact="")
	{
		global $MyOpt;

		$sql=$this->sql;
		$query="SELECT * FROM ".$this->tbl."_mouvement WHERE id='".$poste."'";
		$res=$sql->QueryRow($query);
		$compte=$res["compte"];

		$deb=array();
		if ($res["debiteur"]=="B")
		  { $deb[0]=$MyOpt["uid_banque"]; }
		else if ($res["debiteur"]=="C")
		  { $deb[0]=$MyOpt["uid_club"]; }
		else if ($res["debiteur"]>0)
		  { $deb[0]=$res["debiteur"]; }
		else if ($uid=="*")
		{
			$query = "SELECT id FROM ".$MyOpt["tbl"]."_utilisateurs WHERE actif='oui' AND virtuel='non'";
			$sql->Query($query);
			for($i=0; $i<$sql->rows; $i++)
			{ 
				$sql->GetRow($i);
				$deb[$i]=$sql->data["id"];
			}
		}
		else
		{
			$deb[0]=$uid;
		}
		$this->deb=$deb[0];

		$cre=array();
		if ($res["crediteur"]=="B")
		  { $cre[0]=$MyOpt["uid_banque"]; }
		else if ($res["crediteur"]=="C")
		  { $cre[0]=$MyOpt["uid_club"]; }
		else if ($res["crediteur"]>0)
		  { $cre[0]=$res["crediteur"]; }
		else if ($uid=="*")
		{
			$query = "SELECT id FROM ".$MyOpt["tbl"]."_utilisateurs WHERE actif='oui' AND virtuel='non'";
			$sql->Query($query);
			for($i=0; $i<$sql->rows; $i++)
			{ 
				$sql->GetRow($i);
				$cre[$i]=$sql->data["id"];
			}
		}
		else
		{
			$cre[0]=$uid;
		}
		$this->cre=$cre[0];

		$i=1;
		foreach ($deb as $d)
		{
		    foreach ($cre as $c)
		    {
				if (($c>0) && ($d>0) && ($dte!=""))
				{
					// Vérifie le montant
					preg_match("/^(-?[0-9]*)\.?,?([0-9]*)?$/",$montant,$t);
					$montant=$t[1].".".$t[2];

					// Parcours les ventilations
					$tot["debiteur"]=$montant;
					$tot["crediteur"]=$montant;
					foreach ($ventilation["data"] as $i=>$v)
					{
						// Vérifie le montant
						preg_match("/^(-?[0-9]*)\.?,?([0-9]*)?$/",$v["montant"],$t);
						$m=$t[1].".".$t[2];
						// Créé les lignes de mouvement
						$this->mvt[$i]["uid"]=$v["tiers"];
						$this->mvt[$i]["tiers"]=($ventilation["ventilation"]=="debiteur") ? $d : $c;
						$this->mvt[$i]["montant"]=($ventilation["ventilation"]=="debiteur") ? -$m : $m;
						$this->mvt[$i]["poste"]=$v["poste"];
						$this->mvt[$i]["facture"]=$facture;
						$this->mvt[$i]["rembfact"]=$rembfact;
						$i=$i+1;
						$tot[$ventilation["ventilation"]]=$tot[$ventilation["ventilation"]]-$m;
					}
					
					// Complète s'il y a un reste
					if ($tot["debiteur"]<>0)
					{
						$this->mvt[$i]["uid"]=$d;
						$this->mvt[$i]["tiers"]=$c;
						$this->mvt[$i]["montant"]=-$tot["debiteur"];
						$this->mvt[$i]["poste"]=$poste;
						$this->mvt[$i]["facture"]=$facture;
						$this->mvt[$i]["rembfact"]=$rembfact;
						$i=$i+1;
					}

					if ($tot["crediteur"]<>0)
					{
						$this->mvt[$i]["uid"]=$c;
						$this->mvt[$i]["tiers"]=$d;
						$this->mvt[$i]["montant"]=$tot["crediteur"];
						$this->mvt[$i]["poste"]=$poste;
						$this->mvt[$i]["facture"]=$facture;
						$this->mvt[$i]["rembfact"]=$rembfact;
						$i=$i+1;
					}
				}
			}
		}
		
		$this->poste=$poste;
		$this->commentaire=$txt;
		$this->compte=$compte;
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
			$query="INSERT INTO ".$this->tbl."_comptetemp SET deb='".$this->deb."', cre='".$this->cre."', ventilation='".$this->ventilation."',montant='".$this->montant."', poste='".$this->poste."', commentaire='".addslashes($this->commentaire)."', date_valeur='".$this->date_valeur."', compte='".$this->compte."', facture='".$this->facture."', rembfact='".$this->rembfact."', status='".$this->status."', uid_creat='".$this->myuid."',date_creat='".now()."'";
			$this->id=$sql->Insert($query);
		}
		else
		{
			$query="UPDATE ".$this->tbl."_comptetemp SET deb='".$this->deb."', cre='".$this->cre."', ventilation='".$this->ventilation."',montant='".$this->montant."', poste='".$this->poste."', commentaire='".addslashes($this->commentaire)."', date_valeur='".$this->date_valeur."', compte='".$this->compte."', facture='".$this->facture."', rembfact='".$this->rembfact."', status='".$this->status."', uid_creat='".$this->myuid."',date_creat='".now()."' WHERE id='".$this->id."'";
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
			$query="SELECT MAX(id) AS maxid FROM ".$this->tbl."_compte";
			$res=$sql->QueryRow($query);
			$maxid=$res["maxid"];
			$query="SELECT id,signature FROM ".$this->tbl."_compte WHERE id='".$maxid."'";
			$res=$sql->QueryRow($query);
			$precedent=$res["signature"];

			$montant=number_format($m["montant"],2,'.','');


			$query="SELECT description FROM ".$this->tbl."_mouvement WHERE id='".$m["poste"]."'";
			$res=$sql->QueryRow($query);

			$query ="INSERT ".$this->tbl."_compte SET ";
			$query.="mid='".$this->id."', ";
			$query.="uid='".$m["uid"]."', ";
			$query.="tiers='".$m["tiers"]."', ";
			$query.="montant='".$montant."', ";
			$query.="mouvement='".addslashes($res["description"])."', ";
			$query.="commentaire='".addslashes($this->commentaire)."', ";
			$query.="date_valeur='".$this->date_valeur."', ";
			$query.="dte='".date("Ym",strtotime($this->date_valeur))."', ";
			$query.="compte='".$this->compte."', ";
			$query.="facture='".$m["facture"]."', ";
			$query.="rembfact='".$m["rembfact"]."', ";
			$query.="uid_creat=".$this->uid_creat.", date_creat='".now()."'";
			// echo "$query<BR>";
			$id=$sql->Insert($query);

			// Signe la transaction
			$signature=md5($id."_".$m["uid"]."_".$m["tiers"]."_".$montant."_".$this->date_valeur."_".$maxid."_".$precedent);

			$query="UPDATE ".$this->tbl."_compte SET ";
			$query.="signature='".$signature."', ";
			$query.="precedent='".$precedent."' ";
			$query.="WHERE id='".$id."'";
			$sql->Update($query);

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

// Affiche le détail de toutes les lignes d'un mouvement
function AfficheDetailMouvement($id,$mid)
{ global $MyOpt,$sql;
	$txt="Mouvement : ".$mid."\n";
	$query = "SELECT * FROM ".$MyOpt["tbl"]."_compte WHERE mid=$mid AND mid>0 ORDER BY uid";
	$sql->Query($query);
	$t=array();
	for($i=0; $i<$sql->rows; $i++)
	{ 
		$sql->GetRow($i);
		$t[$i]=$sql->data;
	}
	foreach ($t as $i=>$d)
	{
		$usrc = new user_class($d["uid"],$sql);
		$usrd = new user_class($d["tiers"],$sql);

		$txt.=(($d["uid"]==$id) ? "* " : "").$usrc->fullname." (".$usrd->fullname.") : ".$d["mouvement"]." (".AffMontant($d["montant"]).")\n";
	}
	return $txt;
}

function AfficheSignatureCompte($lid)
{ global $MyOpt,$sql;
	$query="SELECT * FROM ".$MyOpt["tbl"]."_compte WHERE id='".$lid."'";
	$res_l=$sql->QueryRow($query);

	$query="SELECT id,signature FROM ".$MyOpt["tbl"]."_compte WHERE signature='".$res_l["precedent"]."'";
	$res_p=$sql->QueryRow($query);

	$sign_l=md5($res_l["id"]."_".$res_l["uid"]."_".$res_l["tiers"]."_".$res_l["montant"]."_".$res_l["date_valeur"]."_".$res_p["id"]."_".$res_p["signature"]);

	if ($sign_l==$res_l["signature"])
	{
		return "ok";
	}
	else
	{
		return "nok";
	}
}

?>