<?php
class GitTag extends GitObject
{
	public $target = "";
	public $tagger = "";
	public $type = "";
	public $tag = "";
	public $message = "";
	
	function __construct($repo, $headerdata, $data)
	{
		parent::__construct($repo, $headerdata, $data);
		
		$lines = explode("\n", $data);
		$message_parts = array();
		$parsing_message = false;
		
		foreach($lines as $line)
		{
			$line = trim($line);
			
			if(!empty($line))
			{
				if($parsing_message === false)
				{
					list($key, $value) = explode(" ", $line, 2);
					
					switch($key)
					{
						case "object":
							$this->target = $value;
							break;
						case "tagger":
							$this->tagger = new GitActor($value);
							break;
						case "type":
							$this->type = $value;
							break;
						case "tag":
							$this->tag = $value;
							break;
					}
				}
				else
				{
					$message_parts[] = $line;
				}
			}
			else
			{
				$parsing_message = true;
			}
		}
		
		$this->message = implode("\n", $message_parts);
	}
	
	function GetCommit()
	{
		return $this->repo->GetObject($this->target);
	}
	
	function GetTree()
	{
		return $this->GetCommit()->GetTree();
	}
}
