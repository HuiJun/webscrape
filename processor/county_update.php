<?php
include('../webscrape.class.php');
require('../include/constant.inc.php');
require('../include/states.inc.php');
require('../adodb/adodb.inc.php');

$html = new WebScrape();
$DB = NewADOConnection('mysql');
$DB->Connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);

foreach($allstates as $key=>$value) {
  $url = "http://www.epa.gov/enviro/html/codes/".strtolower($key).".html";
  $dom_query = "//table//td[@scope='row']";
  $dom_query2 = "//table//td[not(self::td[@scope='row'])]";
  $result = $html->get_html($url);
  $final[$key][county] = $html->html_dom($result,$dom_query);
  $fips[$key] = $html->html_dom($result,$dom_query2);
}

foreach($fips as $key => $value) {
  for($i = 0; $i < 3; $i++) {
    $final[$key][state][] = array_shift($value);
  }
  $final[$key][fips] = $value;
}
foreach($final as $key=>$value) {
  $array = $value[county];
  $count = count($array);
  for($j = 0; $j < $count; $j++) {
    $query = "INSERT INTO `_fips` (st_fips,co_fips,county,state,abv) VALUES ('".addslashes($value[state][1])."','".addslashes($value[fips][$j])."','".addslashes($value[county][$j])."','".addslashes($value[state][2])."','".addslashes($value[state][0])."')";
    $DB->Execute($query);
  }
}


/*
foreach($allstates as $key=>$value) {
  $url = "http://www.epodunk.com/counties/".strtolower($key)."_county.html";
  $dom_query = "//font";
  $result = $html->get_html($url,'0','0');
  $dom = $html->html_dom($result,$dom_query);
  array_walk($dom,create_function('&$v', '
    $replace = array(
      "@[\s|]County@si",
      "@[\s|]Parish@si",
      "@[\s|]Borough@si",
      "@ the@si",
      "@ of@si"
    );
    $v = preg_replace($replace,"",$v);
  '));
}

if (($handle = fopen("counties.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $all[] = $data[0];
    }
    fclose($handle);
}

for($x = 0;$x < count($all); $x++) {
  $all[$x] = explode('|',$all[$x]);
}
$i = 0;
foreach($all as $some) {
  if(!preg_match('@city@si',$some[0])) {
    $truth = $DB->GetOne("SELECT * FROM `_counties` WHERE county LIKE '%$some[0]%' AND state = '$some[1]'");
    if(!$truth) {
      $replace = array(
        "@ the@si",
        "@ of@si"
      );
      $some_county = preg_replace($replace,"",$some[0]);
      $new_truth = $DB->GetOne("SELECT * FROM `_counties` WHERE county LIKE '%$some_county%' AND state = '$some[1]'");
      if(!$new_truth) {
        $new[] = array(
          "county"=>$some[0],
          "state"=>$some[1]
        );
      }
    }
  }
}

foreach($new as $old) {
  if($DB->Execute("INSERT INTO `_counties` (county,state) VALUES('$old[county]','$old[state]')")) {
    echo "Inserted new record for $old[county], $old[state]<br />\n";
  }
}
*/