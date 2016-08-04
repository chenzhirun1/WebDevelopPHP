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
class DBLink{
	private $link=null;
	private $lastResult;
	
	public function __construct($dbname = 'int322_143d02'){
		$lines = file('/home/int322_143d02/secret/topsecret');
			$dbserver = trim($lines[0]);
			$uid = trim($lines[1]);
			$pw = trim($lines[2]);
        $links = mysql_connect($dbserver, $uid, $pw) or die('Could not connect: ' . mysql_error($links));
        mysql_select_db($dbname,$links) or die('Could not select database');
        $this->link=$links;
	}	
	public function query($sql_query){
		$result=mysql_query($sql_query,$this->link) or die(mysql_error($this->link));
		return $result;
	}
	
	public function __destruct(){
		mysql_close($this->link);
	}
	
	function runQuery($sql){
		$this->lastResult = mysql_query($this->link, $sql) or die(mysql_error($this->link)."<br>".$sql);
		return $this->lastResult;
	}
	
	function isLastEmpty(){
		if(!$this->lastResult)
			return true;
		else
			return false;
	}
	
	function fetchNextRecord($ref){
		return mysql_fetch_assoc($ref);
	}
	
	function sanitize($str){
		return htmlentities(mysql_real_escape_string($this->link, strip_tags($str)));
	}
}
	
class Menu{
	private $items;
	
	function __construct($items){
		$this->items = $items;
	}
	
	function menuGenerate(){
	?>
	<nav>
		<ul>
			<?php
			foreach($this->items as $name => $link){
			?>
			<li><a href='<?=$link?>'><?=$name?></a></li>
			<?php
			}
			?>
			<li class='searchLi'>
				<form action="view.php" method="POST" id='menuSearchForm'>
					<label for ='searchBar'>Search in description:</label>
					<input type='text' name='searchBar' id='searchBar' value="<?=(isset($_SESSION['keywords']))?$_SESSION['keywords']:""?>"/>
					<input type='submit' value='Search'/>
				</form>
			</li>
			<li class='infoLi'>
				<span>User:<?$_SESSION['username']?>, Role: <?=$_SESSION['role']?></span>
				<a href="logout.php">Logout</a>
			</li>
		</ul>
	</nav>
	<?php
	}
}	

function generate_header($page_title = "No Title"){		
	?>
	<!DOCTYPE html>
	<html>
	<head>
		<meta charset='utf-8'>
		<title><?=$page_title?></title>
	</head>
	<body>
	<header>LD2009 Games Company</header>
	<?php
}

function generate_footer(){
	?>
	<footer>Copyright &copy 2014 ZHIRUN CHEN LD2009 Games Company</footer>
	</body>
	</html>
	<?php
}

function profuct_from($data,$id = null){
	global $errors;
	?>
	<form class='add_product_form' action='<?=empty($id)?"":"?update"?>' method='POST'>
		<?php
		if(!empty($id)){
			//Modifying existing item, show the id and fetch the deleted for UPDATE statement
			?>
			<label for='id'>Item ID:</label><input type='text' id='id' name='id' value='<?=$data['id']?>' readonly/><br>
			<input type='hidden' name='deleted' value='<?=$data['deleted']?>'/>
			<?php
		}
		?>
		<label for='name'>Item name:</label><input type='text' id='name' name='name' value="<?=isset($data['itemName'])?$data['itemName']:""?>"/>
			<?php echo isset($errors['name']) ? $errors['name'] : "" ?><br>
		<label for='description'>Description:</label><textarea id='description' name='description' /><?=isset($data['description'])?$data['description']:""?></textarea>
			<?php echo isset($errors['description'])?$errors['description']:""?><br>
		<label for='supplierCode'>Supplier Code:</label><input type='text' id='supplierCode' name='supplierCode' value='<?=isset($data['supplierCode'])?$data['supplierCode']:""?>'/>
			<?php echo isset($errors['supplierCode'])?$errors['supplierCode']:""?><br>
		<label for='cost'>Cost:</label><input type='text' id='cost' name='cost' value='<?=isset($data['cost'])?$data['cost']:""?>'/>
			<?php echo isset($errors['cost'])?$errors['cost']:""?><br>
		<label for='price'>Selling Price:</label><input type='text' id='price' name='price' value='<?=isset($data['price'])?$data['price']:""?>'/>
			<?php echo isset($errors['price'])?$errors['price']:""?><br>
		<label for='onHand'>Number on hand:</label><input type='text' id='onHand' name='onHand' value='<?=isset($data['onHand'])?$data['onHand']:""?>'/>
			<?php echo isset($errors['onHand'])?$errors['onHand']:""?><br>
		<label for='reorderPoint'>Reorder Point:</label><input type='text' id='reorderPoint' name='reorderPoint' value='<?=isset($data['reorderPoint'])?$data['reorderPoint']:""?>'/>
			<?php echo isset($errors['reorderPoint'])?$errors['reorderPoint']:""?><br>
		<label for='backOrder'>On Back Order:</label><input type='checkbox' id='backOrder' name='backOrder' <?=isset($data['backOrder'])?"checked":""?>/><br>
		<input type='submit' value='Submit'/>
	</form>
	<?php
}

//Function outputting the product list, accepting filter keywords for search and order field
function view_product_table($search, $order){
	$link = new DBLink();
	?>
	<table class='view_product'>
		<tr>
		  <th><a href='?order=id'>ID</a></th>
		  <th><a href='?order=itemName'>Item Name</a></th>
		  <th><a href='?order=description'>Description</a></th>
		  <th><a href='?order=supplierCode'>Supplier</a></th>
		  <th><a href='?order=cost'>Cost</a></th>
		  <th><a href='?order=price'>Price</a></th>
		  <th><a href='?order=onHand'>Number On Hand</a></th>
		  <th><a href='?order=reorderPoint'>Reorder Level</a></th>
		  <th><a href='?order=backOrder'>On Back Order?</a></th>
		  <th><a href='?order=deleted'>Delete/Restore</a></th>
		</tr>
		<?php
			if(!empty($search)){
				//Search keywords passed
				$search = trim($link->sanitize($search));
				$order = $link->sanitize($order);
				if(!empty($order)){
					//Order directly passed
					$sql = "SELECT * FROM `inventory` WHERE `description` LIKE '%$search%' ORDER BY `$order`";
				} elseif(!empty($_COOKIE['viewOrder'])) {
					//Reading order from COOKIE
					$sql = "SELECT * FROM `inventory` WHERE `description` LIKE '%$search%' ORDER BY `{$_COOKIE['viewOrder']}`";
				} else {
					//No order passed, order by id by default
					$sql = "SELECT * FROM `inventory` WHERE `description` LIKE '%$search%' ORDER BY `id`";
				}
			} else {
				//Showing all records
				if(!empty($order)){
					$sql = "SELECT * FROM `inventory` ORDER BY `$order`";
				} elseif(!empty($_COOKIE['viewOrder'])) {
					$sql = "SELECT * FROM `inventory` ORDER BY `{$_COOKIE['viewOrder']}`";
				} else {
					$sql = "SELECT * FROM `inventory` ORDER BY `id`";
				}
			}
		  $data = $link->runQuery($sql);

			if($link->isLastEmpty()){
				//No record found
				?>
				<tr><td colspan="10">Sorry, no related record is found.</td></tr>
				<?php
			} else {
				while($d = $link->fetchNextRecord($data)){	
					?>
					<tr>
					<td><a href='add.php?id=<?=$d['id']?>'><?=$d["id"]?></a></td>
					<td><?=$d["itemName"]?></td>
					<td><?=nl2br($d["description"])?></td>		
					<td><?=$d["supplierCode"]?></td>
					<td><?=$d["cost"]?></td>
					<td><?=$d["price"]?></td>
					<td><?=$d["onHand"]?></td>
					<td><?=$d["reorderPoint"]?></td>
					<td><?=$d["backOrder"]?></td>
					<?php 
					if($d["deleted"] == "n"){	
					?>
						<td><a href='delete.php?delete=1&id=<?=$d["id"]?>'>Delete</a></td>
					<?php
					} else {
					?>
						<td><a href='delete.php?restore=1&id=<?=$d["id"]?>'>Restore</a></td>
					<?php
					}
					?>
					</tr>
					<?php
				}
			}
		?>
	</table>
	<?php
}

//Function checking whether user has logged in, redirect to login page if not
function checkLogin(){
	if(!isset($_SESSION['username'])){
		header("Location: login.php");
		exit();
	}
}

//Function checking whether using HTTPS, redirect to HTTPS if not
function checkHttps(){
	if(!isset($_SERVER['HTTPS'])){
		header("Location: https://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
		exit();
	}
}

//Fetch all data by item's id, return an association array if found, false if not found
function getItemDataById($id){
	$link = new DBLink();
	$id = $link->sanitize($id);
	$ref = $link->runQuery("SELECT * FROM `inventory` WHERE `id` = '$id'");
	if($link->isLastEmpty()){
		$result = false;
	} else {
		$result = $link->fetchNextRecord($ref);
	}
	return $result;
}

//Function inserting new item of modifying existing item, return the DML statement status result
function itemUpdate($data, $id){
	$link = new DBLink();
	$insertingFields = array("itemName", "description", "supplierCode", "cost", "price", "onHand", "reorderPoint", "backOrder", "deleted");		

	$insertingData = array();
	$insertingData[] = $link->sanitize($data['name']);									
	$insertingData[] = $link->sanitize($data['description']);
	$insertingData[] = $link->sanitize($data['supplierCode']);
	$insertingData[] = $link->sanitize($data['cost']);
	$insertingData[] = $link->sanitize($data['price']);
	$insertingData[] = $link->sanitize($data['onHand']);
	$insertingData[] = $link->sanitize($data['reorderPoint']);
	$insertingData[] = isset($data['backOrder']) ? "y" : "n";			
	$insertingData[] = isset($data['deleted']) ? $data['deleted'] : "n";

	if(empty($id)){
		//Inserting new record
		$sql = "INSERT INTO `inventory` (`" . implode("`, `", $insertingFields) . "`) VALUES ('" . implode("', '", $insertingData) . "')";
	} else {
		//Updating existing record
		$id = $link->sanitize($id);
		$sql = "UPDATE `inventory` SET `";
		for($i = 0; $i < count($insertingFields); $i++){
			if($i){
				$sql .= ", `";
			}
			$sql .= $insertingFields[$i] . "` = '" . $insertingData[$i] . "' ";
		}
		$sql .= "WHERE `id` = '$id'";
	}

	$status = $link->runQuery($sql);
	return $status;
}
?>

