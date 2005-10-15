<?php
require("php/includes.php");
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
<META http-equiv="Content-Type" content="text/html">
<META name="DESCRIPTION" content="North American QuakeWorld resource website">
<META name="KEYWORDS" content="QuakeWorld"> 
<META name="AUTHOR" content="Viktor Persson"> 
<LINK REL="stylesheet" HREF="css/default.css" TYPE="text/css">
<link rel="Shortcut Icon" type="image/ico" href="favicon.ico">
<TITLE>QuakeWorld.US</TITLE>

<script language="JavaScript" type="text/javascript">
function hideShow(which)
{
  if (!document.getElementById | document.all)
  {
    return ;
  }
  else
  {
    if (document.getElementById)
    {
      oWhich = eval ("document.getElementById('" + which + "')") ;
    }
    else
    {
      oWhich = eval ("document.all." + which) ;
    }
  }
  
  window.focus() ;
  
  if (oWhich.style.display=="none")
  {
    oWhich.style.display="" ;
  }
  else
  {
    oWhich.style.display="none" ;
  }
}

function hideShowAll(which)
{
  hideShow(which + '_1') ;
  hideShow(which + '_2') ;
  hideShow(which + '_3') ;
  hideShow(which + '_4') ;
  hideShow(which + '_5') ;
}

function hideShowColumnMenu(which)
{
  for (i=0; i<<?php print count(player::getPlayersWithColumns()); ?>; i++)
    {
      hideShow('column' + i) ;
    }
}

function initTourneyMenus(which)
{
  if  (which!=2) 
    {
      hideShowAll('tourney2') ;
    }
  if  (which!=3) 
    {
      hideShowAll('tourney3') ;
    }
} 
</script>
</HEAD>

<?php

if (!isset($_GET['column']))
{
  $s1 = 'hideShowColumnMenu();' ;
}

print '<BODY onLoad="' . $s1 . '">' ;
?>

<A name="top"></A>
<TABLE cellspacing="0" cellpadding="0" class="tbl_h100">
<TR>
	<TD>
		<TABLE cellspacing="0" cellpadding="0" class="tbl_h100">
		<TR>
			<TD></TD>
		</TR>
		<TR>
			<TD class="q"></TD>
		</TR>
		</TABLE>
	</TD>
	<TD>
		<TABLE cellspacing="8" cellpadding="0" class="tbl_main">
		<TR>
			<TD colspan="2" class="logo"></TD>
		</TR>
		<TR>
			<TD class="content">

<?php
try
  {
    $page = (empty($_GET["a"])) ? "home" : $_GET["a"];
    if (file_exists("php/$page.php"))
    {
      include "php/$page.php" ;
    }
    elseif (file_exists("php/classes/$page.php"))
    {
      include "php/classes/$page.php" ;
    }
  }
catch(Exception $e) {}
?>


			</TD>
			<TD class="menu">
			<TABLE cellspacing="0" cellpadding="0">
			
				<TR>
					<TD class="menuBreak"></TD>
				</TR>
				
				<TR>
					<TD><A href="?a=home">Home</A></TD>
				</TR>
			
				<TR>
					<TD><a href="#" onclick="hideShowColumnMenu(); return false;">Columns</a></TD>
				</TR>
			
				<?php
				$columns = player::getPlayersWithColumns() ;
			
				for ($i=0; $i<count($columns); $i++)
				{
				  $w = $columns[$i] ;
				  $d = $w->getLastNewsColumnDate() ;
				  $name = '<SMALL>' . substr($w->getValue("name") . ' ' . substr($d,5), 0, 15) . '</SMALL>' ;

				  print '<TR id="column' . $i. '" class=submenu>
                                             <TD><a href="?a=home&amp;column=' . $w->getValue("name") . '"><img src="img/red.gif" alt="">' . $name . '</a></TD>
					</TR>';
				}
				?>
				
				<TR>
					<TD><A href="http://www.quakeworld.us/forum">Forum</A></TD>
				</TR>
				
				<TR>
					<TD class="menuBreak"></TD>
				</TR>
				
				<TR>
					<TD><a href="?a=home&amp;tourney_id=1">NA NQR 2</a></TD>
				</TR>
				
				<TR>
					<TD class="menuBreak"></TD>
				</TR>
				
				<TR>
					<TD><A href="?a=signupTeam">Register</A></TD>
				</TR>
			
				<TR>
					<TD><A href="?a=adminHome">Login</A></TD>
				</TR>
				
				<TR>
					<TD class="menuBreak"></TD>
				</TR>
				
				<TR>
					<TD><A href="?a=servers">Servers</A></TD>
				</TR>
				
				<TR>
					<TD><A href="?a=downloads">Downloads</A></TD>
				</TR>
				
				<TR>
					<TD><A href="?a=links">Links</A></TD>
				</TR>

<?php
if (!util::isNull($_REQUEST['tourney_id']))
{
  $l = '&amp;tourney_id=' . $_REQUEST['tourney_id'] ;
}
elseif (!util::isNull($_REQUEST['column']))
{
  $l = '&amp;column=' . $_REQUEST['column'] ;
}
echo '			      
				<TR>
					<TD><A href="?a=newsarchive' . $l. '">Archive</A></TD>
				</TR>' ;
?>			
				<TR>
					<TD class="menuBreak"></TD>
				</TR>
			
			</TABLE>
			</TD>
		</TR>
		<TR>
			<TD colspan="2" class="bottom" height="30">
			<TABLE cellspacing="0" cellpadding="0">
			<TR>
				<TD><A href="#top">back to top</A></TD>
				<TD align="right"><a href="http://validator.w3.org/check?uri=referer" class="valid">html</A>&nbsp;<a href="http://jigsaw.w3.org/css-validator/">css</A></TD>
			</TR>
			</TABLE>
			</TD>
		</TR>
		</TABLE>
	</TD>
</TR>
<TR>
	<TD></TD>
	<TD class="madeby">website design by <A href="http://www.arcsin.se/">Arcsin Webdesign</A></TD>
</TR>
</TABLE>
</BODY>
</HTML>
