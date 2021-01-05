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

if($_GET['sid']!='')
{
	
	$sid = $_GET['sid'];
	echo $db->insertorder($sid,$uid); 
	header('location:view_cart.php');
}

?>
<html>
	<title>Home Page</title>
	<head><link rel="stylesheet" href="style.css">  </head>
	<? include('menu.php'); ?>
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
			<table align="center" style="width:50%;border-spacing: 0 1em;margin-top:20px;" class="displayitems">
				<tr>
					<td class="heading"><U>ELECTRONIC ITEMS</U></td>
				</tr>
				
				<tr>
					<td class="subheading">	
						<? 
						$query = $db->fetchelectronicitems();
						
						while($row=mysqli_fetch_array($query))
						{ 
							$name = $row['name'];
							$extension = '.png';
							echo '<img src=images/'.$name.$extension.' class=itemimage><a href=home.php?item='.$name.' class="anchorspace">'.$name.'</a>';
						} 
						?></td>
				</tr>
			</table>
			<table class="subitemtable">
					
				<? if($_GET['item']!='') {?>
				
					<?
								echo "<tr>";
								echo "<th class=subitemheading>Product</th>";
								echo "<th class=subitemheading>Price</th>";
								echo "<th></th>";
								echo  "</tr>";
								$item = $_GET['item'];
								
								$sqltype = $db->fetchsubitems($item);
								while($type=mysqli_fetch_array($sqltype))
								{
									echo "<tr>";
									echo "<td>";
									$subname = $type['subname'];
									$sid = $type['sid'];
									$setprice = $electobj->setPrice($type['price']);
									$price = $electobj->getPrice();
																		
									echo $subname."<br>";
									$sqlbuiltin = $db->inbuiltcontrollers($_GET['item'],$sid);
									while($rowbuilt = mysqli_fetch_array($sqlbuiltin))
									{
										$typecont = $electobj->setType($rowbuilt['name']);
										echo "[".$electobj->getType()."]<br>";
										
									}
									echo "</td>";
									echo "<td>$".$price."</td>";
									
									echo "</td>";
									echo "<td align=right>";
									echo "<p><a href=home.php?sid=".$type['sid']."><img src=images/addcart.png width=100px height=100px></a></p>";
									echo "</td>";
									echo "</tr>";
								}
								
					
					?>
					
				<?}
				if($_GET['item']!='') { ?>
				<tr>
					<td> 
					<? 
					$extranumber = $db->countextracontroller($_GET['item']);	
					if($extranumber!=0){
					echo $extranumber." Controllers (extra)";
					}
					
					?>
					</td>
					
				</tr>	
				
					<?
						$queryextracontroller = $db->extracontroller($_GET['item']);
						$i=1;
						while($result = mysqli_fetch_array($queryextracontroller))
						{							
							echo "<tr>";
							$extracont = $electobj->setType($result['extra_name']);
							$nameofcontroller =  $result['count']." ".$electobj->getType()." ";	
							$setprice = $electobj->setPrice($result['tprice']);
							$totalprice = $electobj->getPrice();
							
							echo "<td>- ".$nameofcontroller."<br> &nbsp; each price:  $".$result['price']."</td>";
							echo "<td>$".$totalprice."</td>";
							echo "</tr>";
							$i++;
						}
						
					?>
					
				<? } ?>
			</table>	
		</form>	
	</body>	
	
</html>	
