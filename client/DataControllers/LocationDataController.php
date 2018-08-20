<?php
//INCLUDES-----------------------------------------
require_once($_SERVER['DOCUMENT_ROOT'].'/../server/Config/MainConfig.php');
require_once(SERVROOT.'Lib/Common.php');
require_once(SERVROOT.'Handlers/LocationHandler.php');
require_once('DataLib.php');
//-------------------------------------------------

try
{
	$command = ValidateRequest();
	ValidateToken();

	if($command == "get" && !isset($_POST['id']))
	{
		SetResult(ToJson(GetLocations()));
	}

	if($command == "get" && isset($_POST['id']))
	{
		$id = ThrowInvalid(ValidateIntParam($_POST['id']));
		$item = GetLocation($id);
	
		if(!$item)
				throw new Exception("Not Found", 404);
	
		SetResult(ToJson($item));
	}

	if($command == "create" && isset($_POST['location']))
	{
		$item = ThrowInvalid(ValidateLocation($_POST['location']));
		$newLocation = CreateLocation($item);
	
		SetResult(ToJson($newLocation));
	}

	if($command == "update" && isset($_POST['location']))
	{
		$item = ThrowInvalid(ValidateLocation($_POST['location']));

		if(!isset($item['id']))
				throw new Exception("Invalid Request", 400);
			
		$success = UpdateLocation($item);
		SetResult($success);
	}

	if($command == "delete" && isset($_POST['id']))
	{
		$id = ThrowInvalid(ValidateIntParam($_POST['id']));
		$success = DeleteLocation($id);
	
		SetResult($success);
	}

	ReturnResult();
}
catch(Exception $e)
{
	ReturnError($e->getCode(), $e->getMessage());
}

?>
