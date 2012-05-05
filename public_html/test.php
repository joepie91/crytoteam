<?php
require("libgit/base.php");

$repo = new GitRepository("/home/occupy/testrepo.git");
pretty_dump($repo->GetTag("1.0")->GetCommit());

/*pretty_dump($s->GetObject("54e03e490b1bee1c154c3545bf258cab0629ee02"));
pretty_dump($s->GetObject("98d99489382a3541e6783bb2083554785f3eb72a"));
pretty_dump($s->GetObject("9d8e0ba4a30f6a5d775a879c42c7de5aed4530c6"));
pretty_dump($s->GetObject("710bfee4440517255475bf7c5454c0bdbb3b3e56"));
pretty_dump($s->GetObject("ab2d7159831970ca08f2c9fc5c0fa34b17d572e9"));
pretty_dump($s->GetObject("cdebafd9e7426d0243cfb0a4ea2116a8b97b01e7"));*/
