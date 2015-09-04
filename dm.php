<?php
session_start();

function getIgnoreList($file = 'ripignore.txt')
{
    $_SESSION['ignorelist'] = array();
    $files = file($_SESSION['root'] . $file, FILE_IGNORE_NEW_LINES);

    $_SESSION['ignorelist'] = $files;

}


function addVuln($vulnType, $fileName)
{

    if (!array_key_exists('stats', $_SESSION) OR !array_key_exists('vuln', $_SESSION['stats']) OR !array_key_exists($vulnType, $_SESSION['stats']['vuln'])) {
        $_SESSION['stats']['vuln'][$vulnType]['count'] = 0;
        $_SESSION['stats']['vuln'][$vulnType]['issues'] = array();
    }

    if (!array_key_exists('stats', $_SESSION) OR !array_key_exists('files', $_SESSION['stats']) OR !array_key_exists($fileName, $_SESSION['stats']['files'])) {
        $_SESSION['stats']['files'][$fileName]['count'] = 0;
        $_SESSION['stats']['files'][$fileName]['issues'] = array();
    }

    $_SESSION['stats']['vuln'][$vulnType]['count']++;
    $_SESSION['stats']['vuln'][$vulnType]['issues'][] = $fileName;
    $_SESSION['stats']['files'][$fileName]['count']++;
    $_SESSION['stats']['files'][$fileName]['issues'][] = $vulnType;

}


function outputfiles()
{
    $_SESSION['stats']['files'] = arraySortByField($_SESSION['stats']['files'], 'count', 'DESC', true);
    echo '<ul>';
    foreach($_SESSION['stats']['files'] as $file => $data) {
        echo '<li><span class="count clickable jsShowItems">'.$data['count'].'</span> <a href="?url='.$file.'">'.$file.'</a>';
        $thisIssueCount = array();
        foreach($data['issues'] as $issue) {
            if (!array_key_exists($issue, $thisIssueCount)) {
                $thisIssueCount[$issue] = 0;
            }
            $thisIssueCount[$issue]++;
        }
        arsort($thisIssueCount);
        echo '<span class="items hide">';
        foreach($thisIssueCount as $name => $count) {
            echo '<span class="item"><span class="count">'.$count.'</span><a href="?url='.$file.'&type='.$name.'">'.$name.'</a></span>';
        }
        echo '</span>';
        echo '</li>';
    }
    echo '</ul>';

}

function outputissues()
{
    $_SESSION['stats']['vuln'] = arraySortByField($_SESSION['stats']['vuln'], 'count', 'DESC', true);
    echo '<ul>';
    foreach($_SESSION['stats']['vuln'] as $issue => $data) {
        echo '<li><span class="count clickable jsShowItems">'.$data['count'].'</span> <a href="?url='.$_SESSION['get_url'].'&type='.$issue.'">'.$issue.'</a>';
        $thisIssueCount = array();
        foreach($data['issues'] as $issue2) {
            if (!array_key_exists($issue2, $thisIssueCount)) {
                $thisIssueCount[$issue2] = 0;
            }
            $thisIssueCount[$issue2]++;
        }
        arsort($thisIssueCount);
        echo '<span class="items hide">';
        foreach($thisIssueCount as $name => $count) {
            echo '<span class="item"><span class="count">'.$count.'</span><a href="?url='.$name.'&type='.$issue.'">'.$name.'</a></span>';
        }
        echo '</span>';
        echo '</li>';
    }
    echo '</ul>';

}

function arraySortByField($aArrayToSort, $sKeyToSort, $sDir = 'a', $bIsNumber = false)
{

    foreach ($aArrayToSort as $key => $data) {
        if (!array_key_exists($sKeyToSort, $aArrayToSort[$key])) {
            return $aArrayToSort;
        }
        $aArrayTmp[$key] = $aArrayToSort[$key][$sKeyToSort] . '----' . $key;
    }
    if ($bIsNumber == false) {
        if ($sDir == 'a') {
            asort($aArrayTmp);
        } else {
            rsort($aArrayTmp);
        }
    } else {
        natsort($aArrayTmp);
        if ($sDir != 'a') {
            $aArrayTmp = array_reverse($aArrayTmp);
        }
    }

    foreach ($aArrayTmp as $key => $val) {
        $aArrayToSortTmp = explode('----', $val);
        $aSortedArray[$aArrayToSortTmp[1]] = $aArrayToSort[$aArrayToSortTmp[1]];
    }
    return $aSortedArray;

}



$_SESSION['root'] = dirname(__FILE__) . '\\';


?>