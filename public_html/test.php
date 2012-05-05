<?php
require("libgit/base.php");

$repo = new GitRepository("/home/occupy/testrepo.git");
pretty_dump($repo->GetTags());

/*$pack = new GitPack($repo, "pack-8503a2b8cf6e60831dd012afd4d486eb1eddfef8");
pretty_dump($pack->UnpackObject("a6269a2ffd269289d7d026818511fab88718feff"));*/
//pretty_dump($repo->GetObjectForPath($repo->GetBranch("master")->GetTree(), "public_html/css/images/derp"));
