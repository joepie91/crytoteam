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

$constants = array(
	"PRIORITY_LOWEST"	=> 1,
	"PRIORITY_LOW"		=> 2,
	"PRIORITY_NORMAL"	=> 3,
	"PRIORITY_HIGH"		=> 4,
	"PRIORITY_CRITICAL"	=> 5,
	
	"NEWTICKET"		=> 1,
	"OPEN"			=> 2,
	"CLOSED"		=> 3,
	"INVALID"		=> 4,
	"NEEDS_REVIEW"		=> 5,
	"IN_PROGRESS"		=> 6,
	
	"STATUS"		=> 1,
	"PRIORITY"		=> 2,
	"OWNER"			=> 3,
	
	"ATTACHMENT_FILE"	=> 1,
	"ATTACHMENT_COMMIT"	=> 2,
	"ATTACHMENT_TICKET"	=> 3,
	
	"MESSAGE_FIRST"		=> 1,
	"MESSAGE_RESPONSE"	=> 2,
	"MESSAGE_CHANGE"	=> 3,
	
	"TICKET"		=> 1,
	"DOWNLOAD"		=> 2,
	"WIKIPAGE"		=> 3,
	"COMMIT"		=> 4,
	"FORUMPOST"		=> 5,
	"INVITATION"		=> 6,
	"DESCRIPTION"		=> 7,
	
	"CREATE"		=> 1,
	"DELETE"		=> 2,
	"UPDATE"		=> 3,
	"ATTACH"		=> 4,
	"REPLY"			=> 5
);

foreach($constants as $key => $value)
{
	define($key, $value, true);
}
