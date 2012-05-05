<?php
class GitPack
{
	public $repo = null;
	public $index = array();
	
	function __construct($repo, $name)
	{
		$this->repo = $repo;
		
		$index_filename = "{$repo->path}/objects/pack/{$name}.idx";
		$pack_filename = "{$repo->path}/objects/pack/{$name}.pack";
		
		$this->index = new GitPackIndex(file_get_contents($index_filename));
	}
}
