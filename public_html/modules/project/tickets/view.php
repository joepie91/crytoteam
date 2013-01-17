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

$sCurrentPage = "tickets";

try
{
	$sTicket = new Ticket($router->uParameters[2]);
	
	$sInitialMessage = TicketMessage::CreateFromQuery("SELECT * FROM ticket_messages WHERE `TicketId` = :TicketId AND `FirstMessage` = 1", 
							  array(":TicketId" => $sTicket->sId), 0, true);
	
	$sUpdates = array();
	
	try
	{
		$result = TicketMessage::CreateFromQuery("SELECT * FROM ticket_messages WHERE `TicketId` = :TicketId AND `FirstMessage` = 0 ORDER BY `Date` ASC", 
							 array(":TicketId" => $sTicket->sId), 0);
	}
	catch (NotFoundException $e)
	{
		$result = array();
	}
						 
	foreach($result as $sMessage)
	{
		if($sMessage->sIsEvent)
		{
			$uEventData = json_decode($sMessage->uBody);
			
			$sUpdates[] = array(
				"event"		=> true,
				"user"		=> $sMessage->sAuthor->sDisplayName,
				"component"	=> $sMessage->sComponent,
				"operation"	=> $sMessage->sOperation,
				"date"		=> local_from_unix($sMessage->sDate, $locale->datetime_short)
			);
		}
		else
		{
			$sUpdates[] = array(
				"event"		=> false,
				"author"	=> $sMessage->sAuthor->sDisplayName,
				"body"		=> $sMessage->sBody,
				"date"		=> local_from_unix($sMessage->sDate, $locale->datetime_short)
			);
		}
	}
	
	$sPageContents = NewTemplater::Render("project/tickets/view", $locale->strings, array(
		"title"		=> $sTicket->sSubject,
		"priority"	=> $sTicket->sPriorityName,
		"status"	=> $sTicket->sStatusName,
		"owner"		=> $sTicket->sOwner->sDisplayName,
		"creator"	=> $sTicket->sCreator->sDisplayName,
		"date"		=> local_from_unix($sTicket->sCreationDate, $locale->datetime_short),
		"body"		=> $sInitialMessage->sBody,
		"updates"	=> $sUpdates
	));
}
catch (NotFoundException $e)
{
	pretty_dump($e);
	require("modules/error/404.php");
}
