<?php

function icon($ext)
{
	$icon = "txt";

	if ($ext=="mvd") $icon = "mvd";
	else if ($ext=="zip"||$ext==".gz"||$ext=="rar"||$ext=="bz2") $icon="zip";
	else if ($ext=="avi"||$ext=="mov"||$ext=="mpg"||$ext=="peg") $icon = "avi";

	return $icon;
}
		

function getFiles($ext)
{
	$root = array("/usr/quake","/usr/www","/usr/quake");
	$basedir = array("/demos/","/files/","/maps/");
	$base = explode("/",$_GET["d"]);
	
	if ($base[0] == "demos") $target = 0;	
	else if ($base[0] == "files") $target = 1;
	else {$target = 2;}

	$path = (empty($_GET["d"])) ? $root[$target].$basedir[$target] : $root[$target]."/".$_GET["d"];
	$GLOBALS['link'] = str_replace($root[$target]."/","",$path);
	$path_parts = explode("/",$GLOBALS['link']);
	$pathlink = '<A href="?a=downloads">Home</A>&nbsp;/&nbsp;';

	for ($i=0;$i<count($path_parts)-1;$i++)
	{
          if ($path_parts[$i]=="..")
            {
              return ;
            }

	  $pathlink .= '<A href="?a=downloads&amp;d=';
	  for ($j=0;$j<$i+1;$j++)
	    {
	      $pathlink .= $path_parts[$j] . '/';
	    }
	  $pathlink .= '">' . ucfirst(strtolower($path_parts[$i])) . '</A>&nbsp;/&nbsp;';
	}

	$dir_handle = opendir($path) or die("Unable to open $path");
	$return = "";
	$files = array();
	$dirs = array();
	
	while ($file = readdir($dir_handle)) 
	{
		if (!is_dir($path.$file) && $file != '.' && $file != '..')
		{
			if ($ext != 'all' && strtolower(substr ($file, -3, 3)) == $ext)
			{
				$files[]='<TR><TD class="file_' . icon(strtolower(substr ($file, -3, 3))) . '"></TD><TD><A href="http://quakeworld.us' . str_replace($root[$target],"",$path) . $file . '">' . $file . '</A></TD></TR>';	
			}
			else
			{
				$files[]='<TR><TD class="file_' . icon(strtolower(substr ($file, -3, 3))) . '"></TD><TD><A href="http://quakeworld.us' . str_replace($root[$target],"",$path) . $file . '">' . $file . '</A></TD></TR>';	
			}
		}
		else if ($file != '.' && $file != '..')
		{
			$dirs[]='<TR><TD class="file_dir"></TD><TD><A href="?a=downloads&amp;d=' . str_replace($root[$target]."/","",$path.$file) . '/">' . $file . '</TD></TR>';
		}
	}
	sort($dirs);
	for ($i=0;$i<count($dirs);$i++)
	{
		$return .= $dirs[$i];
	}
	for ($i=0;$i<count($files);$i++)
	{
		$return .= $files[$i];
	}
	closedir($dir_handle);
	echo '<TR><TD colspan="2" class="downloads_path">' . $pathlink . '</TD></TR>';
	echo '<TR><TD colspan="2" height="5"></TD></TR>';
	echo $return;
}

?>
<TABLE cellspacing="0" cellpadding="1" class="downloads">
<?php
if (empty($_GET["d"]))
{
	echo '<TR><TD colspan="2" class="downloads_path"><A href="?a=downloads">Home</A>&nbsp;/&nbsp;</TD></TR>';
	echo '<TR><TD colspan="2" height="5"></TD></TR>';
	echo '<TR><TD class="file_dir"></TD><TD><A href="?a=downloads&d=demos/">Demos</A></TD></TR>';
        echo '<TR><TD class="file_dir"></TD><TD><A href="?a=downloads&d=equake/">eQuake</A></TD></TR>';
        echo '<TR><TD class="file_dir"></TD><TD><A href="?a=downloads&d=mvdsv/"\>mvdsv</A></TD></TR>';
	echo '<TR><TD class="file_dir"></TD><TD><A href="?a=downloads&d=ctf/">CTF</A></TD></TR>';
        echo '<TR><TD class="file_dir"></TD><TD><A href="?a=downloads&d=dmmaps/">DM Maps</A></TD></TR>';

}
else
{
	getFiles("all");
}
?>
</TABLE>