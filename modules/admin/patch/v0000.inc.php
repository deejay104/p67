<?
	require ("version.php");

	$query="CREATE TABLE IF NOT EXISTS `".$MyOpt["tbl"]."_config` (`param` VARCHAR( 20 ) NOT NULL ,`value` VARCHAR( 20 ) NOT NULL) ENGINE = MYISAM ";
	$res = $sql->Update($query);


	$q=array();
	$q[]="INSERT INTO `".$MyOpt["tbl"]."_utilisateurs` SET id=1, nom='admin', prenom='admin', initiales='adm', password='21232f297a57a5a743894a0e4a801fc3', idcpt=1, notification='oui', droits='SYS', actif='oui', virtuel='non', type='membre', uid_maj=1, dte_maj=NOW()";
	$q[]="INSERT INTO `".$MyOpt["tbl"]."_utilisateurs` SET id=2, nom='system', prenom='', initiales='', password='', idcpt=2, notification='non', droits='SYS', actif='oui', virtuel='oui', type='membre', uid_maj=1, dte_maj=NOW()";
	$q[]="INSERT INTO `".$MyOpt["tbl"]."_utilisateurs` SET id=3, nom='banque', prenom='', initiales='', password='', idcpt=3, notification='non', droits='', actif='oui', virtuel='oui', type='membre', uid_maj=1, dte_maj=NOW()";
	$q[]="INSERT INTO `".$MyOpt["tbl"]."_utilisateurs` SET id=4, nom='club', prenom='', initiales='', password='', idcpt=4, notification='non', droits='', actif='oui', virtuel='oui', type='membre', uid_maj=1, dte_maj=NOW()";

	$q[]="INSERT INTO `".$MyOpt["tbl"]."_groupe` SET groupe='ADM', description='Administrateurs'";
	$q[]="INSERT INTO `".$MyOpt["tbl"]."_groupe` SET groupe='ALL', description='Tout le monde'";
	
	$q[]="INSERT INTO `".$MyOpt["tbl"]."_droits` SET groupe='SYS', uid=1, uid_creat=1, dte_creat=NOW()";
	$q[]="INSERT INTO `".$MyOpt["tbl"]."_droits` SET groupe='SYS', uid=2, uid_creat=1, dte_creat=NOW()";

	$q[]="INSERT INTO `".$MyOpt["tbl"]."_ressources` SET nom='Default', immatriculation='F-XXXX', actif='oui', typehora='min'";

	$q[]="DELETE FROM `".$MyOpt["tbl"]."_cron`";

	$q[]="INSERT INTO `".$MyOpt["tbl"]."_cron` SET description='Notification des échéances', module='comptabilite', script='echeances', schedule='10080', actif='non'";
	$q[]="INSERT INTO `".$MyOpt["tbl"]."_cron` SET description='Notification de decouvert', module='comptabilite', script='decouvert', schedule='10080', actif='non'";
	$q[]="INSERT INTO `".$MyOpt["tbl"]."_cron` SET description='Mail d\'actualités', module='actualites', script='sendmail', schedule='5', actif='non'";

  	foreach($q as $i=>$query)
	{
		$sql->Update(utf8_decode($query));
	}

?>