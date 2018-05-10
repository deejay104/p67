<?
/*
    Easy Aero v2.4
    Copyright (C) 2018 Matthieu Isorez

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
    ($Date: 2016-04-22 22:08:32 +0200 (ven., 22 avr. 2016) $)
    ($Revision: 460 $)
*/

$tabValeurRex=array();
$tabValeurRex["new"]="Nouveau";
$tabValeurRex["inprg"]="En cours";
$tabValeurRex["close"]="Cloturé";
$tabValeurRex["cancel"]="Annulé";


// Class Utilisateur
class rex_class{
	# Constructor
	function __construct($id=0,$sql){
		global $MyOpt;
		global $gl_uid;

		$this->tbl=$MyOpt["tbl"];
		$this->sql=$sql;

		$this->id=$id;

		$this->data=array();
		$this->data["titre"] = "";
		$this->data["status"] = "new";
		$this->data["description"] = "";
		$this->data["commentaire"] = "";
		$this->data["synthese"] = "";
		$this->data["planaction"] = "";
		$this->data["categorie"] = "";
		$this->data["nature"] = "";
		$this->data["mto"] = "";
		$this->data["environnement"] = "";
		$this->data["phase"] = "";
		$this->data["typevol"] = "";
		$this->data["typeevt"] = "";
		$this->data["uid_avion"] = 0;
		$this->data["dte_rex"] = date("Y-m-d");
		$this->data["actif"] = "oui";
		$this->data["uid_creat"] = $gl_uid;
		$this->data["dte_creat"] = date("Y-m-d H:i:s");
		$this->data["uid_modif"] = $gl_uid;
		$this->data["dte_modif"] = date("Y-m-d H:i:s");


		if ($id>0)
		{
			$this->load($id);
		}
	}

	# Load user informations
	function load($id)
	{
		$this->id=$id;
		$sql=$this->sql;

		$query = "SELECT * FROM ".$this->tbl."_rex WHERE id='$id'";
		$res = $sql->QueryRow($query);
		if (!is_array($res))
		{
			return "";
		}

		foreach($res as $k=>$v)
		{
			if (!is_numeric($k))
			{
				$this->data[$k]=$v;
			}
		}
		
		$this->avion=new ress_class($this->data["uid_avion"],$sql);
	}


	# Show user informations
	function aff($key,$typeaff="html",$formname="form_rex")
	{ global $MyOpt,$gl_uid,$tabValeurRex;

		$txt=$this->data[$key];

		// Variables
		$tabCol=array();
		$tabLig=array();
		$tabBg=array();

		$tabCol[1]="Très Probable";
		$tabCol[2]="Probable";
		$tabCol[3]="Très improbable";
		$tabCol[4]="Improbable";
		$tabCol[5]="Non défini";

		$tabLig[1]="Accident matériel et corporel";
		$tabLig[2]="Accident corporel";
		$tabLig[3]="Accident matériel";
		$tabLig[4]="Incident grave";
		$tabLig[5]="Incident";
		$tabLig[6]="Annomalie";
		$tabLig[7]="Non défini";

		$tabBg["11"]="#ff0000"; $tabBg["12"]="#ff0000"; $tabBg["13"]="#ff0000"; $tabBg["14"]="#ffff00"; $tabBg["15"]="#ffffff";
		$tabBg["21"]="#ff0000"; $tabBg["22"]="#ff0000"; $tabBg["23"]="#ff0000"; $tabBg["24"]="#ffff00"; $tabBg["25"]="#ffffff";
		$tabBg["31"]="#ff0000"; $tabBg["32"]="#ff0000"; $tabBg["33"]="#ffc529"; $tabBg["34"]="#ffff00"; $tabBg["35"]="#ffffff";
		$tabBg["41"]="#ff0000"; $tabBg["42"]="#ffc529"; $tabBg["43"]="#ffff00"; $tabBg["44"]="#00ff00"; $tabBg["45"]="#ffffff";
		$tabBg["51"]="#ffc529"; $tabBg["52"]="#ffff00"; $tabBg["53"]="#ffff00"; $tabBg["54"]="#00ff00"; $tabBg["55"]="#ffffff";
		$tabBg["61"]="#ffff00"; $tabBg["62"]="#00ff00"; $tabBg["63"]="#00ff00"; $tabBg["64"]="#00ff00"; $tabBg["65"]="#ffffff";
		$tabBg["71"]="#ffffff"; $tabBg["72"]="#ffffff"; $tabBg["73"]="#ffffff"; $tabBg["74"]="#ffffff"; $tabBg["75"]="#ffffff";


		
		$type="";
		if (is_numeric($key))
		  { $ret="******"; }
		else if ($key=="dte_rex")
		  { $ret=$txt; $type="date"; }
		else
		  { $ret=$txt; }

		// Défini les droits de modification des utilisateurs
		$mycond=false;
		if ($this->data["uid_creat"]==$gl_uid)
		{
			$mycond=true;
		}
		
		// Test les exceptions
		if ($key=="status")
		{
			if (!GetDroit("ModifRexStatus"))
			  { $mycond=false; }
		}
		if (($key=="planaction") || ($key=="synthese"))
		{
			if (!GetDroit("ModifRexSynthese"))
			  { $mycond=false; }
		}
		if ($key=="dte_creat")
		{
			$mycond=false;
		}
		if ($key=="dte_modif")
		{
			$mycond=false;
		}

		// Si on a le droit de modif on autorise
		if (GetDroit("ModifRexAll"))
		  { $mycond=true; }

		// Si on a pas le droit on repasse en visu
		if ((!$mycond) && ($typeaff!="val"))
		  { $typeaff="html"; }
 	
		if ($typeaff=="form")
		{
			if ($key=="description")
		  	  { $ret="<TEXTAREA id='".$key."'  name=\"".$formname."[$key]\" rows=5>$ret</TEXTAREA>"; }
			else if ($key=="commentaire")
		  	  { $ret="<TEXTAREA id='".$key."'  name=\"".$formname."[$key]\" rows=5>$ret</TEXTAREA>"; }
			else if ($key=="synthese")
		  	  { $ret="<TEXTAREA id='".$key."'  name=\"".$formname."[$key]\" rows=5>$ret</TEXTAREA>"; }
			else if ($key=="planaction")
		  	  { $ret="<TEXTAREA id='".$key."'  name=\"".$formname."[$key]\" rows=5>$ret</TEXTAREA>"; }
			else if ($key=="status")
		  	{
				$ret="<SELECT id='".$key."' name=\"".$formname."[$key]\">";
				foreach ($tabValeurRex as $vv=>$dd)
				{
					$ret.="<option value='".$vv."' ".(($txt==$vv) ? "selected" : "").">".$dd."</option>";
				}
				$ret.="</select>";
			}
			else if ($key=="uid_avion")
		  	{
				$ret="<select id='".$key."' name=\"".$formname."[$key]\">";
				$lstress=ListeRessources($this->sql);

				foreach($lstress as $i=>$rid)
				{
					$resr=new ress_class($rid,$this->sql);
					$ret.="<option value='".$rid."' ".(($txt==$rid) ? "selected" : "").">".$resr->immatriculation."</option>";
				}
				$ret.="</select>";
			}
			else if ($key=="risque")
			{
				$ret ="<style>.risqueREX td { width:140px; text-align: center; border: 1px solid #000000; font-weight: bold; }</style>";

				$ret.="<table class='risqueREX'>";
				$ret.="<tr><th></th>";
				foreach($tabCol as $i=>$d)
				{
					$ret.="<th>".$d."</th>";
				}
				$ret.="</tr>";

				foreach($tabLig as $l=>$ln)
				{
					$ret.="<tr><th>".$ln."</th>";
					foreach($tabCol as $c=>$cn)
					{
						$ret.="<td style='background-color:".$tabBg[$l.$c].";'>"."<input type='radio' name='".$formname."[$key]' value='".$l.$c."' ".(($txt==$l.$c) ? "checked='checked'" : "")."></td>";
					}
					$ret.="</tr>";
				}

				$ret.="</table>";
			}
			else
			{
				$ret="<INPUT id='".$key."'  name=\"".$formname."[$key]\" id=\"$key\" value=\"$ret\" ".(($type!="") ? "type=\"".$type."\"" : "").">";
			}
		}
		else if ($typeaff=="val")
		{
			if ($key=="description")
			  { $ret=nl2br(htmlentities($ret,ENT_HTML5,"ISO-8859-1")); }
			else if ($key=="commentaire")
			  { $ret=nl2br(htmlentities($ret,ENT_HTML5,"ISO-8859-1")); }
			else if ($key=="synthese")
			  { $ret=nl2br(htmlentities($ret,ENT_HTML5,"ISO-8859-1")); }
			else if ($key=="planaction")
			  { $ret=nl2br(htmlentities($ret,ENT_HTML5,"ISO-8859-1")); }
	
			else if (($key=="dte_creat") || ($key=="dte_modif"))
			{
				if ($txt=="0000-00-00")
					{ $ret="-"; }
			}
		}
		else
		{
			if ($key=="description")
			  { $ret=nl2br(htmlentities($ret,ENT_HTML5,"ISO-8859-1")); }
			else if ($key=="commentaire")
			  { $ret=nl2br(htmlentities($ret,ENT_HTML5,"ISO-8859-1")); }
			else if ($key=="synthese")
			  { $ret=nl2br(htmlentities($ret,ENT_HTML5,"ISO-8859-1")); }
			else if ($key=="planaction")
			  { $ret=nl2br(htmlentities($ret,ENT_HTML5,"ISO-8859-1")); }
			else if ($key=="status")
			{
				$ret=$tabValeurRex[$txt];
			}
			else if ($key=="uid_avion")
			{
				$ret=$this->avion->immatriculation;
			}
			else if (($key=="dte_creat") || ($key=="dte_modif"))
			{
				$ret=sql2date($ret);
			}
			else if ($key=="risque")
			{
				$ret ="<style>.risqueREX td { width:140px; text-align: center; border: 1px solid #000000; font-weight: bold; }</style>";

				$ret.="<table class='risqueREX'>";
				$ret.="<tr><th></th>";
				foreach($tabCol as $i=>$d)
				{
					$ret.="<th>".$d."</th>";
				}
				$ret.="</tr>";

				foreach($tabLig as $l=>$ln)
				{
					$ret.="<tr><th>".$ln."</th>";
					foreach($tabCol as $c=>$cn)
					{
						$ret.="<td style='background-color:".$tabBg[$l.$c].";'>".(($txt==$l.$c) ? "x" : "")."</td>";
					}
					$ret.="</tr>";
				}

				$ret.="</table>";
			}
		}
	
		return $ret;
	}


	function Create()
	{ global $gl_uid;
		$sql=$this->sql;

		$this->id=$sql->Edit("rex",$this->tbl."_rex",$this->id,array("uid_maj"=>$uid, "dte_maj"=>now()));		
		
		return $this->id;
	}
	
	function Valid($k,$v,$ret=false)
	{ global $gl_uid;
		$vv="**none**";

		if (($k=="uid_creat") || ($k=="uid_modif"))
		{
			if ($this->data[$k]==0)
			{
				$vv=$gl_uid;
			}
		}
		else
		{
			$vv=$v;
		}

		if ( (!is_numeric($k)) && ("($vv)"!="(**none**)") && ($ret==false))
		  { $this->data[$k]=$vv; }
		else if ($ret==true)
		  { return addslashes($vv); }
	}

	function Save()
	{ global $gl_uid;
		$sql=$this->sql;

		$td=array();
		foreach($this->data as $k=>$v)
		{ 
			if (!is_numeric($k))
			{
				$td[$k]=$this->Valid($k,$v,true);
			}
		}
		$td["uid_modif"]=$gl_uid;
		$td["dte_modif"]=now();
		$id=$sql->Edit("rex",$this->tbl."_rex",$this->id,$td);

		if ($this->id==0)
		{
			$this->id=$id;
		}
	}
	
	function Delete(){
		global $gl_uid;
		$sql=$this->sql;
		$this->actif="non";

		$sql->Edit("rex",$this->tbl."_rex",$this->id,array("actif"=>'non', "uid_modif"=>$gl_uid, "dte_modif"=>now()));
	}	


} # End of class




function ListRex($sql,$fields=array())
{ global $MyOpt;

	$f=implode(",",$fields);
	$lst=array();
 
	$query="SELECT id".((count($fields)>0) ? ",".$f : "")." FROM ".$MyOpt["tbl"]."_rex WHERE actif='oui'";
	$sql->Query($query);
	for($i=0; $i<$sql->rows; $i++)
	  { 
		$sql->GetRow($i);
		$lst[$i]["id"]=$sql->data["id"];
		if (count($fields)>0)
		{
			foreach ($fields as $f)
			{
				$lst[$i][$f]=$sql->data[$f];
			}
		}
	  }
	return $lst;
}

  
?>