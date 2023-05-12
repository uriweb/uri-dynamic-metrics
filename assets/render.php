<?php

include __DIR__ . '/../assets/google-sheets-fetcher.php';

// fetches the cell at the position of dataLocation, ensures the attributes are set before trying to fetch
function uri_dynamic_metrics_pullData($attributes) {
    if(!array_key_exists("sheetCSVURL", $attributes) && !array_key_exists("dataLocation", $attributes)) {
        error_log('Google Sheet Integration Error: Invalid Sheet URL Attribute'); 
        return "Invalid Google Sheet URL";   
    }
    //sanitize url input
    else {
        $cleanUrl = sanitize_text_field( $attributes["sheetCSVURL"] );
    }

    //if(!array_key_exists("dataLocation", $attributes)) {
    //    error_log('Google Sheet Integration Error: Invalid Data Location Attribute'); 
     //   return "ERR";
    //}

    $metricNumber = uri_dynamic_metrics_fetchSheetData($cleanUrl, $attributes["dataLocation"]);

    if(is_numeric($metricNumber)) {
        return $metricNumber;
    }
}

// fetches the cell at the position of descriptionLocation, ensures the attributes are set before trying to fetch
function uri_dynamic_metrics_pullDescription($attributes) {
    if(!array_key_exists("sheetCSVURL", $attributes)) {
        error_log('Google Sheet Integration Error: Invalid Sheet URL Attribute'); 
        return "Invalid Google Sheet URL";
    }
    else {
        $cleanUrl = sanitize_text_field( $attributes["sheetCSVURL"] );
    }

    $descriptionField = sanitize_text_field(uri_dynamic_metrics_fetchSheetData($cleanUrl, $attributes["descriptionLocation"]));

    return $descriptionField;

}

// creates a new description element
function uri_dynamic_metrics_createDescriptionElement($attributes) {
    $descriptionSize = $attributes["descriptionSize"];
    $descriptionAlignment = $attributes["descriptionAlignment"];
    $descriptionColor = $attributes["descriptionColor"];

    $flipPositions = $attributes["flipPositions"];

    $enableManualDescription = !array_key_exists("descriptionLocation", $attributes) || $attributes["descriptionLocation"] == "";
    $descriptionValue = $enableManualDescription ? esc_html($attributes["description"]) : esc_html(uri_dynamic_metrics_pullDescription($attributes));

    $description = "<p ";
        $description .= "style='";
        $description .=    "font-size: " . $descriptionSize . "px; ";
        $description .=    "text-align: " . $descriptionAlignment . "; ";
        $description .=    "color: " . $descriptionColor . "; ";
        $description .=    ($flipPositions ? "margin-bottom: 0; " : "margin-top: 0; ");
        $description .=    ($flipPositions ? "padding-bottom: 0; " : "padding-top: 0; ");
        $description .= "'";
    $description .= ">" . $descriptionValue . "</p>";

    return $description;
}

// creates the data element
function uri_dynamic_metrics_createDataElement($attributes) {
    $dataValue = esc_html(uri_dynamic_metrics_pullData($attributes));
    if ($dataValue == null) {
        return "Invalid data";
    }

    $dataSize = $attributes["dataSize"];
    $dataAlignment = $attributes["dataAlignment"];
    $dataColor = $attributes["dataColor"];

    $flipPositions = $attributes["flipPositions"];

    $enableAnimatedCounting = $attributes["animatedCounting"];
    $animatedCountDuration = $attributes["countDuration"];

    $dataPrefix = esc_html($attributes["dataPrefix"]);
    $enableDataPrefix = $dataPrefix && $dataPrefix != "";

    $dataSuffix = esc_html($attributes["dataSuffix"]);
    $enableDataSuffix = $dataSuffix && $dataSuffix != "";

    $data = "<p ";
        if($enableAnimatedCounting) { // sets the attributes if this will be animated
            $data .= "class='google-sheets-counter' ";
            $data .= "duration='" . $animatedCountDuration . "' ";
            $data .= "countgoal='" . $dataValue . "' ";

            // add prefix and suffix to attributes, allows me to readd them with javascript
            if($enableDataPrefix) {
                $data .= "prefix='" . $dataPrefix . "' ";
            }
            
            if($enableDataSuffix) {
                $data .= "suffix='" . $dataSuffix . "' ";
            }
        }

        $data .= "style='";
        $data .=    "font-size: " . $dataSize . "px; ";
        $data .=    "text-align: " . $dataAlignment . "; ";
        $data .=    "color: " . $dataColor . "; ";
        $data .=    ($flipPositions ? "margin-top: 0; " : "margin-bottom: 0; ");
        $data .=    ($flipPositions ? "padding-top: 0; " : "padding-bottom: 0; ");
        $data .= "'";

    // if animated counting is enabled, make it start at 0.
    if($enableAnimatedCounting) {
        $data .= ">" . $dataPrefix . "0" . $dataSuffix . "</p>";
    } else {
        $data .= ">" . $dataPrefix . $dataValue . $dataSuffix . "</p>";
    }

    return $data;
}

// renders the block
function uri_dynamic_metrics_create_block_google_sheets_block_render($attributes, $content, $block) {
    $wrapper_attributes = get_block_wrapper_attributes();

    $flipPositions = $attributes["flipPositions"];

    $description = uri_dynamic_metrics_createDescriptionElement($attributes);
    $data = uri_dynamic_metrics_createDataElement($attributes);

    return 
        "<div " . $wrapper_attributes . ">"
            . ($flipPositions ? $description : $data)
            . ($flipPositions ? $data : $description) .
        "</div>";
}