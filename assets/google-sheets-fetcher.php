<?php

function interpretCoordinates($dataCoordinates) {
    if(is_numeric($dataCoordinates[0])) { // incorrect input
        error_log('Google Sheet Integration Error: Data Coordinate Value Invalid (0)'); 
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
                    error_log('Google Sheet Integration Error: Data Coordinate Value Invalid (1)'); 
                    return null;
                }

                $numberPortion .= $intChar;
            }

            break;
        }
    }

    if($letterPortion == "" || $numberPortion == "") {
        error_log('Google Sheet Integration Error: Data Coordinate Value Invalid (2)'); 
        return null;
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
        error_log('Google Sheet Integration Error: Invalid Sheet CSV URL'); 
        return "ERR";
    }

    // get the data from the sheet's CSV
    $rows = explode("\n", $csvData); 
    $data = array();

    for($i=0; $i<count($rows); $i++) {
        $data[] = str_getcsv($rows[$i]);
    }

    if($data == null) {
        error_log('Google Sheet Integration Error: Invalid Data'); 
        return "ERR";
    }

    // convert google sheet coordinates to array of column, row
    $coordinates = interpretCoordinates($dataCoordinates);

    // interpretCoordinates returns null if the input was wrong
    if($coordinates == null) {
        error_log('Google Sheet Integration Error: Invalid Coordinate Input'); 
        return "ERR";
    }

    $column = $coordinates[0];
    $row = $coordinates[1];

    $numColumns = count($data[0]);
    $numRows = count($data);

    //return $column . "," . $row . " " . $numColumns . "," . $numRows;

    // if the coordinate input is outside of the range, throw an error 
    if($column > $numColumns - 1 || $column < 0 || $row > $numRows - 1 || $row < 0) {
        error_log('Google Sheet Integration Error: Coordinate Input outside of Sheet Range'); 
        return "ERR";
    }

    return $data[$coordinates[1]][$coordinates[0]];
}