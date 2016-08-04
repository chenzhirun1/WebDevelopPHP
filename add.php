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

	$ok = true;					//Declare a validation flag
	$errors = array();			//Declare an array for errors
	if($_POST){					//Start validation if anything is posted
		foreach($_POST as $k => $v){		//Loop through and trim all the data posted
			$_POST[$k] = trim($v);
		}

		//Validate 'name' field using regex
		if(!preg_match("/^[a-z0-9 :;\-,\']+$/i", $_POST['name'])){		//Validation not passed, similar below
			$ok = false;
			if(strlen($_POST['name'])){
				$errors['name'] = "Name contains invalid characters, only letters, numbers, colons, semicolons, dashes, commas, apostrophes and spaces are allowed";		//Put the problem into $error array, using proper keys, similar below 
			}	else{
				$errors['name'] = "Name is required";
			}
		}

		//Validate 'description' field using regex
		if(!preg_match("/^[a-z0-9.,\- \']+$/im", $_POST['description'])){
			$ok = false;
			if(strlen($_POST['description'])){
				$errors['description'] = "Description contains invalid characters, only letters, numbers, periods, commas, dashes, apostrophes and spaces are allowed";
			}	else{
				$errors['description'] = "Description is required";
			}
		}

		//Validate 'supplierCode' field using regex
		if(!preg_match("/^[a-z0-9 \-]+/i", $_POST['supplierCode'])){
			$ok = false;
			if(strlen($_POST['supplierCode'])){
				$errors['supplierCode'] = "Supplier Code can only contain letters, numbers, dashes and spaces";
			}	else{
				$errors['supplierCode'] = "Supplier Code is required";
			}
		}

		//Validate 'cost' field using regex
		if(!preg_match("/^[0-9]+\.[0-9][0-9]$/", $_POST['cost'])){
			$ok = false;
			if(strlen($_POST['cost'])){
				$errors['cost'] = "Cost can only contain numbers, one period and then 2 numbers";
			}	else
				$errors['cost'] = "Cost is required";
		}

		//Validate 'price' field using regex
		if(!preg_match("/^[0-9]+\.[0-9][0-9]$/", $_POST['price'])){
			$ok = false;
			if(strlen($_POST['price'])){
				$errors['price'] = "Selling Price can only contain numbers, one period and then 2 numbers";
			}	else{
				$errors['price'] = "Selling Price is required";
			}
		}

		//Validate 'onHand' field using regex
		if(!preg_match("/^[0-9]+$/", $_POST['onHand'])){
			$ok = false;
			if(strlen($_POST['onHand'])){
				$errors['onHand'] = "Number on hand can only contain numbers";
			}	else{
				$errors['onHand'] = "Number on hand is required";
			}
		}

		//Validate 'reorderPoint' field using regex
		if(!preg_match("/^[0-9]+$/", $_POST['reorderPoint'])){
			$ok = false;
			if(strlen($_POST['reorderPoint'])){
				$errors['reorderPoint'] = "Reorder Point can only contain numbers";
			}	else{
				$errors['reorderPoint'] = "Reorder Point is required";
			}
		}
	}

	if(!empty($_POST) && $ok){					//Data has been posted and all validations have been passed
		if($stauts = itemUpdate($_POST, isset($_GET['update'])?$_POST['id']:"")){
			header("Location:view.php");				//Adding succeeds, heading to view.php page for checking
			exit();															//Exit this program
		} else {
			die("Item information update failed: ".$status);						//DB query fails, die the program
		}
	} else {																//No data is posted, print the adding page for user to input
		generate_header("PG | Adding Game");

		//Generate menu by using class
		$menu = new Menu(array("Add"=>"add.php", "View All"=>"view.php?all"));
		$menu->menuGenerate();

		//An id is passed, looking for existing item
		if(isset($_GET['id'])){
			//An item is found, print the form with all the existing data for modification
			if($data = getItemDataById($_GET['id'])){
				product_form($data, $_GET['id']);
			} else {
				//Item with that id is not found
				?>
				<p>No product has been found.</p>
				<?php
			}
		} else {
			//No id is passed, print empty form for adding new item
			product_form($_POST);
		}

		generate_footer();
	}
?>

   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   