<?php
//INCLUDES-----------------------------------------
require_once($_SERVER['DOCUMENT_ROOT'].'/../richard-api/Config/MainConfig.php');
require_once(SERVROOT.'Lib/Common.php');
require_once(SERVROOT.'Lib/db_functions.php');
//-------------------------------------------------

function GetPosts($pageNum = 0, $pageSize = 0)
{
	connect_to_db();
	$paging = get_paging($pageNum, $pageSize);
	$customers = do_query("SELECT * FROM NewsItems ORDER BY Date DESC".$paging,"","");
	close_db();
	return $customers;
}

function GetPost($postId)
{
	connect_to_db();
	$customer = do_query("SELECT * FROM NewsItems WHERE Id = ?","i", [$postId]);
	close_db();
	return $customer;
}

?>
