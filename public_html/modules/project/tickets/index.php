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

$sTickets = array();

try
{
	$result = Ticket::CreateFromQuery("SELECT * FROM tickets WHERE `ProjectId` = :ProjectId", array(":ProjectId" => $sProject->sId));
}
catch (NotFoundException $e)
{
	$result = array();
}

foreach($result as $sTicket)
{
	$sTickets[] = array(
		"id"			=> $sTicket->sId,
		"title"			=> $sTicket->sSubject,
		"priority"		=> $sTicket->sPriorityName,
		"priority-lowercase"	=> strtolower($sTicket->sPriorityName),
		"status"		=> $sTicket->sStatusName,
		"status-lowercase"	=> strtolower($sTicket->sStatusName)
	);
}

$sPageContents = NewTemplater::Render("project/tickets/index", $locale->strings, array(
	"tickets" => $sTickets
));
