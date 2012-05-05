<?php
require("libgit/base.php");

$s = new GitRepository("/home/occupy/testrepo.git");
pretty_dump($s->GetObject("54e03e490b1bee1c154c3545bf258cab0629ee02"));
pretty_dump($s->GetObject("9d8e0ba4a30f6a5d775a879c42c7de5aed4530c6"));
