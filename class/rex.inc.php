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
		$this->data["uid_creat"] = 0;
		$this->data["dte_creat"] = date("Y-m-d H:i:s");
		$this->data["uid_modif"] = 0;
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
	}


	# Show user informations
	function aff($key,$typeaff="html",$formname="form_rex")
	{ global $MyOpt,$gl_uid;

		$txt=$this->data[$key];


		// Ne prend que ceux qui sont activÃ©s

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
		if ($key=="planaction")
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
				$ret.="<option value='new' ".(($txt=="new") ? "selected" : "").">Nouveau</option>";
				$ret.="<option value='inprg' ".(($txt=="inprg") ? "selected" : "").">En cours</option>";
				$ret.="<option value='close' ".(($txt=="close") ? "selected" : "").">Cloturé</option>";
				$ret.="<option value='delete' ".(($txt=="delete") ? "selected" : "").">Supprimé</option>";
				$ret.="</select>";
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
			else if (($key=="dte_creat") || ($key=="dte_modif"))
			{
				$ret=sql2date($ret);
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
	{
		$sql=$this->sql;

		$td=array();
		foreach($this->data as $k=>$v)
		{ 
			if (!is_numeric($k))
			{
				$td[$k]=$this->Valid($k,$v,true);
			}
		}
		$td["uid_modif"]=$uid;
		$td["dte_modif"]=now();
		$id=$sql->Edit("rex",$this->tbl."_rex",$this->id,$td);

		if ($this->id==0)
		{
			$this->id=$id;
		}
	}
	
	function Delete(){
		global $uid;
		$sql=$this->sql;
		$this->status="delete";

		$sql->Edit("rex",$this->tbl."_rex",$this->id,array("status"=>'delete', "uid_maj"=>$gl_uid, "dte_maj"=>now()));
	}	


} # End of class




function ListRex($sql,$fields=array())
{ global $MyOpt;

	$f=implode(",",$fields);
	$lst=array();
 
	$query="SELECT id".((count($fields)>0) ? ",".$f : "")." FROM ".$MyOpt["tbl"]."_rex ";
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