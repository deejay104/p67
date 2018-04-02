<?

class mysql_class{
	# Constructor
	function __construct($user="phpuser",$pass="php",$host="localhost",$db="test",$port=3306){
		$this->user=$user;
		$this->host=$host;
		$this->db=$db;
		$this->show=true;

		//$this->class = new mysqli($this->host,$this->user,$pass,$this->db,$port);

		$this->id=@mysqli_connect($this->host,$this->user,$pass,$db,$port);

		// mysqli_set_charset($this->id,'iso-8859-1');

		if (mysqli_connect_errno()) {
	    $this->mysql_ErrorMsg("Échec de la connexion : ".mysqli_connect_error());
		}		

	}

	# Close the connection
	function closedb(){
		$this->result=@mysqli_close($this->id);
	}


///////////////////////////////////////////	

	# Delete rows from a table
	function Delete($query){
		$this->result=@mysqli_query($this->id,$query) or
			$this->mysql_ErrorMsg("Unable to perform Delete: $query");
		$this->a_rows=@mysqli_affected_rows($this->result);
	}

	# Update elements in database
	function Update($query){
		$this->result=mysqli_query($this->id,$query) or
			$this->mysql_ErrorMsg("Unable to perform update: $query",$this->show);
		return $this->a_rows=@mysqli_affected_rows($this->id);

	}
	
	# Insert row into a table
	function Insert($query){
		$this->result=@mysqli_query($this->id,$query) or
			$this->mysql_ErrorMsg("Unable to perform insert: $query",$this->show);
		$this->a_rows=@mysqli_affected_rows($this->id);
		return @mysqli_insert_id($this->id);
	}

	# Get last insert id from an auto_incremented field
	function InsertID(){
		$this->result=@mysqli_insert_id($this->id) or
			$this->mysql_ErrorMsg("Cannot retrieve auto_increment value: $this->id");
		return($this->result);
	}

	# Multiple row return query - Use GetRow function to loop through
	function Query($query){
		$this->result=@mysqli_query($this->id,$query) or
			$this->mysql_ErrorMsg("Unable to perform query: $query",$this->show);
		$this->query=$query;
		$this->rows=@mysqli_num_rows($this->result);
	}

	# Get a row of data from a multiple row query
	function GetRow($row)
	{
		@mysqli_data_seek($this->result,$row) or
			$this->mysql_ErrorMsg("Unable to seek data row: $row for this query $this->query");
		$this->data=@mysqli_fetch_array($this->result) or
			$this->mysql_ErrorMsg("Unable to fetch row: $row",$this->show);
	}
	
	# Single row return query
	function QueryRow($query)
	{
		$this->result=@mysqli_query($this->id,$query) or
			$this->mysql_ErrorMsg("Unable to perform query row: $query",$this->show);
		$this->rows=@mysqli_num_rows($this->result);

		if ($this->rows)
		{
			$this->data=@mysqli_fetch_array($this->result) or
				$this->mysql_ErrorMsg("Unable to fetch data from query row: $query",$this->show);
			$this->numfields=mysqli_num_fields($this->result);
			return($this->data);
		} else {
			return(-1);
		}

	}

	# Single element return query
	function QueryItem($query)
	{
		$this->result=@mysqli_query($this->id,$query) or
			$this->mysql_ErrorMsg("Unable to perform query item: $query");
		$this->rows=@mysqli_num_rows($this->result);
		$this->data=@mysqli_fetch_array($this->result) or
			$this->mysql_ErrorMsg("Unable to fetch data from query item: $query");
		return($this->data[0]);
	}

	# Use if checking for empty query result returns 0 if empty,
	# and 1 if there is at least one result
	function Exists($query)
	{
		$this->result=@mysqli_query($this->id,$query);
		if(@mysqli_num_rows($this->result)) return 1;
		else return 0;
	}

	# Add/Update a line in the table
	function Edit($class,$tab,$id,$val,$comment="")
	{ global $MyOpt,$uid;
		$v="";
		$c="";
		$s="";
		foreach($val as $f=>$d)
		{
			$v.=$s.$f."='".$d."'";
			$c.=$s.$f."->".$d;
			$s=",";
		}

		$res["id"]=0;
		if ($id>0)
		{
			$query="SELECT id FROM ".$tab." WHERE id='".$id."'";
			$res=$this->QueryRow($query);
		}

		if ($res["id"]>0)
		{
			$query="UPDATE ".$tab." SET ".$v." WHERE id='".$id."'";
			$ret=$this->Update($query);

			$query ="INSERT INTO ".$MyOpt["tbl"]."_historique (`class` ,`table` ,`idtable` ,`uid_maj` ,`dte_maj` ,`type` ,`comment`) ";
			$query.="VALUES ('".$class."', '".$tab."', '$id', '$uid', '".now()."', 'MOD', '".(($comment!="") ? $comment : $c)."')";
			$this->Insert($query);
		}
		else
		{
			$query="INSERT INTO ".$tab." SET ".$v;
			$ret=$this->Insert($query);

			$query ="INSERT INTO ".$MyOpt["tbl"]."_historique (`class` ,`table` ,`idtable` ,`uid_maj` ,`dte_maj` ,`type` ,`comment`) ";
			$query.="VALUES ('".$class."', '".$tab."', '$ret', '$uid', '".now()."', 'ADD', '".(($comment!="") ? $comment : $c)."')";
			$this->Insert($query);
		}
		return $ret;
	}

	# MySQL error message function
	function mysql_ErrorMsg($msg,$show=true){
		global $resume;
		# Get out of html constraints so we can see the message
		$text="</ul></ul></ul></dl></dl></dl></ol></ol></ol>\n";
		$text="</table></table></table></script></script></script>\n";
	
		# Display the error message
		$text ="<font color=\"#ff0000\">Error: $msg :";
		$text .= "</font><BR>\n";
		if ($show)
		{
			echo "$text";
	
			if ($resume==false)
			{
				exit;
			}
		}
		else
		{
			return "NOK";
		}
	}


} # End of class


?>
