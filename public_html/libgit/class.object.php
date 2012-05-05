<?php
class GitObject
{
	public $rawdata = "";
	public $size = 0;
	public $repo = null;
	
	function __construct($repo, $headerdata, $data)
	{
		$this->repo = $repo;
		$this->size = (int)$headerdata;
		$this->rawdata = $data;
	}
}
