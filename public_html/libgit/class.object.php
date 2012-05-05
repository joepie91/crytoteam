<?php
class GitObject
{
	public $rawdata = "";
	public $size = 0;
	
	function __construct($headerdata, $data)
	{
		$this->size = (int)$headerdata;
		$this->rawdata = $data;
	}
}
