<?php
require("include/base.php");

echo(Templater::AdvancedParse("layout", $locale->strings, array(
	"project-name"		=> "Demo project",
	"long-description"	=> "A large, multi-paragraph description of the project would go here.",
	"no-downloads"		=> false,
	"stable-version"	=> "1.5.3",
	"experimental-version"	=> "1.6.1",
	"line-count"		=> "62,671",
	"ticket-count"		=> 12,
	"tickets"		=> array(),
	"more-tickets"		=> false
)));
