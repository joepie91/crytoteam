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

class TicketMessage extends CPHPDatabaseRecordClass
{
	public $table_name = "ticket_messages";
	public $fill_query = "SELECT * FROM ticket_messages WHERE `Id` = :Id";
	public $verify_query = "SELECT * FROM ticket_messages WHERE `Id` = :Id";
	
	public $prototype = array(
		'simplehtml' => array(
			'Body'			=> "Body"
		),
		'boolean' => array(
			'IsFirstMessage'	=> "FirstMessage"
		),
		'numeric' => array(
			'AuthorId'		=> "UserId",
			'TicketId'		=> "TicketId",
			'ProjectId'		=> "ProjectId"
		),
		'timestamp' => array(
			'Date'			=> "Date"
		),
		'boolean' => array(
			'IsEvent'		=> "Event"
		),
		'user' => array(
			'Author'		=> "UserId"
		),
		'ticket' => array(
			'Ticket'		=> "Ticket"
		),
		'project' => array(
			'Project'		=> "ProjectId"
		)
	);
	
	public function __get($name)
	{
		switch($name)
		{
			case "sComponent":
				return $this->GetComponentName();
				break;
			case "sOperation":
				return $this->GetOperationName();
				break;
			default:
				return parent::__get($name);
				break;
		}
	}
	
	private function UnpackEvent()
	{
		if(empty($this->uEventData))
		{
			$this->uEventData = json_decode($this->uBody);
		}
	}
	
	public function GetComponentName()
	{
		$this->UnpackEvent();
		
		switch($this->uEventData->component)
		{
			case STATUS:
				return "status";
			case PRIORITY:
				return "priority";
			case OWNER:
				return "owner";
			default:
				return "unknown";
		}
	}
	
	public function GetOperationName()
	{
		$this->UnpackEvent();
		
		if($this->uEventData->component == OWNER)
		{
			$sEventUser = new User($this->uEventData->operation);
			return $sEventUser->sDisplayName;
		}
		elseif($this->uEventData->component == PRIORITY)
		{
			switch($this->uEventData->operation)
			{
				case PRIORITY_LOWEST:
					return "Lowest";
				case PRIORITY_LOW:
					return "Low";
				case PRIORITY_NORMAL:
					return "Normal";
				case PRIORITY_HIGH:
					return "High";
				case PRIORITY_CRITICAL:
					return "Critical";
				default:
					return "Unknown";
			}
		}
		elseif($this->uEventData->component == STATUS)
		{
			switch($this->uEventData->operation)
			{
				case NEWTICKET:
					return "New";
				case OPEN:
					return "Open";
				case CLOSED:
					return "Closed";
				case INVALID:
					return "Invalid";
				case NEEDS_REVIEW:
					return "Needs Review";
				case IN_PROGRESS:
					return "In Progress";
				default:
					return "Unknown";
			}
		}
	}
}
