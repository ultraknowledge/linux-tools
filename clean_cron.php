<?php
$cron_path  = "/etc/cron.d";
$backup_dir  = $_SERVER['HOME']. "/ukn_cron_bak";

echo "=======================================
	UKn Cron File Cleaner
=======================================\n";

if ( !isset($_SERVER['argv'][1]) )
{
  echo ("ERROR: Missing Cron Script File! %php clean_cron.php cron-file \n\n");

  if ($handle = opendir($cron_path)) 
  {
    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != "..") {
            echo " - $entry\n";
        }
    }
    closedir($handle);
  }
  die("...\n");
}  
$cron_file 		= $cron_path ."/". $_SERVER['argv'][1];  
$backup_file 	= $backup_dir ."/". $_SERVER['argv'][1] ."_". date("Y-m-d_Hi");
  
if ( !file_exists($cron_file ) )
  die("ERROR: Cron File Not Found: $cron_file\n");
  
if ( !is_writable($cron_file) )
  die("Error: No write permissions to $cron_file \n");
  


# Backup file
if ( !file_exists($backup_dir) )
{
	if ( mkdir($backup_dir)  === FALSE)
		die("ERROR: Unable to create backup directory: $backup_dir");
}

echo "Creating backup: $backup_file \n";
if ( copy($cron_file, $backup_file) === FALSE )
	die("Unable to create backup");

$new_cron_content = '';

$fp = @fopen($cron_file, "r");
if ($fp) {
    while (($line = fgets($fp, 4096)) !== false) {
    	$line = trim($line) . PHP_EOL;
    	$new_cron_content .= $line;
        // echo $line;
    }
    if (!feof($fp)) {
        echo "Error: unexpected fgets() fail\n";
    }
    fclose($fp);
}

if ( !empty($new_cron_content) )
{
	if ( file_put_contents($cron_file, $new_cron_content) )
	{
		echo "Success! $cron_file cleaned! \n";
	}else
		die("ERROR: Could not write clened cron file\n");
}else{
	die("ERROR: Could not write clened cron file. Empty Content\n");
}