<?php
//INCLUDES-----------------------------------------
require_once($_SERVER['DOCUMENT_ROOT'].'/../server/Config/MainConfig.php');
require_once(SERVROOT.'Lib/Common.php');
require_once(SERVROOT.'Handlers/UserHandler.php');
//-------------------------------------------------

$errors = "";
$result = "";
$baseRestFunctions = ["get", "create", "update", "delete"];
$userRestFunctions = ["login", "logout", "refresh"];
$purchaseRestFunctions = ["shipping", "rates", "card", "refund", "complete", "backorder"];

function ValidateRequest($type = '')
{
	global $baseRestFunctions, $userRestFunctions, $purchaseRestFunctions;

	$restFunctions = $baseRestFunctions;
	if($type == 'user')
		$restFunctions = $userRestFunctions;
	if($type == 'purchase')
		$restFunctions = $purchaseRestFunctions;
		
	if(!isset($_POST['func']))
		throw new Exception("Invalid Request", 400);

	$command = strtolower(SanitizeString($_POST['func'], 9));
	
	if(!in_array($command, $restFunctions))
		throw new Exception("Invalid Request", 400);
	
	return $command;
}

function ThrowInvalid($data)
{
	if($data == false || is_null($data) || $data === "")	
		throw new Exception("Invalid Request", 400);
	
	return $data;
}

function ValidateToken()
{
	if(!isset($_POST['authId']) || !isset($_POST['auth']))
		throw new Exception("Invalid Request", 400);

	$id = ValidateIntParam($_POST['authId']);
	$token = SanitizeString($_POST['auth'], 150); 
	
	if(!ExternalCheckToken($id, $token))
		throw new Exception("Unauthorized", 403);

	return true;
}

function ToJson($resultSet)
{
	$jsonResult = '{"data":[';

	if(is_array($resultSet))
	{
		foreach($resultSet as $obj)
		{
			$jsonResult .= "{";
		
			foreach($obj as $key => $value)
			{
				$jsonResult .= '"'.$key.'":"'.$value.'",';
			}
		
			$jsonResult = substr($jsonResult, 0, - 1);
			$jsonResult .= "},";
		}
		$jsonResult = substr($jsonResult, 0, - 1);
	}
	
	$jsonResult .= ']}';
	return $jsonResult;
}

function ReturnError($code, $message)
{
	if($code != 400 && $code != 403 && $code != 404)
		$code = 500;
		
	header($_SERVER["SERVER_PROTOCOL"]." ".$code." ".$message, true, $code);
}

function SetResult($data)
{
	global $results;
	$results = $data;
}

function ReturnResult()
{
	global $results;
	echo $results;
}

?>
