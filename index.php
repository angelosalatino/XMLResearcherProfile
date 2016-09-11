<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->

<?php

class Link {

    // variables
    public $node1;
    public $node2;
    public $link = 0;

    public function __construct($a1, $a2, $a3) {
        $this->node1 = $a1;
        $this->node2 = $a2;
        $this->link = $a3;
    }

    public function show() {
        echo $this->node1 . " " . $this->node2 . " " . $this->link . "<br/>";
    }

    // methods
    public function update() {
        $this->link = $this->link + 1;
    }

    public function __get($property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

}

function printPublications($xmlfile) {

    // put your code here
    $xml = simplexml_load_file($xmlfile) or die("Error: Cannot create object");
    //echo "<pre>";
    //print_r($xml);
    //echo "</pre>";
    $lastYear = $xml->publications->publication[0]->year;

    echo '<div id = "PublicationList">'
    . '<div class="year">'
    . '<p>' . $lastYear . '</p><ul>';

    foreach ($xml->publications->publication as $publication) {
        if (strcmp($lastYear, $publication->year) != 0) {
            $lastYear = $publication->year;
            echo '</ul></div>'
            . '<div class="year">'
            . '<p>' . $lastYear . '</p><ul>';
        }
        foreach ($publication->author as $author) {
            $author_arr[] = $author;
        }
        echo '<li><div class= "publication">'
        . '<p>' //open new publication view
        . '<strong>' . implode(", ", $author_arr) . '</strong>';
        unset($author_arr);
        echo ' <a href="' . $publication->post . '" target="_blank">' . $publication->title . '</a>.';

        if (!empty($publication->booktitle)) {
            echo ' In <i>' . $publication->booktitle . '</i>,';
        }
        if (!empty($publication->pages)) {
            echo ' pp. ' . $publication->pages . '.';
        }
        if (!empty($publication->publisher)) {
            echo ' ' . $publication->publisher . ', ';
        }
        echo ' ' . $publication->year;
        if (!empty($publication->source)) {
            echo ' <a href="' . $publication->source . '" target="_blank"><img src="images/pdf2.png" title="Download PDF File"/></a>';
        }
        if (!empty($publication->oro)) {
            echo ' <a href="' . $publication->oro . '" target="_blank"><img src="images/oro2.png" title="Open Research Online (ORO) Link"/></a>';
        }
        if (!empty($publication->website)) {
            echo ' <a href="' . $publication->website . '" target="_blank"><img src="images/website2.png" title="Visit External Website"/></a>';
        }
        if (!empty($publication->ee)) {
            echo ' <a href="' . $publication->ee . '" target="_blank"><img src="images/doi.png" title="DOI | Digital Object Identifier"/></a>';
        }
        if (!empty($publication->rash)) {
            echo ' <a href="' . $publication->rash . '" target="_blank"><img src="images/rash.png" title="RASH | Research Articles in Simplified HTML"/></a>';
        }
        echo '</p>'
        . '</div></li>';
    }
    echo '</ul></div>'
    . '</div>';
}

function checkGraph($xmlfile, $graphfile) {
    if (!file_exists($graphfile)) {
        createGraph($xmlfile, $graphfile);
    } else {
        if (filemtime($xmlfile) >= filemtime($graphfile)) {
            createGraph($xmlfile, $graphfile);
        }
    }
}

function createGraph($xmlfile, $graphfile) {
    echo "Creating file $graphfile now!!!<br />";
    $xml = simplexml_load_file($xmlfile) or die("Error: Cannot create object");

    // loading authors
    foreach ($xml->publications->publication as $publication) {
        foreach ($publication->author as $author) {
            $author_arr[] = $author;
        }
    }
    $uniqueauths = array_unique($author_arr);
    $auths = array();
    $auths = array_fill_keys($uniqueauths, 0);
    //unique authors + num of publications
    for ($i = 0; $i < count($author_arr); $i++) {
        $auths[(string) $author_arr[$i]] = $auths[(string) $author_arr[$i]] + 1;
    }

    //loading coauthorship
    $cooc = array();
//    foreach ($uniqueauths as $author) {
//        foreach ($uniqueauths as $author2) {
//            if (strcmp($author, $author2) != 0) {
//                $cooc[$author . $author2] = new Link($author, $author2);
//                //$cooc[$author . $author2]->show();
//            }
//        }
//    }
    unset($author_arr);
    // print_r($cooc);
    foreach ($xml->publications->publication as $publication) {
        foreach ($publication->author as $author) {
            $author_arr[] = $author;
        }
//        print_r($author_arr);
//        echo "weilaa<br/>";

        foreach ($author_arr as $author) {
            foreach ($author_arr as $author2) {
                if (strcmp($author, $author2) != 0) {
                    //echo $author . $author2;
                    if (array_key_exists($author . $author2, $cooc)) {
                        $cooc[$author . $author2]->update();
                    } else {
                        $cooc[$author . $author2] = new Link($author, $author2, 1);
                    }
                }
            }
        }

        unset($author_arr);
    }

    //printing graph on a file
    $nodes = array();
    $ids = array();
    $links = array();

    $i = 0;
    foreach ($auths as $key => $value) {
        $ids[$key] = "n" . $i;

        $tmp_node = array(
            'id' => "n" . $i,
            'label' => $key,
            'x' => rand(0, 15),
            'y' => rand(0, 15),
            'color' => "#06c",
            'size' => $value
        );
        $nodes[$i] = $tmp_node;
        $i++;
    }
    $graph["nodes"] = $nodes;



    $i = 0;
    $duplicates = array();
    foreach ($cooc as $key => $value) {
        $alternativeKey = $value->node2 . $value->node1;
        if (array_key_exists($alternativeKey, $duplicates)) {
            //do nothing 
        } else {
            $tpmlabel = (1 < $value->link) ? $value->link . ' Co-authored Publications' : '';
            $tmp_link = array(
                'id' => "e" . $i,
                'source' => $ids[(string) $value->node1],
                'target' => $ids[(string) $value->node2],
                'color' => "#eee",
                'type' => 'curve',
                'label' => $tpmlabel,
                'size' => $value->link
            );
            $links[$i] = $tmp_link;
            $i++;
            //avoid the alternative
            $duplicates[$key] = 'done';
        }
    }
    unset($duplicates);
    $graph["edges"] = $links;


    $fp = fopen($graphfile, 'w');
    fwrite($fp, json_encode($graph));
    fclose($fp);

//    echo "<pre>";
//    print_r($graph);
//    //echo json_encode($graph);
//    echo "</pre>";
}

function printGraph($graphfile) {

//    echo '<div id="container">
//    <div id="graph-container">';
    require("./graph_json.html");
//    echo '</div></div>';
}
?>

<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <link rel='stylesheet' type='text/css' href='myCss.css' />
    </head>
    <body>
        <div id="container">
<!--            <div id="PublicationList">Scroll down to see the graph</div>-->
            <?php
            $xmlfile = "myPubs.xml";
            $graphfile = "myCoauth.json";
            checkGraph($xmlfile, $graphfile);
            printPublications($xmlfile);
            printGraph($graphfile);
            ?>
        </div>
    </body>
</html>
