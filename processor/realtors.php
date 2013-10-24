<?php
require('../webscrape.class.php');
require('../include/constant.inc.php');
require('../adodb/adodb.inc.php');

$html = new WebScrape();

if($_GET[ZipCode]) {
  $data = $_GET[ZipCode];
  if(preg_match('@-@',$_GET[ZipCode])) {
    preg_match('@(.*?)-@si',$zipcode,$matches);
    $data = $matches[1];
  }
} elseif($_GET[CityName] && $_GET[State]) {
  $cityname = preg_replace("@\s@si","-",$_GET[CityName]);
  $data = ucwords(strtolower($cityname)) .'_'. ucwords(strtolower($_GET[State]));
}


$url = "http://www.realtor.com/realestateagents/$data/realtorType-Agent";

$page = $html->get_html($url);
preg_match_all('@<div agentid="(.*?)"(.*?)</div>(.*?)</div>(.*?)</div>(.*?)</div>(.*?)</div>(.*?)</div>@si',$page,$match);

$i = 0;
$agent[page] = '//div[@agentid]//a/@href';
$agent[name] = '//div[@agentid]//a/em';
$agent[city] = '//div[@agentid]//div[@class="city"]';
$agent[pic] = '//div[@agentid]//img/@src';
$agent[contact] = '//div[@agentid]//ul/li';

foreach($match[0] as $new_page) {
  $dom = new DOMDocument('1.0', 'utf-8');
  @$dom->loadHTML($new_page);
  $xpath = new DOMXPath($dom);
  foreach($agent as $key=>$query) {
    foreach($xpath->query($query) as $node) {
      if($key == 'name' || $key == 'city' || $key == 'contact') {
        $temp = trim($node->nodeValue);
        if($temp) {
          $new_string = preg_replace("@\n@"," ",$temp);
          if($key == 'contact') {
            $agents[$key][] = ucwords(strtolower($new_string));
          } else {
            $agents[$key] = ucwords(strtolower($new_string));
          }
        }
      } else {
        $agents[$key][] = trim($node->nodeValue);
      }
    }
  }
  $agentid = $match[1][$i];
  $i++;
  $final[] = $agents;
  unset($agents);
}
?>
<html>
<head>
<style>
body {
  font-family:Tahoma,Helvetica,sans-serif;
  text-align:center;
  background-color:#fffdd7;
  font-size:18px;
  font-weight:bolder;
  color:#be1a20;
}
.comps {
  margin:30px;
  padding: 15px;
  border-style:solid;
  border-width: 1px;
  border-color: #000;
  background-color:#eaeaea;
  text-align:left;
  position:relative;
}
.image-container {
  float:left;
  text-align:center;
  margin-right:20px;
}
.contact-details {
  list-style:none;
}
.company {
  font-weight:bolder;
}
.city {
  font-size:0.9em;
}
.phone {

}
</style>
</head>
<body>
<?php
if($data) {
  foreach($final as $each) {
?>
  <div class="comps">
    <div class="image-container">
      <img src="<?php echo ($each[pic][0] ? $each[pic][0] : '/sites/all/custom/images/nobody.jpg'); ?>" style="width:100px;" /><br />
      <img src="<?php echo ($each[pic][1] ? $each[pic][1] : '/sites/all/custom/images/spacer.gif'); ?>" style="width:70px;"/>
      <div style="clear:both;"></div>
    </div>
    <a style="font-size:20px;"><?php echo $each[name]; ?></a><br />
    <span class="city"><?php echo $each[city]; ?></span>
    <div class="contact-info">
      <ul class="contact-details">
        <li class="company"><?php echo $each[contact][0]; ?></li>
<?php
    $x = 0;
    foreach($each[contact] as $contact) {
      if($x) {
        echo '
          <li class="phone">'.$contact.'</li>
        ';
      } else {
        $x = 1;
      }
    }

?>
      </ul>
    </div>
    <div style="clear:both;"></div>
  </div>
<?php
  }
} else {
?>
  Please enter either a Zipcode or City.
<?php
}
?>
</body>
</html>