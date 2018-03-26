<?
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

    ($Author: miniroot $)
    ($Date: 2012-07-23 10:18:01 +0200 (lun., 23 juil. 2012) $)
    ($Revision: 385 $)
*/

// Class Document

class document_class{

 	# Constructor
	function __construct($id="",$sql,$type="document"){
		global $MyOpt;
		global $gl_uid;

		$this->sql=$sql;
		$this->tbl=$MyOpt["tbl"];
		$this->myuid=$gl_uid;
		$this->expire=$MyOpt["expireCache"];

		$this->id="";
		$this->name="";
		$this->filename="";
		$this->uid="";
		$this->type=$type;
		$this->dossier="";
		$this->droit="";
		$this->actif="";
		$this->uid_creat="";
		$this->dte_creat="";
		$this->editmode="std";
		$this->filepath="documents";

		if ($id>0)
		{
			$this->load($id);
		}
		// else if ($id==-1)
		// {
			// $this->id=0;
			// $this->filepath="static/images";
			// $this->filename="icn64_membre.png";
			// $this->droit="ALL";
		// }
		else
		{
			$this->id=0;
			$this->filename="";
			$this->droit="ALL";
		}
	}

	# Load document
	function load($id){
		$this->id=$id;
		$sql=$this->sql;
		$query = "SELECT * FROM ".$this->tbl."_document WHERE id='$id'";
		$res = $sql->QueryRow($query);

		// Charge les variables
		$this->name=$res["name"];
		$this->filename=$res["filename"];
		$this->uid=$res["uid"];
		$this->type=$res["type"];
		$this->dossier=$res["dossier"];
		$this->droit=$res["droit"];
		$this->actif=$res["actif"];
		$this->uid_creat=$res["uid_creat"];
		$this->dte_creat=$res["dte_creat"];
	}

	function Valid($k,$v) 
	{
	}


	function Save($id,$data)
	{ global $gl_uid;
		$sql=$this->sql;

		$ret="";
		
		$name=$data["name"];

		$myext=GetExtension($name);
		if (strlen($name)>100)
		{
			$filename=substr(GetFilename($name),0,96).".".$myext;
		}

	  	$query="INSERT INTO ".$this->tbl."_document SET name='$name', uid='$id', droit='$this->droit', type='$this->type', actif='oui', uid_creat='$gl_uid',dte_creat='".now()."'";
		$this->id=$sql->Insert($query);

		$myname=CompleteTxt($this->id,6,"0");
		$mypath=substr($myname,0,3);

		if (!is_dir($this->filepath."/".$mypath))
		{
		  	mkdir($this->filepath."/".$mypath);
		}

		$this->uid=$id;
		$this->filename=$mypath."/".$myname.".".$myext;
		if (!move_uploaded_file($data["tmp_name"],$this->filepath."/".$this->filename))
		  {
		  	$ret.="Erreur de chargement du fichier<br/>";
		  }
		else
		  {
		  	$query="UPDATE ".$this->tbl."_document SET filename='".$this->filename."' WHERE id='".$this->id."'";
			$sql->Update($query);
		  }

		return $ret;
	}

	function Import($id,$name,$filename="",$droit="")
	{ global $gl_uid;
		$sql=$this->sql;

		$ret="";
		
		$myext=GetExtension($name);
		if (strlen($name)>100)
		{
			$filename=substr(GetFilename($name),0,96).".".$myext;
		}

	  	$query="INSERT INTO ".$this->tbl."_document SET name='".(($filename!="") ? $filename : $name)."', uid='$id', type='$this->type', droit='$droit',actif='oui', uid_creat='$gl_uid',dte_creat='".now()."'";
		$this->id=$sql->Insert($query);

		$myname=CompleteTxt($this->id,6,"0");
		$mypath=substr($myname,0,3);

		if (!is_dir($this->filepath."/".$mypath))
		  {
		  	mkdir($this->filepath."/".$mypath);
		  }
		$this->uid=$id;
		$this->filename=$mypath."/".$myname.".".$myext;

		rename($name,$this->filepath."/".$this->filename);
	  	$query="UPDATE ".$this->tbl."_document SET filename='".$this->filename."' WHERE id='".$this->id."'";
		$sql->Update($query);

		return $ret;		
	}

	function Delete()
	{ global $mysql,$gl_uid,$myuser;
		$sql=$this->sql;
		$ret="";

		if ( ($this->uid==$gl_uid) || (GetDroit("ADM")) || ($myuser->role[$this->droit]) )
		{
			if (file_exists($this->filepath."/".$this->filename))
			{
				if (unlink($this->filepath."/".$this->filename))
				  {
				  	$ret.="Fichier supprimé";
				  }
			}

			
			if (!file_exists($this->filepath."/".$this->filename))
			  {
				$query="UPDATE ".$this->tbl."_document SET actif='non' WHERE id='".$this->id."'";
				$sql->Update($query);
			  }
		}
	}

	function Affiche()
	{ global $MyOpt;
		$myext=GetExtension($this->name);
		
		if ($myext=="xls")
		  { $icon="excel"; }
		else if ($myext=="doc")
		  { $icon="word"; }
		else if ($myext=="ppt")
		  { $icon="powerpoint"; }
		else if ($myext=="pps")
		  { $icon="powerpoint"; }
		else if ($myext=="jpg")
		  { $icon="image"; }
		else if ($myext=="gif")
		  { $icon="image"; }
		else if ($myext=="png")
		  { $icon="image"; }
		else if ($myext=="pdf")
		  { $icon="pdf"; }
		else if ($myext=="mp3")
		  { $icon="sound"; }
		else if ($myext=="zip")
		  { $icon="compressed"; }
		else if ($myext=="rar")
		  { $icon="compressed"; }
		else if ($myext=="xml")
		  { $icon="document"; }
		else if ($myext=="css")
		  { $icon="document"; }
		else if ($myext=="txt")
		  { $icon="document"; }
		else
		  { $icon="file"; }


		$txt ="";
		if ($this->editmode=="form")
		  {
		  	$txt="<input name=\"form_adddocument\" type=\"file\" size=\"60\" />";
		  }
		else
		  {
				if (file_exists($this->filepath."/".$this->filename))
				  {
						$fsize=CalcSize(filesize($this->filepath."/".$this->filename));
						$txt.="<a href='".$MyOpt["host"]."/doc.php?id=".$this->id."' target='_blank'><img src='".$MyOpt["host"]."/static/images/icn16_".$icon.".png' width=16 height=16 border=0> ".$this->name." ($fsize) </a>";
				  }
				else
				  {
						$txt.="<img src='".$MyOpt["host"]."/static/images/icn16_".$icon.".png' style='vertical-align:middle; border: 0px; height: 16px; width: 16px;'> <s>".$this->name."</s>";
					}

				// Si mode édition
				if ($this->editmode=="edit")
				  {
		  			$txt.=" <a href=\"#\" OnClick=\"var win=window.open('doc.php?id=".$this->id."&fonc=delete','scrollbars=no,resizable=no,width=10'); return false;\" class='imgDelete'><img src='static/images/icn16_supprimer.png'></a>";
		  		}
		  }

		return $txt;
	}

	function Download($mode)
	{ global $myuser;
		$myext=GetExtension($this->name);

		if ( ($this->myuid!=$this->uid) && (!$myuser->role[$this->droit]) && ($this->droit!="ALL") && (!GetDroit("VisuDocument")) )
		{
			echo "Access denied!";
			exit;
		}

		if ($mode=="inline")
		{
			$mode="inline;";
		}
		else if ($mode=="attachment")
		{
			$mode="attachment;";
		}
		else
		{
			$mode="";
		}

		$fname=$this->filepath."/".$this->filename;
		if (!file_exists($fname))
		{
		  	$fname="static/images/icn32_erreur.png";
		  	$myext="png";
		}

		if ($fd = fopen ($fname, "r"))
		{
			$fsize = filesize($fname);
		
			if ($myext=="jpg")
			  { header("Content-Type: image/jpeg"); }
			else if ($myext=="png")
			  { header("Content-Type: image/png"); }
			else if ($myext=="pdf")
			  { header("Content-type: application/pdf"); }
			else
			  { header("Content-type: application/octet-stream"); }

			header("Content-Disposition: ".$mode." filename=\"".$this->name."\";");
			header("Content-length: $fsize");
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
		
			while(!feof($fd))
			{
				$buffer = fread($fd, 2048);
				echo $buffer;
			}
		}
	}

	function Resize($newwidth,$newheight,$dest="")
	{
		$file=$this->filepath."/".$this->filename;
		if ($dest=="")
		{
			$dest=$this->filepath."/".$this->filename;
		}

		if ((!is_numeric($newwidth)) || (!is_numeric($newheight)))
		{
		  	list($newwidth, $newheight) = getimagesize($file);
		}
		
		if (!file_exists($file))
		{
		  	$file="static/images/icn32_erreur.png";
		}

		$thumb = imagecreatetruecolor($newwidth, $newheight);
		$white = imagecolorallocate ($thumb, 255, 255, 255);
		imagefill($thumb,0,0,$white); 

		if (exif_imagetype($file)==IMAGETYPE_JPEG)
		{
			$source = imagecreatefromjpeg($file);
		}
		else if (exif_imagetype($file)==IMAGETYPE_PNG)
		{
			$source = imagecreatefrompng($file);
		}
		else if (exif_imagetype($file)==IMAGETYPE_GIF)
		{
			$source = imagecreatefromgif($file);
		}
		else
		{
			$file="static/images/icn32_erreur.png";
			$source = imagecreatefrompng($file);
		}

		list($width, $height) = getimagesize($file);
		
		if (($width<$height) && ($newwidth>0))
		{
			$w = $newwidth;
			$h = floor(($height/$width) * $newwidth );
			imagecopyresampled($thumb, $source, 0, ($newheight-$h)/2, 0, 0, $w, $h, $width, $height);
		}
		else if (($width>$height) && ($newheight>0))
		{
			$w = floor(($width/$height) * $newheight);
			$h = $newheight;
			imagecopyresampled($thumb, $source, 0, 0, 0, 0, $w, $h, $width, $height);
		}
		else
		{
			imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newwidth, $newwidth, $width, $height);
		}

		
		if ($dest=="show")
		{
			return $thumb;
		}
		
		if (exif_imagetype($file)==IMAGETYPE_JPEG)
		{
			imagejpeg($thumb,$dest,95);
		}
		else if (exif_imagetype($file)==IMAGETYPE_PNG)
		{
			imagepng($thumb,$dest,6);
		}
		else if (exif_imagetype($file)==IMAGETYPE_GIF)
		{
			imagegif($thumb,$dest);
		}
	}

	function ShowImage($newwidth,$newheight)
	{
		$file=$this->filepath."/".$this->filename;

		$thumb=$this->Resize($newwidth,$newheight,"show");

		header('Content-Type: image/png');
		imagepng($thumb);
	}

	function GenerePath($w,$h)
	{
		// $f=preg_split("/\\//",$this->filename);
		// $file=$f[1];
		// if ($file=="")
		// {
			// $file=$f[0];
		// }
		$myid=CompleteTxt($this->id,6,"0");
		$myext=GetExtension($this->filename);

		$type="";
		if (($w>0) && ($h>0))
		{
			$type="&type=".$type."&width=".$w."&height=".$h;
			$file=$w."x".$h.".".$myext;
		}
		else
		{
			$file="original.".$myext;
		}
		$mypath="static/cache/".$myid."/".$file;
		
		if ($this->droit=="ALL")
		{
			if (!is_dir("static/cache/".$myid))
			{
				mkdir("static/cache/".$myid);
			}

			if ((file_exists($mypath)) && ($this->expire>0) && (time()-filectime($mypath)>3600*$this->expire))
			{
				error_log("clear cache:".$mypath);
				unlink($mypath);
			}
			
			if (!file_exists($mypath))
			{
				if (($w>0) && ($h>0))
				{
					$this->Resize($w,$h,$mypath);
				}
				else
				{
					copy($this->filepath."/".$this->filename,$mypath);
				}
			}

			if (file_exists($mypath))
			{
				error_log("path from cache:".$mypath);
				return $mypath;
			}
		}
		return "doc.php?id=".$this->id.$type;
	}
}
	
// Gestion de fichier
function GetExtension($file)
{
	$myext=strtolower(substr($file,strrpos($file,".")+1,strlen($file)-strrpos($file,".")-1));
	return $myext;
}
function GetFilename($file)
{
	$p=strrpos("/".$file,"/");
	$myfile=substr("/".$file,$p+1,strlen($file)-$p-1);
	$myfile=substr($myfile,0,strrpos($myfile,"."));
	return $myfile;
}

function ListDocument($sql,$id,$type)
  {
	global $MyOpt, $gl_uid, $myuser;

	$query="SELECT ".$MyOpt["tbl"]."_document.id,".$MyOpt["tbl"]."_document.uid,".$MyOpt["tbl"]."_document.droit  FROM ".$MyOpt["tbl"]."_document WHERE ".$MyOpt["tbl"]."_document.actif='oui' ".(($id>0) ? "AND ".$MyOpt["tbl"]."_document.uid='$id'" : "" )." ".(($type!="") ? "AND ".$MyOpt["tbl"]."_document.type='$type'" : "" )." ORDER BY name";
	$sql->Query($query);
	$lstdoc=array();
	for($i=0; $i<$sql->rows; $i++)
	  {
		$sql->GetRow($i);
		if ( ($gl_uid==$sql->data["uid"]) || (($sql->data["droit"]!="") && ((isset($myuser->role[$sql->data["droit"]])) && ($myuser->role[$sql->data["droit"]])) ) || ($sql->data["droit"]=="ALL") || (GetDroit("VisuDocument")) )
		{
			$lstdoc[$i]=$sql->data["id"];
		}
	  }

	return $lstdoc;
  }
	
?>
