<?
session_start();

if(!isset($_SESSION['uid']))
{
	header("location: index.php");	
}
include('database.php');
$db = new Database();

$uid = $_SESSION['uid'];

//get userdetails
$user = $db->getuserdetails($uid);
$rowuser = mysqli_fetch_array($user);
$name = $rowuser['name'];
$address = $rowuser['address'];
$email = $rowuser['email'];
$uname = $rowuser['username'];
$pswd = $rowuser['password'];

if($_POST['submit'] == "Edit")
{
	$name = $_POST['name'];
	$uname = $_POST['uname'];
	$pswd = $_POST['pswd'];
	$email = $_POST['email'];
	$address = $_POST['address'];
	
	
	echo $db->edituser($uid,$name,$uname,$pswd,$email,$address);
	
	header('location:user_profile.php');
	
}
?>

<!DOCTYPE html>
<html lang="en">   
<head> 
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <!--<meta http-equiv="X-UA-Compatible" content="ie=edge">-->
    <link rel="stylesheet" href="style.css">  
	<link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700" rel="stylesheet" />
    <title>Edit Profile</title> 
</head> 
  <? include('menu.php');?>
	<div class="addtocart">
		<?
		$countcart = $db->cartquantity($uid);
		$rowcount = mysqli_fetch_array($countcart);
		$quantity = $rowcount['count'];
		echo $quantity;
		?>
		<br><a href="view_cart.php"><img src="images/cart.png" width="35px" height="35px"></a>
	</div>	
<body> 
    
<form method="post">
 
  <div class="container">
		<table width="50%" class="profile">
		<tr>
			<td width="20%">Name: </td><td> <input type="text" name="name" value="<?=$name?>" required></td>	
		</tr>			
		<tr>
			<td width="20%">Username:</td><td><input type="text" name="uname" value="<?=$uname?>" required></td>	
		</tr>	
		<tr>
			<td width="20%">Password:</td><td><input type="password" name="pswd" value="<?=$pswd?>" required></td>	
		</tr>		
		<tr>
			<td width="20%">Email:</td><td><input type="text" name="email" value="<?=$email?>" required></td>	
		</tr>	
		<tr>
			<td>Address:</td><td><textarea name="address" required><?=$address?></textarea></td>	
		</tr>	
		<tr>
			<td></td><td><input type="submit" name="submit" value="Edit" class="submitsend"></td>	
		</tr>	
	   </table>	
    </div>

  
</form>
</body>
</html>