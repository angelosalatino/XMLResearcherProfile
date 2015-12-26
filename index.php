<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->

<?php
function printPublications(){
            
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
            foreach($publication->author as $author)
            {
                $author_arr[] = $author;
            }
            echo '<li><div class= "publication">'
                . '<p>' //open new publication view
                . '<strong>'.implode(", ", $author_arr). '</strong>';
            unset($author_arr);
            echo ' <a href="'.$publication->post.'" target="_blank">'.$publication->title.'</a>.';
            
            if(!empty($publication->booktitle)) {echo ' In <i>'.$publication->booktitle.'</i>,';}
            if(!empty($publication->pages)){ echo ' pp. '.$publication->pages.'.';}
            if(!empty($publication->publisher)) {echo ' '.$publication->publisher.', ';}
            echo ' '.$publication->year;
            if(!empty($publication->source)){echo ' <a href="'.$publication->source.'" target="_blank"><img src="images/pdf.gif" title="Download PDF File"/></a>';}
            if(!empty($publication->oro)){echo ' <a href="'.$publication->oro.'" target="_blank"><img src="images/oro.png" title="Open Research Online (ORO) Link"/></a>';}
            if(!empty($publication->website)){echo ' <a href="'.$publication->website.'" target="_blank"><img src="images/website.png" title="Visit External Website"/></a>';}
            if(!empty($publication->ee)){echo ' <a href="'.$publication->ee.'" target="_blank"><img src="images/doi.gif" title="DOI | Digital Object Identifier"/></a>';}
            echo '</p>'
                . '</div></li>';
        }
        echo '</ul></div>'
        . '</div>';
}
?>

<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <link rel='stylesheet' type='text/css' href='xmlReferences.css' />
    </head>
    <body>
        <?php printPublications();  ?>
    </body>
</html>
