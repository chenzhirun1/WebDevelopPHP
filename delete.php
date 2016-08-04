<?php
/********************************************************************************
INT322 D 
ZHIRUN CHEN
2014 Oct 13

Student Declaration

I/we declare that the attached assignment is my/our own work in accordance 
with Seneca Academic Policy. No part of this assignment has been copied manually 
or electronically from any other source (including web sites) or distributed to 
other students.

		
Name ------ZHIRUN CHEN----------------------------

Student ID -----076-898-121--------------------

************************************************************************************/?>
<?php ob_start();

require_once("library.php");
session_start();
checkLogin();

$link = new DBLink();		//Connect database
if(isset($_GET['delete'])){		//Setting `deleted` to 'y' if $_GET['delete'] is being passed
	$newVal = "y";
}

if(isset($_GET['restore'])){	//Setting `deleted` to 'n' if $_GET['restore'] is being passed
	$newVal = "n";
}

if(isset($newVal) && isset($_GET['id'])){	//Checking if $_GET['id'] is passed for updating
	$id = $link->sanitize($_GET['id']);
	$status = $link->runQuery("UPDATE `inventory` SET `deleted` = '$newVal' WHERE `id` = " . $_GET['id']);
	if($status){
		header("Location:view.php");	//Redirect back to view.php after update
		exit();
	} else {
		die("Status changing failed: ".$stauts);
	}
}
?>
