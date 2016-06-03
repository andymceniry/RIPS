<?php
/** 

RIPS - A static source code analyser for vulnerabilities in PHP scripts 
	by Johannes Dahse (johannes.dahse@rub.de)
			

Copyright (C) 2012 Johannes Dahse

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; if not, see <http://www.gnu.org/licenses/>.		

**/
require_once 'dm.php';
include_once('dm_functions.php');
unset($_SESSION['stats']);
$_SESSION['get_url'] = isset($_GET['url']) ? $_GET['url'] : false;
$_SESSION['get_type'] = isset($_GET['type']) ? $_GET['type'] : false;

getIgnoreList();

include 'config/general.php';

if (isset($_GET['filetosniff'])) {

    //  check for open file
    if (array_key_exists('view', $_GET) AND $_GET['view'] != '') {
        $aTmp = explode('&view=', 'http://'.$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        $return_url = $aTmp[0];
        exec(urldecode($_GET['view']));
        header("location: $return_url");
        die();
    }

    $_GET['url'] = $_GET['path'] .'/'.$_GET['filetosniff'];
    $_SESSION['geturl'] = $_GET['url'];
}

$default_path = isset($_GET['url']) ? $_GET['url'] : $default_path;
$default_stylesheet = 'notepad';


?><html>
<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" type="text/css" href="css/rips.css" />
	<?php

	foreach($stylesheets as $stylesheet)
	{
		echo "\t<link type=\"text/css\" href=\"css/$stylesheet.css\" rel=\"";
		if($stylesheet != $default_stylesheet) echo "alternate ";
		echo "stylesheet\" title=\"$stylesheet\" />\n";
	}
	?>
	<link rel="stylesheet" type="text/css" href="css/dm.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="js/jquery-1.11.3.min.js"><\/script>')</script>
	<script src="js/script.js"></script>
	<script src="js/exploit.js"></script>
	<script src="js/hotpatch.js"></script>
	<script src="js/netron.js"></script>
	<title>RIPS - A static source code analyser for vulnerabilities in PHP scripts</title>
</head>
<body>

   
    
<div class="menu hide">
	<div style="float:left; width:100%;">
	<table width="100%">
	<tr><td width="75%" nowrap>
		<table class="menutable" width="50%" style="float:left;">
		<tr>
			<td nowrap><b>path / file:</b></td>
			<td colspan="3" nowrap><input type="text" size=80 id="location" value="<?php echo $default_path; ?>" title="enter path to PHP file(s)" placeholder="/var/www/">
			</td>
			<td nowrap><input type="checkbox" id="subdirs" value="1" title="check to scan subdirectories" checked/>subdirs</td>
            <td><input type="button" value="scan" style="width:100%" class="Button" onClick="scan(false);" title="start scan" /></td>
		</tr>
		<tr class="hide">
			<td nowrap>verbosity level:</td>
			<td nowrap>
				<select id="verbosity" style="width:100%" title="select verbosity level">
					<?php 
					
						$verbosities = array(
							1 => '1. user tainted only',
							2 => '2. file/DB tainted +1',
							3 => '3. show secured +1,2',
							4 => '4. untainted +1,2,3',
							5 => '5. debug mode'
						);
						
						foreach($verbosities as $level=>$description)
						{
							echo "<option value=\"$level\">$description</option>\n";							
						}
					?>
				</select>
			</td>
			<td align="right" nowrap>
			vuln type:
			</td>
			<td>
				<select id="vector" style="width:100%" title="select vulnerability type to scan">
					<?php 
					
						$vectors = array(
							'all' 			=> 'All',
							'server' 		=> 'All server-side',							
							'code' 			=> '- Code Execution',
							'exec' 			=> '- Command Execution',
							'file_read' 	=> '- File Disclosure',
							'file_include' 	=> '- File Inclusion',							
							'file_affect' 	=> '- File Manipulation',
							'ldap' 			=> '- LDAP Injection',
							'unserialize' 	=> '- PHP Object Injection',
							'connect'		=> '- Protocol Injection',							
							'ri'		 	=> '- Reflection Injection',
							'database' 		=> '- SQL Injection',
							'xpath' 		=> '- XPath Injection',
							'other' 		=> '- other',
							'client' 		=> 'All client-side',
							'xss' 			=> '- Cross-Site Scripting',
							'httpheader'	=> '- HTTP Response Splitting',
							'fixation'		=> '- Session Fixation',
							//'crypto'		=> 'Crypto hints'
						);
						
						foreach($vectors as $vector=>$description)
						{
							echo "<option value=\"$vector\" ";
							if($vector == $default_vector) echo 'selected';
							echo ">$description</option>\n";
						}
					?>
				</select>
			</td>
			<td><input type="button" value="scan" style="width:100%" class="Button" onClick="scan(false);" title="start scan" /></td>
		</tr>
		<tr class="hide">
			<td nowrap>code style:</td>
			<td nowrap>
				<select name="stylesheet" id="css" onChange="setActiveStyleSheet(this.value);" style="width:49%" title="select color schema for scan result">
					<?php 
						foreach($stylesheets as $stylesheet)
						{
							echo "<option value=\"$stylesheet\" ";
							if($stylesheet == $default_stylesheet) echo 'selected';
							echo ">$stylesheet</option>\n";
						}
					?>	
				</select>
				<select id="treestyle" style="width:49%" title="select direction of code flow in scan result">
					<option value="1">bottom-up</option>
					<option value="2">top-down</option>
				</select>	
			</td>	
			<td align="right">
				/regex/:
			</td>
			<td>
				<input type="text" id="search" style="width:100%" />
			</td>
			<td>
				<input type="button" class="Button" style="width:100%" value="search" onClick="search()" title="search code by regular expression" />
			</td>
		</tr>
		</table>
		<div id="options" style="margin-top:-10px; display:none; text-align:center;" class="hide">
			<p class="textcolor">windows</p>
			<input type="button" class="Button" style="width:50px" value="files" onClick="openWindow(5);eval(document.getElementById('filegraph_code').innerHTML);" title="show list of scanned files" />
			<input type="button" class="Button" style="width:80px" value="user input" onClick="openWindow(4)" title="show list of user input" /><br />
			<input type="button" class="Button" style="width:50px" value="stats" onClick="document.getElementById('stats').style.display='block';" title="show scan statistics" />
			<input type="button" class="Button" style="width:80px" value="functions" onClick="openWindow(3);eval(document.getElementById('functiongraph_code').innerHTML);" title="show list of user-defined functions" />
		</div>
	</td>
	<td width="25%" align="center" valign="center" nowrap class="hide">
		<!-- Logo by Gareth Heyes -->
		<div class="logo"><a id="logo" href="http://sourceforge.net/projects/rips-scanner/files/" target="_blank" title="get latest version"><?php echo VERSION ?></a></div>
	</td></tr>
	</table>
	</div>
	
	<div style="clear:left;"></div>
</div>
<div class="menushade hide"></div>
<?php



        if (isset($_GET['dir'])) {
            if ($_GET['dir'] == 'previous') {
                $dir = dirname($_GET['path']);    
            } elseif ($_GET['dir'] == 'current') {
                $dir = $_GET['path'];    
            } elseif ($_GET['dir'] == 'next') {    
                $dir = $_GET['path'] . '/' . $_GET['dir_name'];
            }
        } else {
            $dir = dirname(getcwd());
        }
        
        if (isset($_GET['filetosniff'])) {

    $file_anchor = str_replace('\\', '/', str_replace('//', '/', $dir)).'/'.$_GET['filetosniff'];
    $file_link = "explorer+" . urlencode(str_replace('/', '\\', str_replace('//', '/', $dir . '\\' . $_GET['filetosniff'])));


    #echo '<div class="infopath clearfix"><p>' . $file_anchor.'</p>';
    echo '<div class="infopath clearfix"><p><a href="http://'.$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'].'&view='.$file_link.'">' . $file_anchor .'</a></p>';
?>
        <form action="<?php echo basename(__FILE__); ?>" method="get" class="header-back-btn">
        <input type="hidden" name="path" value="<?php echo $dir; ?>" />    
        <input type="hidden" name="dir" value="previous" />
        <input type="image" src="img/back.png" class="submit_back" onclick="window.history.back();" />
        </form>
        <?php

    echo '</div>';

echo '<div id="dmscanning" class="report"><pre><div class="report_summary"> SCANNING... </div></pre></div>';

    }
    ?>
<div class="scanning" id="scanning">scanning ...
<div class="scanned" id="scanned"></div>
</div>




<div id="result">
	
    <?php
    if (!isset($_GET['url'])) {
        
        if (isset($_GET['dir'])) {
            if ($_GET['dir'] == 'previous') {
                $dir = dirname($_GET['path']);    
            } elseif ($_GET['dir'] == 'current') {
                $dir = $_GET['path'];    
            } elseif ($_GET['dir'] == 'next') {    
                $dir = $_GET['path'] . '/' . $_GET['dir_name'];
            }
        } else {
            $dir = dirname(getcwd());
        }

        
        
if (is_dir($dir) AND $handle = opendir($dir)) {
echo '<div class="infopath clearfix"><p>' . str_replace('\\', '/', $dir).'</p>';
    if ($dir != dirname(getcwd())) {
        ?>
        <form action="<?php echo basename(__FILE__); ?>" method="get" class="header-back-btn">
        <input type="hidden" name="path" value="<?php echo $dir; ?>" />    
        <input type="hidden" name="dir" value="previous" />
        <input type="image" src="img/back.png" class="submit_back" />
        </form>
        <?php
    }
    echo '</div>';

    echo '<div class="entry_row_holder">';

    $extensionstosniff = array('php');
    
    $folders = array();
    $files = array();
    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != ".." && $entry != "webcodesniffer") {
            if (is_dir($dir."/".$entry) === true) {
                    $folders[] = $entry;
                } else {
                    if (in_array(pathinfo($dir."/".$entry, PATHINFO_EXTENSION), $extensionstosniff)) {
                        $files[] = $entry;
                    }
            }
        }
    }


    sort($folders);
    foreach($folders as $entry) {
        ?>
        <div class='entry_row_dir'>
            <input type="hidden" name="dir" value="next" />
            <a class="folder_link" href="?path=<?php echo $dir;?>&dir=next&dir_name=<?php echo $entry; ?>"/><?php echo $entry; ?></a>
        </div>
        <?php 
    }


    sort($files);
    foreach($files as $entry) {
            ?>
            <div class='entry_row_filetosniff'>
                <div class='entry_name'><a class="file_link" href="?path=<?php echo $dir;?>&standard=DM&sniff=TEST&dir=current&filetosniff=<?php echo $entry; ?>"/><?php echo $entry; ?></a></div>
                <div class="entry_history">
                <?php
                $filename = $dir.'/'.$entry;
                $file_last_change = date("F d Y H:i:s.", filemtime($filename));
                $log_filename = getLogFilename($filename);
                $log_filename = 'logs/'.urlencode($log_filename);

                if (file_exists($log_filename)) {
                    $fp = fopen($log_filename, 'r');
                    $fp_content = array();
                    while(! feof($fp)) {
                        $fp_content[] = fgets($fp);
                    }
                    fclose($fp);
                    if (date('U', strtotime($fp_content[0])) != date('U', strtotime($file_last_change))) {
                        echo '<span class="out-of-date">out of date</span>';
                    } else {
                        if ($fp_content[1] == 0 AND $fp_content[2] == 0) {
                            echo '<span class="all-good">all good</span>';
                        } else {
                            echo '<span class="warning">'.$fp_content[2].'</span>';
                            echo '<span class="error">'.$fp_content[1].'</span>';
                        }
                    }
                } else {
                    echo '<span class="not-tested">not tested</span>';
                }

                ?>
                </div>
                <br style='clear:both;' />
            </div>
            <?php
    }

    if (count($folders) < 1 AND count($files) < 1) {
        echo '<p><b> &nbsp; &nbsp; &nbsp; no matching files or folders found.</b></p>';
    }

    echo '</div>';
} else {
    echo "<p>Invalid Directory '$dir'</p>";
    echo "<p>Redirecting...</p>";
}



    ?>

	<?php
    }
    ?>
</div>

<script src="js/dm.js"></script>
</body>
</html>

<?php
if (isset($_GET['url'])) {
?>
<script>scan(false);</script>
<?php
}
?>