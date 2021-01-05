<?
session_start();
if(!isset($_SESSION['uid']))
{
	header('index.php');	
}

?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="style.css">	

</head>
<body>

<div class="navbar">
  <a href="home.php">Home</a>
  <a href="user_profile.php">Profile</a>	
  <a href="order_history.php">Order History</a>	
  <a href="logout.php" style="float:right;">Logout</a>	
 
</div>	
	
</body>
</html>
