<?php
//INCLUDES-----------------------------------------
require_once($_SERVER['DOCUMENT_ROOT'].'/../richard-api/Config/MainConfig.php');
require_once(SERVROOT.'Lib/Common.php');
require_once(SERVROOT.'Lib/DataLib.php');
require_once(SERVROOT.'Handlers/PublicationsHandler.php');
//-------------------------------------------------

try
{
	$command = ValidateRequest();

	if($command == "get" && !isset($_POST['id']) && isset($_POST['param']))
	{
		$type = SanitizeString($_POST['param']); 
		
		if(isset($_POST['page']) && isset($_POST['size']))
		{
			$page = ValidateIntParam($_POST['page']);
			$size = ValidateIntParam($_POST['size']);
			if($page && $size)
				SetResult(ToJson(GetPublications($type, $page, $size)));
			else 
				SetResult(ToJson(GetPublications($type)));
		}
		else
			SetResult(ToJson(GetPublications($type)));
	}

	if($command == "get" && isset($_POST['id']))
	{
		$id = ThrowInvalid(ValidateIntParam($_POST['id']));
		$item = GetPublications($id);
	
		if(!$item)
			throw new Exception("Not Found", 404);
		
		SetResult(ToJson($item));
	}
		
	ReturnResult();
}
catch(Exception $e)
{
	ReturnError($e->getCode(), $e->getMessage());
}

?>
