<?php
//INCLUDES-----------------------------------------
require_once($_SERVER['DOCUMENT_ROOT'].'/../richard-api/Config/MainConfig.php');
require_once(SERVROOT.'Lib/Common.php');
require_once(SERVROOT.'Lib/DataLib.php');
require_once(SERVROOT.'Handlers/ProjectsHandler.php');
//-------------------------------------------------

try
{
	$command = ValidateRequest();

	if($command == "get" && !isset($_POST['id']))
	{
		if(isset($_POST['page']) && isset($_POST['size']))
		{
			$page = ValidateIntParam($_POST['page']);
			$size = ValidateIntParam($_POST['size']);
			if($page && $size)
				SetResult(ToJson(GetProjects($page, $size)));
			else 
				SetResult(ToJson(GetProjects()));
		}
		else
			SetResult(ToJson(GetProjects()));
	}

	if($command == "get" && isset($_POST['id']))
	{
		$id = ThrowInvalid(ValidateIntParam($_POST['id']));
		$item = GetProject($id);
	
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
