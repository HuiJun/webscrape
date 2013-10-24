<?php
/*
 * WebScrape class created to gather information
 * from various websites and then insert and
 * update information in a database.
 *
 * Requires ADODB, cURL, DOMDocument, and XPath
 *
 * @author Jason Han
 *
 */

class WebScrape {

  public function __construct() {
  }

  /*
   * Use cURL to retrieve HTML page. Clean HTML with Tidy
   */
  public function get_html($url,$post_string = NULL,$skip_tidy = '0') {
    $curl_connection = curl_init($url);
      curl_setopt($curl_connection, CURLOPT_CONNECTTIMEOUT, 30);
      curl_setopt($curl_connection, CURLOPT_USERAGENT,
        'Googlebot/2.1 (+http://www.google.com/bot.html)'
        );
      curl_setopt($curl_connection, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl_connection, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($curl_connection, CURLOPT_COOKIEFILE, 'cookie.txt');
      curl_setopt($curl_connection, CURLOPT_COOKIEJAR, 'cookie.txt');
//      curl_setopt($curl_connection, CURLOPT_FOLLOWLOCATION, 1);
      if($post_string) {
        curl_setopt($curl_connection, CURLOPT_POSTFIELDS, $post_string);
      }
      $html = curl_exec($curl_connection);
    curl_close($curl_connection);
    if($html && $skip_tidy) {
      $result = tidy_parse_string($html);
      $result->cleanRepair();
      return $result->value;
    } elseif($html) {
      return $html;
    }
  }
  
  /*
   * Use DOMDocument to parse HTML. Use XPath to query data
   */
  public function html_dom($html,$query,$type = 'nodeValue') {
    $dom = new DOMDocument('1.0', 'utf-8');
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    switch($type) {
      case 'nodeValue':
        foreach($xpath->query($query) as $node) {
          $nodes[] = $node->nodeValue;
        }
      break;
      case 'tagName':
        foreach($xpath->query($query) as $node) {
          $nodes[] = $node->tagName;
        }
      break;
    }
    return $nodes;
  }

  /*
   * Take data from XPath and parse into array
   */
  public function extract_data($array,$start,$rows,$spacing = '1',$clean = NULL) {
    $count = 1;
    $array_key = 0;
    if(is_array($array)) {
      foreach($array as $key=>$value) {
        if($key == $start) {
          if($clean) {
            $data[$array_key][] = $this->clean($value,$clean);
          } else {
            $data[$array_key][] = $this->clean($value);
          }
          $count++;
          $start = $start + $spacing;
          if($count > $rows) {
            $count = 1;
            $array_key++;
          }
        }
      }
    } else {
      $data[0] =  $this->clean($array);
    }
    if(is_array($data))
      array_walk_recursive(&$data,trim);
    return $data;
  }

  /*
   * Remove any unwanted characters from data string
   */
  private function clean($value,$clean = NULL) {
    $remove = array(
      "@\n@si",
      "@\r@si",
      "@\r\n@si",
      "@\n\r@si"
    );
    $value = preg_replace($remove," ",$value);
    if($clean) {
      $value = preg_replace($clean,"",$value);
    }
    $time = strtotime($value);
    if($time) {
      $value = date('Y-m-d',$time);
    }
    return utf8_encode($value);
  }

  /*
   * Add keys to generated array
   */
  public function add_keys($array,$keys) {
    foreach($array as $row) {
      $added_keys[] = array_combine($keys,$row);
    }
    return $added_keys;
  }

  /*
   * Insert values from scrapper into DB
   */
  public function insert_db($array,$table) {
    foreach($array as $arr) {
      $query = "INSERT INTO `$table` (";
      $values = "(";
      foreach($arr as $key=>$value) {
        $query .= "$key,";
        $values .= "'".addslashes($value)."',";
      }
      $query = substr($query,0,-1).')';
      $values = substr($values,0,-1).')';
      $query = $query." ".$values;
      $final[] = $query;
    }
    return $final;
  }

  public function pc_fixed_width_unpack($format_string,$data) {
    $r = array();
    for ($i = 0, $j = count($data); $i < $j; $i++) {
      $r[$i] = unpack($format_string,$data[$i]);
    }
    return $r;
  }

  public function dash_sequence($string,$array = NULL) {
    preg_match_all('@([A-Za-z0-9])@',$string,$matches);
    if(!empty($array)) {
      $i = 0;
      $x = 0;
      if(is_array($array)) {
        foreach($matches[1] as $match) {
          $output[$i] .= $match;
          if($x < ($array[$i] - 1)) {
            $x++;
          } else {
            $x = 0;
            $i++;
          }
        }
      }
    }
    if(is_array($output))
      $dashed['text'] = implode('-',$output);
    if(is_array($matches[1]))
      $dashed['strip'] = implode($matches[1]);
    $dashed['arr'] = $output;
    return $dashed;
  }
}