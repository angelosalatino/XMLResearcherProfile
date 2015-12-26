<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <link rel='stylesheet' type='text/css' href='myCss.css' />
    </head>
    <body>
        <?php
        
        // put your code here
        $xml=simplexml_load_file("myPubs.xml") or die("Error: Cannot create object");
        //echo "<pre>";
        //print_r($xml);
        //echo "</pre>";
        $lastYear = $xml->publications->publication[0]->year;
        
        echo '<div id = "PublicationList">'
                . '<div class="year">'
                . '<p>'.$lastYear.'</p><ul>';
        
        foreach($xml->publications->publication as $publication){
            if(strcmp($lastYear,$publication->year)!=0){
                $lastYear = $publication->year;
                echo '</ul></div>'
                . '<div class="year">'
                . '<p>'.$lastYear.'</p><ul>';
            }
            echo '<li><div class= "publication">'
                . '<p>' //open new publication view
                . '<strong>'.implode(", ", (array)$publication->author). '</strong>';
            echo ' <a href="'.$publication->post.'" target="_blank">'.$publication->title.'</a>.';
            
            if(!empty($publication->booktitle)) {echo ' In <i>'.$publication->booktitle.'</i>,';}
            if(!empty($publication->pages)){ echo ' pp. '.$publication->pages.'.';}
            if(!empty($publication->publisher)) {echo ' '.$publication->publisher.', ';}
            echo ' '.$publication->year;
            if(!empty($publication->source)){echo ' <a href="'.$publication->source.'" target="_blank"><img src="images/pdf.gif" alt="PDF File"/></a>';}
            if(!empty($publication->oro)){echo ' <a href="'.$publication->oro.'" target="_blank"><img src="images/oro.png" alt="ORO Link"/></a>';}
            if(!empty($publication->ee)){echo ' <a href="'.$publication->ee.'" target="_blank"><img src="images/doi.gif" alt="DOI"/></a>';}
            echo '</p>'
                . '</div></li>';
        }
        echo '</ul></div>'
        . '</div>';
        
        //echo $xml->publications->publication[0]->year;
        
        
        
        ?>
    </body>
</html>
