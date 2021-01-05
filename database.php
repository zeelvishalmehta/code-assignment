<?
session_start();
if(!isset($_SESSION["uid"]))
  {
      header("location: index.php");
  }
include('electronic.php');
class Database
{
	var $host = "localhost";
	var $user = "tracktick";
	var $pass = "jdZ4i52@";
	var $db = "tracktick";
	
	public function connect()
	{
		$link = mysqli_connect("".$this->host."","".$this->user."","".$this->pass."");
		mysqli_select_db($link,"".$this->db."");
		return $link;
	}
	
	public function login($username)
	{		
		$link = $this->connect();
		$checkuser = mysqli_query($link,"select uid, username, password from user where username = '".$username."' ");
		return $checkuser;
	}
	public function fetchelectronicitems()
	{
		$link = $this->connect();
		$query = mysqli_query($link,'select * from electronic_items') or die (mysqli_error($link));
		return $query;	
	 }
	public function fetchsubitems($item)
	{
		$link = $this->connect();
		$queryitems = mysqli_query($link,"select * from electronic_items where name = '".$item."' ") or die (mysqli_error($link));
		$row = mysqli_fetch_array($queryitems);
		$electronicid = $row['eid'];		
		
		//get subtypes
		$sql_type =  mysqli_query($link,"select * from subelectronic_item where eid = '".$electronicid."' ") or die (mysqli_error($link));
		return $sql_type;
	}
	
	public function countextracontroller($item)
	{
		$link = $this->connect();
		$queryitems = mysqli_query($link,"select * from electronic_items where name = '".$item."' ") or die (mysqli_error($link));
		$row = mysqli_fetch_array($queryitems);
		$electronicid = $row['eid'];	
		
		//get extra controller
		$sql_control = mysqli_query($link,"select number from limit_controller where eid = '".$electronicid."' ") or die (mysqli_error($link));
		$row_control = mysqli_fetch_array($sql_control);
		
		//call class to define limit
		$objlimit = new $item;
		return $objlimit->maxExtras($row_control['number']);
		
	}
	
	public function extracontroller($item)
	{
		$link = $this->connect();
		$getqueryitems = mysqli_query($link,"select * from electronic_items where name = '".$item."' ") or die (mysqli_error($link));
		$getrow = mysqli_fetch_array($getqueryitems);
		$getelectronicid = $getrow['eid'];	
		
		//get limit for controller
		$sql_control = mysqli_query($link,"select number from limit_controller where eid = '".$getelectronicid."' ") or die (mysqli_error($link));
		$row_control = mysqli_fetch_array($sql_control);
		
		//call class to define limit
		$objlimit = new $item;
		$limitcontroller =  $objlimit->maxExtras($row_control['number']);
		
		//get extra controller
		$getsql_control = mysqli_query($link,"select count(*) as count ,  extra_name, sum(price) as tprice , price  from electronic_extras where eid = '".$getelectronicid."' group by extra_name limit ".$limitcontroller." ") or die (mysqli_error($link));
		
		return $getsql_control;
		
	}
	
	public function inbuiltcontrollers($item,$subitem)
	{
		$link = $this->connect();
		$getqueryitems = mysqli_query($link,"select * from electronic_items where name = '".$item."' ") or die (mysqli_error($link));
		$getrow = mysqli_fetch_array($getqueryitems);
		$getelectronicid = $getrow['eid'];	
				
		//get subelctronic items inbuilt functionality
		$sql_inbuilt = mysqli_query($link,"select name from inbuilt_controller where eid = ".$getelectronicid." and sid = ".$subitem." ") or die (mysqli_error($link));
		return $sql_inbuilt;
	}
	public function insertorder($sid,$uid)
	{
		$link = $this->connect();
		$today_date = date("Y-m-d");
		
		//get name from electronic id
		$sqleid = mysqli_query($link,"select * from subelectronic_item where sid = ".$sid." ") or die (mysqli_error($link)); 
		$rowid = mysqli_fetch_array($sqleid);
		
		$name = $rowid['subname'];
		$price = $rowid['price'];		
		
		$totalprice = $price + $totalcontrollerprice;
		
		mysqli_query($link,"insert into cart (uid,sid,electronic_name,electronic_price,date,status) values ('".$uid."','".$sid."','".$name."','".$totalprice."','".$today_date."','order')") or die (mysqli_error($link));
	}
	public function viewcart($uid)
	{
		$link = $this->connect();
		$viewsql = mysqli_query($link,"SELECT sum(electronic_price) as electronic_price, electronic_name, sid, count(*) as count, count(sid) as quantity FROM cart WHERE status = 'order' and uid = '".$uid."' group by sid order by electronic_price desc") or die (mysqli_error($link));
		return $viewsql;
	}
	public function viewetracart($sid)
	{
		$link = $this->connect();
		$sqlextra = mysqli_query($link,"select sum(price) as total, count(*) as count, extra_name, price from electronic_extras where sid = ".$sid."  group by extra_name ") or die (mysqli_error($link));
		return $sqlextra;
	}
	public function payorder($uid,$invoiceid,$totalprice)
	{
		$link = $this->connect();
		$update = mysqli_query($link,"update cart set status = 'done' , invoice = '".$invoiceid."', total_price = '".$totalprice."' where uid = ".$uid." and status = 'order' ");
		return $update;
	}
	public function invoice($uid,$invoiceid)
	{
		$link = $this->connect();
		$viewinvoice = mysqli_query($link,"SELECT sum(electronic_price) as electronic_price, electronic_name, sid, count(*) as count, invoice, count(sid) as quantity, date, electronic_price FROM cart WHERE status = 'done' and uid = '".$uid."' and invoice = '".$invoiceid."' group by sid order by electronic_price desc") or die (mysqli_error($link));
		
		return $viewinvoice;	
	}
	
	public function getuserdetails($uid)
	{
		$link = $this->connect();
		$sqluser = mysqli_query($link,"select * from user where uid = ".$uid." ") or die (mysqli_error($link));
		return $sqluser;
	}
	
	public function getmainitem($name)
	{
		//get electronic id from subitem
		$link = $this->connect();
		$getid = mysqli_query($link,"select eid from subelectronic_item where subname = '".$name."'");
		$rid = mysqli_fetch_array($getid);
		
		$eid = $rid['eid'];
		
		//get main item name from id
		$sqlename = mysqli_query($link,"select name from electronic_items where eid = ".$eid." ");
		return $sqlename;
	}
	public function cartquantity($uid)
	{
		//get cart quantity
		$link = $this->connect();
		$sqlcart = mysqli_query($link,"SELECT  count(DISTINCT(sid)) as count FROM cart WHERE status = 'order' and uid = '".$uid."' ");
		return $sqlcart;
	}
	public function getinvoiceid($uid)
	{
		//get list of ordered invoiceids
		$link = $this->connect();
		$sqlgetid = mysqli_query($link,"SELECT invoice, total_price, date FROM cart WHERE status= 'done' and uid = '".$uid."' group by invoice");	
		return $sqlgetid;
		
	}
	public function edituser($uid,$name,$uname,$pswd,$email,$address)
	{
		$link = $this->connect();
		$update = mysqli_query($link,"update user set name='".$name."', username = '".$uname."', password = '".$pswd."', email = '".$email."', address = '".$address."' where uid = '".$uid."' ");
		return $update;
	}
	
}


?>


