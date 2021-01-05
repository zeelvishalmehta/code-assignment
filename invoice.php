<?
session_start();
if(!isset($_SESSION['uid']))
{
	header('index.php');	
}
include('database.php');
$db = new Database();

$today = date('Y-m-d');

$uid = $_SESSION['uid'];

$electobj = new ElectronicItem();

if($_POST['submit']=='Send')
{
	$invoice_number = $_GET['invoice'];
	$usersql = $db->getuserdetails($uid);
	$rowuser = mysqli_fetch_array($usersql);
	
	$email = $rowuser['email'];
	$name = $rowuser['name'];
	$explodeaddress = explode(",",$rowuser['address']);	
	
	$to = $_POST['email'];
	$subject = 'Invoice - '.$invoice_number;
	$from = 'mehta786@gmail.com';
 
	// To send HTML mail, the Content-type header must be set
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

	// Create email headers
	$headers .= 'From: '.$from."\r\n".
		'Reply-To: '.$from."\r\n" .
		'X-Mailer: PHP/' . phpversion();

	$message = '<html><body>';
	$message .= "<table width=60% style=margin-bottom:30px>";	
	
		
	$message .= "<tr style=padding-bottom: 2em;>";	
	$message .= "<td align=left>";
	$message .= "From: <b>XYZ LTD.</b><br>Satellite Area<br>Gujarat, 380058 ";
	$message .= "</td>";
		
	$message .= "<tr>";	
	$message .= "<td align=left>";
	$message .= "To: <b>".$name."</b><br>".$explodeaddress[0].",".$explodeaddress[1].",".$explodeaddress[2]."<br>";
	$message .= "</td>";
	$message .= "<tr>";		
	$message .= "</table>";
	
	$message .= "<table border='1' cellspacing='0'>";	
	$message .= "<tr>";
	$message .= "<td colspan=4>Date: ".$today."</td>";
	$message .= "</tr>";
	$message .= "<tr>";
	$message .= "<th width=250 align=center>Description</th>";
	$message .= "<th width=80 align=center>Quantity</th>";
	$message .= "<th width=100 align=center>Unit price</th>";
	$message .= "<th width=100 align=center>Total price</th>";
	$message .= "</tr>";
				$sqlview = $db->invoice($uid,$invoice_number);
				while($view=mysqli_fetch_array($sqlview))
				{
					$setprice = $electobj->setPrice($view['electronic_price']);
					$totalprice1 =  $electobj->getPrice() * $view['count'];
					$sid1 = $view['sid'];
					$count1 = $view['count'];
					$message .= "<tr>";
					$message .=  "<td align=left>".$view['electronic_name']."</td>";
					$message .=  "<td align=center>".$view['count']."</td>";
					$message .=  "<td align=right>$".number_format($electobj->getPrice(),2)."</td>";
					$message .=  "<td align=right>$".number_format($totalprice1,2)."</td>";
					$message .=  "</tr>";
					
							$subcontroller = $db->viewetracart($sid1);
							while($cont=mysqli_fetch_array($subcontroller))
							{
								$sname = $electobj->setType($cont['extra_name']);
								if($cont['count'] > 0){
									$extconprice = $electobj->setPrice($cont['price']);
									$quantity1 = $view['quantity'] + $view['quantity'];
									$rate1 = ($quantity1*$electobj->getPrice());
								$message .=  "<tr>";
								$message .=  "<td align=left>".$electobj->getType()."</td>";
								$message .=  "<td align=center>".$quantity1."</td>";	
								$message .=  "<td align=right>$".number_format($electobj->getPrice(),2)."</td>";
								$message .=  "<td align=right>$".number_format($rate1,2)."</td>";	
								$message .=  "</tr>";
								
								$subtotal1 = $rate1 + $subtotal1;	
								}
							}
					$mainprice1 = $mainprice1 + $totalprice1;
				}
				$total1 = $mainprice1 + $subtotal1;
			$message .= "<tr>";
			$message .= "<td colspan='3' class='text-right'><b>TOTAL</b></td>";
			$message .= "<td class='text-center'><b>$" . number_format(($total1),2) . "</b></td>";
			$message .= "<tr>";	
			
		$message .= "</table>";
	
		$message .= "</body></html>";
	
	mail($to, $subject, $message, $headers);
		
	header('location:invoice.php?invoice='.$invoice_number.'&succ=succ');	
}

?>
<html>
	<title>Invoice</title>
	<head>
		<link rel="stylesheet" type="text/css" href="style.css">
	</head>	
	<body>
		<form method="post">
	
			<div class="thankyou-main">
			<?
			//get user email address
			$sqlemail = $db->getuserdetails($uid);
			$useremail = mysqli_fetch_array($sqlemail);
			$emailaddress =  $useremail['email']
			?>
			<p>Thanks for your order.<br>Your order has been transferred for dispatching and the order will be received within the next 3 - 5 working days.</p>
				<p>The payment receipt has been already emailed on "<? echo $emailaddress;?>" and would like to share this receipt on another email address, please submit the below.</p>	
			
			</div>	
			
			<? if($_GET['succ']!=''){ ?>
				<div class="succmsg">
					<p>Your invoice copy has been forwarded.</p>
				</div>	
			<? } ?>
			
			<div class="friendemail">
				<form method="post">
					Email Address: <input type="text" name="email" value="" class="txtemail">&nbsp;<input type="submit" name="submit" value="Send" class="submitsend">
				</form>	
			</div>	
	<div class="invoice">
		<div class="company-address">
			XYZ LTD.
			<br />
			Satellite Area
			<br />
			Gujarat, 380058
			<br />
		</div>
	
		<div class="invoice-details">
			Invoice No: 
			<?
			$sqlinvoice = $db->invoice($uid,$_GET['invoice']);
			$row = mysqli_fetch_array($sqlinvoice);			
			echo $row['invoice'];
			?>
			<br />
			Date: <? echo $today;?>
		</div>
		
		<div class="customer-address">
			To:
			<br />
			<?  
			$sqluser = $db->getuserdetails($uid);
			$user = mysqli_fetch_array($sqluser);
			echo $user['name']."<br>";
			$explodeaddress = explode(",",$user['address']);
			echo $explodeaddress[0].",".$explodeaddress[1].",<br>";
			echo $explodeaddress[2]."<br>";
			?>
		</div>
		
		<div class="clear-fix"></div>
			<table border='1' cellspacing='0'>
				<tr>
					<th width=250 class="text-center">Description</th>
					<th width=80 class="text-center">Quantity</th>
					<th width=100 class="text-center">Unit price</th>
					<th width=100 class="text-center">Total price</th>
				</tr>

			<?
				$sqlview = $db->invoice($uid,$_GET['invoice']);
				while($view=mysqli_fetch_array($sqlview))
				{
					$mprice = $electobj->setPrice($view['electronic_price']);
					$totalprice =  $electobj->getPrice() * $view['count'];
					$sid = $view['sid'];
					$count = $view['count'];
					
					$nametype = $electobj->setType($view['electronic_name']);
					echo "<tr>";
					echo "<td class='text-center'>".$electobj->getType()."</td>";
					echo "<td class='text-center'>".$view['count']."</td>";
					echo "<td class='text-center'>$".number_format($electobj->getPrice(),2)."</td>";
					echo "<td class='text-center'>$".number_format($totalprice,2)."</td>";
					echo "</tr>";
					
							$subcontroller = $db->viewetracart($sid);
							while($cont=mysqli_fetch_array($subcontroller))
							{
								if($cont['count'] > 0){
									$exprice = $electobj->setPrice($cont['price']);
									$quantity = $view['quantity'] + $view['quantity'];
									$rate = ($quantity*$electobj->getPrice());
									
									$exname = $electobj->setType($cont['extra_name']);
								echo "<tr>";
								echo "<td class='text-center'>".$electobj->getType()."</td>";
								echo "<td class='text-center'>".$quantity."</td>";	
								echo "<td class='text-center'>$".number_format($electobj->getPrice(),2)."</td>";
								echo "<td class='text-center'>$".number_format($rate,2)."</td>";	
								echo "</tr>";
								
								$subtotal = $rate + $subtotal;	
								}
							}
					$mainprice = $mainprice + $totalprice;
				}
				$total = $mainprice + $subtotal;
			echo "<tr>";
			echo "<td colspan='3' class=text-center><b>TOTAL</b></td>";
			echo "<td class='text-center'><b>$" . number_format(($total),2) . "</b></td>";
			echo "<tr>";	
			?>
			</table>
		<p class="backhome"><a href="home.php"><img src="images/backhome.png" width="150px" height="100px;"></a></p>
		</div>
		
		</form>
	</body>

</html>