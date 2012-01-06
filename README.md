# Image resizer

Simple!

	$img = new Image('../some_image.jpg');

	$img->resize(0, 150); // height => 150
	$img->save('some_image.png', IMAGETYPE_PNG, 0, 0777); // save as PNG
	// and / or
	$img->output(IMAGETYPE_JPEG); // show as JPG
