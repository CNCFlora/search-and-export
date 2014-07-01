<?php

function flatten($array, $prefix = '') {
    $result = array();
    foreach($array as $key=>$value) {
        if(is_object($value)) {
            $result = $result + flatten($value, $prefix . $key . '_');
        } else if(is_array($value)) {
            $result = $result + flatten($value, $prefix . $key . '_');
        } else {
            $result[$prefix . $key] = $value;
        }
    }
    return $result;
}

$url =  $_GET["esearch"]."/".$_GET["db"]."/".$_GET["type"]."/_search?"
        ."q=".urlencode($_GET["query"])."&_source=".implode(",",$_GET["fields"])."&size=999999";
$r = json_decode(file_get_contents($url))->hits->hits;
$response=[];
foreach($r as $row) {
    $response[] = flatten( $row->_source );
}

if(isset($_GET["callback"])) {
    #header("Content-Type: application/json");
    echo $_GET["callback"]."(".json_encode($response).")";
} else {
    header("Content-Type: text/csv");
    header('Content-Disposition: attachment; filename="data.csv"');
    foreach($response[0] as $k=>$v) {
        echo '"'.$k.'";';
    }
    echo "\n";
    foreach($response as $row) {
        foreach($row as $v) {
            echo '"'.str_replace('"',"'",$v).'";';
        }
        echo "\n";
    }
}

