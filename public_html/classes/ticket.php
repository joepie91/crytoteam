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

class Ticket extends CPHPDatabaseRecordClass
{
	public $table_name = "tickets";
	public $fill_query = "SELECT * FROM tickets WHERE `Id` = :Id";
	public $verify_query = "SELECT * FROM tickets WHERE `Id` = :Id";
	
	public $prototype = array(
		'string' => array(
			'Subject'		=> "Subject"
		),
		'numeric' => array(
			'Status'		=> "Status",
			'CreatorId'		=> "UserId",
			'OwnerId'		=> "OwnerId",
			'Priority'		=> "Priority",
			'ProjectId'		=> "ProjectId"
		),
		'user' => array(
			'Creator'		=> "UserId",
			'Owner'			=> "OwnerId"
		),
		'project' => array(
			'Project'		=> "ProjectId"
		)
	);
	
	public function __get($name)
	{
		switch($name)
		{
			case "sStatusName":
				return $this->GetStatusName();
				break;
			case "sPriorityName":
				return $this->GetPriorityName();
				break;
			default:
				return parent::__get($name);
				break;
		}
	}
	
	public function GetStatusName()
	{
		switch($this->sStatus)
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
	
	public function GetPriorityName()
	{
		switch($this->sPriority)
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
}
