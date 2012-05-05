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

require(dirname(__FILE__) . "/class.repository.php");
require(dirname(__FILE__) . "/class.object.php");
require(dirname(__FILE__) . "/class.commit.php");
require(dirname(__FILE__) . "/class.actor.php");
?>
