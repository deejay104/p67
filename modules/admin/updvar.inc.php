<?
// ---- Refuse l'accès en direct
	if ((!isset($token)) || ($token==""))
	  { header("HTTP/1.0 401 Unauthorized"); exit; }

// ----
	
	if (GetDroit("ModifUtilDonnees"))
	{
error_log(print_r($_POST,true));
	if (is_array($_POST['id']))
		{
				$i = 1;
				foreach ($_POST['id'] as $varid)
				{
					if (is_numeric($varid))
					{
						$q="UPDATE ".$MyOpt["tbl"]."_utildonneesdef SET ordre='".$i."' WHERE id='".$varid."'";
						$sql->Update($q);
					}
	
					$i++;
				}
		}
		else
		{
			echo("No data provided.");
		}
	}
	else
	{
	  	echo("Access denied.");
	}
?>