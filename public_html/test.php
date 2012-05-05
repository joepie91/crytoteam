<?php
require("libgit/base.php");

$repo = new GitRepository("/home/occupy/testrepo.git");
pretty_dump($repo->GetBranch("master")->GetTree());
