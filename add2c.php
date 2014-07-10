<?php

include 'conn.php';

set_time_limit(0);

function address2coordinate()
{
    $dbh = getDbConnection('test');
    $tblname = 'opendata_lvr_land';

    $sql = 'SELECT `no`, `address`, `object_trans` FROM `' . $tblname . '`  WHERE `object_trans` != "土地" AND `lat` = 0.000000 AND `lng` = 0.000000 ORDER BY `no` ASC ';

    $result = $dbh->query($sql);

    foreach ($result as $row) {

        # 第三個字是段的就 ignore.
        $third_word = mb_substr( $row['address'], 2, 1,'UTF8');
        if ($third_word == '段') continue;

        $coordinate = callGoogleGeocoding($row['address']);
        $dbh->exec('UPDATE `'.$tblname.'` SET lat = '.$coordinate['lat'].' , lng = '. $coordinate['lng'] . ' WHERE `no` = '. $row['no']);
        print $row['no'] . "\t";
        print $row['address'] . "\t";
        print $coordinate['lat'] . "," . $coordinate['lng'] . "\n";
        //print $row['object_trans'] . "\n";
        //sleep(rand(1, 5));
    }

    $dbh = null;
}

function callGoogleGeocoding($address)
{
    $bingApiKey = 'AqXdpSWl2kmG0CzPJI6rPjsVPujMMRQ0f5Oa9CCMBZ9WQPEoANjCZGNdJOr7E5tU';

    # Get Latitude/Longitude From an Address with Google Map  
    $prepAddr = urlencode(str_replace(' ','+',$address));
    $geocode = file_get_contents('http://dev.virtualearth.net/REST/v1/Locations?countryRegion=TW&addressLine='.$prepAddr.'&maxResults=1&o=xml&key='. $bingApiKey); 
    //$geocode = file_get_contents('http://maps.google.com/maps/api/geocode/json?address='.$prepAddr.'&sensor=false'); 
    //echo 'http://dev.virtualearth.net/REST/v1/Locations?countryRegion=TW&addressLine='.$prepAddr.'&maxResults=1&o=xml&key='. $bingApiKey;
    $xml = simplexml_load_string($geocode);
    $lat = 0;
    $lng = 0;

    if (@$xml->StatusDescription == 'OK') {
        $lat = $xml->ResourceSets->ResourceSet->Resources->Location->Point->Latitude;
        $lng = $xml->ResourceSets->ResourceSet->Resources->Location->Point->Longitude;
    } 
    
    /*
    $output = json_decode($geocode);
    switch ($output->status) {
        case 'OK':
            $lat = $output->results[0]->geometry->location->lat;
            $lng = $output->results[0]->geometry->location->lng;
            break;
        case 'ZERO_RESULTS':
            $lat = 0;
            $lng = 0;
            break;
        case 'OVER_QUERY_LIMIT':
            die('OVER_QUERY_LIMIT');
            break;
        case 'REQUEST_DENIED':
            die('REQUEST_DENIED');
            break;
        case 'INVALID_REQUEST':
            die('INVALID_REQUEST');
            break;
        default:
            die('NULL');
    }
    */
    return array('lat' => $lat , 'lng' => $lng);
    
}

address2coordinate();