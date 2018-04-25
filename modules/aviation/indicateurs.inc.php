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
		$dte2=(date("Y")+1);
	}
	else
	{
		$dte2=($dte+1);
	}
	$tmpl_x->assign("aff_annee", $dte);
	  
// ---- Tableau des heures
	$tabHeures=array();

	$query ="SELECT uid_avion AS id, date_format(dte_deb,'%c') AS dte, SUM(temps) AS nb ";
	$query.="FROM ".$MyOpt["tbl"]."_calendrier ";
	$query.="WHERE dte_deb>='$dte-01-01' AND dte_deb<'$dte2-01-01' AND temps<>0 ";
	$query.="GROUP BY uid_avion, date_format(dte_deb,'%Y%m')";
	$sql->Query($query);
	for($i=0; $i<$sql->rows; $i++)
	{ 
		$sql->GetRow($i);
		$tabHeures[$sql->data["id"]]["nb"][$sql->data["dte"]]=$sql->data["nb"];
	}

	$query="SELECT * FROM ".$MyOpt["tbl"]."_prevision WHERE annee='$dte'";
	$sql->Query($query);
	for($i=0; $i<$sql->rows; $i++)
	{ 
		$sql->GetRow($i);
		$tabHeures[$sql->data["avion"]]["prev"][$sql->data["mois"]]=$sql->data["heures"];	
	}

	$col="";
	$s="";
	foreach($tabHeures as $id=>$md)
	{
	  	$ress = new ress_class($id,$sql);
		$tabHeures[$id]["avion"]=$ress->immatriculation;
		$tabHeures[$id]["couleur"]=(($ress->couleur=="") ? dechex(rand(0x000000, 0xFFFFFF)) : $ress->couleur);
		$col.=$s."'#".$tabHeures[$id]["couleur"]."','#".$tabHeures[$id]["couleur"]."'";
		$s=",";
		for($i=1; $i<=12; $i++)
		{
			if (!isset($tabHeures[$id]["nb"][$i]))
			{
				$tabHeures[$id]["nb"][$i]=0;
			}
			if (!isset($tabHeures[$id]["prev"][$i]))
			{
				$tabHeures[$id]["prev"][$i]=0;
			}
			$tabHeures[$id]["total"][$i]=$tabHeures[$id]["total"][$i-1]+$tabHeures[$id]["nb"][$i];
		}
	}

// ---- Affiche le tableau des heures
	$tabTitre=array();
	$tabTitre["avion"]["aff"]="Avion";
	$tabTitre["avion"]["width"]=150;
	$tabTitre["line1"]["aff"]="<line>";
	for ($i=1;$i<=12;$i++)
	{
		$tabTitre["m".$i]["aff"]=$tabm[$i];
		$tabTitre["m".$i]["width"]=80;
		$tabTitre["m".$i]["bottom"]=0;
	}
	$tabTitre["line2"]["aff"]="<line>";
	$tabTitre["total"]["aff"]="Total";
	$tabTitre["total"]["width"]=100;
	$tabTitre["total"]["align"]="center";

	$tabValeur=array();

	foreach($tabHeures as $id=>$md)
	{
		$tabValeur[$id]["avion"]["val"]=$tabHeures[$id]["avion"];
		$tabValeur[$id]["avion"]["aff"]=$tabHeures[$id]["avion"];
		$tabValeur[$id]["avion"]["align"]="center";
		$tabValeur[$id]["line1"]["val"]="<line>";
		$tot=0;
		for ($i=1;$i<=12;$i++)
		{
			$tabValeur[$id]["m".$i]["val"]=$tabHeures[$id]["nb"][$i]."0";
			$tabValeur[$id]["m".$i]["aff"]=AffTemps($tabHeures[$id]["nb"][$i],"no");
			$tabValeur[$id]["m".$i]["align"]="center";
			$tot=$tot+$tabHeures[$id]["nb"][$i];
			
			$tabTitre["m".$i]["bottom"]=$tabTitre["m".$i]["bottom"]+$tabHeures[$id]["nb"][$i];
		}
		$tabValeur[$id]["line2"]["val"]="<line>";
		$tabValeur[$id]["total"]["val"]=$tot."0";
		$tabValeur[$id]["total"]["aff"]=AffTemps($tot,"no");
		$tabValeur[$id]["total"]["align"]="center";
	}
	$tot=0;
	foreach($tabTitre as $i=>$md)
	{
		$tot=$tot+$tabTitre[$i]["bottom"];
		$tabTitre[$i]["bottom"]=AffTemps($tabTitre[$i]["bottom"],"no");
	}
	$tabTitre["avion"]["bottom"]="Total";
	$tabTitre["total"]["bottom"]=AffTemps($tot,"no");

	if ($order=="") { $order="nom"; }
	if ($trie=="") { $trie="d"; }

	$tmpl_x->assign("aff_heures",AfficheTableau($tabValeur,$tabTitre,$order,$trie));


// ---- Affiche la courbe par mois
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
	$cs="";
	foreach ($tabHeures as $id=>$k)
	{
		$txt.=$cs."{ type: 'column', name: '".$tabHeures[$id]["avion"]."', data: [";
		$s="";
		for ($m=1; $m<=12; $m++)
		{
			$txt.=$s."{y:".floor($tabHeures[$id]["nb"][$m]/60).",color:'#".$tabHeures[$id]["couleur"]."'}";!
			$s=",";
		}
		$txt.="] }";
		$txt.=",{ type: 'line', name: 'Prévision ".$tabHeures[$id]["avion"]."', data: [";
		$s="";
		for ($m=1; $m<=12; $m++)
		{
			$txt.=$s."{y:".$tabHeures[$id]["prev"][$m].",color:'#".(($tabHeures[$id]["prev"][$m]<=floor($tabHeures[$id]["nb"][$m]/60)) ? "00ff00" : "ff0000")."'}";
			$s=",";
		}
		$txt.="] }";
		$cs=",";
	}

	$tmpl_x->assign("aff_charttotal",$txt);
	$tmpl_x->assign("aff_chartcolor",$col);

	$tmpl_x->assign("seriesname","\{series.name\}");
	$tmpl_x->assign("seriescolor","\{series.color\}");


	$txt="";
	$cs="";
	foreach ($tabHeures as $id=>$k)
	{
		$txt.=$cs."{ type: 'column', name: '".$tabHeures[$id]["avion"]."', data: [";
		$s="";
		for ($m=1; $m<=12; $m++)
			{
			$txt.=$s."{y:".floor($tabHeures[$id]["total"][$m]/60).",color:'#".$tabHeures[$id]["couleur"]."'}";!
			$s=",";
		}
		$txt.="] }";
		$txt.=",{ type: 'line', name: 'Prévision ".$tabHeures[$id]["avion"]."', data: [";
		$s="";
		$tot=0;
		for ($m=1; $m<=12; $m++)
		{
			$tot=$tot+$tabHeures[$id]["prev"][$m];
			$txt.=$s."{y:".$tot.",color:'#".(($tot<=floor($tabHeures[$id]["total"][$m]/60)) ? "00ff00" : "ff0000")."'}";
			$s=",";
		}
		$txt.="] }";
		$cs=",";
		// $txt.="{ type: 'line', name: 'Prévision ".$tabHeures[$id]["immat"]."', data: [".$chart[$id]["prev"]."] },";
	}

	$tmpl_x->assign("aff_chartcumul",$txt);


// ---- Affiche les années

	$query="SELECT DATE_FORMAT(dte_deb,'%Y') AS annee FROM ".$MyOpt["tbl"]."_calendrier GROUP BY DATE_FORMAT(dte_deb,'%Y') ORDER BY DATE_FORMAT(dte_deb,'%Y')";
	$sql->Query($query);
	for($i=0; $i<$sql->rows; $i++)
	  { 
		$sql->GetRow($i);

		$tmpl_x->assign("form_dte", $sql->data["annee"]);
		$tmpl_x->assign("form_selected", (($sql->data["annee"]==$dte) ? "selected" : "") );
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
