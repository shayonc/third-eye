<html>
<head>
<title>Online PHP Script Execution</title>
</head>
<body>
<?php
  $string = file_get_contents("sampleData.json");

$json_o=json_decode($string);
foreach($json_o->response->annotationResults[0]->labelAnnotations as $p) {
    $confidence = $p->locations[0]->confidence;
    /*
    show the objects with confidence >= 0.95
    */
    if($confidence >= 0.95) {
        echo 'Name: '.$p->description.'    Matching rate: '.$confidence."\r\n";
    }

}


?>
</body>
</html>
