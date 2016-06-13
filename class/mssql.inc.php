<?
/*
This is a PHP class definition for mssql database server connections.
Last Modified on 2/9/2001 1:59PM Justin Koivisto [Koivi Media]
*/
// modifié le 13/2/01 SP : intégration des paramètres connection data
// modif le 14/2/01 SP : ajout de la fonction replace

$mssql_CLASS_INC=true;

class mssql_class{
	# Constructor
	function __construct($user="phpuser",$pass="php",$host="localhost",$db="test"){
		$this->user=$user;
		$this->host=$host;
		$this->id=@mssql_connect($this->host,$this->user,$pass) or
		mssql_ErrorMsg("Unable to connect to mssql server: [$this->host][$this->user]");
		//if (!$db) $db="sdd"; 
		$this->db=$db;
		if ($db!="") { @mssql_select_db($db,$this->id) or
			mssql_ErrorMsg("Unable to select database: $db"); }
	}

	# Close the connection
	function closedb(){
		$this->result=@mssql_close($this->id) or
			mssql_ErrorMsg("Unable to close connection");

	}


	# Get a list of the available databases on the current server
	function GetDatabases(){
		$this->result=@mssql_list_dbs() or
			mssql_ErrorMsg("Unable to find a database on server: $this->host");
		$i=0;
		while($i < mssql_num_rows($this->result)){
			$db_names[$i]=mssql_tablename($this->result,$i);
			$i++;
		}
		return($db_names);
	}

	# Create a database on the server
	function CreateDB($database){
		$this->result= @mssql_create_db($database) or
			mssql_ErrorMsg("Unable to create database: $database");
		$this->a_rows=@mssql_affected_rows($this->result);
	}

	# Drop a database
	function DropDB($database){
		$this->result=@mssql_drop_db($database) or
			mssql_ErrorMsg("Unable to drop database: $database");
		$this->a_rows=@mssql_affected_rows($this->result);
	}

	# Copy a database from this server (even to another)
	# Returns TRUE or FALSE for success or failure
	function CopyDB($database,$dest_db='',$dest_host="localhost",$dest_user="phpuser",$dest_pass="php"){
		# Until I get time to figure this out with using the php
		# functions for mssql, you'll need to know these for the system
		# 1/28/2001 6:54PM
		$mssqlPATH="/usr/local/bin/";
		$mssqlDUMP= $mssqlPATH . "mssqldump";
		$mssql= $mssqlPATH . "mssql";

		if($dest_db='') $dest_db=$database;
		$db_checker=SelectDB($database);
		if(!$db_checker)
			mssql_ErrorMsg("Database $database does not exist.");

		$dest = new mssql_class($dest_user, $dest_pass, $dest_host);
		$db_checker = $dest->SelectDB($dest_db);
		if(!$db_checker)
			$dest->Create_DB($dest_db);

		# Now that connection has been established, we can do the copying
		$system_command= "$mssqlDUMP -u$this->user -p$this->pass --opt $DATABASE | $mssql --host=$dest_host -u$dest_user -p$dest_pass -C $dest_db";
		system($system_command,$system_result);
		if($system_result)
			return FALSE;
		else
			return TRUE;
	}

	//SP: Fonction inutile ici (paramètres de connection data)
	# Select or change databases
	function SelectDB($db){
		$this->db=$db;
		@mssql_select_db($db,$this->id) or
			mssql_ErrorMsg("Unable to select database: $db");
	}
	
	
	# Get a list of the available tables in this database
	function GetTableList(){
		$this->result=@mssql_list_tables($this->db,$this->id) or
			mssql_ErrorMsg("Unable to find any tables in database: $this->db");
		$i=0;
		while($i < mssql_num_rows($this->result)){
			$tb_names[$i]=mssql_tablename($this->result,$i);
			$i++;
		}
		return($tb_names);
	}

	# Get a list of the fields names in the given table of the current database
	function GetFieldList($tbl_name){
		$this->result=@mssql_list_fields($this->db,$tbl_name,$this->id);
		$i=0;
		while($i < mssql_num_fields($this->result)){
			$fd_names[$i]=mssql_field_name($this->result,$i);
			$i++;
		}
		return($fd_names);
	}

	# Delete rows from a table
	function Delete($query){
		$this->result=@mssql_query($query,$this->id) or
			mssql_ErrorMsg("Unable to perform Delete: $query");
		$this->a_rows=@mssql_affected_rows($this->result);
	}

	# Update elements in database
	function Update($query){
		$this->result=mssql_query($query,$this->id) or
			mssql_ErrorMsg("Unable to perform update: $query");
		return $this->a_rows=@mssql_affected_rows($this->id);

	}

	
	
	
///////////////////////////////////////////	
	
	# Insert row into a table
	function Insert($query){
		$this->result=@mssql_query($query,$this->id) or
			mssql_ErrorMsg("Unable to perform insert: $query");
		$this->a_rows=@mssql_affected_rows($this->id);
		return @mssql_insert_id();
	}

	# Get last insert id from an auto_incremented field
	function InsertID(){
		$this->result=@mssql_insert_id($this->id) or
			mssql_ErrorMsg("Cannot retrieve auto_increment value: $this->id");
		return($this->result);
	}

	# Multiple row return query - Use GetRow function to loop through
	function Query($query){
		$this->result=@mssql_query($query,$this->id) or
			mssql_ErrorMsg("Unable to perform query: $query");
		$this->query=$query;
		$this->rows=@mssql_num_rows($this->result);
	}

	# LP: return query field name 
	function GetFieldName(){
	  $i=0;
//		echo $this->result;
	  while ($meta=mssql_fetch_field($this->result)) {
//		print_r($meta);
		  $name[$i++]=$meta->name;
		}
		// Error handler to write
		return($name);
	}
	
	
	# Get a row of data from a multiple row query
	function GetRow($row){
		@mssql_data_seek($this->result,$row) or
			mssql_ErrorMsg("Unable to seek data row: $row for this query $this->query");
		$this->data=@mssql_fetch_array($this->result) or
			mssql_ErrorMsg("Unable to fetch row: $row");
	}
	
	# Get a row of data from a multiple row query
	function GetR($row){
		@mssql_data_seek($this->result,$row) or
			mssql_ErrorMsg("Unable to seek data row: $row for this query $this->query");
		$this->data=@mssql_fetch_row($this->result) or
			mssql_ErrorMsg("Unable to fetch row: $row");
	}	

	# Single row return query
	function QueryRow($query){
		$this->result=mssql_query($query,$this->id) or
			mssql_ErrorMsg("Unable to perform query row: $query");
		$this->rows=@mssql_num_rows($this->result);
// Correction LP		
		if ($this->rows) {
		   $this->data=@mssql_fetch_array($this->result) or
			   mssql_ErrorMsg("Unable to fetch data from query row: $query");
 		   $this->numfields=mssql_num_fields($this->result);
		   return($this->data);
		} else {
		   return(-1);
		}
	}

	# Single element return query
	function QueryItem($query){
		$this->result=@mssql_query($query,$this->id) or
			mssql_ErrorMsg("Unable to perform query item: $query");
		$this->rows=@mssql_num_rows($this->result);
		$this->data=@mssql_fetch_array($this->result) or
			mssql_ErrorMsg("Unable to fetch data from query item: $query");
		return($this->data[0]);
	}

	# Use if checking for empty query result returns 0 if empty,
	# and 1 if there is at least one result
	function Exists($query){
		$this->result=@mssql_query($query,$this->id);
		if(@mssql_num_rows($this->result)) return 1;
		else return 0;
	}
} # End of class

# mssql error message function
function mssql_ErrorMsg($msg){
	# Get out of html constraints so we can see the message
	$text="</ul></ul></ul></dl></dl></dl></ol></ol></ol>\n";
	$text="</table></table></table></script></script></script>\n";

	# Display the error message
	$text ="<font color=\"#ff0000\" size=+2><p>Error: $msg :";
	//$text .= mssql_error();
	$text .= "</font>\n";
	echo $text;

}

?>
