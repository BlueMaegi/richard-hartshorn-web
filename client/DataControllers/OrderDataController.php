<?php
//INCLUDES-----------------------------------------
require_once($_SERVER['DOCUMENT_ROOT'].'/../server/Config/MainConfig.php');
require_once(SERVROOT.'Lib/Common.php');
require_once(SERVROOT.'Handlers/OrderHandler.php');
require_once('DataLib.php');
//-------------------------------------------------

try
{
	$command = ValidateRequest();

	if($command == "get" && !isset($_POST['id']) && !isset($_POST['groupBy']) && !isset($_POST['code']))
	{
		ValidateToken();
		SetResult(ToJson(GetOrders()));
	}

	if($command == "get" && isset($_POST['id']))
	{
		$id = ThrowInvalid(ValidateIntParam($_POST['id']));
		$item = GetOrder($id);
	
		if(!$item)
			throw new Exception("Not Found", 404);
	
		SetResult(ToJson($item));
	}
	
	if($command == "get" && isset($_POST['code']))
	{
		$code = SanitizeString($_POST['code'], 12);
		$item = GetOrderByCode($code);
	
		if(!$item)
			throw new Exception("Not Found", 404);
	
		SetResult(ToJson($item));
	}
	
	if($command == "get" && isset($_POST['groupBy']) && isset($_POST['startDate']) && isset($_POST['endDate']))
	{
		$groupBy = SanitizeString($_POST['groupBy'], 6);
		$startDate = SanitizeString($_POST['startDate'], 11);
		$endDate = SanitizeString($_POST['endDate'], 11);
		
		$summary = GetOrderSummary($startDate, $endDate, $groupBy);
	
		if(!$summary)
			throw new Exception("Not Found", 404);
	
		SetResult(ToJson($summary));
	}

	if($command == "create" && isset($_POST['order']))
	{
		$item = ThrowInvalid(ValidateOrder($_POST['order']));
		$newOrder = CreateOrder($item);
	
		SetResult(ToJson($newOrder));
	}

	if($command == "update" && isset($_POST['order']))
	{
		$item = ThrowInvalid(ValidateOrder($_POST['order']));
		if(!isset($item['id']))
			throw new Exception("Invalid Request", 400);
		
		$success = UpdateOrder($item);
		SetResult($success);
	}

	if($command == "delete" && isset($_POST['id']))
	{
		ValidateToken();
		$id = ThrowInvalid(ValidateIntParam($_POST['id']));
	
		$success = DeleteOrder($id);
		SetResult($success);
	}
	
	ReturnResult();
}
catch(Exception $e)
{
	ReturnError($e->getCode(), $e->getMessage());
}

?>
