<?
session_start();
if(!isset($_SESSION["uid"]))
  {
      header("location: index.php");
  }
class ElectronicItems
{

	private $items = array();
	public $price;
	public $type;
	
	public function __construct()
		{
			$this->items = $items;
		}

	public function getSortedItems($type)
	{
	
	$sorted = array();
	foreach ($this->items as $item)
		{

			$sorted[($item->price * 100)] = $item;
		}

	return ksort($sorted);
	
}

public function getItemsByType($type)
{

	if (in_array($type, ElectronicItem::$types))
		{

			$callback = function($item) use ($type)
			{

				return $item->type == $type;
			};

		$items = array_filter($this->items, $callback);
		}

	return false;
}


}
 

class ElectronicItem
{

/**
*	@var float
*/
public $price;

/**
*	@var string
*/
private $type; 
public $wired;

const ELECTRONIC_ITEM_TELEVISION = 'television'; 
const ELECTRONIC_ITEM_CONSOLE = 'console';
const ELECTRONIC_ITEM_MICROWAVE = 'microwave';

private static $types = array(self::ELECTRONIC_ITEM_CONSOLE, self::ELECTRONIC_ITEM_MICROWAVE, self::ELECTRONIC_ITEM_TELEVISION);

function getPrice()
{
return $this->price;
}

function getType()
{
return $this->type;
}

function getWired()
{
return $this->wired;
}

function setPrice($price)
{
$this->price = $price;
}

function setType($type)
{
$this->type = $type;
}

function setWired($wired)
{
$this->wired = $wired;
}
}

class console
{
	public $limit;
	public function maxExtras($limit)
	{
		$this->limit = $limit;	
		return $this->limit;
	}
}

class television
{
	public $limit;
	public function maxExtras($limit)
	{
		$this->limit = $limit;	
		return $this->limit;
	}
}

class microvawe
{
	public $limit;
	public function maxExtras($limit)
	{
		$this->limit = $limit;	
		return $this->limit;
	}
}
