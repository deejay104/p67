<?

class echeance_class
{

 	# Constructor
	function __construct($id="",$sql,$uid=0){
		global $MyOpt;
		global $gl_uid;

		$this->sql=$sql;
		$this->tbl=$MyOpt["tbl"];
		$this->myuid=$gl_uid;

		$this->id=0;
		$this->typeid="";
		$this->poste=0;
		$this->description="";
		$this->uid=$uid;
		$this->dte_echeance="";
		$this->paye="non";
		$this->editmode="html";
		$this->droit="html";

		if ($id>0)
		{
			$this->load($id);
		}
	}

	# Charge une échéance par son id
	function load($id){
		$this->id=$id;
		$sql=$this->sql;
		$query = "SELECT echeance.*, echeancetype.poste, echeancetype.description, echeancetype.droit, echeancetype.multi, echeancetype.resa FROM ".$this->tbl."_echeance AS echeance LEFT JOIN ".$this->tbl."_echeancetype AS echeancetype ON echeance.typeid=echeancetype.id WHERE echeance.id='$id'";
		$res = $sql->QueryRow($query);
		// Charge les variables
		$this->typeid=$res["typeid"];
		$this->poste=$res["poste"];
		$this->uid=$res["uid"];
		$this->dte_echeance=$res["dte_echeance"];
		$this->paye=$res["paye"];
		$this->description=$res["description"];
		$this->droit=$res["droit"];
		$this->multi=$res["multi"];
		$this->resa=$res["resa"];
	}

	# Charge une échéance par son type
	function loadtype($tid){
		$sql=$this->sql;
		$query = "SELECT echeance.*, echeancetype.poste, echeancetype.description, echeancetype.droit, echeancetype.multi, echeancetype.resa FROM ".$this->tbl."_echeance AS echeance LEFT JOIN ".$this->tbl."_echeancetype AS echeancetype ON echeance.typeid=echeancetype.id WHERE echeance.typeid='$tid' AND echeance.uid='".$this->uid."'";
		$res = $sql->QueryRow($query);
		// Charge les variables
		$this->id=$res["id"];
		$this->typeid=$res["typeid"];
		$this->poste=$res["poste"];
		// $this->uid=$res["uid"];
		$this->dte_echeance=$res["dte_echeance"];
		$this->paye=$res["paye"];
		$this->description=$res["description"];
		$this->droit=$res["droit"];
		$this->multi=$res["multi"];
		$this->resa=$res["resa"];
	}

	function Valid($k,$v) 
	{

		$vv=$v;
		if ($k=="dte_echeance")
		{
	  	  	if (date2sql($v)!="nok")
	  	  	  { $vv=date2sql($v); }
	  	  	else if (preg_match("/^([0-9]{2,4})-([0-9]{1,2})-([0-9]{1,2})([0-9: ]*)$/",$v))
	  	  	  { $vv=$v; }
		}
		else if ($k=="typeid")
		{
			if ( (is_numeric($v)) && ($v>0) )
			{
				$vv=$v;
			}
			else
			{
				$vv=$this->typeid;
			}
		}
		else if ($k=="uid")
		{
			if ( (is_numeric($v)) && ($v>0) )
			{
				$vv=$v;
			}
			else
			{
				$vv=$this->uid;
			}
		}
		return $vv;
	}

	function Create()
	{
		$sql=$this->sql;
		// $query="INSERT INTO ".$this->tbl."_echeance SET uid_create='".$this->myuid."', dte_create='".now()."', uid_maj='".$this->myuid."', dte_maj='".now()."'";
		// $this->id=$sql->Insert($query);

		// $query="INSERT INTO ".$this->tbl."_historique (`id` ,`class` ,`table` ,`idtable` ,`uid_maj` ,`dte_maj` ,`type` ,`comment`) VALUES (NULL , 'echeance', '".$this->tbl."_echeance', '".$this->id."', '".$this->myuid."', '".now()."', 'ADD', 'Create echeance')";
		// $sql->Insert($query);
		$this->id=$sql->Edit("echeance",$this->tbl."_echeance",0,array("uid_create"=>$this->myuid, "dte_create"=>now(), "uid_maj"=>$this->myuid, "dte_maj"=>now()));		

	}

	function Delete()
	{
		$sql=$this->sql;
		// $query="UPDATE ".$this->tbl."_echeance SET actif='non', uid_maj='".$this->myuid."', dte_maj='".now()."' WHERE id='$this->id'";
		// $this->id=$sql->Update($query);

		// $query="INSERT INTO ".$this->tbl."_historique (`id` ,`class` ,`table` ,`idtable` ,`uid_maj` ,`dte_maj` ,`type` ,`comment`) VALUES (NULL , 'echeance', '".$this->tbl."_echeance', '".$this->id."', '".$this->myuid."', '".now()."', 'DEL', 'Delete echeance')";
		// $sql->Insert($query);
		$sql->Edit("echeance",$this->tbl."_echeance",$this->id,array("actif"=>"non", "uid_maj"=>$this->myuid, "dte_maj"=>now()));
	}

	function Save()
	{
		$sql=$this->sql;
		if ($this->id==0)
		{
			$this->Create();
		}

		// $query ="UPDATE ".$this->tbl."_echeance SET ";
	  	// $query.="typeid='".$this->Valid("typeid",$this->typeid)."',";
	  	// $query.="uid='".$this->Valid("uid",$this->uid)."',";
	  	// $query.="dte_echeance='".$this->Valid("dte_echeance",$this->dte_echeance)."',";
	  	// $query.="paye='".$this->Valid("paye",$this->paye)."',";
		// $query.="uid_maj=".$this->myuid.", dte_maj='".now()."' ";
		// $query.="WHERE id='$this->id'";
		// $sql->Update($query);
		// $query="INSERT INTO ".$this->tbl."_historique (`id` ,`class` ,`table` ,`idtable` ,`uid_maj` ,`dte_maj` ,`type` ,`comment`) VALUES (NULL , 'echeance', '".$this->tbl."_echeance', '".$this->id."', '".$this->myuid."', '".now()."', 'MOD', 'Modify echeance')";
		// $sql->Insert($query);

		$sql->Edit("echeance",$this->tbl."_echeance",$this->id,array("typeid"=>$this->Valid("typeid",$this->typeid),"uid"=>$this->Valid("uid",$this->uid),"dte_echeance"=>$this->Valid("dte_echeance",$this->dte_echeance),"paye"=>$this->Valid("paye",$this->paye),"uid_maj"=>$this->myuid, "dte_maj"=>now()));		
	}

	function Affiche($type="") 
	{ global $MyOpt;
		$ret="";

		if ($this->editmode=="form")
		{
			$sql=$this->sql;
			$n=0;
			$ret.="<img src='static/images/icn16_vide.png' style='vertical-align:middle; border: 0px;  height: 16px; width: 16px;'>&nbsp;";
			$ret.="<select name='form_echeance_type'>";

			$tabEcheance=array();
			$query="SELECT echeance.typeid,echeancetype.multi FROM ".$MyOpt["tbl"]."_echeance AS echeance LEFT JOIN ".$MyOpt["tbl"]."_echeancetype AS echeancetype ON echeance.typeid=echeancetype.id WHERE echeance.uid='".$this->uid."' and actif='oui'";

			$sql->Query($query);
			for($i=0; $i<$sql->rows; $i++)
			{
				$sql->GetRow($i);
				if ($sql->data["multi"]=="non")
				{
					$tabEcheance[$sql->data["typeid"]]="ok";
				}
			}

			$query="SELECT id,description,droit FROM ".$MyOpt["tbl"]."_echeancetype ORDER BY description";
			$sql->Query($query);
			for($i=0; $i<$sql->rows; $i++)
			{
				$sql->GetRow($i);
				if ( (GetDroit($sql->data["droit"])) && ($tabEcheance[$sql->data["id"]]=="") )
				{
					$ret.="<option value='".$sql->data["id"]."'>".$sql->data["description"]."</option>";
					$n=$n+1;
				}
			}
			$ret.="</select>&nbsp;";
			
			$ret.="<input name='form_echeance[".$this->id."]' id='form_echeance".$this->id."' value='".$this->dte_echeance."' type='date' style='width: 140px;'>";
			if ($n==0)
			{
				$ret="";
			}
		}
		else if ( ($this->editmode=="edit") && (GetDroit($this->droit)) )
		{
			$ret ="<div id='aff_echeance".$this->id."'>";
			$ret.="<img src='static/images/icn16_vide.png' style='vertical-align:middle; border: 0px;  height: 16px; width: 16px;'>&nbsp;";
			$ret.="Echéance ".$this->description." le <input name='form_echeance[".$this->id."]' id='form_echeance".$this->id."' value='".$this->dte_echeance."' type='date' style='width: 165px;'>&nbsp;";
			$ret.="<a href=\"#\" OnClick=\"document.getElementById('form_echeance".$this->id."').value=''; document.getElementById('aff_echeance".$this->id."').style.display='none';\" class='imgDelete'><img src='static/images/icn16_supprimer.png'></a>";
			$ret.="</div>";
		}
		else if ($type=="val")
		{
			$ret=AffDate($this->dte_echeance);
		}
		else
		{
			$ret ="<img src='static/images/icn16_".EcheanceDate($this->dte_echeance).".png' style='vertical-align:middle; border: 0px;  height: 16px; width: 16px;'>&nbsp;";
			$ret.="Echéance ".$this->description." le ".AffDate($this->dte_echeance);
		}
		return $ret;
	}

	function Val() 
	{ global $MyOpt;
		
		return $this->dte_echeance;
	}

}


function ListEcheance($sql,$id)
  {
	global $MyOpt, $gl_uid, $myuser;

	$query="SELECT id FROM ".$MyOpt["tbl"]."_echeance WHERE actif='oui' ".(($id>0) ? "AND uid='$id'" : "" )." ORDER BY dte_echeance";
	$sql->Query($query);
	$lstdte=array();
	for($i=0; $i<$sql->rows; $i++)
	{
		$sql->GetRow($i);
		$lstdte[$i]=$sql->data["id"];
	}

	return $lstdte;
  }

function VerifEcheance($sql,$id)
  {
	global $MyOpt, $gl_uid, $myuser;

	$query ="SELECT echeancetype.description,echeancetype.resa,echeance.dte_echeance FROM ".$MyOpt["tbl"]."_echeancetype AS echeancetype LEFT JOIN ".$MyOpt["tbl"]."_echeance AS echeance ON echeancetype.id=echeance.typeid AND echeance.actif='oui' AND echeance.uid='$id' ";
	$query.="WHERE echeance.dte_echeance<'".now()."' OR echeance.dte_echeance IS NULL ORDER BY echeance.dte_echeance";

	$sql->Query($query);
	$lstdte=array();
	for($i=0; $i<$sql->rows; $i++)
	{
		$sql->GetRow($i);
		$lstdte[$i]["description"]=$sql->data["description"];
		$lstdte[$i]["resa"]=$sql->data["resa"];
		$lstdte[$i]["dte_echeance"]=$sql->data["dte_echeance"];
	}

	return $lstdte;
  }

function ListeEcheanceType($sql,$id) 
{ global $MyOpt;
	$query="SELECT uid FROM ".$MyOpt["tbl"]."_echeance AS echeance LEFT JOIN ".$MyOpt["tbl"]."_utilisateurs AS usr ON echeance.uid=usr.id WHERE echeance.actif='oui' AND echeance.typeid='".$id."' AND usr.actif='oui' GROUP BY echeance.uid";
	$query="SELECT echeance.id FROM ".$MyOpt["tbl"]."_echeance AS echeance LEFT JOIN ".$MyOpt["tbl"]."_utilisateurs AS usr ON echeance.uid=usr.id WHERE echeance.actif='oui' AND echeance.typeid='".$id."' AND usr.actif='oui'";
	$sql->Query($query);
	$lstdte=array();
	for($i=0; $i<$sql->rows; $i++)
	{
		$sql->GetRow($i);
		$lstdte[$i]=$sql->data["id"];
	}

	return $lstdte;		
}

?>