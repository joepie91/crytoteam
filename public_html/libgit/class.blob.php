<?php
class GitBlob extends GitObject
{
	function __construct($repo, $headerdata, $data)
	{
		parent::__construct($repo, $headerdata, $data);
	}
}
