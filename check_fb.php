<?php
$content = file_get_contents('storage/app/tmp/fb_metadata.json');
echo "Length: " . strlen($content) . PHP_EOL;
$m = json_decode($content, true);
echo json_last_error_msg() . PHP_EOL;
