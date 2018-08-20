<?php
//INCLUDES-----------------------------------------
require_once($_SERVER['DOCUMENT_ROOT'].'/../server/Config/MainConfig.php');
require_once(SERVROOT.'Lib/Common.php');
require_once(SERVROOT.'Handlers/ShipmentHandler.php');
require_once('DataLib.php');
//-------------------------------------------------

try
{
	$command = ValidateRequest();

	if($command == "get" && !isset($_POST['id']) && !isset($_POST['orderId']) && !isset($_POST['code']))
	{
		ValidateToken();
		SetResult(ToJson(GetShipments()));
	}

	if($command == "get" && isset($_POST['id']))
	{
		ValidateToken();
		$id = ThrowInvalid(ValidateIntParam($_POST['id']));
		$item = GetShipment($id);
	
		if(!$item)
			throw new Exception("Not Found", 404);
	
		SetResult(ToJson($item));
	}
	
	if($command == "get" && isset($_POST['orderId']))
	{
		ValidateToken();
		$id = ThrowInvalid(ValidateIntParam($_POST['orderId']));
		$item = GetOrderShipments($id);
	
		if(!$item)
			throw new Exception("Not Found", 404);
	
		SetResult(ToJson($item));
	}
	
	if($command == "get" && isset($_POST['code']))
	{
		$code = SanitizeString($_POST['code'], 9);
		$item = GetOrderShipmentsByCode($code);
	
		if(!$item)
			throw new Exception("Not Found", 404);
	
		SetResult(ToJson($item));
	}

	if($command == "create" && isset($_POST['shipment']))
	{
		$item = ThrowInvalid(ValidateShipment($_POST['shipment']));
		$newShipment = CreateShipment($item);
	
		SetResult(ToJson($newShipment));
	}

	if($command == "update" && isset($_POST['shipment']))
	{
		$item = ThrowInvalid(ValidateShipment($_POST['shipment']));
		if(!isset($item['id']))
			throw new Exception("Invalid Request", 400);
	
		$success = UpdateShipment($item);
		SetResult($success);
	}

	if($command == "delete" && isset($_POST['id']))
	{
		ValidateToken();
		$id = ThrowInvalid(ValidateIntParam($_POST['id']));
		$success = DeleteShipment($id);
	
		SetResult($success);	
	}
	
	ReturnResult();
}
catch(Exception $e)
{
	ReturnError($e->getCode(), $e->getMessage());
}

?>
