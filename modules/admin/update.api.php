<?
// ---- Refuse l'accès en direct
	if ((!isset($token)) || ($token==""))
	  { header("HTTP/1.0 401 Unauthorized"); exit; }

// ---- 
	$ret=array();
	$ret["result"]="OK";
	$ret["data"]="";

	$sql->show=false;

function AjoutLog($txt)
{
	return utf8_encode(htmlentities($txt,ENT_HTML5,"ISO-8859-1"))."<br />";
}	

// ---- Vérification des variables
	require ("modules/$mod/conf/variables.tmpl.php");

	$ret["data"].=AjoutLog("Vérification de la présence des variables");

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
					$ret["data"].=AjoutLog(" - Ajout : ".$nom.":".$var."='".$dd."'");
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
				$ret["data"].=AjoutLog(" - Ajout : ".$nom."='".preg_replace("/\//","-",$d)."'");
			}
			else
			{
				$MyOptTab[$nom]["valeur"]=$MyOpt[$nom];
			}
		}
	}

	if ($nb>0)
	{
		// echo $nb." variables ajoutées<br>";
		$ret["data"].=AjoutLog($nb." variables ajoutées");

		$res=GenereVariables($MyOptTab);
		$ret["data"].=AjoutLog($res);
		$MyOpt=UpdateVariables($MyOptTab);
	}

	if (!file_exists("config/variables.inc.php"))
	{
		error_log("easy-aero cannot variable file");
		$ret["result"]="NOK";
		$ret["data"]=AjoutLog("La création du fichier variables a échouée");
		echo json_encode($ret);
		exit;
	}
	
// ---- Charge la structure des tables de la version_compare
	$ret["data"].=AjoutLog("Vérification de la base de données");
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
    // [ae_abo_ligne] => Array
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
			$q="CREATE TABLE `".$MyOpt["tbl"]."_".$tab."` (`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, PRIMARY KEY (`id`)) ENGINE=InnoDB COLLATE=latin1_general_ci;";
			$res=$sql->Update($q);
			if ($res==-1)
			{
				$ret["result"]="NOK";
				$ret["data"].=AjoutLog(" ! Erreur création ".$MyOpt["tbl"]."_".$tab);	
			}
			else
			{
				$ret["data"].=AjoutLog(" - Création table ".$MyOpt["tbl"]."_".$tab);
				$tabProd[$MyOpt["tbl"]."_".$tab]["id"]["Type"]=(isset($fields["id"]["Type"])) ? $fields["id"]["Type"] : "int(10) UNSIGNED NOT NULL AUTO_INCREMENT";
				$tabProd[$MyOpt["tbl"]."_".$tab]["id"]["Index"]="PRIMARY";
			}
		}

		// Si la table existe ou qu'elle a pu être créée
		if (isset($tabProd[$MyOpt["tbl"]."_".$tab]))
		{
			foreach($fields as $field=>$d)
			{
				// Le champ n'existe pas
				if (!isset($tabProd[$MyOpt["tbl"]."_".$tab][$field]))
				{
					$q="ALTER TABLE `".$MyOpt["tbl"]."_".$tab."` ADD `".$field."` ".$tabTmpl[$tab][$field]["Type"]." DEFAULT ".(isset($tabTmpl[$tab][$field]["Default"]) ? " '".$tabTmpl[$tab][$field]["Default"]."'" : "NULL");
					$res=$sql->Update($q);
					if ($res==-1)
					{
						$ret["result"]="NOK";
						$ret["data"].=AjoutLog(" ! Erreur création ".$MyOpt["tbl"]."_".$tab.":".$field);	
					}
					else
					{
						$ret["data"].=AjoutLog(" - Création ".$MyOpt["tbl"]."_".$tab.":".$field." -> ".$tabTmpl[$tab][$field]["Type"]);
					}
				}
				// Le champ n'a pas le bon type
				else if ( ($tabTmpl[$tab][$field]["Type"]!=$tabProd[$MyOpt["tbl"]."_".$tab][$field]["Type"]) && ($tabTmpl[$tab][$field]["Index"]!="PRIMARY") )
				{
					$q="ALTER TABLE `".$MyOpt["tbl"]."_".$tab."` MODIFY `".$field."` ".$tabTmpl[$tab][$field]["Type"]." NOT NULL ".(isset($tabTmpl[$tab][$field]["Default"]) ? "DEFAULT '".$tabTmpl[$tab][$field]["Default"]."'" : "");
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
						$ret["data"].=AjoutLog(" ! Erreur création Index ".$MyOpt["tbl"]."_".$tab.":".$field);	
					}
					else
					{
						$ret["data"].=AjoutLog(" - Création Index ".$MyOpt["tbl"]."_".$tab.":".$field);
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
			$ok=0;
			require($dir."/".$d);
			if ($ok==0)
			{
				$ret["data"].=AjoutLog(" - Patch ".$p[1]);
				$q="INSERT INTO ".$MyOpt["tbl"]."_config SET param='patch',value='".$num."',dte_creat='".now()."'";
				$sql->Insert($q);
			}
			else
			{
				$ret["data"].=AjoutLog(" ! Erreur patch ".$p[1]);
			}
		}
	}

// ---- Mise à jour de la base
	require("version.php");
	$query="SELECT value FROM ".$MyOpt["tbl"]."_config WHERE param='version'";
	$res=$sql->QueryRow($query);
	$ver=$res["value"];

	if ($ver=="")
	{
		$query="INSERT INTO ".$MyOpt["tbl"]."_config SET param='version',value='".$myrev."',dte_creat='".now()."'";
		$sql->Insert($query);
	}
	else
	{
		$query="UPDATE ".$MyOpt["tbl"]."_config SET value='".$myrev."',dte_creat='".now()."' WHERE param='version'";
		$sql->Update($query);
	}
	
// ---- Renvoie le log
	echo json_encode($ret);
  
?>