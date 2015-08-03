<?php
session_start();

function getIgnoreList($file = 'ripignore.txt')
{
    $_SESSION['ignorelist'] = array();
    $files = file($_SESSION['root'] . $file, FILE_IGNORE_NEW_LINES);

    $_SESSION['ignorelist'] = $files;

}

$_SESSION['root'] = dirname(__FILE__) . '\\';
getIgnoreList();

?>