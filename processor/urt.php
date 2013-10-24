<?php
require('../webscrape.class.php');
require('../include/constant.inc.php');
require('../adodb/adodb.inc.php');

$html = new WebScrape();
$DB = NewADOConnection('mysql');
$DB->Connect(DB_HOST,DB_USER,DB_PASS,DB_DRUPAL);

$wid = $_POST[wid];
$query = "SELECT * FROM `users_download_watched` WHERE wid = '$wid'";
$watched = $DB->GetRow($query);
foreach($watched as $key=>$value) {
  if(is_int($key))
    unset($watched[$key]);
}

$DB->Connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);

$get_cid = "SELECT id FROM `_counties` WHERE state = '$watched[State]' AND county LIKE '%$watched[County]%'";
$cid = $DB->GetRow($get_cid);

if($cid[id]) {
  $query = "SELECT * FROM `_urt_url` WHERE cid = '$cid[id]'";
  $row = $DB->GetRow($query);
}

if(!empty($row)) {

  preg_match_all('@{(.*?)}@si',$row[url],$matches);

  foreach($matches[1] as $match) {
    if(!empty($match)) {
      $key = $match;
      if($key == 'ParcelNumber' && $row[dash]) {
        $dashes = explode(",",$row[dash]);
        if(!is_array($dashes)) {
          $dashes = NULL;
        }
        $dashed = $html->dash_sequence($watched[ParcelNumber],$dashes);
        if(($row[dash] == 'strip') || ($row[dash] == '0')) {
          $watched[ParcelNumber] = $dashed[strip];
        } else {
          $watched[ParcelNumber] = $dashed[text];
        }
      }
      $replace[] = $watched[$key];
    }
  }
  foreach($matches[0] as $match) {
    if(!empty($match))
      $to_match[] = '@'.$match.'@si';
  }
  if(!empty($to_match)) {
    $url = preg_replace($to_match,$replace,$row[url]);
  } else {
    $url = $row[url];
  }

  unset($matches);
  unset($to_match);
  unset($replace);

  preg_match_all('@{(.*?)}@si',$row[post],$matches);
  foreach($matches[1] as $match) {
    if(!empty($match)) {
      $key = $match;
      if(($key == 'ParcelNumber' || $key == 'ItemNumber') && $row[dash]) {
        $dashes = explode(",",$row[dash]);
        if(!is_array($dashes)) {
          $dashes = NULL;
        }
        if($watched[ParcelNumber]) {
          $dashed = $html->dash_sequence($watched[ParcelNumber],$dashes);
          if(($row[dash] == 'strip') || ($row[dash] == '0')) {
            $watched[ParcelNumber] = $dashed[strip];
          } else {
            $watched[ParcelNumber] = $dashed[text];
          }
        }
        if($watched[ItemNumber] && !$watched[ItemNumber]) {
          $dashed = $html->dash_sequence($watched[ItemNumber],$dashes);
          if(($row[dash] == 'strip') || ($row[dash] == '0')) {
            $watched[ItemNumber] = $dashed[strip];
          } else {
            $watched[ItemNumber] = $dashed[text];
          }
        }      }
      $replace[] = $watched[$key];
    }
  }
  foreach($matches[0] as $match) {
    if(!empty($match))
      $to_match[] = '@'.$match.'@si';
  }
  if(!empty($to_match)) {
    $post = preg_replace($to_match,$replace,$row[post]);
  } else {
    $post = $row[post];
  }

  $page = $html->get_html($url,$post);

  $multicheck = explode('||',$row[xpath]);
  if(is_array($multicheck)) {
    foreach($multicheck as $multi) {
      $dom = $html->html_dom($page,$multi);
      $url = $dom[0];
      $page = $html->get_html($url);
    }
  } else {
    $query = $row[xpath];
    $dom = $html->html_dom($page,$query);
  }

  $sorted = $html->extract_data(
              $dom,
              $row[start],
              $row[rows],
              $row[spacing],
              $row[clean]
              );

  $sort = explode("|",$row[sort]);

  foreach($sorted as $sorting) {
    if(is_array($sorting)) {
      foreach($sorting as $key=>$to_sort) {
        if($sort[$key])
          $final[$sort[$key]] .= $to_sort;
      }
    }
  }

  foreach($watched as $key=>$value) {
    if(!$value) {
      $new_array[$key] .= $final[$key];
    } else {
      $new_array[$key] .= $value;
    }
  }

  $query = "UPDATE `users_download_watched` SET ";
  foreach($new_array as $key=>$value) {
    $query .= "$key='$value',";
  }
  $query = substr($query,0,-1);
  $query .= " WHERE wid='$wid'";

  $DB->Connect(DB_HOST,DB_USER,DB_PASS,DB_DRUPAL);
  $DB->Execute($query);
}