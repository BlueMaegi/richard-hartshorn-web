<?php
//INCLUDES-----------------------------------------
require_once($_SERVER['DOCUMENT_ROOT'].'/../server/Config/MainConfig.php');
require_once(SERVROOT.'Lib/Common.php');
require_once(SERVROOT.'Handlers/ProductHandler.php');
require_once(SERVROOT.'Handlers/EasyPostHandler.php');
require_once('DataLib.php');
//-------------------------------------------------

try
{
	$command = ValidateRequest();

	if($command == "get" && !isset($_POST['id']))
	{
		SetResult(ToJson(GetProducts()));
	}

	if($command == "get" && isset($_POST['id']))
	{
		$id = ThrowInvalid(ValidateIntParam($_POST['id']));
		$item = GetProduct($id);
	
		if(!$item)
			throw new Exception("Not Found", 404);
		
		SetResult(ToJson($item));
	}

	if($command == "create" && isset($_POST['product']) && isset($_POST['parcel']))
	{
		ValidateToken();
		$item = ThrowInvalid(ValidateProduct($_POST['product']));
		$parcel = ThrowInvalid(ValidateParcel($_POST['parcel']));

		$epParcel = CreateParcel($parcel);
		$item['epParcelId'] = $epParcel['id'];

		$newProduct = CreateProduct($item);
		SetResult(ToJson($newProduct));	
	}

	if($command == "update" && isset($_POST['product']))
	{
		ValidateToken();
		$item = ThrowInvalid(ValidateProduct($_POST['product']));
		if(!isset($item['id']))
			throw new Exception("Invalid Request", 400);
	
		$success = UpdateProduct($item);
		SetResult($success);
	}

	if($command == "delete" && isset($_POST['id']))
	{
		ValidateToken();
		$id = ThrowInvalid(ValidateIntParam($_POST['id']));
		
		$success = DeleteProduct($id);
		SetResult($success);
	}
	
	ReturnResult();
}
catch(Exception $e)
{
	ReturnError($e->getCode(), $e->getMessage());
}

?>
