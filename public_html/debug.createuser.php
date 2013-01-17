<?php
require("include/base.php");

$sUser = new User(0);
$sUser->uUsername = "test";
$sUser->uPassword = "test";
$sUser->uDisplayName = "Test user";
$sUser->uEmailAddress = "test@test.com";
$sUser->uIsActivated = true;
$sUser->uIsAdmin = true;
$sUser->GenerateSalt();
$sUser->GenerateHash();
$sUser->InsertIntoDatabase();
