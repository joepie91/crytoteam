<?php
class GitActor
{
	public $timestamp = 0;
	public $name = "";
	
	function __construct($stamp)
	{
		$parts = explode(" ", $stamp);
		$parts_count = count($parts);
		
		$name_parts = array();
		
		for($i = 0; $i < $parts_count - 2; $i++)
		{
			$name_parts[] = $parts[$i];	
		}
		
		$this->name = implode(" ", $name_parts);
		$timestamp = $parts[$parts_count - 2] . " " . $parts[$parts_count - 1];
		
		date_default_timezone_set("GMT");
		$this->timestamp = strtotime($parts[$parts_count - 1], $parts[$parts_count - 2]);
	}
}
