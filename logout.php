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

************************************************************************************/
require_once("library.php");
session_start();
checkLogin();

//Destroying all session information
unset($_SESSION);
session_destroy();
setcookie("PHPSESSID", "", time()-10000, "/");

//Redirect back to login page
header("Location: login.php");
exit();

?>