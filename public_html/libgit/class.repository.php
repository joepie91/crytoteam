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
				return new GitCommit($headerdata, $data);
				break;
			case "blob":
				return new GitBlob($headerdata, $data);
				break;
			case "tree":
				return new GitTree($headerdata, $data);
				break;
			case "tag":
				return new GitTag($headerdata, $data);
				break;
			default:
				return new GitObject($headerdata, $data);
				break;
		}
	}
}
