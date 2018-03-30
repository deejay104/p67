<?
	$q="ALTER TABLE `".$MyOpt["tbl"]."_compte` CHANGE `compte` `compte` VARCHAR(10) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL;";
	$sql->Update($q);

?>