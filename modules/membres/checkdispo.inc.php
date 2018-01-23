<?
/*
    SoceIt v2.0
    Copyright (C) 2007 Matthieu Isorez

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
    ($Date: 2012-10-22 22:22:55 +0200 (lun., 22 oct. 2012) $)
    ($Revision: 407 $)
*/
// ---- Refuse l'accès en direct
	if ((!isset($token)) || ($token==""))
	  { header("HTTP/1.0 401 Unauthorized"); exit; }

  
// ---- Header de la page

	// Date du passé
	header("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
	
	// toujours modifié
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	
	// HTTP/1.1
	header("Cache-Control: no-store, no-cache, must-revalidate");
	
	// HTTP/1.0
	header("Pragma: no-cache");

	// Image PNG
	header('Content-type: image/png');

// ---- Variables d'affichage
	$l = 100;
	$h = 20;

// ---- Récupère les paramètres
	$mid=(is_numeric($_REQUEST["mid"]) ? $_REQUEST["mid"] : 0);
	$deb=(is_numeric($_REQUEST["deb"]) ? $_REQUEST["deb"] : 0);
	$fin=(is_numeric($_REQUEST["fin"]) ? $_REQUEST["fin"] : 0);

//	$deb=strtotime("2013-08-07 17:00");
//	$fin=strtotime("2013-08-07 18:00");

// ---- Charge les informations sur le chargement
	if (($mid>0) && ($deb>0) && ($fin>0))
	{
		$dte_deb=date("Y-m-d H:i:s",$deb);
		$dte_fin=date("Y-m-d H:i:s",$fin);
		$usr_inst=new user_class($mid,$sql,false,true);

		if ($usr_inst->CheckDisponibilite($dte_deb,$dte_fin))
		{
			$ok="ok";
			$txt="Disponible";
		}
		else
		{
			$ok="nok";
			$txt="Occupé";
		}
	}
	else
	{
		if ($mid==0)
		{
			$img = imagecreate($l, $h);
			$white = imagecolorallocate ($img, 255, 255, 255);
			$black = imagecolorallocate($img, 0, 0, 0);
			$grisclair = imagecolorallocate($img, 240, 240, 240);
			$gris = imagecolorallocate($img, 170, 170, 170);
			$textcolor = imagecolorallocate($img, 0, 0, 0);
			imagefill($img,0,0,$white); 
			imagepng($img);
			exit;
		}
		else
		{
			erreur("Les paramètres sont incorrects.");
			exit;
		}
	}


// ---- Affiche le graph
	$img = imagecreate($l, $h);
	$white = imagecolorallocate ($img, 255, 255, 255);
	$black = imagecolorallocate($img, 0, 0, 0);
	$grisclair = imagecolorallocate($img, 240, 240, 240);
	$gris = imagecolorallocate($img, 170, 170, 170);
	$textcolor = imagecolorallocate($img, 0, 0, 0);
	imagefill($img,0,0,$white); 

	$logo = imagecreatefrompng($module."/".$mod."/img/icn16_$ok.png");
	list($width, $height) = getimagesize($module."/".$mod."/img/icn16_".$ok.".png");
	imagecopy($img,$logo,2,2,0,0,$width,$height);

	imagestring($img, 2, 20, 2, $txt, $textcolor);

	// Affiche l'image
	imagepng($img);


// ---- Fonctions

function erreur($txt)
  {
	$error = imagecreate(320, 16);
	$logo = imagecreatefrompng($module."/".$mod."/img/icn16_erreur.png");
	list($width, $height) = getimagesize($module."/".$mod."/img/icn16_erreur.png");
	$white = imagecolorallocate ($error, 255, 255, 255);
	$textcolor = imagecolorallocate($error, 255, 0, 0);

	imagefill($error,0,0,$white); 
	imagecopy($error,$logo,0,0,0,0,$width,$height);

	imagestring($error, 2, 30, 0, $txt, $textcolor);
	imagepng($error);
  }

function CalcCoor($x,$y)
  { global $aminx,$amaxx,$aminy,$amaxy,$l,$h;
  	$t=array();
  	$t[0]=round(($x-$aminx)*($l-40)/($amaxx-$aminx)+20,0);
	$t[1]=round($h-20-($y-$aminy)*($h-80)/($amaxy-$aminy),0);
	return $t;
  }

function parsePlace($mvalues)
  {
	for ($i=0; $i < count($mvalues); $i++)
	$t[$mvalues[$i]["tag"]] = $mvalues[$i]["value"];
	return $t;
  }


?>
