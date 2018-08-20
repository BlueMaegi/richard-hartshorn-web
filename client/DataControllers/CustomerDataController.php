<?php
//INCLUDES-----------------------------------------
require_once($_SERVER['DOCUMENT_ROOT'].'/../server/Config/MainConfig.php');
require_once(SERVROOT.'Lib/Common.php');
require_once(SERVROOT.'Handlers/CustomerHandler.php');
require_once('DataLib.php');
//-------------------------------------------------

try
{

$command = ValidateRequest();

	if($command == "get" && !isset($_POST['id']))
	{
		SetResult(ToJson(GetCustomers()));
	}

	if($command == "get" && isset($_POST['id']))
	{
		$id = ThrowInvalid(ValidateIntParam($_POST['id']));
		$item = GetCustomer($id);
	
		if(!$item)
			throw new Exception("Not Found", 404);
		
		SetResult(ToJson($item));
	}

	if($command == "create" && isset($_POST['customer']))
	{
		$item = ThrowInvalid(ValidateCustomer($_POST['customer']));
		$newCustomer = CreateCustomer($item);
	
		SetResult(ToJson($newCustomer));
	}

	if($command == "update" && isset($_POST['customer']))
	{
		ValidateToken();
		$item = ThrowInvalid(ValidateCustomer($_POST['customer']));
		if(!isset($item['id']))
				throw new Exception("Invalid Request", 400);
	
		$success = UpdateCustomer($item);
		SetResult($success);
	}

	if($command == "delete" && isset($_POST['id']))
	{
		ValidateToken();
		$id = ThrowInvalid(ValidateIntParam($_POST['id']));
	
		$success = DeleteCustomer($id);
		SetResult($success);
	}
	
	ReturnResult();
}
catch(Exception $e)
{
	ReturnError($e->getCode(), $e->getMessage());
}

?>
