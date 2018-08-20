<?php

function SanitizeString($dirty, $maxLength = 255)
{
	$clean = substr($dirty, 0, $maxLength);
	$clean = preg_replace('/[^a-zA-Z0-9_\-?!.,@ ]/', '', $clean);
	return $clean;
}

function ValidateIntParam($data, $maxLength = 11, $allowNegative = false)
{
	$integer = intval(substr($data,0,$maxLength));
	
	if($allowNegative)
		return $integer;
	
	if($integer >= 0)
		return $integer;
		
	return false;
}

function ValidateFloatParam($data, $decimals = 2)
{
	$float = round(floatval(substr($data,0,15)), $decimals);
	if($float >= 0)
		return $float;
		
	return false;
}

?>
