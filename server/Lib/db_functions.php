<?php
//INCLUDES-----------------------------------------------------------------
require_once($_SERVER['DOCUMENT_ROOT'].'/../server/Config/MainConfig.php');
//-------------------------------------------------------------------------
$DB = "";
//connect_to_db(); //automatically attempt to connect on inclusion


//Attempt a global connection to the DB
 function connect_to_db()
 {
	global $DB;
	$DB = new mysqli("localhost:8889", DB_UNAME, DB_PWD, DB_NAME);

	if (mysqli_connect_errno($DB))
	{
	  //echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
 }
 
 //closes the database connection and resets the global variable
 //TODO: CALL THIS IN A PLACE THAT MAKES SENSE, like Logout or something
 function close_db()
 {
	global $DB;
	mysqli_close($DB);
	$DB = "";
 }
 
 //Wrapper for handling sql queries
 //$string = a query string, in the form of "Select x1, x2 from y where z = ?"
 //$paramIDs = a string of the parameter types, in the form "iisss" 
 //$params = an array of parameters to take the place of the "?" in the query
 //Returns a 2-D array in the format of array[rowNumber][columnName]
 //Returns false if the query string is bad or no results were found
 function do_query($string,$paramIDs,$params)
 {
	global $DB;
	if($stmt = $DB->prepare($string)) //check if the query string is valid
	{
		if(strlen($paramIDs) > 0) //if we have parameters to bind
		{	
			$paramArr[] = $paramIDs;
			for($i = 0; $i < strlen($paramIDs); $i++)
			{
				$bind_name = 'bind' . $i; //force array values to be references
				$$bind_name = sanitize_for_HTML($params[$i]); //NOTE: sanitization for Avery Manufacturing only. May not apply to other systems
				$paramArr[] = &$$bind_name;
			}
			call_user_func_array(array($stmt,'bind_param'),$paramArr); //pass in each array value as a parameter
		}
		$stmt->execute();
		
		$meta = $stmt->result_metadata();
		$returnArr = "";
		$count = 0;
		if($meta)
		{
			while ($field = $meta->fetch_field())
			{
				$var = strtolower($field->name); 
            	$$var = null; 
            	$parameters[strtolower($field->name)] = &$$var; 
			}
			call_user_func_array(array($stmt, 'bind_result'), $parameters);
		
			if(!$stmt->error)
			{
				while($stmt->fetch())
				{
					$hhh = "";
					foreach($parameters as $k => $v)
                		$hhh[$k] = $v;
                	$returnArr[] = $hhh;
					$count++;
				}
			}
		}
		
		$stmt->close();
		if($count <= 0) //return false if nothing was found. An empty array is a waste of time
			return $DB->insert_id;
		return $returnArr;
	}

	return false;
 }
 
 //Just a test function. Do not use in production
 function test_db()
 {
	global $DB;
	$stmt = $DB->prepare("SELECT first, last FROM user");
	$stmt->execute();
	$stmt->bind_result($first);
	while($stmt->fetch())
		echo "<p>".$first."</p>";
	$stmt->close();
 }

function mysql_to_unix_time($mysqlTime)
{
	$date = date_create_from_format('Y-m-d H:i:s',$mysqlTime);
	return date_timestamp_get($date);
}

function unix_to_mysql_time($unixTime)
{
	return date("Y-m-d H:i:s", $unixTime);
}
 
function sanitize_string($uncleanString)
{
	$uncleanString = substr($uncleanString, 0, 255);
	$uncleanString = str_replace("<br>", "&br&", $uncleanString);
	$uncleanString = strip_tags($uncleanString);
	$cleanString = preg_replace('/[^a-zA-Z0-9_&?!., ]/', '', $uncleanString);
	
	return $cleanString;
}

function sanitize_for_HTML($uncleanString)
{
	$uncleanString = strip_tags($uncleanString);
	$uncleanString = htmlentities($uncleanString);
	$cleanString = preg_replace('/[&<>"=]/','', $uncleanString);
	return $cleanString;
}
?>
