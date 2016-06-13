<?
	$tabExport=array();

	if ($list!="")
	  {
		$query ="SELECT id FROM ".$MyOpt["tbl"]."_export WHERE nom='$list'";
		$res=$sql->QueryRow($query);
		$id=$res["id"];
	  }
			
	if ($id>0)
	  {
		$query ="SELECT * FROM ".$MyOpt["tbl"]."_export WHERE id='$id'";
		$res=$sql->QueryRow($query);

	  	$dtey=substr($dte,0,4);
	  	if (!is_numeric($dtey))
	  	  { $dtey="0"; }

		$list=$res["nom"];		
		$query=preg_replace("/\\\$tbl/",$MyOpt["tbl"],$res["requete"]);
		$query=preg_replace("/\\\$dte/",$dtey,$query);
		$sql->Query($query);

		for($i=0; $i<$sql->rows; $i++)
		  {
			$sql->GetRow($i);
			$tabExport[$i]=ConvertToArray($sql->data);
		  }

	  }
	else if ($list=="famille")
	  {
		$lstusr=ListActiveUsers($sql,"","enfant");

		$tabValeur=array();
		foreach($lstusr as $i=>$id)
		  {
			$usr = new user_class($id,$sql,false);

			if (GetModule("aviation"))
			  { $usr->LoadLache(); }
			if (GetModule("creche"))
			  { $usr->LoadEnfants(); }		

			foreach($usr->data as $key=>$v)
			  {
			  	$val=$usr->Aff($key,"val");
			  	if ($val!="******")
				  { $tabExport[$i][$key]=$val; }
			  }
		  }
	  }
	else
	  {
		FatalError( "L'export demandé n'existe pas.");
	  }


	if (count($tabExport)>0)
	  {
		header("Content-Type: application/vnd.ms-excel;");
		header('Content-Disposition: filename="export-'.$list.'.csv";');

	  	foreach($tabExport[0] as $key=>$val)
	  	  {
	  	  	echo $key.";";
	  	  }
		echo "\n";
		
		foreach($tabExport as $i=>$txt)
		  {
		  	foreach($txt as $key=>$val)
		  	  {
				$v=preg_replace("/\n/"," ",$val);
				$v=preg_replace("/\r/","",$v);
				$v=preg_replace("/;/",",",$v);

		  	  	echo $v.";";
		  	  }
			echo "\n";
		  }
		exit;
	  }
	else
	  {
		FatalError( "La liste est vide.");
	  }


function ConvertToArray($tab)
  {
  	$ret=array();
  	foreach($tab as $k => $v)
  	  {
  	  	if (!is_numeric($k))
  	  	  {
  	  	  	$ret[$k]=$v;
  	  	  }
  	  }
  	return $ret;
  }
?>