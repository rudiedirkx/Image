<?php

require 'Image.php';

$img = new Image('../darude.gif');

$img->resize(0, 150);
$img->save('created-darude.png', IMAGETYPE_PNG, 0, 0777);
$img->output(IMAGETYPE_PNG);

