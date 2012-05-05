<?php
class GitRepository
{
	public $path = "";
	
	function __construct($path)
	{
		$this->path = $path;
	}
	
	function GetObjectRaw($sha)
	{
		return gzuncompress(
		file_get_contents(
		sprintf("{$this->path}/objects/%s/%s", 
			substr($sha, 0, 2), 
			substr($sha, 2)
		)));
	}
	
	function GetObject($sha)
	{
		list($header, $data) = explode("\0", $this->GetObjectRaw($sha), 2);
		
		if(strpos($header, " ") !== false)
		{
			list($type, $headerdata) = explode(" ", $header, 2);
		}
		else
		{
			$type = $header;
			$headerdata = "";
		}
		
		switch($type)
		{
			case "commit":
				return new GitCommit($this, $headerdata, $data);
				break;
			case "blob":
				return new GitBlob($this, $headerdata, $data);
				break;
			case "tree":
				return new GitTree($this, $headerdata, $data);
				break;
			case "tag":
				return new GitTag($this, $headerdata, $data);
				break;
			default:
				return new GitObject($this, $headerdata, $data);
				break;
		}
	}
	
	function GetBranch($name)
	{
		$filename = "{$this->path}/refs/heads/{$name}";
		
		if(file_exists($filename))
		{
			$sha = trim(file_get_contents($filename));
			return new GitBranch($this, $sha);
		}
		else
		{
			throw new GitBranchNotFoundException("The {$name} branch does not exist.");
		}
	}
}
