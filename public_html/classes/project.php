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

class Project extends CPHPDatabaseRecordClass
{
	public $table_name = "projects";
	public $fill_query = "SELECT * FROM projects WHERE `Id` = :Id";
	public $verify_query = "SELECT * FROM projects WHERE `Id` = :Id";
	
	public $prototype = array(
		'string' => array(
			'Name'			=> "Name",
			'ShortDescription'	=> "ShortDescription",
			'Slug'			=> "Slug"
		),
		'simplehtml' => array(
			'LongDescription'	=> "LongDescription"
		),
		'numeric' => array(
			'CreatorId'		=> "UserId",
			'LineCount'		=> "LineCount",
			'RepositorySize'	=> "RepositorySize",
			'ContributorCount'	=> "ContributorCount"
		),
		'timestamp' => array(
			'CreationDate'		=> "CreationDate",
			'LastActivity'		=> "LastActivity"
		),
		'boolean' => array(
			'IsActive'		=> "Active",
			'IsPublic'		=> "IsPublic"
		),
		'user' => array(
			'Creator'		=> "CreatorId"
		)
	);
	
	public function MarkActivity($user, $component, $operation, $description = "")
	{
		$sLogEntry = new LogEntry(0);
		$sLogEntry->uComponent = $component;
		$sLogEntry->uOperation = $operation;
		$sLogEntry->uUserId = $user;
		$sLogEntry->uProjectId = $this->sId;
		$sLogEntry->uDescription = $description;
		$sLogEntry->InsertIntoDatabase();
		
		$this->uLastActivity = time();
		$this->InsertIntoDatabase();
	}
}
