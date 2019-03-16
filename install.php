<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Warbler Installation</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/skeleton/2.0.4/skeleton.css" rel="stylesheet">
  </head>
  <body>
<div class="container">
  <div class="row">
    <div class="one column"></div>
    <div class="ten columns">
	
<br />

<h5>PHP requirements</h5>
<?php
extension_check(array( 
	'openssl',
	'pdo',
	'curl',
	'pdo_mysql', 
	'mbstring', 
	'tokenizer',
	'XML',
	'JSON',
	'PCRE',
	'GD',
	'fileinfo',
	'zip',
));
function extension_check($extensions) {
	$fail = '';
	$pass = '';
	
	if(version_compare(PHP_VERSION, '5.6.4', '<')) {
		$fail .= '<li style="color: red">You need<strong> PHP 5.6.4</strong> (or greater). You have PHP '.PHP_VERSION.'</li>';
	}
	
	if(strtolower(ini_get('short_open_tag')) == 'off') {
		$fail .= '<li style="color: red">This software requires to have short tags enabled ("short_open_tag = On")</li>';
	}
	
	foreach($extensions as $extension) {
		if(!extension_loaded($extension)) {
			$fail .= '<li style="color: red"> You are missing the <strong>'.$extension.'</strong> extension</li>';
		}
	}
	
	if($fail) {
		echo '<p><strong>Your server does not meet the following requirements in order to install Warbler.</strong>';
		echo '<br>The following requirements failed, please contact your hosting provider in order to receive assistance with meeting the system requirements for Warbler:';
		echo '<ul style="color: red">'.$fail.'</ul></p>';
		echo 'The following requirements were successfully met:';
		echo '<ul>'.$pass.'</ul>';
		die();
	} else {
		echo '<p><strong></strong> Your PHP version of '.PHP_VERSION.' meets the requirements for Warbler.</p>';
		echo '<ul>'.$pass.'</ul>';
	}
}
?>

<h5>Unzipping</h5>
<?php
umask(0);
$zip = new ZipArchive;
if ($zip->open('warbler.zip') === TRUE) {
    $zip->extractTo(__DIR__);
    $zip->close();
	
	@unlink('warbler.zip');
    echo 'Successfully unzipped.';
	
	@chmod(__DIR__."/bootstrap/cache", 0777);
	@chmod(__DIR__."/storage", 0777);
	@symlink(__DIR__.'/public/fonts', __DIR__.'/fonts');
	
} else {
    echo '<span style="color: red">Unable to unzip. Please make sure you have the correct permissions for this file/folder.<br />If you have trouble, please contact us on contact@trywarbler.com.</span>';
	die();
}

?>
	<small>
		<p>* Remember to have mod_rewrite enabled, enable short_open_tags, give execute permissions to resources/ffmpeg/linux binaries and give write permissions to folders (storage and bootstrap/cache)<br />
		chmod -R 777 bootstrap/cache; chmod -R 777 storage</p>
	</small>

	<a href="/install" class="button button-primary">Click here to start installing</a>

	
	</div>
    <div class="one column"></div>
  </div>
  </body>
</html>