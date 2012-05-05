<?php
class GitTreeElement
{
	public $mode = "";
	public $filename = "";
	public $hash = "";
	
	function __construct($mode, $filename, $hash)
	{
		$this->mode = $mode;
		$this->filename = $filename;
		$this->hash = $hash;
	}
}
