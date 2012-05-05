<?php
class GitPack
{
	public $repo = null;
	public $index = null;
	public $pack_filename = "";
	
	function __construct($repo, $name)
	{
		$this->repo = $repo;
		
		$index_filename = "{$repo->path}/objects/pack/{$name}.idx";
		$this->pack_filename = "{$repo->path}/objects/pack/{$name}.pack";
		
		$this->index = new GitPackIndex(file_get_contents($index_filename));
	}
	
	function UnpackObject($sha)
	{
		if(isset($this->index->index[$sha]))
		{
			$start = $this->index->index[$sha];
			
			$file = fopen($this->pack_filename, "rb");
			fseek($file, $start);
			
			$header = ord(fread($file, 1));
			$type = ($header >> 4) & 7;
			$hasnext = ($header & 128) >> 7;
			$size = $header & 0xf;
			$offset = 4;
			
			while($hasnext)
			{
				$byte = ord(fread($file, 1));
				$size |= ($byte & 0x7f) << $offset;
				$hasnext = ($byte & 128) >> 7;
				$offset += 7;
			}
			
			switch($type)
			{
				case OBJ_COMMIT:
				case OBJ_TREE:
				case OBJ_BLOB:
				case OBJ_TAG:
					// this is a compressed object
					$data = fread($file, $size);
					$uncompressed = gzuncompress($data);
					
					if($uncompressed === false)
					{
						$uncompressed = $data;
					}
					
					return $this->repo->CreateObject($uncompressed, $type, $size);
					break;
				case OBJ_OFS_DELTA:
				case OBJ_REF_DELTA:
					// this is a delta
					throw new Exception("This is not yet implemented.");
					break;
				default:
					throw new GitUnknownTypeException("The object type is not supported.");
			}
		}
		else
		{
			throw new GitObjectNotFoundException("The specified object does not exist in this pack.");
		}
	}
}
