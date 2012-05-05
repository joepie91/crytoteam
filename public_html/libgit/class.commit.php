<?php
class GitCommit extends GitObject
{
	public $tree = "";
	public $author = "";
	public $committer = "";
	public $message = "";
	public $parents = array();
	
	function __construct($headerdata, $data)
	{
		parent::__construct($headerdata, $data);
		
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
						case "tree":
							$this->tree = $value;
							break;
						case "parent":
							$this->parents[] = $value;
							break;
						case "committer":
							$this->committer = new GitActor($value);
							break;
						case "author":
							$this->author = new GitActor($value);
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
}
