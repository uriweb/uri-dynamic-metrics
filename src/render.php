<?php

include __DIR__ . '/../assets/google-sheets-fetcher.php';

// fetches the cell at the position of dataLocation, ensures the attributes are set before trying to fetch
function pullData($attributes) {
    if(!array_key_exists("sheetCSVURL", $attributes)) {
        error_log('Google Sheet Integration Error: Invalid Sheet URL Attribute'); 
        return "ERR";
    }

    if(!array_key_exists("dataLocation", $attributes)) {
        error_log('Google Sheet Integration Error: Invalid Data Location Attribute'); 
        return "ERR";
    }

    return fetchSheetData($attributes["sheetCSVURL"], $attributes["dataLocation"]);
}

// fetches the cell at the position of descriptionLocation, ensures the attributes are set before trying to fetch
function pullDescription($attributes) {
    if(!array_key_exists("sheetCSVURL", $attributes)) {
        error_log('Google Sheet Integration Error: Invalid Sheet URL Attribute'); 
        return "ERR";
    }

    return fetchSheetData($attributes["sheetCSVURL"], $attributes["descriptionLocation"]);
}

// renders the block
function create_block_google_sheets_block_render($attributes, $content, $block) {
    $wrapper_attributes = get_block_wrapper_attributes();

    // whether or not the user has set a description manually
    $manualDescription = !array_key_exists("descriptionLocation", $attributes) || $attributes["descriptionLocation"] == "";

    $dataValue = pullData($attributes);

    $data = "<p ";
        if($attributes["animatedCounting"]) { // sets the attributes if this will be animated
            $data .= "class='google-sheets-counter' ";
            $data .= "duration='" . $attributes["countDuration"] . "' ";
            $data .= "countgoal='" . $dataValue . "' ";

            if($attributes["dataPrefix"] && $attributes["dataPrefix"] != "") {
                $data .= "prefix='" . $attributes["dataPrefix"] . "' ";
            }
            
            if($attributes["dataSuffix"] && $attributes["dataSuffix"] != "") {
                $data .= "suffix='" . $attributes["dataSuffix"] . "' ";
            }
        }

        $data .= "style='";
        $data .=    "font-size: " . $attributes["dataSize"] . "px; ";
        $data .=    "text-align: " . $attributes["dataAlignment"] . "; ";
        $data .=    "color: " . $attributes["dataColor"] . "; ";
        $data .=    ($attributes["flipPositions"] ? "margin-top: 0; " : "margin-bottom: 0; ");
        $data .=    ($attributes["flipPositions"] ? "padding-top: 0; " : "padding-bottom: 0; ");
        $data .= "'";

    // don't show value immediately if counting
    if($attributes["animatedCounting"]) {
        $data .= ">" . $attributes["dataPrefix"] . "0" . $attributes["dataSuffix"] . "</p>";
    } else {
        $data .= ">" . $attributes["dataPrefix"] . $dataValue . $attributes["dataSuffix"] . "</p>";
    }
    
    $description = "<p ";
        $description .= "style='";
        $description .=    "font-size: " . $attributes["descriptionSize"] . "px; ";
        $description .=    "text-align: " . $attributes["descriptionAlignment"] . "; ";
        $description .=    "color: " . $attributes["descriptionColor"] . "; ";
        $description .=    ($attributes["flipPositions"] ? "margin-bottom: 0; " : "margin-top: 0; ");
        $description .=    ($attributes["flipPositions"] ? "padding-bottom: 0; " : "padding-top: 0; ");
        $description .= "'";
    $description .= ">" . ($manualDescription ? $attributes["description"] : pullDescription($attributes)) . "</p>";

    return 
        "<div " . $wrapper_attributes . ">"
            . ($attributes["flipPositions"] ? $description : $data)
            . ($attributes["flipPositions"] ? $data : $description) .
        "</div>";
}