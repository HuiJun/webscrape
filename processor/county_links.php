<?php
include('../webscrape.class.php');
require('../include/constant.inc.php');
require('../include/states.inc.php');
require('../adodb/adodb.inc.php');

foreach($allstates as $key=>$value) {
  $states[$key] = preg_replace("@\s@",'',$value);
}

print_r($states);

$html = new WebScrape();
$DB = NewADOConnection('mysql');
$DB->Connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);

$url = 'http://publicrecords.onlinesearches.com/';

$page = $html->get_html($url);
$query = '//li/a/@href';
$dom = $html->html_dom($page,$query);


foreach($dom as $next) {
  preg_match('@onlinesearches.com/(.*?).htm@',$next,$state);
  if(in_array($state[1],$states)) {
    $next_url[] = $next;
  }
  unset($next);
}
foreach($next_url as $next) {
  $new_page = $html->get_html($next);
  $new_query = '//select[@name="guidelinks"]/option/@value';
  $new_dom = $html->html_dom($new_page,$new_query);
}
print_r($new_dom);