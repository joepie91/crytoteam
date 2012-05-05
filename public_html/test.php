<?php
require("libgit/base.php");

$repo = new GitRepository("/home/occupy/testrepo.git");
pretty_dump($repo->GetObjectForPath($repo->GetBranch("master")->GetTree(), "public_html/css/images/derp"));
