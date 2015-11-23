<?php

$host		=	"";
$username	=	"";
$password	=	"";
$database	=	"";

$GLOBALS['EXTENSION']  = array(
    'java'  => '.java',
    'cpp'   => '.cpp',
    'c'     => '.c',
    'python'=> '.py',
    'ruby'  => '.rb',
    'php'   => '.php',
    'javascript'=>'.js');




$GLOBALS['mysqli'] = new mysqli($host ,$username,$password,$database);

if ($GLOBALS['mysqli']->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


function make_link($raw){
    return "https://www.aroliant.com/code/".$raw;
}

function get_program_from_language($lang){
$query=$GLOBALS['mysqli']->query("SELECT * FROM code  WHERE lang='$lang' ORDER BY dtime DESC LIMIT 10");

$DStore = array();
$i=0;
while ( $data = $query->fetch_object()) {

    $DStore[$i]["title"]    = $data->title;
    $DStore[$i]["author"]   = $data->author;
    $DStore[$i]["time"]     = strtotime($data->dtime);
    $DStore[$i]["lang"]     = $data->lang;
    $DStore[$i]["link"]     = make_link($data->lang.'/'.$data->url_id);
$i++;
}

return $DStore;
}

function recent_programs(){
$query=$GLOBALS['mysqli']->query("SELECT * FROM code  ORDER BY dtime DESC LIMIT 5");

$DStore = array();
$i=0;
while ( $data = $query->fetch_object()) {

    $DStore[$i]["title"]    = $data->title;
    $DStore[$i]["author"]   = $data->author;
    $DStore[$i]["time"]     = strtotime($data->dtime);
    $DStore[$i]["lang"]     = $data->lang;
    $DStore[$i]["link"]     = make_link($data->lang.'/'.$data->url_id);
$i++;
}

return $DStore;
}

function get_program_from_id($id){

    $query = $GLOBALS['mysqli']->query("SELECT content,lang FROM code WHERE id=$id");
    $data = $query->fetch_object();
    return $data;

}

function get_program($lang,$url){

    $query = $GLOBALS['mysqli']->query("SELECT * FROM code WHERE lang='$lang' AND url_id='$url'");
    $data = $query->fetch_object();
    return $data;
}

function code_num_stats(){
    $query = $GLOBALS['mysqli']->query("SELECT lang,COUNT(lang) AS count  FROM code GROUP BY lang");

$DStore = array();

while ( $data = $query->fetch_object()) {

    $DStore[$data->lang] = $data->count;
}
return $DStore;

}

function livesearch($keyword,$BASE_URL){


$query=$GLOBALS['mysqli']->query("SELECT * FROM code  WHERE title LIKE '%$keyword%' LIMIT 10;");

$dataOUT = "<ul>";
while ( $data = $query->fetch_object()) {
    $dataOUT .= "<li><a href='".$BASE_URL.$data->lang."/".$data->url_id."' >".$data->title."</a></li>";
}

return $dataOUT."</ul>";

}
