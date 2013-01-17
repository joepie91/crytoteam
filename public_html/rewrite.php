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

/* Define the different routes for the application */

$routes = array(
	"home" => array(
		"^/$"						=> "modules/home/index.php"
	),
	"project" => array(
		"^/project/([a-zA-Z0-9_-]+)$"			=> "modules/project/index.php",
		"^/project/([a-zA-Z0-9_-]+)/tickets$"		=> "modules/project/tickets/index.php",
		"^/project/([a-zA-Z0-9_-]+)/ticket/([0-9]+)$"	=> "modules/project/tickets/view.php"
	)
);

/* Define the preset values for the route "categories" */

$presets = array(
	"home" => array(
		"_page_type"	=> "home"
	),
	"project" => array(
		"_page_type"	=> "project",
		"authenticator"	=> "authenticators/project.php",
		"auth_error"	=> "modules/error/project.php"
	)
);

/* Generate a routing table */

$router = new CPHPRouter();
$router->allow_slash = true;
$router->ignore_query = true;
$router->routes = array(0 => array());

foreach($routes as $category => $items)
{
	foreach($items as $route => $target)
	{
		$router->routes[0][$route] = $presets[$category];
		$router->routes[0][$route]['target'] = $target;
	}
}

/* Route the actual request */

try
{
	$router->RouteRequest();
}
catch (RouterException $e)
{
	http_status_code(404);
	$sPageContents = NewTemplater::Render("error/404", $locale->strings, array());
}

/* Render the resulting page */

if(empty($router->uVariables['page_type']) || $router->uVariables['page_type'] == "home")
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

