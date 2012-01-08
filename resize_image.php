<?php

require 'Image.php';

$img = new Image('dansje.png');
$img->background_color = array(255, 255, 255);

//$img->resize(0, 150);
//$img->save('created/darude.png', IMAGETYPE_PNG, array('chmod' => 0777));

$img->scale(1.5);
$img->output(IMAGETYPE_PNG);

