<?php
//INCLUDES-----------------------------------------
require_once($_SERVER['DOCUMENT_ROOT'].'/../server/Config/MainConfig.php');
require_once(SERVROOT.'Lib/Common.php');
require_once(SERVROOT.'Handlers/InventoryHandler.php');
require_once('DataLib.php');
//-------------------------------------------------

try
{
	$command = ValidateRequest();
	ValidateToken();

	if($command == "get" && isset($_POST['locationId']) && !isset($_POST['id']))
	{
		$id = ThrowInvalid(ValidateIntParam($_POST['locationId']));
		SetResult(ToJson(GetInventory($id)));
	}

	if($command == "get" && isset($_POST['id']))
	{
		$id = ThrowInvalid(ValidateIntParam($_POST['id']));
		$item = GetInventoryItem($id);
	
		if(!$item)
			throw new Exception("Not Found", 404);
	
		SetResult(ToJson($item));
	}

	if($command == "create" && isset($_POST['inventory']))
	{
		$item = ThrowInvalid(ValidateInventoryItem($_POST['inventory']));
		$newInventory = CreateInventoryItem($item);
	
		SetResult(ToJson($newInventory));
	}

	if($command == "update" && isset($_POST['inventory']))
	{
		$item = ThrowInvalid(ValidateInventoryItem($_POST['inventory']));
		if(!isset($item['id']))
			throw new Exception("Invalid Request", 400);
	
		$success = UpdateInventoryItem($item);
		SetResult($success);
	}

	if($command == "delete" && isset($_POST['id']))
	{
		$id = ThrowInvalid(ValidateIntParam($_POST['id']));
		$success = DeleteInventory($id);
	
		SetResult($success);	
	}
	
	ReturnResult();
}
catch(Exception $e)
{
	ReturnError($e->getCode(), $e->getMessage());
}

?>
