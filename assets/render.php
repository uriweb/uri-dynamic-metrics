<?php
/**
 * RENDERING
 *
 * @package uri-dynamic-metrics
 */

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

    $enableManualDescription = !array_key_exists("descriptionLocation", $attributes) || $attributes["descriptionLocation"] == "";
    $descriptionValue = $enableManualDescription ? esc_html($attributes["description"]) : esc_html(uri_dynamic_metrics_pullDescription($attributes));

    $description = "<p class='dynamic-metrics-description'>" . $descriptionValue . "</p>";

    return $description;
}

// creates the data element
function uri_dynamic_metrics_createDataElement($attributes) {
    $dataValue = esc_html(uri_dynamic_metrics_pullData($attributes));
    if ($dataValue == null) {
        return "Invalid data";
    }

    $enableAnimatedCounting = $attributes["animatedCounting"];
    $animatedCountDuration = $attributes["countDuration"];

    $dataPrefix = esc_html($attributes["dataPrefix"]);
    $enableDataPrefix = $dataPrefix && $dataPrefix != "";

    $dataSuffix = esc_html($attributes["dataSuffix"]);
    $enableDataSuffix = $dataSuffix && $dataSuffix != "";

    $data = "<p class='dynamic-metrics-datapoint";
    if($enableAnimatedCounting) { // sets the attributes if this will be animated
        $data .= " google-sheets-counter' ";
        $data .= "duration='" . $animatedCountDuration . "' ";
        $data .= "countgoal='" . $dataValue . "' ";

        // add prefix and suffix to attributes, allows me to readd them with javascript
        if($enableDataPrefix) {
            $data .= "prefix='" . $dataPrefix . "' ";
        }
        
        if($enableDataSuffix) {
            $data .= "suffix='" . $dataSuffix . "' ";
        }

        $data .= ">" . $dataPrefix . "0" . $dataSuffix . "</p>";
    } else {
        $data .= "'>" . $dataPrefix . $dataValue . $dataSuffix . "</p>";
    }

    return $data;
}

// renders the block
function uri_dynamic_metrics_render_block($attributes, $content, $block) {
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