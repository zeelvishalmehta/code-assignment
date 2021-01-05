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
$today = date('Y-m-d');

if($_POST['submit']=='Pay Now')
{
	$invoice_number = rand(0,1000);
	$totalprice = $_POST['total'];
	
	 echo $db->payorder($uid,$invoice_number,$totalprice);
	
	$usersql = $db->getuserdetails($uid);
	$rowuser = mysqli_fetch_array($usersql);
	
	$email = $rowuser['email'];
	$name = $rowuser['name'];
	$explodeaddress = explode(",",$rowuser['address']);	
	$to = $email;

	
	$sqlinvoice = $db->invoice($uid,$invoice_number);
	$row = mysqli_fetch_array($sqlinvoice);			
	$invoicenumber = $row['invoice'];
	
	$subject = 'Invoice - '.$invoicenumber;

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
					$extracprice = $electobj->setPrice($view['electronic_price']);
					$totalprice1 =  $electobj->getPrice() * $view['count'];
					$sid1 = $view['sid'];
					$count1 = $view['count'];
					$sname = $electobj->setType($view['electronic_name']);
					
					$message .= "<tr>";
					$message .=  "<td align=left>".$electobj->getType()."</td>";
					$message .=  "<td align=center>".$view['count']."</td>";
					$message .=  "<td align=right>$".number_format($electobj->getPrice(),2)."</td>";
					$message .=  "<td align=right>$".number_format($totalprice1,2)."</td>";
					$message .=  "</tr>";
					
							$subcontroller = $db->viewetracart($sid1);
							while($cont=mysqli_fetch_array($subcontroller))
							{
								$setname = $electobj->setType($cont['extra_name']);
								
								if($cont['count'] > 0){
									$quantity1 = $view['quantity'] + $view['quantity'];
									$setprice = $electobj->setPrice($cont['price']);
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
			$message .= "<td class='text-right'><b>$" . number_format(($total1),2) . "</b></td>";
			$message .= "<tr>";	
			
		$message .= "</table>";
	
		$message .= "</body></html>";
	
	mail($to, $subject, $message, $headers);
		
	header('location:invoice.php?invoice='.$invoice_number.'');	
}

if($_POST['submit']=='Back To Shopping')
{
	header('location:home.php');	
}
?>
<html>
	<head><link rel="stylesheet" href="style.css">  </head>
	<title>View Cart</title>
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
			<table class="subitemtable"> 
				<?
				$sqlcount = $db->viewcart($uid);
				$rowcount = mysqli_fetch_array($sqlcount);
				
				if($rowcount['count']>0) {
				?>
				<tr>
					<th>Item No</th>
					<th>Item Name</th>
					<th>Quantity</th>
					<th>Price</th>
				</tr>	
				
						<?
						$uid = $_SESSION['uid'];
						$sqlview = $db->viewcart($uid);
						$i=1;
						while($rview=mysqli_fetch_array($sqlview))
						{
							
							echo "<tr class=rowbackgrnd>";
							$gname = $electobj->setType($rview['electronic_name']);
							$name = $electobj->getType();	
							
							$setprice = $electobj->setPrice($rview['electronic_price']);
							$price = $electobj->getPrice();

							$sid = $rview['sid'];
							
							$count = $rview['count'];
							
							echo "<td width=15%>(".$i.")</td>";
							echo "<td>".$name."</td>";
							echo "<td>".$rview['count']."</td>";
							echo "<td>$".number_format($price,2)."</td>";
							echo "</tr>";			
							
														
							//get main item name from subitem
							$mainsql = $db->getmainitem($name);
							$rowmain = mysqli_fetch_array($mainsql);
							$mainitem = $rowmain['name'];
							
							//check item have any builtin functionality
							$sqlbuiltin = $db->inbuiltcontrollers($mainitem,$sid);
							$countbuilt = mysqli_num_rows($sqlbuiltin);
							while($rowbuilt = mysqli_fetch_array($sqlbuiltin))
									{
										
										if($countbuilt > 0){
										echo "<tr>";
										$settype = $electobj->setType($rowbuilt['name']);								
										echo "<td colspan=4>BuiltIn: ".$electobj->getType()."</td>";
										echo "</tr>";	
										}
									}
							
							//check have any extra controller	
							$subcontroller = $db->viewetracart($sid);
							while($cont=mysqli_fetch_array($subcontroller))
							{
								if($cont['count'] > 0){
									$quantity =  $rview['quantity'] + $rview['quantity'];
									$rate = $quantity * $cont['price'];
									$rateprice = $electobj->setPrice($rate);
								echo "<tr>";
								echo "<td>Extra</td>";
								$setextratype = $electobj->setType($cont['extra_name']);	
								echo "<td>".$electobj->getType()."</td>";
								echo "<td>".$quantity."</td>";	
								echo "<td>$".number_format($electobj->getPrice(),2)."</td>";	
								echo "</tr>";
								
								$subtotal = $rate + $subtotal;	
								}
							}
							$totoalprice = $price + $totoalprice;
							$i++;
							
						}
						
						?>
					<tr>
						<td  colspan="3"><b>Total</b></td>
						<?
							$finalprice = $totoalprice + $subtotal;
						?>
						<input type='hidden' name='total' value='<? echo $finalprice;?>'>
						<td><? echo "$".number_format(($totoalprice + $subtotal),2); ?></td>										
					</tr>	
					<tr>
						<td colspan="4" align="center">
						<input type="submit" name="submit" value="Back To Shopping" class="submit">	
						<input type="submit" name="submit" value="Pay Now" class="submit"></td>
					</tr>
				<? } else { ?>
				<tr><td class="emptycart">There is no item in the cart.</td></tr>
				<? } ?>
			</table>	
			
		</form>
		
	</body>		
	
</html>	