<?
// ---- Refuse l'acc�s en direct
	if ((!isset($token)) || ($token==""))
	  { header("HTTP/1.0 401 Unauthorized"); exit; }

// ---- 
	$ret=array();
	$ret["result"]="OK";
	$ret["data"]="";

	$sql->show=false;

function AjoutLog($txt)
{
	return htmlentities($txt)."<br />";
}	

// ---- V�rification des variables
	require ("modules/$mod/conf/variables.tmpl.php");

	$ret["data"].=AjoutLog("V�rification de la pr�sence des variables");

	$nb=0;
	$MyOptTab=array();
	foreach ($MyOptTmpl as $nom=>$d)
	{
		if (is_array($d))
		{
			foreach($d as $var=>$dd)
			{
				if(!isset($MyOpt[$nom][$var]))
				{
					$MyOptTab[$nom][$var]=$dd;
					$nb=$nb+1;
					// echo "Ajout : \$MyOpt[\"".$nom."\"][\"".$var."\"]='".$dd."'<br>";
					$ret["data"].=AjoutLog(" - Ajout : \$MyOpt[\"".$nom."\"][\"".$var."\"]='".$dd."'");
				}
				else
				{
					$MyOptTab[$nom][$var]=$MyOpt[$nom][$var];
				}
			}
		}
		else
		{
			if(!isset($MyOpt[$nom]))
			{
				$MyOptTab[$nom]["valeur"]=$d;
				$nb=$nb+1;
				// echo "Ajout : \$MyOpt[\"".$nom."\"]='".$d."'<br>";
				$ret["data"].=AjoutLog(" - Ajout : \$MyOpt[\"".$nom."\"]='".$d."'");
			}
			else
			{
				$MyOptTab[$nom]["valeur"]=$MyOpt[$nom];
			}
		}
	}

	if ($nb>0)
	{
		// echo $nb." variables ajout�es<br>";
		$ret["data"].=AjoutLog($nb." variables ajout�es");

		$res=GenereVariables($MyOptTab);
		$MyOpt=UpdateVariables($MyOptTab);
		$ret["data"].=AjoutLog($res);
	}
	
	
// ---- Charge la structure des tables de la version_compare
	$ret["data"].=AjoutLog("V�rification de la base de donn�es");
	require ("modules/admin/conf/structure.tmpl.php");

// ---- Charge la structure des tables en base
	$tabProd=array();
	$q="SHOW TABLES;";
	$sql->Query($q);
	for($i=0; $i<$sql->rows; $i++)
	{
		$sql->GetRow($i);
		$tabProd[$sql->data["Tables_in_".$db]]=array();
	}
	
	foreach($tabProd as $tab=>$t)
	{
		$q="DESCRIBE ".$tab.";";
		$sql->Query($q);
		for($i=0; $i<$sql->rows; $i++)
		{
			$sql->GetRow($i);
			$tabProd[$tab][$sql->data["Field"]]["Type"]=$sql->data["Type"];
			if ($sql->data["Default"]!="")
			{
				$tabProd[$tab][$sql->data["Field"]]["Default"] = $sql->data["Default"];
			}
		}
		$q="SHOW INDEX FROM ".$tab.";";
		$sql->Query($q);
		for($i=0; $i<$sql->rows; $i++)
		{
			$sql->GetRow($i);
			$tabProd[$tab][$sql->data["Column_name"]]["Index"]=($sql->data["Key_name"]=="PRIMARY") ? "PRIMARY" : 1;
		}

	}

// ---- Exporte la structure existante
/*
	//[Non_unique] => 0
	echo "Array\n(\n";
	foreach($tabProd as $tab=>$t)
	{
		echo "\t\"".$tab."\" => Array (\n";

		foreach($t as $f=>$d)
		{
			echo "\t\t\"".$f."\" => Array(";
			foreach($d as $ff=>$dd)
			{
				echo "\"".$ff."\" => \"".$dd."\", ";
			}
			echo "),\n";
		}
		echo "\t),\n";
	}
	echo ");";
*/

	
// ---- Compare les structures
    // [p67_abo_ligne] => Array
        // (
            // [id] => Array
                // (
                    // [Type] => int(10) unsigned
                    // [Index] => PRIMARY
                // )

            // [abonum] => Array
                // (
                    // [Type] => varchar(8)
                    // [Index] => 1
                // )
        // )

	foreach($tabTmpl as $tab=>$fields)
	{
		// Tester si la table n'existe pas
		if (!isset($tabProd[$MyOpt["tbl"]."_".$tab]))
		{
			$q="CREATE TABLE `".$MyOpt["tbl"]."_".$tab."` (`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, PRIMARY KEY (`id`)) ENGINE=InnoDB; ";
			$res=$sql->Update($q);
			if ($res==-1)
			{
				$ret["result"]="NOK";
				$ret["data"].=AjoutLog(" ! Erreur cr�ation ".$MyOpt["tbl"]."_".$tab);	
			}
			else
			{
				$ret["data"].=AjoutLog(" - Cr�ation table ".$MyOpt["tbl"]."_".$tab);
				$tabProd[$MyOpt["tbl"]."_".$tab]["id"]["Type"]=(isset($fields["id"]["Type"])) ? $fields["id"]["Type"] : "int(10) UNSIGNED NOT NULL AUTO_INCREMENT";
				$tabProd[$MyOpt["tbl"]."_".$tab]["id"]["Index"]="PRIMARY";
			}
		}

		// Si la table existe ou quelle a pu �tre cr��e
		if (isset($tabProd[$MyOpt["tbl"]."_".$tab]))
		{
			foreach($fields as $field=>$d)
			{
				// Le champ n'existe pas
				if (!isset($tabProd[$MyOpt["tbl"]."_".$tab][$field]))
				{
					$q="ALTER TABLE `".$MyOpt["tbl"]."_".$tab."` ADD `".$field."` ".$tabTmpl[$tab][$field]["Type"]." DEFAULT '".(isset($tabTmpl[$tab][$field]["Default"]) ? $tabTmpl[$tab][$field]["Default"] : "")."'";
					$res=$sql->Update($q);
					if ($res==-1)
					{
						$ret["result"]="NOK";
						$ret["data"].=AjoutLog(" ! Erreur cr�ation ".$MyOpt["tbl"]."_".$tab.":".$field);	
					}
					else
					{
						$ret["data"].=AjoutLog(" - Cr�ation ".$MyOpt["tbl"]."_".$tab.":".$field." -> ".$tabTmpl[$tab][$field]["Type"]);
					}
				}
				// Le champ n'a pas le bon type
				else if ($tabTmpl[$tab][$field]["Type"]!=$tabProd[$MyOpt["tbl"]."_".$tab][$field]["Type"])
				{
					$q="ALTER TABLE `".$MyOpt["tbl"]."_".$tab."` MODIFY `".$field."` ".$tabTmpl[$tab][$field]["Type"]." DEFAULT '".(isset($tabTmpl[$tab][$field]["Default"]) ? $tabTmpl[$tab][$field]["Default"] : "")."'";
					$res=$sql->Update($q);
					if ($res==-1)
					{
						$ret["result"]="NOK";
						$ret["data"].=AjoutLog(" ! Erreur modification ".$MyOpt["tbl"]."_".$tab.":".$field);	
					}
					else
					{
						$ret["data"].=AjoutLog(" - Modification ".$MyOpt["tbl"]."_".$tab.":".$field." -> ".$tabTmpl[$tab][$field]["Type"]);
					}
				}

				// Index
				if ((isset($tabTmpl[$tab][$field]["Index"])) && (!isset($tabProd[$MyOpt["tbl"]."_".$tab][$field]["Index"])))
				{
					$q="ALTER TABLE `".$MyOpt["tbl"]."_".$tab."` ADD INDEX (`".$field."`)";
					$res=$sql->Update($q);
					if ($res==-1)
					{
						$ret["result"]="NOK";
						$ret["data"].=AjoutLog(" ! Erreur cr�ation Index ".$MyOpt["tbl"]."_".$tab.":".$field);	
					}
					else
					{
						$ret["data"].=AjoutLog(" - Cr�ation Index ".$MyOpt["tbl"]."_".$tab.":".$field);
					}
				}
			}
		}
	}

// ---- Applique les patchs
	$ret["data"].=AjoutLog("Application des patchs");

	$tabPatch=array();
	$q="SELECT * FROM ".$MyOpt["tbl"]."_config WHERE param='patch'";
	$sql->Query($q);
	for($i=0; $i<$sql->rows; $i++)
	{
		$sql->GetRow($i);
		$tabPatch[$sql->data["value"]]=$sql->data["dte_creat"];
	}

	$dir = "modules/$mod/patch";
	$tdir = array_diff(scandir($dir), array('..', '.'));

	foreach ($tdir as $ii=>$d)
	{
		preg_match("/v([0-9]*)\.inc\.php/",$d,$p);
		$num=$p[1];
		if (!isset($tabPatch[$num]))
		{
			$ret["data"].=AjoutLog(" - ".$p[1]);
			$q="INSERT INTO ".$MyOpt["tbl"]."_config SET param='patch',value='".$num."',dte_creat='".now()."'";
			$sql->Insert($q);
		}
	}

// ---- Renvoie le log
	echo json_encode($ret);
  
?>