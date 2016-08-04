<?php
/********************************************************************************
INT322 D 
ZHIRUN CHEN
2014 NOV 29

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
	checkHttps();

	if(isset($_GET['order'])){
		setcookie("viewOrder", $_GET['order'], time()+60*60*24*30, "/");
	}

	if(isset($_POST['searchBar'])){
		if(preg_match("/^ *$/", $_POST['searchBar'])){
			$_POST['searchBar'] = "";
			unset($_SESSION['keywords']);
		} else {
			$link = new DBLink();
			$_SESSION['keywords'] = htmlspecialchars($_POST['searchBar']);		//Prevent XSS attack
		}
	}

	if(isset($_GET['all'])){
		unset($_SESSION['keywords']);
	}

	generate_header("PG | Viewing Games");
	
	$menu = new Menu(array("Add"=>"add.php", "View All"=>"view.php?all"));
	$menu->menuGenerate();

	view_product_table(isset($_SESSION['keywords'])?$_SESSION['keywords']:"", isset($_GET['order'])?$_GET['order']:"");

	generate_footer();
?>
