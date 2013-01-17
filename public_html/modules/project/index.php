<?php
/*
 * Cryto Team is more free software. It is licensed under the WTFPL, which
 * allows you to do pretty much anything with it, without having to
 * ask permission. Commercial use is allowed, and no attribution is
 * required. We do politely request that you share your modifications
 * to benefit other developers, but you are under no enforced
 * obligation to do so :)
 * 
 * Please read the accompanying LICENSE document for the full WTFPL
 * licensing text.
 */

if(!isset($_APP)) { die("Unauthorized."); }

$sPageTitle = "Overview";
$sCurrentPage = "overview";
$sPageContents = Templater::AdvancedParse("project/index", $locale->strings, array(
	"long-description"	=> $sProject->sLongDescription,
	"no-downloads"		=> false,
	"stable-version"	=> "1.5.3",
	"experimental-version"	=> "1.6.1",
	"line-count"		=> "62,671",
	"ticket-count"		=> 12,
	"tickets"		=> array(),
	"more-tickets"		=> false
));
