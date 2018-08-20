<?php
//INCLUDES-----------------------------------------
require_once($_SERVER['DOCUMENT_ROOT'].'/../server/Config/MainConfig.php');
require_once(SERVROOT.'Lib/Common.php');
require_once(SERVROOT.'Handlers/OrderItemHandler.php');
require_once('DataLib.php');
//-------------------------------------------------
try
{

	$command = ValidateRequest();

	if($command == "get" && isset($_POST['orderId']) && !isset($_POST['productId']))
	{
		ValidateToken();
		$id = ThrowInvalid(ValidateIntParam($_POST['orderId']));
		SetResult(ToJson(GetOrderItems($id)));
	}

	if($command == "get" && isset($_POST['orderId']) && isset($_POST['productId']))
	{
		ValidateToken();
		$orderId = ThrowInvalid(ValidateIntParam($_POST['orderId']));
		$productId = ThrowInvalid(ValidateIntParam($_POST['productId']));
		$item = GetOrderItem($orderId, $productId);
	
		if(!$item)
			throw new Exception("Not Found", 404);
	
		SetResult(ToJson($item));
	}

	if($command == "create" && isset($_POST['orderItem']))
	{
		$item = ThrowInvalid(ValidateOrderItem($_POST['orderItem']));
		$newOrder = CreateOrderItem($item);
	
		SetResult(ToJson($newOrder));
	}

	if($command == "update" && isset($_POST['orderItem']))
	{
		$item = ThrowInvalid(ValidateOrderItem($_POST['orderItem']));
		$success = UpdateOrderItem($item);
	
		SetResult($success);
	}

	if($command == "delete" && isset($_POST['orderId']) && isset($_POST['productId']))
	{
		ValidateToken();
		$orderId = ThrowInvalid(ValidateIntParam($_POST['orderId']));
		$productId = ThrowInvalid(ValidateIntParam($_POST['productId']));
		$success = DeleteOrderItem($orderId, $productId);
	
		SetResult($success);
	}

	ReturnResult();
}
catch(Exception $e)
{
	ReturnError($e->getCode(), $e->getMessage());
}

?>
