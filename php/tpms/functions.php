<?php

/**
 * Fetches TPMS data returned by given URL using CURL
 *
 * @param $url
 * @return mixed
 */
function getUrlData($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Accept-Language: en-gb',
    ));
    $output = curl_exec($ch);

    //convert JSON response to associative array
    $output = json_decode($output, true);

    // handle error; error output
    if (curl_getinfo($ch, CURLINFO_HTTP_CODE) !== 200) {
        var_dump($output);
        curl_close($ch);
        exit;
    }

    curl_close($ch);

    return $output;
}

/**
 * Returns dash if given $value parameter is null
 *
 * @param $value
 * @return string
 */
function returnDashIfValueIsNull($value)
{
    return (!is_null($value)) ? $value : '-';
}

/**
 * Stores images part of TPMS response to session
 *
 * @param $tpmsResponse
 */
function storeTpmsVariantImagesToSession($tpmsResponse)
{
    $_SESSION['tpms_variant_images'] = $tpmsResponse['__images'];
}

/**
 * Gets images part of TPMS response from session
 *
 * @return mixed
 */
function getTpmsVariantImagesFromSession()
{
    if (isset($_SESSION['tpms_variant_images'])) {
        return $_SESSION['tpms_variant_images'];
    }

    return null;
}

/**
 * Function gets image URL based on given image ID from TPMS response
 *
 * @param $id
 * @return null
 */
function getImageUrlByIdFromTpmsResponse($id)
{
    $images = getTpmsVariantImagesFromSession();

    if (!is_null($images)) {
        foreach ($images as $image) {
            if ($image['id'] == $id) {
                if (isset($image['graphic']) && isset($image['graphic']['url'])) {
                    return $image['graphic']['url'];
                }
            }
        }
    }

    return null;
}

/**
 * Function shows general information of TPMS response
 *
 * @param $data
 */
function showGeneralInformation($data)
{
    if (isset($data['general_information'])) {
        echo '<h1>General information</h1>';
        if (!empty($data['general_information'])) {
            echo $data['general_information'];
        } else {
            echo 'Not available';
        }
    }
}

/**
 * Function shows tyre pressures information of TPMS response
 *
 * @param $data
 */
function showTyrePressures($data)
{
    if (isset($data['tyre_pressures'])) {
        echo '<h1>Type pressures</h1>';
        if (!empty($data['tyre_pressures'])) {
            echo $data['tyre_pressures'];
        } else {
            echo 'Not available';
        }
    }
}

/**
 * Function shows table with special tools information of TPMS response
 *
 * @param $data
 */
function showSpecialTools($data)
{
    if (isset($data['special_tools'])) {
        $specialTools = $data['special_tools'];
        echo '<h1>Special tools</h1>';
        if (!empty($specialTools)) {
            echo '<table border="1" cellpadding="4" cellspacing="0">';
            echo '<tr>';
            echo '<th>Tool name</th>';
            echo '<th>Tool number</th>';
            echo '<th>Tool manufacturer</th>';
            echo '</tr>';

            //iterate over special tools
            foreach ($specialTools as $tool) {

                //tool has numbers
                if (!empty($tool['tool_numbers'])) {
                    $toolNumbers       = $tool['tool_numbers'];
                    $toolNumbersAmount = count($toolNumbers);

                    //display table rows with different numbers for current tool
                    foreach ($toolNumbers as $key => $item) {
                        echo '<tr>';

                        //display tool name for different tool numbers
                        if ($key == 0) {
                            echo '<td rowspan="' . $toolNumbersAmount . '">' . returnDashIfValueIsNull($tool['tool_name']) . '</td>';
                        }

                        echo '<td>' . returnDashIfValueIsNull($item['tool_number']) . '</td>';
                        echo '<td>' . returnDashIfValueIsNull($item['manufacturer']) . '</td>';
                        echo '</tr>';
                    }
                } else {
                    //tool without numbers
                    echo '<tr>';
                    echo '<td>' . returnDashIfValueIsNull($tool['tool_name']) . '</td>';
                    echo '<td>-</td>';
                    echo '<td>-</td>';
                    echo '</tr>';
                }
            }
            echo '</table>';
        } else {
            echo 'Not available';
        }
    }
}

/**
 * Function shows table with tightening torques information of TPMS response
 *
 * @param $data
 */
function showTighteningTorques($data)
{
    if (isset($data['tightening_torques'])) {
        $tighteningTorques = $data['tightening_torques'];
        echo '<h1>Tightening torques</h1>';
        if (!empty($tighteningTorques)) {
            echo '<table border="1" cellpadding="4" cellspacing="0">';
            echo '<tr>';
            echo '<th>Component</th>';
            echo '<th>Details</th>';
            echo '<th>Range</th>';
            echo '<th>Value</th>';
            echo '<th>Minimum</th>';
            echo '<th>Maximum</th>';
            echo '<th>Unit</th>';
            echo '</tr>';

            //display table rows for tightening torques
            foreach ($tighteningTorques as $item) {
                $torqueData = $item['torque_data'];
                echo '<tr>';
                echo '<td>' . $item['component_description'] . '</td>';
                echo '<td>' . returnDashIfValueIsNull($torqueData['text']) . '</td>';
                echo '<td>' . ((!$torqueData['range']) ? 'No' : 'Yes') . '</td>';
                echo '<td>' . returnDashIfValueIsNull($torqueData['value']) . '</td>';
                echo '<td>' . returnDashIfValueIsNull($torqueData['minimum']) . '</td>';
                echo '<td>' . returnDashIfValueIsNull($torqueData['maximum']) . '</td>';
                echo '<td>' . returnDashIfValueIsNull($torqueData['unit']) . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo 'Not available';
        }
    }
}

/**
 * Function shows system operation information of TPMS response
 *
 * @param $data
 */
function showSystemOperation($data)
{
    if (isset($data['system_operation'])) {
        $systemOperation = $data['system_operation'];
        echo '<h1>System operation</h1>';
        if (!empty($systemOperation)) {
            if (isset($systemOperation['value']) && !empty($systemOperation['value'])) {
                $systemOperation = $systemOperation['value'];
                echo '<ul>';

                //iterate over system operation items and display them as list elements
                foreach ($systemOperation as $operation) {
                    echo '<li>';
                    showContent($operation);
                    echo '</li>';
                }
                echo '</ul>';
            //if system operation part of TPMS response contains procedures then display the part as procedures
            } elseif (isset($systemOperation['procedures'])) {
                //show system operation procedures menu
                showProcedures($systemOperation, false, 'so');

                echo '<hr>';
                showProcedures($systemOperation, true, 'so');
            }
        } else {
            echo 'Not available';
        }
    }
}

/**
 * Recursively show procedures inside TPMS response.
 *
 * @param $data
 * @param bool $withSteps
 * @param null $parentId
 */
function showProcedures($data, $withSteps = true, $parentId = null)
{
    if (!empty($data['procedures'])) {
        $procedures = $data['procedures'];
        foreach ($procedures as $procedure) {
            echo '<ul>';
            echo '<li>';

            /* use parent element identifier and procedure title to generate procedure identifier needed to create anchors for navigating across
             * procedures easily
             */
            $procedureId = md5($parentId . $procedure['title']);

            // if $withSteps parameter is true show procedures with steps, otherwise show only procedures titles
            if ($withSteps) {
                echo '<div style="margin-bottom: 5px;">';

                //generate anchors to navigate across procedure groups
                echo '<h3 id="' . $procedureId . '">' . $procedure['title'] . '</h3>';

                //for each procedure add link for returning to top of the page
                echo '<div style="text-align: right;"><a href="#top">Back to top</a></div>';
                echo '</div>';
                showStepsForProcedure($procedure);
            } else {
                echo '<a href="#' . $procedureId . '">' . $procedure['title'] . '</a>';
            }

            //show nested procedures
            if (isset($procedure['procedures']) && !empty($procedure['procedures'])) {
                showProcedures($procedure, $withSteps, $procedureId);
            }
            echo '</li>';
            echo '</ul>';
        }
    } else {
        echo 'Not available';
    }
}

/**
 * Show steps for procedure
 *
 * @param $procedure
 */
function showStepsForProcedure($procedure)
{
    if (isset($procedure['steps']) && !empty($procedure['steps'])) {
        $procedureSteps = $procedure['steps'];
        echo '<ul>';
        foreach ($procedureSteps as $key => $step) {
            $previousStepKind = null;
            $nextStepKind     = null;
            $lastStepKind     = null;

            //check previous and next steps kind
            if ($key > 0) {
                $lastStepIndex     = count($procedureSteps) - 1;
                $previousStepIndex = $key - 1;
                $nextStepIndex     = $key + 1;
                $previousStepKind  = $procedureSteps[$previousStepIndex]['kind'];

                if ($key != $lastStepIndex) {
                    $nextStepKind = $procedureSteps[$nextStepIndex]['kind'];
                }
            }

            $stepDetails = $step['value'];
            $stepKind    = $step['kind'];

            //based on step's kind display steps at appropriate nesting levels
            switch ($stepKind) {
                case 'substep':
                    echo ($previousStepKind == 'step') ? '<ul>' : '';
                    echo '<li>';

                    //show step content
                    showContent($stepDetails, $stepKind);

                    echo '</li>';
                    echo ($nextStepKind == 'step' || $key == $lastStepIndex) ? '</ul>' : '';
                    break;
                default:
                    echo '<li>';

                    //show step content
                    showContent($stepDetails, $stepKind);

                    echo '</li>';
            }
        }
        echo '</ul>';
    }
}

/**
 * Show content for a step
 *
 * @param $step
 * @param null $kind
 */
function showContent($step, $kind = null)
{
    //customize CSS styles based on step kind
    if ($kind == 'warning' || $kind == 'caution') {
        echo '<div style="border: 2px solid #000; padding: 4px; font-weight: bold; font-style: italic; color: red;">';
        echo '<span style="font-size: larger;">' . ucfirst($kind) . ':</span><br/><br/>';
    } elseif ($kind == 'note') {
        echo '<div style="border: 2px solid #000; padding: 4px; font-weight: bold; font-style: italic;">';
        echo '<span style="font-size: larger;">' . ucfirst($kind) . ':</span><br/><br/>';
    } else {
        echo '<div style="border: 2px solid #000; padding: 4px;">';
    }

    //if step type is compound_text display text and image parts in a grouped way
    if ($step['type'] == 'compound_text') {
        $images        = [];
        $imagesCounter = 0;
        foreach ($step['value'] as $item) {
            if ($item['type'] == 'text') {
                echo '<span>' . $item['value'] . ' </span>';
            } elseif ($item['type'] == 'image') {
                $imageId    = null;
                $imageLabel = null;

                //display repeating images only one time
                if (!array_key_exists($item['value'], $images)) {
                    $imagesCounter += 1;
                    $imageId                = $imagesCounter;
                    $image                  = [];
                    $image['label']         = 'Image ' . $imageId;
                    $image['url']           = getImageUrlByIdFromTpmsResponse($item['value']);
                    $imageLabel             = $image['label'];
                    $images[$item['value']] = $image;
                } else {
                    $imageLabel = $images[$item['value']]['label'];
                }

                //display text with image label and reference
                echo '<strong>(' . $imageLabel . ', Reference: ' . $item['reference'] . ')</strong>';
            }
        }

        //display images with captions
        if (!empty($images)) {
            foreach ($images as $image) {
                echo '<figure style="border: 1px solid #000;">';
                echo '<img src="' . $image['url'] . '"/>';
                echo '<figcaption><strong style="font-size: 24px;">' . $image['label'] . '</strong></figcaption>';
                echo '</figure>';
                echo '<br/>';
                echo '<br/>';

            }
        }
    } elseif ($step['type'] == 'text') {
        echo '<span>' . $step['value'] . ' </span>';
    }

    echo '</div>';
    echo '<br/>';
    echo '<br/>';
}

/**
 * Function shows procedure groups with nested procedures
 *
 * @param $data
 * @param bool $onlyMenu
 */
function showProcedureGroups($data, $onlyMenu = false)
{
    if (isset($data['procedure_groups'])) {
        $procedureGroups = $data['procedure_groups'];
        if (!empty($procedureGroups)) {

            //show sections header only if procedure group menu is displayed
            if ($onlyMenu) {
                echo '<h1>Procedure groups</h1>';
            }

            echo '<ul>';
            foreach ($procedureGroups as $key => $group) {
                echo '<li>';

                if ($onlyMenu) {
                    echo '<a href="#pg' . $key . '">' . $group['title'] . '</a>';
                } else {
                    //generate anchors to navigate across procedure groups
                    echo '<h2 id="pg' . $key . '">' . $group['title'] . '</h2>';
                }

                if (isset($group['procedures'])) {
                    //generate procedure group ID and pass it nested procedures
                    $procedureGroupId = 'pg' . $key;
                    if ($onlyMenu) {
                        //display procedures tree without steps (only menu)
                        showProcedures($group, false, $procedureGroupId);
                    } else {
                        //display procedure with steps
                        showProcedures($group, true, $procedureGroupId);
                    }
                }
                echo '</li>';
            }
            echo '</ul>';
        } else {
            echo 'Not available';
        }
    }
}