<?php
require("libgit/base.php");

$s = new GitRepository("/home/occupy/testrepo.git");
pretty_dump($s->GetObject("54e03e490b1bee1c154c3545bf258cab0629ee02"));
