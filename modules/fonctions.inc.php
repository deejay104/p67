<?
// ---------------------------------------------------------------------------------------------
//   Fonctions
// ---------------------------------------------------------------------------------------------

function GetModule($mod)
  { global $MyOpt;
	if ($MyOpt["module"][$mod]=="on")
	  { return true; }
	else
	  { return false; }
  }

function MyRep($file)
  { global $module, $mod, $lang,$theme;
  	$myfile=substr($file,0,strrpos($file,"."));
		$myext=substr($file,strrpos($file,".")+1,strlen($file)-strrpos($file,".")-1);

  	if ((file_exists("$module/$mod/tmpl/$myfile.$theme.$myext")) && ($mod!=""))
  	  { return "$module/$mod/tmpl/$myfile.$theme.$myext"; }
		else if ((file_exists("$module/$mod/tmpl/$file")) && ($mod!=""))
  	  { return "$module/$mod/tmpl/$file"; }
		else if ((file_exists("$module/$mod/$file")) && ($mod!=""))
  	  { return "$module/$mod/$file"; }
  	else if (file_exists("$module/$myfile.$theme.$myext"))
  	  { return "$module/$myfile.$theme.$myext"; }
  	else if (file_exists("$module/$file"))
  	  { return "$module/$file"; }
  	else if (file_exists("config/$file"))
  	  { return "config/$file"; }
  	else
  	  { return ""; }
  }

function GetDroit($droit)
  { global $myuser;

		if (trim($droit)=="")
		  { return true; }
		else if ($myuser->role[$droit])
		  { return true; }
		else if ($myuser->groupe["ADM"])
		  { return true; }
		elseif ($myuser->groupe[$droit])
		  { return true; }
		else
		  { return false; }
  }

function myPrint($txt)
{ global $gl_mode,$gl_myprint_txt;
	if ($gl_mode=="batch")
	{
		echo $txt."\n";
	}
	else
	{
		$gl_myprint_txt.=$txt."<br />";
	}
}


// Affiche un temps en minute en heures/minutes
function AffTemps($tps,$short="yes") {
	$th=floor($tps/60);
	$tm=$tps-$th*60;
	$tm=substr("00",0,2-strlen($tm)).$tm;

	if (($th>0) || ($short=="no"))
	  { return $th."h ".$tm; }
	else
	  { return $tm."min"; }
}

// Transforme une date en format SQL
function date2sql($date) {
	if (preg_match("/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/",$date))
	  { return $date; }

  $d = preg_replace('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2,4})$/','\\3-\\2-\\1', $date);
  if ($d == $date) { $d = preg_replace('/^([0-9]{1,2})-([0-9]{1,2})-([0-9]{2,4})$/','\\3-\\2-\\1', $date); }
  if ($d == $date) { $d = preg_replace('/^([0-9]{1,2}).([0-9]{1,2}).([0-9]{2,4})$/','\\3-\\2-\\1', $date); }
  if ($d == $date) { $d = preg_replace('/^([0-9]{2,4})\/([0-9]{1,2})\/([0-9]{1,2})$/','\\1-\\2-\\3', $date); }
  if ($d == $date) { $d = preg_replace('/^([0-9]{2,4}).([0-9]{1,2}).([0-9]{1,2})$/','\\1-\\2-\\3', $date); }
  if ($d == $date) { $d = preg_replace('/^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ?([0-9:]*)?$/','\\1-\\2-\\3', $date); }
  if ($d == $date) { $d = preg_replace('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2,4}) ?([0-9:]*)?$/','\\3-\\2-\\1', $date); }
  if (($d == $date) && ($date != '')) { $d = "nok"; }
  return $d;
}

// Transforme une date SQL en date jj/mm/aaaa
function sql2date($date,$aff="") {
	if ($aff=="jour")
	  { return preg_replace('/^([0-9]{2,4})-([0-9]{1,2})-([0-9]{1,2}) [^$]*$/','\\3/\\2/\\1', $date); }
	else if ($aff=="nosec")
	  { return preg_replace('/^([0-9]{2,4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]*):([0-9]*):([0-9 ]*)$/','\\3/\\2/\\1 \\4:\\5', $date); }
	else if ($aff=="heure")
	  {
			$h=preg_replace('/^([0-9]{2,4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):?([0-9]{1,2})?:?([0-9]{1,2})?$/','\\4', $date);
			$m=preg_replace('/^([0-9]{2,4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):?([0-9]{1,2})?:?([0-9]{1,2})?$/','\\5', $date);
	  	return $h.(($m!="") ? ":$m" : ":00");
	  }
	else
	  { return preg_replace('/^([0-9]{2,4})-([0-9]{1,2})-([0-9]{1,2})([0-9: ]*)$/','\\3/\\2/\\1\\4', $date); }
}

// Transforme une date SQL en heure hh:mm
function sql2time($date,$aff="") {
	if ($aff=="nosec")
	  { return preg_replace('/^([0-9]{2,4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]*):([0-9]*):([0-9]*)$/','\\4:\\5', $date); }
	else
	  { return preg_replace('/^([0-9]{2,4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]*):([0-9]*):([0-9]*)$/','\\4:\\5:\\6', $date); }
}


// Calcul le nombre de secondes entre deux dates
function date_diff_txt($date1, $date2) {
  $s = strtotime($date2)-strtotime($date1);
  return $s;
}



// Ajoute un nombre de jour à une date
function CalcDate($dte, $n)
  {
		return date("Y-m-d",mktime(0, 0, 0, date("n",strtotime($dte)), date("j",strtotime($dte))+$n, date("Y",strtotime($dte))));
  }	


function AffInitiales($res)
  {
  	if ($res["initiales"]!="")
  	  { return strtoupper($res["initiales"]); }
  	else
  	  { return strtoupper(substr($res["prenom"],0,1).substr($res["nom"],0,1)); }
  }

function AffInfo($txt,$key,$typeaff="html",$cond=true)
  {
	if ($key=="prenom")
	  { $ret=ucwords($txt); }
	else if ($key=="nom")
	  { $ret=strtoupper($txt); }
	else if ($key=="fullname")
	  { $ret=strtoupper($txt); }
	else if ($key=="mail")
	  { $ret=strtolower($txt); }
	else if ($key=="initiales")
	  { $ret=strtoupper($txt); }
	else if ($key=="ville")
	  { $ret=strtoupper($txt); }
	else if (($key=="tel_fixe") || ($key=="tel_portable") || ($key=="tel_bureau"))
	  { $ret=AffTelephone($txt); }
	else if ($key=="type")
	  { $ret=ucwords($txt); }
	else if ($key=="aff_rapide")
	  { $ret=($txt=="n") ? "Normal" : "Rapide"; }
	else if (($key=="dte_licence") || ($key=="dte_medicale") || ($key=="dte_naissance"))
	  { $ret=sql2date($txt); }
	else
	  { $ret=$txt; }

	// Défini les droits de modification des utilisateurs
	$mycond=$cond;				// Le user a le droit de modifier toutes ses données
	$tabdef["INS"]=false;			// TRE n'a le droit de modifier aucun champs
	$tabdef["TRE"]=false;			// TRE n'a le droit de modifier aucun champs
	$tabdef["PRE"]=false;			// TRE n'a le droit de modifier aucun champs
	$tabdef["USR"]=true;			// USR a le droit de modifier tous les champs

	$tabcond["_ME"]["decouvert"]=-1;
	$tabcond["_ME"]["dte_licence"]=-1;
	$tabcond["_ME"]["dte_medicale"]=-1;
	$tabcond["USR"]["decouvert"]=-1;
	$tabcond["USR"]["dte_licence"]=-1;
	$tabcond["USR"]["dte_medicale"]=-1;
	$tabcond["TRE"]["decouvert"]=1;
	$tabcond["PRE"]["decouvert"]=1;
	$tabcond["PRE"]["dte_licence"]=1;
	$tabcond["PRE"]["dte_medicale"]=1;

	// Prend les droits par défaut
	foreach($tabdef as $d=>$t)
	  {
		if ((GetDroit($d)) && (!$cond))
		  { $mycond=$t; }
	  }

	// Surclasse les droits par défaut par les droits du user
	foreach($tabcond as $d=>$t)
	  {
		if (($t[$key]==-1) && ( (($d=="_ME") && ($cond)) || (GetDroit($d)) ) )
		  { $mycond=false; }

		if (($t[$key]==1) && ( (($d=="_ME") && ($cond)) || (GetDroit($d)) ) )
		  { $mycond=true; }
	  }

	if ($typeaff=="html")
	  {
		if ($key=="commentaire")
	  	  { $ret=nl2br(htmlentities($ret)); }
		else if ($key=="mail")
	  	  { $ret="<A href=\"mailto:".strtolower($ret)."\">".strtolower($ret)."</A>"; }
		else if (($key=="dte_licence") || ($key=="dte_medicale"))
	  	  {
	  	  	if ($txt!="0000-00-00")
	  	  	  {
		  	  	if (date_diff($txt,date("Y-m-d"))>0)
		  	  	  { $ret="<B><FONT color=\"red\">$ret</A></B>"; }
		  	  	else if (date_diff($txt,date("Y-m-d"))>-30*24*3600)
		  	  	  { $ret="<B><FONT color=\"orange\">$ret</A></B>"; }
			  }
			else
			  {	$ret="-"; }
	  	  }
	  }
	else if (($typeaff=="form") && ($mycond))
	  {
		if ($key=="commentaire")
	  	  { $ret="<TEXTAREA name=\"form_info[$key]\" cols=60 rows=5>$ret</TEXTAREA>"; }
		else if ($key=="type")
	  	  {
	  	  	$ret ="<SELECT name=\"form_info[$key]\">";
	  	  	$ret.="<OPTION value=\"pilote\" ".(($txt=="pilote")?"selected":"").">Pilote</OPTION>";
	  	  	$ret.="<OPTION value=\"eleve\" ".(($txt=="eleve")?"selected":"").">Elève</OPTION>";
	  	  	$ret.="<OPTION value=\"instructeur\" ".(($txt=="instructeur")?"selected":"").">Instructeur</OPTION>";
	  	  	$ret.="<OPTION value=\"membre\" ".(($txt=="membre")?"selected":"").">Membre</OPTION>";
	  	  	$ret.="<OPTION value=\"invite\" ".(($txt=="invite")?"selected":"").">Invité</OPTION>";
	  	  	$ret.="</SELECT>";
	  	  }
		else if ($key=="aff_rapide")
	  	  {
	  	  	$ret ="<SELECT name=\"form_info[$key]\">";
	  	  	$ret.="<OPTION value=\"n\" ".(($txt=="n")?"selected":"").">Normal</OPTION>";
	  	  	$ret.="<OPTION value=\"y\" ".(($txt=="y")?"selected":"").">Rapide</OPTION>";
	  	  	$ret.="</SELECT>";
	  	  }
		else
		  { $ret="<INPUT name=\"form_info[$key]\" value=\"$ret\">"; }
	  }

	return $ret;
  }

function MyMail($from,$to,$tabcc,$subject,$message,$headers="",$files="")
{ global $MyOpt;

	if (is_array($from))
  {
	 	$me=$from["name"];
	  $fromadd=$from["mail"];
	}
	else
	{
		if ($from=="") { $from = ini_get("sendmail_from"); }

		preg_match("/^([^@]*)@([^$]*)$/",$from,$t);
		$me=$t[0];
		$fromadd=$from;
	}

	if ($MyOpt["sendmail"]==1) { MyPrint("From:$from - To:$to - Cc:$cc - Subject:$subject"); return -1; }

	require_once 'external/PHPMailer/PHPMailerAutoload.php';
	
	//Create a new PHPMailer instance
	$mail = new PHPMailer;


	if ($MyOpt["mail"]["smtp"]==1)
	{
		// Set PHPMailer to use SMTP transport
		$mail->isSMTP();
		//Set the hostname of the mail server
		$mail->Host = $MyOpt["mail"]["host"];
		//Set the SMTP port number - likely to be 25, 465 or 587
		$mail->Port = $MyOpt["mail"]["port"];
		// Do not close connection to SMTP
		$mail->SMTPKeepAlive = true;
		//Whether to use SMTP authentication
		if ($MyOpt["mail"]["username"]!="")
		{
			$mail->SMTPAuth = true;
			//Username to use for SMTP authentication
			$mail->Username = $MyOpt["mail"]["username"];
			//Password to use for SMTP authentication
			$mail->Password = $MyOpt["mail"]["password"];
		}
	}
	else
	{
		$mail->isSendmail();
	}

	//Set who the message is to be sent from
	$mail->setFrom($fromadd, $me);
	//Set an alternative reply-to address
	$mail->addReplyTo($fromadd, "");
	//Set who the message is to be sent to

	$mail->addAddress($to);
//$mail->addAddress("matthieu@les-mnms.net");

	if ((is_array($tabcc)) && (count($tabcc)>0))
	{
		foreach($tabcc as $i=>$m)
		{
			$mail->addCC($m);
		}
	}
	
	//Set the subject line
	$mail->Subject = $subject;

	$mail->msgHTML($message);
	$mail->AltBody = strip_tags($message);

	if (is_array($files))
	{
		foreach($files as $i=>$d)
		{
			if ($d["type"]=="text")
			{
				$mail->AddStringAttachment($d["data"],$d["nom"]);
			}
			else if ($d["type"]=="file")
			{
				$mail->AddAttachment($d["data"],$d["nom"]);
			}
		}
	}
	
	//send the message, check for errors
	return $mail->send();
}


function SendMail($From,$To,$Cc,$Subject,$Text,$Html,$AttmFiles)
{ global $MyOpt;
	/*
	function SendMail($From, $FromName, $To, $ToName, $Subject, $Text, $Html, $AttmFiles)
	$From      ... sender mail address like "my@address.com"
	$FromName  ... sender name like "My Name"
	$To        ... recipient mail address like "your@address.com"
	$ToName    ... recipients name like "Your Name"
	$Subject   ... subject of the mail like "This is my first testmail"
	$Text      ... text version of the mail
	$Html      ... html version of the mail
	$AttmFiles ... array containing the filenames to attach like array("file1","file2")
	*/

	// attachments
	$attach=array();
	if($AttmFiles)
	{
		$i=0;
		foreach($AttmFiles as $AttmFile)
		{
			$patharray = explode ("/", $AttmFile); 
			$FileName=$patharray[count($patharray)-1];
			$attach[$i]["nom"]=$FileName;
			$attach[$i]["type"]="file";
			$attach[$i]["data"]=$AttmFile;

			$i=$i+1;
		}
  }
	
	//message ends
	$Msg.="\n--".$OB."--\n";

	if ($MyOpt["sendmail"]==1) { echo "From:$From - To:$To - Cc:$Cc - Subject:$Subject<BR>"; return -1; }

	return MyMail($From,$To,$Cc,$Subject,$Msg,$headers);
}


/* **** Fonction d'affichage d'un tableau ****

	$tab	Tableau contenant l'ensemble des entrées à afficher, chaque ligne est constituée d'un tableau
		par ex $tab[0]["nom"]="Produit 1"; $tab[0]["statut"]="OK"; $tab[0]["id"]="104"; $tab[0]["type"]="P";
		       $tab[1]["nom"]="Produit 2"; $tab[1]["statut"]="NOK"; $tab[1]["id"]="97"; $tab[1]["type"]="P";
		       ...
	$varaff	Tableau ou liste (séparé par des virgules) des champs à afficher
		par ex $varaff="nom,statut";
	
	$varlar	Tableau ou liste (séparé par des virgules) de la largeur de chaque colonne
		par ex $varlar="350,50";
	
	$order	Nom du champs sur lequel va être trié la sortie (default sélectionne le résultat de la fonction AffProduit)
		par ex $order="nom";
	
	$vartitre Tableau ou liste (séparé par des virgules) indiquant le nom à afficher en haut de chaque colonne
		par ex $vartitre="Produit,Statut";

	$valign	Alignement du texte dans les cellules (top, middle, bottom)
	
	$sens	Sens pour le trie des colonnes 'i' -> normal, 'd' -> inversé

	$skey	Si 'yes' test l'appuye de touche pour le raccourcis clavier

	Les exemples donnerait la sortie suivante :
		Produit		Statut

		Produit1	OK
		Produit2	NOK
*/


function AfficheTableau($tabValeur,$tabTitre="",$order="",$trie="",$url="",$start=0,$limit="",$nbline=0)
  {global $mod,$rub;
	$myColor[50]="E7E7E7";
	$myColor[55]="FFB1B1";
	$myColor[60]="F7F7F7";
	$myColor[65]="FFB1B1";
	$col=50;

	$ret ="\n<table class='tableauAff'>\n";

	$ret.="<tr>";
	$ret.="<th width=20>&nbsp;</th>";
	$nb=1;
	
	$page=$_SERVER["SCRIPT_NAME"]."?mod=$mod&rub=$rub";

	if (!is_array($tabTitre))
	  {
	  	$tabTitre=array();
	  	foreach($tabValeur[0] as $name=>$t)
	  	  {
	  	  	$tabTitre[$name]["aff"]=$name;
	  	  }
	  }
  	foreach($tabTitre as $name=>$v)
	  {
		if ($name==$order)
		  {
			$ret.="<th width='".$v["width"]."'".(($v["align"]!="") ? " align='".$v["align"]."'" : "").">";
			$ret.="<b><a href='$page&order=$name&trie=".(($trie=="d") ? "i" : "d").(($url!="") ? "&$url" : "")."&ts=$i'>".$v["aff"]."</a></b>";
		  	$ret.=" <img src='images/sens_$trie.gif' border=0>";
		  }
		else if ($v["aff"]=="<line>")
		  {
			$ret.="<th style='width:".$v["width"]."px; background-color:black;'>";
		  }
		else
		  {
			$ret.="<th width='".$v["width"]."'".(($v["align"]!="") ? " align='".$v["align"]."'" : "").">";
			$ret.="<b><a href='$page&order=$name&trie=d".(($url!="") ? "&$url" : "")."&ts=$i'>".$v["aff"]."</a></b>";
		  }
		$ret.="</th>";
		$nb++;
	  }

	
	$ret.="</tr>\n";
/*
	$ret.="<tr bgcolor='black'>";
	$ret.="<td height='1' colspan='$nb'><img src='images/rien.gif' height=1 alt=''></td>";
	$ret.="</tr>\n";
*/

	if (is_array($tabValeur))
	  {
		if ($trie=="d")
		  { usort($tabValeur,"TrieVal"); }
		else if ($trie=="i")
		  { usort($tabValeur,"TrieValInv"); }
		$ii=0;
	
		if ($limit=="")
		  { $limit=count($tabValeur); }

		foreach($tabValeur as $i=>$val)
		  { 
			if (($ii>=$start) && ($ii<$start+$limit))
			  {
				$col = abs($col-110);
				$ret.="<tr onmouseover=\"setPointer(this, 'over', '#".$myColor[$col]."', '#".$myColor[$col+5]."', '#FF0000')\" onmouseout=\"setPointer(this, 'out', '#".$myColor[$col]."', '#".$myColor[$col+5]."', '#FF0000')\">";
				$ret.="<td bgcolor=\"#".$myColor[$col]."\">&nbsp;</td>";
		
				foreach($tabTitre as $name=>$v)
				  {
					if ($val[$name]["val"]=="<line>")
					  {
						$ret.="<td style='background-color:black;'></td>";
					  }
					else
					  {
						$ret.="<td bgcolor=\"#".$myColor[$col]."\"".(($val[$name]["align"]!="") ? " align='".$val[$name]["align"]."'" : "").">".(($val[$name]["aff"]=="") ? $val[$name]["val"] : $val[$name]["aff"])."</td>";
					  }
				  }
				$ret.="</tr>\n";
			  }
			$ii=$ii+1;
		  }
	  }
	
	$ret.="<tr>";
	$ret.="<td colspan='$nb' class='tableauEnd'></td>";
	$ret.="</tr>\n";
	$ret.="</table>\n";

	// Affiche la liste des pages
	$nbtot=($nbline>0) ? $nbline : count($tabValeur);
	if ($nbtot>$limit)
	  {
		$lstpage="";
		$ii=1;
  	  	$t=0;
		$nbp=10;

		for($i=0; $i<$nbtot; $i=$i+$limit)
		  {
		  	if (($i<=$start) && ($i>$start-$limit))
		  	  {
		  	  	$lstpage.="<a href='$page&order=$order".(($trie!="") ? "&trie=$trie" : "").(($url!="") ? "&$url" : "")."&ts=$i'>[$ii]</a> ";
		  	  	$t=0;
		  	  }
			else if ( (($i>$start-$nbp*$limit/2) && ($i<$start+$nbp*$limit/2)) || ($i>$nbtot-$limit) || ($i==0))
		  	  {
		  	  	$lstpage.="<a href='$page&order=$order".(($trie!="") ? "&trie=$trie" : "").(($url!="") ? "&$url" : "")."&ts=$i'>$ii</a> ";
		  	  	$t=0;
		  	  }
		  	else if ($t==0)
		  	  {
		  	  	$lstpage.=" ... ";
				$t=1;
		  	  }
		  	$ii=$ii+1;
		  }

		$ret.="Pages : $lstpage<br />\n";
	  }


	return $ret;
  }

function TrieVal ($a, $b)
  { global $order;
	if (strtolower($a[$order]["val"]) == strtolower($b[$order]["val"]))
	  { return 0; }
	else if (strtolower($a[$order]["val"]) < strtolower($b[$order]["val"]))
	  { return -1; }
	else
	  { return 1; }
//	return (strtolower($a[$order]["val"]) < strtolower($b[$order]["val"])) ? -1 : 1;
  }
function TrieValInv ($a, $b)
  { global $order;
//	return (strtolower($a[$order]["val"]) < strtolower($b[$order]["val"])) ? 1 : -1;
	if (strtolower($a[$order]["val"]) == strtolower($b[$order]["val"]))
	  { return ""; }
	else if (strtolower($a[$order]["val"]) < strtolower($b[$order]["val"]))
	  { return 1; }
	else
	  { return -1; }
  }


function TrieProduit ($a, $b)
  {
	$a["nom_produit"]=preg_replace("/<[^>]*>/i","",$a["nom_produit"]);
	$b["nom_produit"]=preg_replace("/<[^>]*>/i","",$b["nom_produit"]);

	if (strtolower($a["nom_produit"]) == strtolower($b["nom_produit"]))
	  {
		$a["nom_produit2"]=preg_replace("/<[^>]*>/i","",$a["nom_produit2"]);
		$b["nom_produit2"]=preg_replace("/<[^>]*>/i","",$b["nom_produit2"]);
		if (strtolower($a["nom_produit2"]) == strtolower($b["nom_produit2"])) { return 0; }
		return (strtolower($a["nom_produit2"]) < strtolower($b["nom_produit2"])) ? -1 : 1;
	  }
	return (strtolower($a["nom_produit"]) < strtolower($b["nom_produit"])) ? -1 : 1;
  }

function TrieProduit2 ($a, $b)
  {
	$a["nom_produit"]=preg_replace("/<[^>]*>/i","",$a["nom_produit"]);
	$b["nom_produit"]=preg_replace("/<[^>]*>/i","",$b["nom_produit"]);

	if (strtolower($a["nom_produit"]) == strtolower($b["nom_produit"]))
	  {
		$a["nom_produit2"]=preg_replace("/<[^>]*>/i","",$a["nom_produit2"]);
		$b["nom_produit2"]=preg_replace("/<[^>]*>/i","",$b["nom_produit2"]);
		if (strtolower($a["nom_produit2"]) == strtolower($b["nom_produit2"])) { return 0; }
		return (strtolower($a["nom_produit2"]) < strtolower($b["nom_produit2"])) ? 1 : -1;
	  }
	return (strtolower($a["nom_produit"]) < strtolower($b["nom_produit"])) ? 1 : -1;
  }

/*
function CalcColor($colora,$pourc,$colorb="013366")
  {
	$color1=ereg_replace('#','',$colora);
	$color2=ereg_replace('#','',$colorb);
	$rr=floor(hexdec(substr($color1, 0, 2))*($pourc/100)+hexdec(substr($color2, 0, 2))); 
	if ($rr>255) { $rr = 255; }
	if ($rr<0)   { $rr = 0; }
	
	$vv=floor(hexdec(substr($color1, 2, 2))*($pourc/100)+hexdec(substr($color2, 2, 2))); 
	if ($vv>255) { $vv = 255; }
	if ($vv<0)   { $vv = 0; }

	$bb=floor(hexdec(substr($color1, 4, 2))*($pourc/100)+hexdec(substr($color2, 4, 2))); 
	if ($bb>255) { $bb = 255; }
	if ($bb<0)   { $bb = 0; }

	$colorf=dechex($rr).dechex($vv).dechex($bb);
	return $colorf;
  }
*/

// Calcul un dégradé de couleur
function CalcColor($color,$pour,$fcolor="FFFFFF")
  {
	$color2=str_replace('#','',$color);

	$rr=hexdec(substr($color2, 0, 2))*((100-$pour)/100)+hexdec(substr($fcolor, 0, 2))*($pour/100);
	if ($rr>254) { $rr=255; }
	$rr=strtoupper(dechex($rr));
	$rr=substr("00",0,2-strlen($rr)).$rr;

	$vv=hexdec(substr($color2, 2, 2))*((100-$pour)/100)+hexdec(substr($fcolor, 2, 2))*($pour/100);
	if ($vv>254) { $vv=255; }
	$vv=strtoupper(dechex($vv));
	$vv=substr("00",0,2-strlen($vv)).$vv;

	$bb=hexdec(substr($color2, 4, 2))*((100-$pour)/100)+hexdec(substr($fcolor, 4, 2))*($pour/100);
	if ($bb>254) { $bb=255; }
	$bb=strtoupper(dechex($bb));
	$bb=substr("00",0,2-strlen($bb)).$bb;

	$color2=$rr.$vv.$bb;
	return $color2;
  }


/* **** Complète une chaine de caractères ****

	$txt	Chaine à compléter
	$nb	Nb de caractères que doit comporter la chaine
	$car	Caractère de remplissage  
*/
function CompleteTxt($txt,$nb,$car)
  {
	$n=$nb-strlen($txt);
	if ($n<0) { $n=0; }
	$ret="";
	for ($i=0;$i<$nb;$i++) { $ret.=$car; }
	return substr($ret,0,$n).$txt;
  }

function InvCompleteTxt($txt,$car)
  {
	$n=-1;
	for ($i=0;$i<strlen($txt);$i++) { if ((substr($txt,$i,1)!=$car) && ($n==-1)) { $n=$i; } }
	return substr($txt,$n,strlen($txt)-$n);
  }


/* **** Return a chain with the first letter in uppercase ****
	$txt	Chain
*/

function UpperFirstLetter($txt)
  {
  	$t=strtoupper(substr($txt,0,1)).substr($txt,1,strlen($txt)-1);
  	return $t;
  }

function FatalError($txt,$msg="")
  { global $tmpl_prg;
  	if (isset($tmpl_prg))
  	{
		$tmpl_prg->assign("icone","<IMG src=\"images/icn48_erreur.png\">");
		$tmpl_prg->assign("infos","$txt");
		$tmpl_prg->assign("corps","$msg");
		$tmpl_prg->parse("main");
		echo $tmpl_prg->text("main");
	}
	else
	{
		echo $txt."\"n";
		echo $msg."\"n";
	}
	exit;			 
  }

// Affiche une valeur au format xxx,yy
function AffMontant($val)
  {
  	global $MyOpt;
  	preg_match("/([\-0-9]*)\.?([0-9]*)/i",$val,$m);
	$ret=$m[1].",".$m[2].substr("00",0,2-strlen($m[2]));
	
	$ret=$ret." ".$MyOpt["devise"];
	
	return $ret;
  }

// Duplique une chaine de caractères
function Duplique($txt,$nb)
  {
	$ret="";
	for($i=0;$i<$nb;$i++)
	  { $ret.=$txt; }
	return $ret;
  }

// Affiche la taille d'un fichier en human reading
function CalcSize($s)
{
	if ($s<1024)
	{
		return $s." octets";
	}
	else if ($s<1024*1024)
	{
		return floor($s/1024)." ko";
	}
	else if ($s<1024*1024*1024)
	{
		return floor($s/1024/1024)." Mo";
	}
	else if ($s<1024*1024*1024*1024)
	{
		return floor($s/1024/1024/1024)." Go";
	}
}

// Affiche les 4 premières lignes d'un texte

/*
Truc mavchin<BR>chose et companie<BR>1<BR>
2<BR>3<BR>4<BR>
5&gt;<BR>6<BR>
7<BR>8<BR>
9<BR>

**

Truc mavchin<BR>chose et companie<BR>1<BR>
2<BR>3<BR>4<BR>
5&gt;<BR>6<BR>
7<BR>8<BR>
9<BR>


*/

function GetFirstLine($txt,$nb=4)
  {
  	$p=0;
  	$i=0;

	$txt=preg_replace("/<br ?\/?>/i","<br/>",$txt);
	$txt=preg_replace("/<br\/><br\/>/","<br/>",$txt);
	$txt=preg_replace("/<br\/><br\/>/","<br/>",$txt);
	$txt=preg_replace("/\r|\n/i","",$txt);

	while($i<$nb)
	  {
		$p0=strpos($txt,"<br/>",$p);
		if ($p0>0)
		  {
			$p=$p0+1;
		  }
		else
		  {
			$p=strlen($txt);
		  	$i=$nb+1;
		  }
		$i=$i+1;
	  }
	if ($p==strlen($txt))
	  { return $txt; }
	else
	  { return substr($txt,0,$p-1)."<br/>..."; }
  }

// Convertie une couleur en RGB
function ConvertColor2RGB($col,$add=0)
  {
  	$r=hexdec(substr($col,0,2));
  	$r=($r+$add>255) ? 255 : $r+$add;
  	$g=hexdec(substr($col,2,2));
  	$g=($g+$add>255) ? 255 : $g+$add;
  	$b=hexdec(substr($col,4,2));
  	$b=($b+$add>255) ? 255 : $b+$add;
  	return "rgb($r, $g, $b)";
  }

// Test si un ID correspond à l'utilisateur ou un de ses enfants
function GetMyId($id)
  { global $myuser;
  	if ($id==$myuser->uid)
  	  { return true; }

	if (GetModule("creche"))
	  {
	  	$myuser->LoadEnfants();
	  }
	
  	if (is_array($myuser->data["enfant"]))
  	  {
        	foreach($myuser->data["enfant"] as $enfant)
          	  {
          		if ($enfant["id"]==$id)
          		  { return true; }
          	  }
	  }
  	return false;
  } 

// Affiche une date
function DisplayDate($dte)
  {
	$d=time()-strtotime($dte);
	$mid=time()-strtotime(date("Y-m-d 23:59:59",time()-3600*24));

	$h=floor($d/3600);
	$m=floor(($d-$h*3600)/60);
	$s=$d-$h*3600-$m*60;

	if (($s<60) && ($m==0) && ($h==0))
	  {
			return "il y a ".$s." secondes";
	  }
	else if (($m<2) && ($h==0))
	  {
			return "il y a 1 minute";
	  }
	else if (($m<60) && ($h==0))
	  {
			return "il y a ".$m." minutes";
	  }
	else if (($h<2) && ($m==0))
	  {
			return "il y a  1 heure";
	  }
	else if (($h<2) && ($m<2))
	  {
			return "il y a  1 heure"." et 1 minute";
	  }
	else if ($h<2)
	  {
			return "il y a  1 heure"." et ".$m." minutes";
	  }
	else if (($d<$mid) && ($h<2))
	  {
			return "il y a  1 heure et ".$m." minutes";
	  }
	else if ($d<$mid)
	  {
			return "il y a ".$h." heures et ".$m." minutes";
	  }
	else if (($d<$mid+3600*34) && ($d>$mid))
	  {
			return "hier à ".sql2time($dte,"no");
	  }
	else
	  {
			return "le ".sql2date($dte,"jour")." à ".sql2time($dte,"no");
	  }	


  }


// Affiche un temps en minute en heure:minute
function AffHeures($min){
	$t=$min;
	$h=floor($t/60);
	$m=$t-$h*60;
	$m=substr("00",0,2-strlen($m)).$m;

	$ret=$h."h $m";
	return $ret;
}

// Affiche un téléphone
function AffTelephone($txt)
  {
  	$rtxt=$txt;
		$rtxt=preg_replace("/^0([1-9])([0-9]*)$/","+33\\1\\2",$txt);
		return $rtxt;
  }


// Génère le fichier des variables
function GenereVariables($tab)
{
	$ret="";
	$conffile="config/variables.inc.php";
	if (!file_exists($conffile))
		{ $ret.="Création du fichier";}


	if(is_writable($conffile))
	{
		$fd=fopen($conffile,"w");
		fwrite($fd,"<?\n");
		foreach($tab as $nom=>$d)
	  {
			if (is_array($d))
			{
					foreach($d as $var=>$dd)
					{
						if ($var=="valeur")
						{
							fwrite($fd,"\$MyOpt[\"".$nom."\"]=\"".$dd."\";\n");
						}
						else
						{
							fwrite($fd,"\$MyOpt[\"".$nom."\"][\"".$var."\"]=\"".$dd."\";\n");
						}
					}
			}
	  }
		
		fwrite($fd,"?>\n");
		fclose($fd);
		$ret.="Enregistrement effectué";
	}
	else
	{
		$ret.="Accès refusé. Fichier : ".$conffile;
	}
	return $ret;
}

?>
