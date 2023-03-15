<?php
/**
 * Plugin Name:       Google Sheets Integration
 * Description:       A block to display data from google sheets inside your website.
 * Requires at least: 5.8
 * Requires PHP:      7.0
 * Version:           1.0.0
 * Author:            Nathan Lannon
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       googlesheets
 *
 * @package           nathanlannon
 */

function create_block_google_sheets_block_init() {
	register_block_type( __DIR__ . '/build', array(
        'supports' => array(
            'html' => false,
            'color' => array(
                'gradients' => true,
                'link' => true,
                '__experimentalDefaultControls' => array(
                    'background' => true,
                    'text' => false,
                    'link' => false
                )
            )    
        ),
		'render_callback' => 'create_block_google_sheets_block_render'
	) );
}
add_action( 'init', 'create_block_google_sheets_block_init' );

function create_block_google_sheets_block_render($attributes, $content, $block){
    $wrapper_attributes = get_block_wrapper_attributes();
    $text = "test";

    return 
        "<div " . $wrapper_attributes . ">
            <p style='
                font-size: " . $attributes["descriptionSize"] . "px;
                text-align: " . $attributes["descriptionAlignment"] . ";
                color: " . $attributes["descriptionColor"] . ";
                margin-bottom: 0;
                padding-bottom: 0;
            '> " . $attributes["description"] . " </p>

            <p style='
                font-size: " . $attributes["dataSize"] . "px;
                text-align: " . $attributes["dataAlignment"] . ";
                color: " . $attributes["dataColor"] . ";
                margin-top: 0;
                padding-top: 0;
            '> " . pullData($attributes) . " </p>
        </div>";
}

function interpretCoordinates($dataCoordinates) {
    if(is_numeric($dataCoordinates[0])) { // incorrect input
        echo "<script> console.log('Google Sheet Integration Error: Data Coordinate Value Invalid (0)'); </script>";
        return null;
    }

    // splits the coordinate input between letters and numbers (AA15) -> (AA) & (15)
    $letterPortion = "";
    $numberPortion = "";

    for($i=0; $i<strlen($dataCoordinates); $i++) {
        $char = $dataCoordinates[$i];

        if(!is_numeric($char)) { // handles up till first number
            $letterPortion .= $char;
        } else { // handles all values after last letter
            for($j=$i; $j<strlen($dataCoordinates); $j++) {
                $intChar = $dataCoordinates[$j];

                if(!is_numeric($intChar)) { // shouldn't be any letters at this point
                    echo "<script> console.log('Google Sheet Integration Error: Data Coordinate Value Invalid (1)'); </script>";
                    return null;
                }

                $numberPortion .= $intChar;
            }

            break;
        }
    }

    // coordinate value for the columns (letters) and rows (numbers)
    $letterValue = 0;
    $numberValue = (int) $numberPortion;

    for($i=0; $i<strlen($letterPortion); $i++) { // converts google sheet's column letters to coordinate numbers
        $char = $letterPortion[$i];
        $pow = strlen($letterPortion) - $i - 1;

        $charValue = ord(strtoupper($char)) - ord("A") + 1; // get alphabetical index of letter

        $letterValue += $charValue * (pow(26, $pow));
    }

    return array($letterValue - 1, $numberValue - 1); // php arrays start at 0 not 1, subtract 1 to handle that
}

// pulls data from google sheet
function fetchSheetData($sheetCSVURL, $dataCoordinates) {
    $csvData = @file_get_contents($sheetCSVURL); // pull csv data from sheet url

    if($csvData === FALSE) { // makes sure file opened correctly
        echo "<script> console.log('Google Sheet Integration Error: Invalid Sheet CSV URL'); </script>";
        return "ERR";
    }

    // get the data from the sheet's CSV
    $rows = explode("\n", $csvData); 
    $data = array();

    for($i=0; $i<count($rows); $i++) {
        $data[] = str_getcsv($rows[$i]);
    }

    // convert google sheet coordinates to array of column, row
    $coordinates = interpretCoordinates($dataCoordinates);

    // interpretCoordinates returns null if the input was wrong
    if($coordinates == null) {
        echo "<script> console.log('Google Sheet Integration Error: Invalid Coordinate Input'); </script>";
        return "ERR";
    }

    // if the coordinate input is outside of the range, throw an error 
    if($coordinates[1] > count($data) || $coordinates[0] < 0 || $coordinates[0] > count($data[$coordinates[1]]) || $coordinates[1] < 0) {
        echo "<script> console.log('Google Sheet Integration Error: Coordinate Input outside of Sheet Range'); </script>";
        return "ERR";
    }

    return $data[$coordinates[1]][$coordinates[0]];
}

// ensures attributes are correct then pulls data from google sheet
function pullData($attributes) {
    // ensures the attributes are set
    if(!array_key_exists("sheetCSVURL", $attributes)) {
        echo "<script> console.log('Google Sheet Integration Error: Invalid Sheet URL Attribute'); </script>";
        return "ERR";
    }

    if(!array_key_exists("dataLocation", $attributes)) {
        echo "<script> console.log('Google Sheet Integration Error: Invalid Data Location Attribute'); </script>";
        return "ERR";
    }

    $sheetURL = $attributes["sheetCSVURL"];
    $dataLocation = $attributes["dataLocation"];

    $data = fetchSheetData($sheetURL, $dataLocation);

    return $data;
}