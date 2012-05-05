<?php
class GitBlob extends GitObject
{
	function __construct($repo, $headerdata, $data, $sha)
	{
		parent::__construct($repo, $headerdata, $data, $sha);
	}
}
