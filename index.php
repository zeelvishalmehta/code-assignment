<?
session_start();
include("database.php");
$db = new Database();

if($_POST['Submit'] == "Log In")
{
	if(($_POST['uname'] == '') && ($_POST['psw'] == ''))
	{
		header('location:index.php');
	}
	else
	{
		
		$cookie_time = 60 * 60 * 24 * 30; //30 days
		$cookie_time_onset = $cookie_time + time();
		setcookie("username",$_POST['uname'],$cookie_time_onset);
		setcookie("password",$_POST['psw'],$cookie_time_onset);		
		
		$checkuser = $db->login($_POST['uname']);	
		while($row=mysqli_fetch_array($checkuser))
		{
			$uname = $row['username'];
			$pswd = $row['password'];
			
			$uid = $row['uid'];
			
			$_SESSION['uid'] = $uid;
			
		}
		if(($_POST['uname']==$uname) && ($_POST['psw']==$pswd))
		{
			header('location:home.php');
		}
		else
		{
			header('location:index.php?error=error');
		}
	
	}

	
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
    <title>Login Page</title> 
</head> 
  
<body> 
    
<form method="post">
  <div class="textcontainer">
	  <label>USER LOGIN</label>
  </div>

  <div class="container">
	  <div class="emsg"><? if($_GET['error']!=''){ echo "Invalid username or password";}?></div>
    
    <input type="text" placeholder="Enter Username" name="uname" class="usertxt" required>

  
    <input type="password" placeholder="Enter Password" name="psw" class="usertxt" required>
        
	  <input type="submit" name="Submit" class="login-btn" Value="Log In">
    </div>

  
</form>
</body>
</html>