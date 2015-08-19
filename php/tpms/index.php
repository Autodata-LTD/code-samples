<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title>TPMS processor</title>
</head>
<body>

<?php
ini_set('display_errors', 1);
require_once('functions.php');

if (isset($_GET['mid']) && !empty($_GET['mid'])) {

    //navigation link
    echo '<a href="index.php"><<< Show TPMS information for different MID</a>';

    $mid = $_GET['mid'];

    //URL to get TPMS information for given MID
    $url = 'http://integration.api-v1-paywk2tn.autodata-group.com/v1/vehicles/' . $mid . '/tpms?country-code=gb';

    //get TPMS JSON data for URL
    $urlData = getUrlData($url);
    $tpmsData = $urlData['data'];

    //count TPMS variants for given MID
    $tpmsVariantsNumber = count($tpmsData);

    //show list with TPMS variants for MID
    if ($tpmsVariantsNumber > 0) {
        echo '<h1>MID ' . $mid . ' has ' . $tpmsVariantsNumber . ' TPMS variants. Please select required TPMS variant from the following list:</h1>';
        echo '<ul>';
        foreach ($tpmsData as $key => $item) {
            $tpmsVariantsCounter = $key + 1;
            echo '<li>';
            echo '<a href="http://' . $_SERVER['HTTP_HOST'] . '/tpms_variant.php?mid=' . $mid . '&url=' . urlencode('http://integration.api-v1-paywk2tn.autodata-group.com' . $item['href']) . '">';
            echo 'Variant ' . $tpmsVariantsCounter;
            echo (!empty($item['tpms_description'])) ? ' - (' . $item['tpms_description'] . ')'  : '';
            echo '</a>';
            echo '</li>';
        }
        echo '</ul>';
    }
} else {
    echo '<h2>Please select or enter a MID you would like to see TMPS information for.</h2>';
    echo '<hr>';
    echo '<h3>Enter MID:</h3>';
    echo '<form action="index.php" method="GET">';
    echo 'MID: <input type="text" name="mid">';
    echo '<input type="submit" value="Submit">';
    echo '</form>';
    echo '<h3>or select MID from the following examples:</h3>';
    echo '<ul>';
    echo '<li><a href="index.php?mid=BMW00785">BMW00785</a></li>';
    echo '<li><a href="index.php?mid=HYU26443">HYU26443</a></li>';
    echo '<li><a href="index.php?mid=OPL29021">OPL29021</a></li>';
    echo '<li><a href="index.php?mid=LAN27671">LAN27671</a></li>';
    echo '<li><a href="index.php?mid=FIA37035">FIA37035</a></li>';
    echo '<li><a href="index.php?mid=PEU14794">PEU14794</a></li>';
    echo '</ul>';
}

?>

</body>
</html>