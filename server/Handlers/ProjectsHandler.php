<?php
//INCLUDES-----------------------------------------
require_once($_SERVER['DOCUMENT_ROOT'].'/../richard-api/Config/MainConfig.php');
require_once(SERVROOT.'Lib/Common.php');
require_once(SERVROOT.'Lib/db_functions.php');
//-------------------------------------------------

function GetProjects($pageNum = 0, $pageSize = 0)
{
	connect_to_db();
	$paging = get_paging($pageNum, $pageSize);
	$customers = do_query("SELECT * FROM Projects".$paging,"","");
	close_db();
	return $customers;
}

function GetProject($postId)
{
	connect_to_db();
	$customer = do_query("SELECT * FROM Projects WHERE Id = ?","i", [$postId]);
	close_db();
	return $customer;
}

?>
