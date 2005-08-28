<?php
require("php/dbConnect.php");
$page = (empty($_GET["a"])) ? "home" : $_GET["a"];
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
</HEAD>
<BODY>
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
			<?php include("docs/$page.php");?>
			</TD>
			<TD class="menu">
			<TABLE cellspacing="0" cellpadding="0">
			<TR>
				<TD><A href="?a=home">Home</A></TD>
			</TR>
			<TR>
				<TD class="menuBreak"></TD>
			</TR>
			<TR>
				<TD><A href="?a=newsarchive">Archive</A></TD>
			</TR>
                        <TR>
                                <TD class="menuBreak"></TD>
                        </TR>

                        <TR>
                                <TD><A href="http://www.quakeworld.us/forum">Forum</A></TD>
                        </TR>
			<TR>
				<TD class="menuBreak"></TD>
			</TR>
			<TR>
				<TD><A href="?a=servers">Servers</A></TD>
			</TR>
			<TR>
				<TD class="menuBreak"></TD>
			</TR>
			<TR>
				<TD><A href="?a=downloads">Downloads</A></TD>
			</TR>
			<TR>
				<TD class="menuBreak"></TD>
			</TR>
                  <TR>
                        <TD><A href="?a=tourneyHome&tourney_id=2">NA NQR 2</A></TD>
                  </TR>
                  <TR>
                        <TD class="menuBreak"></TD>
                  </TR>
			<TR>
				<TD><A href="?a=links">Links</A></TD>
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
	<TD class="madeby">website by <A href="http://www.arcsin.se/">Arcsin Webdesign</A></TD>
</TD>
</TABLE>
</BODY>
</HTML>