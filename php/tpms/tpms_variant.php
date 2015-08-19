<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title>TPMS processor</title>
</head>
<body>
<a id="top"></a>

<?php
session_start();
ini_set('display_errors', 1);
require_once('functions.php');

if (isset($_GET['mid']) && !empty($_GET['mid'])) {
    if (isset($_GET['url']) && !empty($_GET['url'])) {

        $url = $_GET['url'];
        $mid = $_GET['mid'];

        //navigation links
        echo '<a href="index.php"><<< Show TPMS information for different MID</a>';
        echo '<br /><a href="index.php?mid=' . $mid . '"><<< Return to available TPMS variants for ' . $mid . '</a>';

        if (isset($_GET['section']) && !empty($_GET['section'])) {

            //navigation link
            echo '<br/><a href="tpms_variant.php?mid=' . $mid . '&url=' . urlencode($url) . '"><<< Return to available TPMS sections for chosen variant of ' . $mid . '</a>';

            //get TPMS JSON data for URL
            $urlData  = getUrlData($url);
            $tpmsData = $urlData['data'];

            //store images for TPMS variant to session
            storeTpmsVariantImagesToSession($tpmsData);

            $section = $_GET['section'];

            //show TPMS information based on section chosen by user
            switch ($section) {
                case 'gi':
                    //display general information
                    showGeneralInformation($tpmsData);
                    break;
                case 'tp':
                    //display tyre pressures
                    showTyrePressures($tpmsData);
                    break;
                case 'st':
                    //display special tools
                    showSpecialTools($tpmsData);
                    break;
                case 'tt':
                    //display tightening torques
                    showTighteningTorques($tpmsData);
                    break;
                case 'so':
                    //display system operation
                    showSystemOperation($tpmsData);
                    break;
                case 'pg':
                    //display only procedure groups menu
                    showProcedureGroups($tpmsData, true);
                    echo '<hr>';
                    //display only procedure groups tree
                    showProcedureGroups($tpmsData);
                    break;
                default:
                    //show general information by default
                    showGeneralInformation($tpmsData);
            }
        } else {
            //show links to sections for chosen TPMS variant
            echo '<h1>Select section for chosen variant of ' . $mid . ':</h1>';
            echo '<ul>';
            echo '<li><a href="http://' . $_SERVER['HTTP_HOST'] . '/tpms_variant.php?mid=' . $mid . '&url=' . urlencode($url) . '&section=gi">General information</a>';
            echo '<li><a href="http://' . $_SERVER['HTTP_HOST'] . '/tpms_variant.php?mid=' . $mid . '&url=' . urlencode($url) . '&section=tp">Tyre pressures</a>';
            echo '<li><a href="http://' . $_SERVER['HTTP_HOST'] . '/tpms_variant.php?mid=' . $mid . '&url=' . urlencode($url) . '&section=st">Special tools</a>';
            echo '<li><a href="http://' . $_SERVER['HTTP_HOST'] . '/tpms_variant.php?mid=' . $mid . '&url=' . urlencode($url) . '&section=tt">Tightening torques</a>';
            echo '<li><a href="http://' . $_SERVER['HTTP_HOST'] . '/tpms_variant.php?mid=' . $mid . '&url=' . urlencode($url) . '&section=so">System operation</a>';
            echo '<li><a href="http://' . $_SERVER['HTTP_HOST'] . '/tpms_variant.php?mid=' . $mid . '&url=' . urlencode($url) . '&section=pg">Procedure groups</a>';
            echo '</ul>';
        }
    }
}

?>

</body>
</html>