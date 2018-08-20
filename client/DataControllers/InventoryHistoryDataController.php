<?php
//INCLUDES-----------------------------------------
require_once($_SERVER['DOCUMENT_ROOT'].'/../server/Config/MainConfig.php');
require_once(SERVROOT.'Lib/Common.php');
require_once(SERVROOT.'Handlers/InventoryHistoryHandler.php');
require_once('DataLib.php');
//-------------------------------------------------

try
{
	$command = ValidateRequest();
	ValidateToken();

	if($command == "get" && !isset($_POST['id']) && isset($_POST['inventoryId']))
	{
		$id = ThrowInvalid(ValidateIntParam($_POST['inventoryId']));
		SetResult(ToJson(GetHistories($id)));
	}

	if($command == "get" && isset($_POST['id']))
	{
		$id = ThrowInvalid(ValidateIntParam($_POST['id']));
		$item = GetInventoryHistory($id);
	
		if(!$item)
			throw new Exception("Not Found", 404);
	
		SetResult(ToJson($item));
	}

	if($command == "create" && isset($_POST['inventoryHistory']))
	{
		$item = ThrowInvalid(ValidateHistory($_POST['inventoryHistory']));
		$newHistory = CreateHistory($item);
	
		SetResult(ToJson($newHistory));
	}

	if($command == "update" && isset($_POST['inventoryHistory']))
	{
		$item = ThrowInvalid(ValidateHistory($_POST['inventoryHistory']));

		if(!isset($item['id']))
			throw new Exception("Invalid Request", 400);

		$success = UpdateHistory($item);
		SetResult($success);
	}

	if($command == "delete" && isset($_POST['id']))
	{
		$id = ThrowInvalid(ValidateIntParam($_POST['id']));
		$success = DeleteHistory($id);
	
		SetResult($success);
	}
	
	ReturnResult();
}
catch(Exception $e)
{
	ReturnError($e->getCode(), $e->getMessage());
}
?>
