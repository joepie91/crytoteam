<?php
define("PACK_IDX_SIGNATURE", "\377tOc");

class GitPackIndex
{
	public $rawdata = "";
	public $version = 0;
	public $index = array();
	
	function __construct($data)
	{
		$this->rawdata = $data;
		
		if(substr($data, 0, 4) == PACK_IDX_SIGNATURE)
		{
			$version = number_from_bin(substr($data, 4, 4));
			
			if($version == 2)
			{
				$this->version = $version;
				$offset = 8;
				
				$indexes = unpack("N*", substr($data, $offset, 256*4));
				$highest = 0;
				
				for($i = 0; $i < 256; $i++)
				{
					if(!isset($indexes[$i + 1]))
					{
						continue;
					}
					
					$n = $indexes[$i + 1];
					
					if($n < $highest)
					{
						throw new GitCorruptIndexException("The pack index file is corrupt.");
					}
					
					$highest = $n;
				}
				
				$offset = $offset + (256 * 4);
				
				$keys = array();
				$values = array();
				
				for($i = 0; $i < $highest; $i++)
				{
					$keys[] = sha1_from_bin(substr($data, $offset, 20));
					$offset += 20;
				}
				
				$offset += ($highest * 4);
				
				for($i = 0; $i < $highest; $i++)
				{
					$values[] = number_from_bin(substr($data, $offset, 4));
					$offset += 4;
				}
				
				$this->index = array_combine($keys, $values);
			}
			else
			{
				throw new GitUnsupportedVersionException("Version {$version} pack indexes are not supported by this version of the library.");
			}
		}
		else
		{
			throw new GitInvalidFormatException("The provided data does not look like a pack index file.");
		}
	}
}
