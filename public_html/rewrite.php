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

require("include/base.php");

$sPageTitle = "";
$sPageContents = "";

$router = new CPHPRouter();
$router->allow_slash = true;
$router->ignore_query = true;

$router->routes = array(
	0 => array(
		"^/$"						=> "modules/home/index.php",
		"^/project/([a-zA-Z0-9_-]+)$"			=> array("target" 		=> "modules/project/index.php",
									 "authenticator" 	=> "authenticators/project.php",
									 "auth_error"		=> "modules/error/project.php",
									 "_page_type"		=> "project"),
		"^/project/([a-zA-Z0-9_-]+)/tickets$"		=> array("target" 		=> "modules/project/tickets/index.php",
									 "authenticator" 	=> "authenticators/project.php",
									 "auth_error"		=> "modules/error/project.php",
									 "_page_type"		=> "project"),
		"^/project/([a-zA-Z0-9_-]+)/ticket/([0-9]+)$"	=> array("target" 		=> "modules/project/tickets/view.php",
									 "authenticator" 	=> "authenticators/project.php",
									 "auth_error"		=> "modules/error/project.php",
									 "_page_type"		=> "project"),
	)
);

$router->RouteRequest();

if(empty($router->uVariables['page_type']))
{
	$sContents = NewTemplater::Render("home/layout", $locale->strings, array(
		"contents"	=> $sPageContents
	));
}
elseif($router->uVariables['page_type'] == "project")
{
	$sContents = NewTemplater::Render("project/layout", $locale->strings, array(
		"contents"	=> $sPageContents
	));
}
else
{
	die();
}

echo(NewTemplater::Render("layout", $locale->strings, array(
	"contents"	=> $sContents
)));

