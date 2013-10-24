<?php
if($_GET[page]) {
  $title = array(
  '1' => 'Assessor',
  '2' => 'Document Search',
  '3' => 'Court Documents',
  '4' => 'County Website',
  '5' => 'Comparables',
  '6' => 'Local Realtors',
  '7' => 'GIS'
  );
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
  There is currently no online <?php echo $title[$_GET[page]]; ?> information available.
</body>
</html>