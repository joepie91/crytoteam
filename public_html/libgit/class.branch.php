<?php
class GitBranch
{
	public $sha = "";
	public $repo = null;
	
	function __construct($repo, $sha)
	{
		$this->repo = $repo;
		$this->sha = $sha;
	}
	
	function GetLastCommit()
	{
		return $this->repo->GetObject($this->sha);
	}
	
	function GetTree()
	{
		return $this->GetCommit()->GetTree();
	}
}
