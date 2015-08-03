<?php
/** 

RIPS - A static source code analyser for vulnerabilities in PHP scripts 
	by Johannes Dahse (johannes.dahse@rub.de)
			
			
Copyright (C) 2012 Johannes Dahse

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; if not, see <http://www.gnu.org/licenses/>.	

**/


	// get all php files from directory, including all subdirectories
	function read_recursiv($path, $scan_subdirs, $result = NULL)
	{  
		if ($result == NULL) {
            $result = array();
        }

		$handle = opendir($path);  

		if ($handle)  
		{  
			while (false !== ($file = readdir($handle)))  
			{  
				if ($file !== '.' && $file !== '..')  
				{  
					$name = $path . '/' . $file; 
                    #echo "<br>$name";
                    $ignorename = str_replace($_SESSION['root'], '', $name);
                    $ignorename = str_replace('\\', '/', $ignorename);
					if (is_dir($name) && $scan_subdirs) 
					{  
                        #echo " is a folder";
                        if (!in_array($ignorename, $_SESSION['ignorelist'])) {
                            $result = read_recursiv($name, true, $result);
                        }
					}
                    #echo " is a file";
                    if(in_array(substr($name, strrpos($name, '.')), $GLOBALS['FILETYPES'])) 
					{  
                        if (!in_array($ignorename, $_SESSION['ignorelist'])) {
                            $result[] = $name;
                        } else {
                            #echo " - IGNORE";
                        }
					}  
				}  
			} 


		}  
		closedir($handle); 
		return $result;  
	}  
	


?>	