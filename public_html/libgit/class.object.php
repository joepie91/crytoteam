<?php
class GitObject
{
	public $rawdata = "";
	public $size = 0;
	public $repo = null;
	public $sha = "";
	
	function __construct($repo, $headerdata, $data, $sha)
	{
		$this->repo = $repo;
		$this->size = (int)$headerdata;
		$this->rawdata = $data;
		$this->sha = $sha;
	}
}
