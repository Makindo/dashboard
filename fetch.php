<?php
/* 
utility script to make a convert a json payload of
makindo people into a a bunch of javascript (markers.js)
*/


# replace with fresh url or wget it into data.json
$dataurl = "https://dataclips.s3.amazonaws.com/vvqmcnpxyczyfxjgluuoadyrfmlw.json?response-content-type=application%2Fjson&AWSAccessKeyId=AKIAJNTVUFFR73H3LQIA&Signature=fSplAM7QN4vbOgjDetKiM%2BSVo1Y%3D&Expires=1386893734";

$data = file_get_contents("data.json");

$json = json_decode($data);

$tweeps = $json->values;

#print_r($tweeps); exit;

$i=0;


foreach($tweeps as $person)
{
#     print_r($person); exit;

     $id = $person[0];
     if($lastid == $id) continue; #hack to get only one per person
     $lastid = $id;

     $i++;
     $name = $person[1];
     #$text = nl2br(htmlentities($person[8]));
     $text = preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', $person[11]);
     $lat  = $person[9];
     $long = $person[8];
     $city = ucfirst(strtolower($person[2]));
     $state = $person[3];
     $minage = $person[5];
     $maxage = $person[6];
     $genderint = $person[7];
     $income = $person[4];
     $handle = $person[10];


     #anonymize name:
     $pieces = explode(" ",$name);
     $name = $pieces[0] . " " . substr($pieces[1], 0, 1);

     unset($gender); unset($agestr); unset($incomestr);
     if($genderint != null)
     {
       if(intval($genderint)==1)      $gender = "woman";
       else if(intval($genderint)==0) $gender = "man";
     }

     if($minage > 17) $agestr =round( ($minage+$maxage)/2 ) . "-year-old";

     if($income != null)
     {
       $incomestr = "who makes \$" . number_format($income) ."/year";
     } 
    
     # let's write javascript with php!
     print "var person$i = L.marker([$lat,$long],{icon: personIcon}).addTo(map);\n" .
           "person$i.bindPopup(\"<b>$name ($agestr $gender in $city, $state $incomestr)</b><br><i>@$handle: $text</i><br><span class=littlegray>#$id</span>\")\n";

}




?>
