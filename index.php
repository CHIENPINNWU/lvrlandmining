<?php

include 'conn.php';

set_time_limit(0);
set_error_handler('errorHandler');

$download_url = 'http://plvr.land.moi.gov.tw/Download?type=zip&fileName=lvr_landxml.zip';
$filename = date("Ymd").'.zip';
$base_path = './';
$dl_path = $base_path . $filename;
$extra_folder = $base_path . 'lvrland_opendata_'.date("Ymd") . '/';


function start_spider($download_url, $filename, $base_path, $dl_path, $extra_folder)
{
    # 先檢查檔案是否存在
    if (!file_exists($filename)) {

        # 下載檔案
        $zipfile = file_get_contents($download_url);
        $rs = file_put_contents($dl_path, $zipfile);

        if ($rs === FALSE) {
            die('cannot find file lvr_landxml.zip');
        }

        # 解壓縮至資料夾 /lvrland
        $zip = new ZipArchive;
        if ($zip->open($filename) !== TRUE) {
            die('failed');
        }

        $zip->extractTo($extra_folder);
        $zip->close();

    }

    # 所有xml檔案路徑
    $xml_array = array();
    foreach (glob($extra_folder."*.XML") as $filename) {
        array_push($xml_array, $filename);
    }

    if (empty($xml_array)) {
        die('empty folder');
    }

    xml_parsing($xml_array);
    echo 'OK';
}

function xml_parsing($xml_array)
{
    foreach ($xml_array as $xml) {

        # 分析檔名suffix 
        # _A 不動產買賣 A
        # _B 預售屋買賣 B 
        # _C 不動產租賃 C

        $trans_type = '';

        $filename = basename($xml, ".XML");

        if ( strpos($filename, '_A') !== FALSE ) {
            $trans_type = 'A';
        } else if ( strpos($filename, '_B') !== FALSE ) {
            $trans_type = 'B';
        } else {
            $trans_type = 'C';
        }

        insertLvrDataToDB($xml, $trans_type);
    }
}


function errorHandler($errno, $errstr, $errfile, $errline) {
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}


function insertLvrDataToDB($xml, $trans_type) {

    $dbh = getDbConnection('test');
    $tblname = 'opendata_lvr_land';
    $tblname_for_error = 'opendata_crawl_error';

    try {
        $xml = simplexml_load_file($xml);
    }catch(ErrorException $e) {
        $dbh->exec("INSERT INTO `".$tblname_for_error."` (`errcode`, `errstr`, `errfile`, `errline`) VALUES ('" . $e->getCode() . "', '" . $e->getMessage() . "', '" . $e->getFile() . "', '" . $e->getLine() . "'); ");
        $dbh = null;
        return false;
    }
    
    # 變數初始化
    $town_dist = '';
    $object_trans = '';
    $address = '';
    $land_area = '';
    $land_zoning = '';
    $non_zoning = '';
    $non_scheduled = '';
    $trans_date = '';
    $towers_trans = '';
    $migration_levels = '';
    $total_floors = '';
    $buliding_type = '';
    $purpose = '';
    $materials = '';
    $construction_completed ='';
    $building_area = '';
    $pattern_house = '';
    $pattern_hall = '';
    $pattern_lav = '';
    $pattern_compartment = '';
    $manage_org = '';
    $price = '';
    $price_m = '';
    $parking_space_type = '';
    $parking_space_area = '';
    $parking_space_price = '';
    $furniture = '';

    foreach($xml as $rec) {

        foreach($rec as $key=>$value) {

            switch ($key) {
                case '鄉鎮市區':
                    $town_dist = (string) $value;
                    break;
                case '交易標的':
                    $object_trans = (string) $value;
                    break;
                case '土地區段位置或建物區門牌':
                    $address = (string) $value;
                    break;
                case '土地移轉總面積平方公尺':
                    $land_area = (string) $value;
                    break;
                case '都市土地使用分區':
                    $land_zoning = (string) $value;
                    break;
                case '非都市土地使用分區':
                    $non_zoning = (string) $value;
                    break;
                case '非都市土地使用編定':
                    $non_scheduled = (string) $value;
                    break;
                case '交易年月':
                case '租賃年月':
                    $trans_date = (string) $value;
                    break;
                case '交易筆棟數':
                case '租賃筆棟數':
                    $towers_trans = (string) $value;
                    break;
                case '移轉層次':
                case '租賃層次':
                    $migration_levels = (string) $value;
                    break;
                case '總樓層數':
                    $total_floors = (string) $value;
                    break;
                case '建物型態':
                    $buliding_type = (string) $value;
                    break;
                case '主要用途':
                    $purpose = (string) $value;
                    break;
                case '主要建材':
                    $materials = (string) $value;
                    break;
                case '建築完成年月':
                    $construction_completed = (string) $value;
                    break;
                case '建物移轉總面積平方公尺':
                case '租賃總面積平方公尺':
                    $building_area = (string) $value;
                    break;
                case '建物現況格局-房':
                     $pattern_house = (string) $value;
                    break;
                case '建物現況格局-廳':
                    $pattern_hall = (string) $value;
                    break;
                case '建物現況格局-衛':
                    $pattern_lav = (string) $value;
                    break;
                case '建物現況格局-隔間':
                     $pattern_compartment = (string) $value;
                    break;
                case '有無管理組織':
                    $manage_org = (string) $value;
                    break;  
                case '總價元':
                     $price = (string) $value;
                    break; 
                case '單價每平方公尺':
                    $price_m = (string) $value;
                    break; 
                case '車位類別':
                    $parking_space_type = (string) $value;
                    break; 
                case '車位移轉總面積平方公尺':
                      $parking_space_area = (string) $value;
                    break; 
                case '車位總價元':
                    $parking_space_price = (string) $value;
                    break;
                case '有無附傢俱':
                    $furniture = (string) $value;
                    break;
                default:
            }
        }

        if ( $object_trans === '土地' ) {

            # Get Latitude/Longitude From an Address with Google Map
            /*
                $prepAddr = urlencode(str_replace(' ','+',$address));
                $geocode = file_get_contents('http://maps.google.com/maps/api/geocode/json?address='.$prepAddr.'&sensor=false'); 
                $output= json_decode($geocode);
                if (!empty($output->results)) {
                    $lat = $output->results[0]->geometry->location->lat;
                    $lng = $output->results[0]->geometry->location->lng;
                    echo $lat.','.$lng;
                    echo "<br/>";   
                }
            */

        }

        $lat = 0.0;
        $lng = 0.0;

        # 寫入資料庫
        $count = $dbh->exec("INSERT INTO `".$tblname."` (
                `town_dist`, 
                `trans_type`,
                `object_trans`, 
                `address`, 
                `lat`, 
                `lng`, 
                `land_area`, 
                `land_zoning`, 
                `non_zoning`, 
                `non_scheduled`, 
                `trans_date`, 
                `towers_trans`, 
                `migration_levels`, 
                `total_floors`, 
                `buliding_type`, 
                `purpose`, 
                `materials`, 
                `construction_completed`, 
                `building_area`, 
                `pattern_house`, 
                `pattern_hall`, 
                `pattern_lav`, 
                `pattern_compartment`, 
                `manage_org`, 
                `price`, 
                `price_per_m`, 
                `parking_space_type`, 
                `parking_space_area`, 
                `parking_space_price`) VALUES (
                    '".$town_dist."',
                    '".$trans_type."',
                    '".$object_trans."', 
                    '".$address."',  
                    '".$lat."', 
                    '".$lng."', 
                    '".$land_area."', 
                    '".$land_zoning."', 
                    '".$non_zoning."', 
                    '".$non_scheduled."', 
                    '".$trans_date."', 
                    '".$towers_trans."', 
                    '".$migration_levels."', 
                    '".$total_floors."', 
                    '".$buliding_type."', 
                    '".$purpose."', 
                    '".$materials."', 
                    '".$construction_completed."', 
                    '".$building_area."', 
                    '".$pattern_house."', 
                    '".$pattern_hall."', 
                    '".$pattern_lav."', 
                    '".$pattern_compartment."', 
                    '".$manage_org."', 
                    '".$price."', 
                    '".$price_m."', 
                    '".$parking_space_type."', 
                    '".$parking_space_area."', 
                    '".$parking_space_price."');");
    }

    $dbh = null;
}

start_spider($download_url, $filename, $base_path, $dl_path, $extra_folder);
