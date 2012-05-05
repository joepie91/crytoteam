<?php
class GitTree extends GitObject
{
	public $elements = array();
	
	function __construct($repo, $headerdata, $data)
	{
		parent::__construct($repo, $headerdata, $data);
		
		$parsing_sha = false;
		$sha_bytecount = 0;
		$lines = array();
		$current_line = "";
		
		for($i = 0; $i < strlen($data); $i++)
		{
			$char = $data[$i];
			$current_line .= $char;
			
			if(ord($char) == 0)
			{
				$parsing_sha = true;
			}
			else
			{
				if($parsing_sha === true)
				{
					$sha_bytecount += 1;
				}
				
				if($sha_bytecount == 20)
				{
					$parsing_sha = false;
					$lines[] = $current_line;
					$current_line = "";
					$sha_bytecount = 0;
				}
			}
		}
		$lines[] = $current_line;
		
		foreach($lines as $line)
		{
			if(!empty($line))
			{
				list($metadata, $binhash) = explode("\0", $line, 2);
				list($mode, $filename) = explode(" ", $metadata, 2);
				$hash = sha1_from_bin($binhash);
				$this->elements[] = new GitTreeElement($mode, $filename, $hash);
			}
		}
	}
}
