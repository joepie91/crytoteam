<?php
function pretty_dump($input)
{
	ob_start();
	
	var_dump($input);
	
	$output = ob_get_contents();
	ob_end_clean();
	
	while(preg_match("/^[ ]*[ ]/m", $output) == 1)
	{
		$output = preg_replace("/^([ ]*)[ ]/m", "$1&nbsp;&nbsp;&nbsp;", $output);
	}
	
	$output = nl2br($output);
	
	echo($output);
}

function sha1_from_bin($bin)
{
	return bin2hex($bin);
}

class GitBranchNotFoundException extends Exception {}
class GitTagNotFoundException extends Exception {}
class GitInvalidOriginException extends Exception {}
class GitInvalidElementException extends Exception {}
class GitPathNotFoundException extends Exception {}

require(dirname(__FILE__) . "/class.repository.php");
require(dirname(__FILE__) . "/class.branch.php");
require(dirname(__FILE__) . "/class.object.php");
require(dirname(__FILE__) . "/class.blob.php");
require(dirname(__FILE__) . "/class.tag.php");
require(dirname(__FILE__) . "/class.tree.php");
require(dirname(__FILE__) . "/class.tree.element.php");
require(dirname(__FILE__) . "/class.commit.php");
require(dirname(__FILE__) . "/class.actor.php");
?>
