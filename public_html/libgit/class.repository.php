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
		return $this->CreateObject($this->GetObjectRaw($sha));
	}
	
	function CreateObject($data, $type = null, $size = null)
	{
		if($type == null && $size == null)
		{
			list($header, $data) = explode("\0", $data, 2);
		}
		else
		{
			switch($type)
			{
				case OBJ_BLOB:
					$typestring = "blob";
					break;
				case OBJ_TREE:
					$typestring = "tree";
					break;
				case OBJ_TAG:
					$typestring = "tag";
					break;
				case OBJ_COMMIT:
					$typestring = "commit";
					break;
				default:
					throw new GitUnknownTypeException("The specified type is not valid for this function.");
			}
			$header = "{$typestring} {$size}";
		}
		
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
			throw new GitBranchNotFoundException("The '{$name}' branch does not exist.");
		}
	}
	
	function GetTag($name)
	{
		$filename = "{$this->path}/refs/tags/{$name}";
		
		if(file_exists($filename))
		{
			$sha = trim(file_get_contents($filename));
			return $this->GetObject($sha);
		}
		else
		{
			throw new GitTagNotFoundException("The '{$name}' tag does not exist.");
		}
	}
	
	function GetObjectForPath($origin, $path)
	{
		$path_parts = explode("/", $path);
		$total_parts = count($path_parts);
		$current_part = 0;
		
		if(!($origin instanceof GitTree))
		{
			$origin = $this->GetObject($origin);
		}
		
		if($origin instanceof GitTree)
		{
			$current_tree = $origin;
			
			for($i = 0; $i < $total_parts; $i++)
			{
				foreach($current_tree->elements as $element)
				{
					if($element->filename == $path_parts[$current_part])
					{
						$current_tree = $this->GetObject($element->hash);
						pretty_dump($current_tree);
						
						if($current_part != ($total_parts - 1) && !($current_tree instanceof GitTree))
						{
							throw new GitInvalidElementException("Encountered a non-GitTree object while walking the specified path.");
						}
						
						$current_part += 1;
						
						continue 2;
					}
					
				}
				pretty_dump($path_parts[$current_part]);
				pretty_dump($current_tree);
				throw new GitPathNotFoundException("The specified path was not found in the specified origin.");
			}
			
			return $current_tree;
		}
		else
		{
			throw new GitInvalidOriginException("You can only use a GitTree hash as origin.");
		}
	}
}
