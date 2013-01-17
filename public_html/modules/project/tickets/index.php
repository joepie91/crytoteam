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

$sPageTitle = "Tickets";
$sCurrentPage = "tickets";
$sPageContents = NewTemplater::Render("project/tickets/index", $locale->strings, array(
	"tickets" => array(
		array(
			"id"			=> 4,
			"title"			=> "This is a sample ticket about some kind of bug.",
			"priority"		=> "High",
			"priority-lowercase"	=> "high",
			"status"		=> "Open",
			"status-lowercase"	=> "open"
		),
		array(
			"id"			=> 3,
			"title"			=> "Some kind of feature suggestion",
			"priority"		=> "Normal",
			"priority-lowercase"	=> "normal",
			"status"		=> "Open",
			"status-lowercase"	=> "open"
		),
		array(
			"id"			=> 5,
			"title"			=> "Aaaaaabsolutely unimportant.",
			"priority"		=> "Low",
			"priority-lowercase"	=> "low",
			"status"		=> "Open",
			"status-lowercase"	=> "open"
		),
		array(
			"id"			=> 1,
			"title"			=> "This is an urgent ticket about something that has been resolved.",
			"priority"		=> "High",
			"priority-lowercase"	=> "high",
			"status"		=> "Closed",
			"status-lowercase"	=> "closed"
		),
		array(
			"id"			=> 2,
			"title"			=> "This is a normal ticket about something that has been resolved.",
			"priority"		=> "Normal",
			"priority-lowercase"	=> "normal",
			"status"		=> "Closed",
			"status-lowercase"	=> "closed"
		),
	)
));
