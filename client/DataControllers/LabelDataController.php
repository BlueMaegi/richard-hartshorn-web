<?php
//INCLUDES-----------------------------------------
require_once($_SERVER['DOCUMENT_ROOT'].'/../server/Config/MainConfig.php');
require_once(SERVROOT.'Lib/Common.php');
require_once(SERVROOT.'Handlers/ShipmentHandler.php');
require_once('DataLib.php');
//-------------------------------------------------

try
{

	//Produce a PDF label for viewing
	if(isset($_GET['id']) && !isset($_GET['complete']))
	{
		$id = ThrowInvalid(ValidateIntParam($_GET['id']));
		$filePath = SERVROOT.'Labels/'.$id.'.pdf';

		if ($id && file_exists($filePath)) {

			header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
			header("Content-Type: application/pdf");
			header("Content-Transfer-Encoding: Binary");
			header("Content-Length:".filesize($filePath));
			header("Content-Disposition: inline; filename=label_$id.pdf");
			//note: use "attachment" ^ disposition to force download rather than view
			readfile($filePath);
			die();        
		} else {
			throw new Exception("Not Found", 404);
		} 
	}

	//Produce a Zip folder of all labels that need printing
	if(isset($_GET['secret']))
	{
		$secret = SanitizeString($_GET['secret'], 15);
		if($secret != POWERSHELL_SECRET)
			throw new Exception("Not Authorized", 403);
			
		$shipments = GetPrintableShipments();
		if(!is_array($shipments))
			throw new Exception("No Content", 204);
			
		$zipFile = 'toprint.zip';
		$zip = new ZipArchive();
		if(file_exists($zipFile))
			$zip->open($zipFile, ZIPARCHIVE::OVERWRITE);
		else
			$zip->open($zipFile, ZIPARCHIVE::CREATE);
			
		
		foreach($shipments as $s)
		{
			$filePath = SERVROOT.'Labels/'.$s['id'].'.pdf';
			if(file_exists($filePath))
				$zip->addFile($filePath, $s['id'].'.pdf');
		}

		$zip->close();

		header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
		header("Content-Type: application/zip");
		header("Content-Transfer-Encoding: Binary");
		header("Content-Length:".filesize($zipFile));
		header("Content-Disposition: attachment; filename=LabelSet.zip");
		readfile($zipFile);
		unlink($zipFile);
		die();        
	}
	
	//Mark a label as printed
	if(isset($_GET['complete']) && isset($_GET['id']))
	{
		$complete = SanitizeString($_GET['complete'], 5);
		$id = ThrowInvalid(ValidateIntParam($_GET['id']));
		
		if($complete != 'true' || !$id)
			throw new Exception("Bad Request", 400);
		
		$success = MarkComplete($id);
		SetResult($success);
		ReturnResult();
	}
}
catch(Exception $e)
{
	ReturnError($e->getCode(), $e->getMessage());
}

?>

