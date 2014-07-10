lvrlandmining
=============

台灣內政部不動產交易實價登錄OpenData爬蟲  
[http://plvr.land.moi.gov.tw/Download?type=zip&fileName=lvr_landxml.zip]()

## Db Schema

    CREATE TABLE IF NOT EXISTS `opendata_lvr_land` (
          `no` int(11) NOT NULL AUTO_INCREMENT COMMENT '主鍵',
          `trans_type` varchar(1) DEFAULT NULL COMMENT '交易類別',
          `town_dist` varchar(100) DEFAULT NULL COMMENT '鄉鎮市區',
          `object_trans` varchar(100) DEFAULT NULL COMMENT '交易標的',
          `address` varchar(100) DEFAULT NULL COMMENT '土地區段位置或建物區門牌',
          `lat` float(10,6) DEFAULT NULL COMMENT '緯度',
          `lng` float(10,6) DEFAULT NULL COMMENT '經度',
          `land_area` varchar(100) DEFAULT NULL COMMENT '土地移轉總面積平方公尺',
          `land_zoning` varchar(100) DEFAULT NULL COMMENT '都市土地使用分區',
          `non_zoning` varchar(100) DEFAULT NULL COMMENT '非都市土地使用分區',
          `non_scheduled` varchar(100) DEFAULT NULL COMMENT '非都市土地使用編定',
          `trans_date` varchar(10) DEFAULT NULL COMMENT '交易年月(租賃年月)',
          `towers_trans` varchar(100) DEFAULT NULL COMMENT '交易筆棟數(租賃筆棟數)',
          `migration_levels` varchar(100) DEFAULT NULL COMMENT '移轉層次(租賃層次)',
          `total_floors` varchar(30) DEFAULT NULL COMMENT '總樓層數',
          `buliding_type` varchar(100) DEFAULT NULL COMMENT '建物型態',
          `purpose` varchar(100) DEFAULT NULL COMMENT '主要用途',
          `materials` varchar(100) DEFAULT NULL COMMENT '主要建材',
          `construction_completed` varchar(30) DEFAULT NULL COMMENT '建築完成年月',
          `building_area` varchar(30) DEFAULT NULL COMMENT '建物移轉總面積平方公尺(租賃總面積平方公尺)',
          `pattern_house` varchar(30) DEFAULT NULL COMMENT '建物現況格局-房',
          `pattern_hall` varchar(30) DEFAULT NULL COMMENT '建物現況格局-廳',
          `pattern_lav` varchar(30) DEFAULT NULL COMMENT '建物現況格局-衛',
          `pattern_compartment` varchar(30) DEFAULT NULL COMMENT '建物現況格局-隔間',
          `manage_org` varchar(6) DEFAULT NULL COMMENT '有無管理組織',
          `price` varchar(50) DEFAULT NULL COMMENT '總價元',
          `price_per_m` varchar(50) DEFAULT NULL COMMENT '單價每平方公尺',
          `parking_space_type` varchar(30) DEFAULT NULL COMMENT '車位類別',
          `parking_space_area` varchar(30) DEFAULT NULL COMMENT '車位移轉總面積平方公尺',
          `parking_space_price` varchar(30) DEFAULT NULL COMMENT '車位總價元',
          `furniture` varchar(6) DEFAULT NULL COMMENT '有無附傢俱',
          PRIMARY KEY (`no`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
        
