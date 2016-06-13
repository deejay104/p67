<?
// ---------------------------------------------------------------------------------------------
//   Visualisation des indicateurs
//   
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
// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("indicateurs.htm"));
	$tmpl_x->assign("path_module","$module/$mod");

	require_once ("class/ressources.inc.php");

// ---- Liste des mois
	$tabm[1]="Jan";
	$tabm[2]="Fév";
	$tabm[3]="Mar";
	$tabm[4]="Avr";
	$tabm[5]="Mai";
	$tabm[6]="Jun";
	$tabm[7]="Jui";
	$tabm[8]="Aou";
	$tabm[9]="Sep";
	$tabm[10]="Oct";
	$tabm[11]="Nov";
	$tabm[12]="Déc";


// ---- Affiche la courbe des heures
	if ((!isset($dte)) && (!preg_match("/[0-9]{4}/",$dte)))
	  {
	  	$dte=date("Y");
			$dte2=(date("Y")+1)."-01-01";
			$tmpl_x->assign("aff_annee", date("Y"));
	  }
	else
	  {
			$dte2=($dte+1)."-01-01";
			$tmpl_x->assign("aff_annee", $dte);
	  }

	$tabprev=array();
	$tabaff=array();
	$tabtot=array();


// ---- Vérifie s'il existe des prévisions pour l'année demandée
	$query="SELECT * FROM p67_prevision WHERE annee='$dte'";
	$sql->Query($query);
	if ($sql->rows==0)
	  {
		$dte=$dte-1;
		$dte2=($dte+1)."-01-01";
		$tmpl_x->assign("aff_annee", $dte);
	  }

  	$dte=date("$dte-01-01");

// ---- Récupère l'échelle max
	
	if ($scale=="yes")
	  {
		$query="SELECT MAX(heures) AS heures FROM p67_prevision";
		$res=$sql->QueryRow($query);
		$mdte1=$res["heures"];

		$query="SELECT YEAR(dte_deb), MONTH(dte_deb),SUM(temps)/60 AS heures FROM p67_calendrier GROUP BY uid_avion, YEAR(dte_deb), MONTH(dte_deb) ORDER BY heures DESC";
		$res=$sql->QueryRow($query);
		$mdte2=floor($res["heures"])+1;

		$maxp=(($mdte1>$mdte2) ? $mdte1 : $mdte2);

		$query="SELECT annee,SUM(heures)*60 AS heures FROM p67_prevision GROUP BY avion, annee ORDER BY heures DESC";
		$res=$sql->QueryRow($query);
		$mdte1=$res["heures"];

		$query="SELECT YEAR(dte_deb),SUM(temps) AS heures FROM p67_calendrier GROUP BY uid_avion, YEAR(dte_deb) ORDER BY heures DESC";
		$res=$sql->QueryRow($query);
		$mdte2=$res["heures"];

		$maxptot=(($mdte1>$mdte2) ? $mdte1 : $mdte2);;
	  }
	else
	  {
		$maxp=0;
		$maxptot=0;
	  }

// ---- Récupère les prévisions
	$tress=array();

	$query="SELECT * FROM p67_prevision WHERE annee='$dte'";
	$sql->Query($query);
	for($i=0; $i<$sql->rows; $i++)
	  { 
		$sql->GetRow($i);
		$tabprev[$sql->data["avion"]][$sql->data["mois"]]=$sql->data["heures"];
		$tabprev[$sql->data["avion"]]["_tot"]=$tabprev[$sql->data["avion"]]["_tot"]+$sql->data["heures"];

		$tress[$sql->data["avion"]]["id"]=$sql->data["avion"];

		if ($sql->data["heures"]>$maxp)
		  { $maxp=$sql->data["heures"]; }
	  }

	$chart=array();
	$chartcol="";
	$cs="";
	
// ---- Récupère la liste des ressources

//	$t=ListeRessources($sql);

	foreach ($tress as $id=>$k)
	  {
	  	$ress = new ress_class($id,$sql);
	  	$tress[$id]["couleur"]=$ress->couleur;
	  	$tress[$id]["immat"]=$ress->immatriculation;

			$chart[$id]["immat"]=$ress->immatriculation;

			$chartcol=$chartcol.$cs."'#".$ress->couleur."','#".$ress->couleur."'";
			$cs=",";
	  }

// ---- Récupère les heures de vols

	$query ="SELECT p67_calendrier.uid_avion AS id, date_format(p67_calendrier.dte_deb,'%c') AS dte2, SUM(p67_calendrier.temps) AS nb ";
	$query.="FROM p67_calendrier ";
	$query.="WHERE p67_calendrier.dte_deb>='$dte' AND p67_calendrier.dte_deb<'$dte2' AND p67_calendrier.prix<>0 ";
	$query.="GROUP BY p67_calendrier.uid_avion, date_format(p67_calendrier.dte_deb,'%Y%m') ORDER BY dte_deb";

	$sql->Query($query);

	$tabval=array();
	for($i=0; $i<$sql->rows; $i++)
	  { 
		$sql->GetRow($i);
		$tabval[$sql->data["id"]][$sql->data["dte2"]]=$sql->data;
	  }

	$id=1;
	$h=400;
	

	$max=$maxp*60;
	$tabaff[0]=0;

	$tabtot=array();

	foreach ($tress as $id=>$k)
	  {
		$tabtot[$id][0]=0;

		for($i=1;$i<=12;$i++)
		  {
			if ($tabval[$id][$i]["nb"]=="")
			  { $tabval[$id][$i]["nb"]=0; }
	
			$tabaff[$id][$i]=$tabval[$id][$i]["nb"];
			$tabtot[$id][$i]=$tabtot[$id][$i-1]+$tabval[$id][$i]["nb"];
	
			if ($tabaff[$id][$i]>$max)
			  { $max=$tabaff[$id][$i]; }
		  }
	

	  }

// ---- Affiche la courbe par mois

  	$x=0;

$cs=array();
	for($i=1;$i<=12;$i++)
	  {
		$tmpl_x->assign("aff_leftm", $x);
		$tmpl_x->assign("aff_widthm", count($tress)*25);
		
		foreach ($tress as $id=>$k)
		  {
			if ($max>0)
			  { $hh=floor($tabaff[$id][$i]*$h/$max); }
			else
			  { $hh=1; }
/*
			if ($hh<16)
			  { $hh=16; }
*/
			$tabprev[$id][$i]=(is_numeric($tabprev[$id][$i])) ? $tabprev[$id][$i] : "0";
			$pp=$tabprev[$id][$i]*60*$h/$max;

			$tot=floor($tabaff[$id][$i]/60);

$chart[$id]["val"]=$chart[$id]["val"].$cs[$id]."{y:".$tot.", color:'#".$tress[$id]["couleur"]."'}";
$chart[$id]["prev"]=$chart[$id]["prev"].$cs[$id]."{y:".$tabprev[$id][$i].", color:'#".(($tot<$tabprev[$id][$i]) ? "ff0000" : "00ff00" )."'}";
$chart[$id]["cumul"]=$chart[$id]["cumul"]+$tot;
$chart[$id]["cumulp"]=$chart[$id]["cumulp"]+$tabprev[$id][$i];

$chart[$id]["cumulval"]=$chart[$id]["cumulval"].$cs[$id]."{y:".$chart[$id]["cumul"].", color:'#".$tress[$id]["couleur"]."'}";
$chart[$id]["cumulpval"]=$chart[$id]["cumulpval"].$cs[$id]."{y:".$chart[$id]["cumulp"].", color:'#".(($chart[$id]["cumul"]<$chart[$id]["cumulp"]) ? "ff0000" : "00ff00" )."'}";
$cs[$id]=", ";

			$tmpl_x->assign("aff_left", $x);
			$tmpl_x->assign("h_width", 20);
			$tmpl_x->assign("h_plein", $hh);
			$tmpl_x->assign("h_vide", $h-$hh);
			$tmpl_x->assign("h_text", $h-$hh-15);
			$tmpl_x->assign("aff_couleur", $tress[$id]["couleur"]);
			$tmpl_x->assign("aff_val", ($tot<100) ? (($tot<10) ? "&nbsp;&nbsp;".$tot : "&nbsp;".$tot) : $tot);
	
			$tmpl_x->assign("prev_left", $x+5);
			$tmpl_x->assign("prev_aff", $tabprev[$id][$i]);
			$tmpl_x->assign("prev_width", ($hh-$pp>0) ? 5 : 5);
			$tmpl_x->assign("prev_text",$h-$pp);
			$tmpl_x->assign("prev_vide", ($hh-$pp>0) ? $h-$hh : $h-$pp);
			$tmpl_x->assign("prev_plein", ($hh-$pp>0) ? $hh-$pp : $pp-$hh);
	
			if ($tabaff[$id][$i]<$tabprev[$id][$i]*60)
			  { $tmpl_x->assign("prev_color","FF0000"); }
			else
			  { $tmpl_x->assign("prev_color","00FF00"); }

			$tmpl_x->parse("corps.tableau.lst_tableau.lst_ressource");
			$x=$x+25;
		  }

		$x=$x+5;
		$tmpl_x->assign("nbress", count($tress)*2);
		$tmpl_x->assign("mois", $tabm[$i]);

		$tmpl_x->parse("corps.tableau.lst_tableau");
		$tmpl_x->parse("corps.tableau.lst_mois");
	  }
	
	$tmpl_x->assign("titre","Total des heures de vols par avion et par mois");
	$tmpl_x->parse("corps.tableau");

/*
       {
            name: 'ZT',
            data: [49.9, 71.5, 106.4, 129.2, 144.0, 176.0, 135.6, 148.5, 216.4, 194.1, 95.6, 54.4]

        }, {
            name: 'XA',
            data: [83.6, 78.8, 98.5, 93.4, 106.0, 84.5, 105.0, 104.3, 91.2, 83.5, 106.6, 92.3]

        },
        */
        
	$txt="";
	$col="";
	foreach ($tress as $id=>$k)
	  {
			$txt.="{ type: 'column', name: '".$chart[$id]["immat"]."', data: [".$chart[$id]["val"]."] },";
			$txt.="{ type: 'line', name: 'Prévision ".$chart[$id]["immat"]."', data: [".$chart[$id]["prev"]."] },";
		}

	$tmpl_x->assign("aff_charttotal",$txt);
	$tmpl_x->assign("aff_colortotal",$chartcol);

	$tmpl_x->assign("seriesname","\{series.name\}");
	$tmpl_x->assign("seriescolor","\{series.color\}");


	$txt="";
	foreach ($tress as $id=>$k)
	  {
			$txt.="{ type: 'column', name: '".$chart[$id]["immat"]."', data: [".$chart[$id]["cumulval"]."] },";
			$txt.="{ type: 'line', name: 'Prévision ".$chart[$id]["immat"]."', data: [".$chart[$id]["cumulpval"]."] },";
		}

	$tmpl_x->assign("aff_chartcumul",$txt);
	$tmpl_x->assign("aff_colorcumul",$chartcol);


// ---- Affiche l'évolution
	$max=$maxptot;

	$prev=array();
	foreach ($tress as $id=>$k)
	  {
		$prev[$id]=0;
		for($i=1;$i<=12;$i++)
		  {
			$prev[$id]=$prev[$id]+$tabprev[$id][$i];
		  }

		if ($tabtot[$id][12]>$max)
		  {	$max=$tabtot[$id][12]; }
		if ($prev[$id]*60>$max)
		  {	$max=$prev[$id]*60; }

	  }

	$prev=array();
  	$x=0;
	for($i=1;$i<=12;$i++)
	  {
		$tmpl_x->assign("aff_leftm", $x);
		$tmpl_x->assign("aff_widthm", count($tress)*25);

		foreach ($tress as $id=>$k)
		  {
			if ($max>0)
			  { $hh=floor($tabtot[$id][$i]*$h/$max); }
			else
			  { $hh=1; $max=1; }
/*
			if ($hh<16)
			  { $hh=16; }
*/
			$prev[$id]=(is_numeric($prev[$id])) ? $prev[$id] : "0";

			$tot=floor($tabtot[$id][$i]/60);
			$prev[$id]=$prev[$id]+$tabprev[$id][$i];

			$pp=$prev[$id]*60*$h/$max;
			  
			$tmpl_x->assign("aff_left", $x);
			$tmpl_x->assign("h_width", 20);
			$tmpl_x->assign("h_plein", $hh);
			$tmpl_x->assign("h_vide", $h-$hh);
			$tmpl_x->assign("h_text", $h-$hh-15);
			$tmpl_x->assign("aff_couleur", $tress[$id]["couleur"]);
			$tmpl_x->assign("aff_val", ($tot<100) ? (($tot<10) ? "&nbsp;&nbsp;".$tot : "&nbsp;".$tot) : $tot);
	
	
			$tmpl_x->assign("prev_left", ($hh-$pp>0) ? $x : $x+5);

			$tmpl_x->assign("prev_aff", $prev[$id]);
			$tmpl_x->assign("prev_width", ($hh-$pp>0) ? 10 : 5);
			$tmpl_x->assign("prev_text",$h-$pp);
			$tmpl_x->assign("prev_vide", ($hh-$pp>0) ? $h-$hh : $h-$pp);
			$tmpl_x->assign("prev_plein", ($hh-$pp>0) ? $hh-$pp : $pp-$hh);
	
			if ($tabtot[$id][$i]<$prev[$id]*60)
			  { $tmpl_x->assign("prev_color","FF0000"); }
			else
			  { $tmpl_x->assign("prev_color","00FF00"); }
	

			$tmpl_x->parse("corps.tableau.lst_tableau.lst_ressource");
			$x=$x+25;
		  }

		$x=$x+5;
		$tmpl_x->assign("nbress", count($tress)*2);
		$tmpl_x->assign("mois", $tabm[$i]);

		$tmpl_x->parse("corps.tableau.lst_tableau");
		$tmpl_x->parse("corps.tableau.lst_mois");
	  }
	$tmpl_x->assign("titre","Cumul des heures de vols par avion sur l'année");
	$tmpl_x->parse("corps.tableau");

// ---- Affiche le total des heures

	foreach ($tress as $id=>$k)
	  {
		$tmpl_x->assign("tot", floor($tabtot[$id][12]/60));
		$tmpl_x->assign("prev", ($tabprev[$id]["_tot"]>0) ? $tabprev[$id]["_tot"] : "0");

		$tmpl_x->assign("ress_couleur", $tress[$id]["couleur"]);
		$tmpl_x->assign("ress_immat", $tress[$id]["immat"]);
		$tmpl_x->parse("corps.lst_ress_tot");
	  }
// ---- Affiche les années

	$query="SELECT annee FROM p67_prevision GROUP BY annee";
	$sql->Query($query);
	for($i=0; $i<$sql->rows; $i++)
	  { 
		$sql->GetRow($i);

		$tmpl_x->assign("form_dte", $sql->data["annee"]);
		$tmpl_x->assign("form_selected", (($sql->data["annee"]."-01-01"==$dte) ? "selected" : "") );
		$tmpl_x->parse("infos.lst_date");
	  }


// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");

?>
