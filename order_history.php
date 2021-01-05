<?
session_start();

if(!isset($_SESSION['uid']))
{
	header("location: index.php");	
}
include('database.php');
$db = new Database();

$electobj = new ElectronicItem();

$uid = $_SESSION['uid'];

if($_POST['submit']=='Send')
{
	$invoice_number = $_GET['invoice'];
	$usersql = $db->getuserdetails($uid);
	$rowuser = mysqli_fetch_array($usersql);
	
	$email = $rowuser['email'];
	$name = $rowuser['name'];
	$explodeaddress = explode(",",$rowuser['address']);	
	
	//call invoice function to get date
	$sqldate = $db->invoice($uid,$invoice_number);
	$rowdate = mysqli_fetch_array($sqldate);
	$today = $rowdate['date'];
			
		
	$to = $_POST['email'];
	$subject = 'Invoice - '.$invoice_number;
	$from = "mehta786@gmail.com";
 
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
					$eprice = $electobj->setPrice($view['electronic_price']);
					$totalprice1 =  $electobj->getPrice() * $view['count'];
					$sid1 = $view['sid'];
					$count1 = $view['count'];
					
					$ename = $electobj->setType($view['electronic_name']);
					$message .= "<tr>";
					$message .=  "<td align=left>".$electobj->getType()."</td>";
					$message .=  "<td align=center>".$view['count']."</td>";
					$message .=  "<td align=right>$".number_format($electobj->getPrice(),2)."</td>";
					$message .=  "<td align=right>$".number_format($totalprice1,2)."</td>";
					$message .=  "</tr>";
					
							$subcontroller = $db->viewetracart($sid1);
							while($cont=mysqli_fetch_array($subcontroller))
							{
								if($cont['count'] > 0){
									$excntprice = $electobj->setPrice($cont['price']);
									
									$quantity1 = $view['quantity'] + $view['quantity'];
									$rate1 = ($quantity1*$electobj->getPrice());
									
									$excnname = $electobj->setType($cont['extra_name']);
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
		
	header('location:order_history.php?succ=succ');	
}

?>
<html>
	<head><link rel="stylesheet" href="style.css">  </head>
	<title>Order History</title>
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
		<? if($_GET['succ']!=''){ ?>
				<div class="succmsg">
					<p>Your invoice copy has been forwarded.</p>
				</div>	
			<? } ?>
		<div> 
				<?
				$sqlinvoice = $db->getinvoiceid($uid);
				$rowcount = mysqli_num_rows($sqlinvoice);
				
				if($rowcount > 0) {
					while($rowivid=mysqli_fetch_array($sqlinvoice))
					{
						$invoice_no = $rowivid['invoice'];
						$total_price = $rowivid['total_price'];
				?>
				<p class="invoiceid">Invoice No: <? echo $invoice_no;?>
				
				&nbsp;&nbsp;&nbsp;&nbsp;<a href="order_history.php?invoice=<? echo $invoice_no;?>" class="sendid">Send Invoice</a></p>	
			<?
				if($_GET['invoice']==$invoice_no)
				{ ?>
					<div class="friendemail">
				<form method="post">
					Email Address: <input type="text" name="email" value="" class="txtemail">&nbsp;
					<input type="submit" name="submit" value="Send" class="submitsend">
				</form>	
			</div>	
				<?}?>
		
			<table class="subitemtable">
				<tr><td><b>Date: <? echo $rowivid['date'];?></b></td></tr>
				<tr>
					<th width=250 class="text-center">Description</th>
					<th width=80 class="text-center">Quantity</th>
					<th width=100 class="text-center">Unit price</th>
					<th width=100 class="text-center">Total price</th>
				</tr>

			<?
				$sqlview = $db->invoice($uid,$invoice_no);
				while($view=mysqli_fetch_array($sqlview))
				{
					$eprice = $electobj->setPrice($view['electronic_price']);
					$totalprice =  $electobj->getPrice() * $view['count'];
					$sid = $view['sid'];
					$count = $view['count'];
					
					$sname = $electobj->setType($view['electronic_name']);
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
									$extraprice = $electobj->setPrice($cont['price']);
									
									$quantity = $view['quantity'] + $view['quantity'];
									$rate = ($quantity*$electobj->getPrice());
									
									$extraname = $electobj->setType($cont['extra_name']);
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
			echo "<td class='text-right'><b>$" . number_format(($total_price),2) . "</b></td>";
			echo "<tr>";	
			?>
			</table>
			
			<?} } else { ?>
				<tr><td class='text-center'>There is no order history.</td></tr>
				<? } ?>
				
			</div>	
	</body>
</html>
