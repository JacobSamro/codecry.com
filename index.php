<?php
$GLOBALS['DEPLOY']  = false;
$GLOBALS['BASE_URL'] = ''.($GLOBALS['DEPLOY']  ? "http://codecry.com/" : "http://localhost/code/");

require_once 'functions.php';
require 'Code.php';

require 'libs/Parsedown.php';

require 'vendor/autoload.php';

Twig_Autoloader::register();

$loader = new Twig_Loader_Filesystem('ui/');
$GLOBALS['twig'] = new Twig_Environment($loader);
$GLOBALS['twig']->addGlobal('base_url', $GLOBALS['BASE_URL']);


//$GLOBALS['twig'] = new Twig_Environment($loader, array(
//     'cache' => 'ui/cache/',
//));


\Slim\Slim::registerAutoloader();

$app =  new \Slim\Slim();


$app->get( '/', function() {
    $CodeStats = code_num_stats();

    $totalPrograms = totalPrograms();

    $pages = ceil($totalPrograms/10)-1;

    if(!isset($_GET['page'])){
        $activePage = 0;
    }else{
        $activePage = $_GET['page'];
    }

    $RecentPrograms = recent_programs($activePage*10);

    echo $GLOBALS['twig']->render('index.html', array(
        'title'=>'Code Snippets Repository - CodeCry',
        'stats'=> $CodeStats,
        'programs'=> $RecentPrograms,
        'pages' => $pages,
        'active_page' => $activePage));
});



#Live Search
$app->get( '/livesearch',function(){

        $query = $_GET['query'];

        print(livesearch($query,$GLOBALS['BASE_URL']));

});



#Language Filter
$app->get('/language/:language/',function($language){

    if(!isset($_GET['page'])){
        $activePage = 0;
    }else{
        $activePage = $_GET['page'];
    }

    $pages = ceil(totalProgramsInLanguage($language)/10)-1;


    $CodeStats = code_num_stats();

    $Programs = get_program_from_language($language,$activePage*10);

    //Changing cpp to C++
    if($language=='cpp'){
        $language = "C++";
    }elseif($language == 'csharp'){
        $language = "C#";
    }elseif($language == "objc"){
        $language = "Objective-C";
    }

    echo $GLOBALS['twig']->render('filter.html', array(
        'title'=>'Programs in '.ucfirst($language).' - CodeCry.com',
        'language'=>ucfirst($language),
        'stats'=> $CodeStats,
        'programs'=> $Programs,
        'pages' => $pages,
        'active_page' => $activePage
        ));


});


#Programs Page
$app->get('/:language/:url_id',function($language,$url_id){

    $Program = get_program($language,$url_id);

    $Program =   (array) $Program;

    $Alt = (array) alt_available($url_id);

    $Alt = array_diff($Alt, array($language));

    $Alt_size = sizeof($Alt);

    $random = random_programs();

    $Parsedown = new Parsedown();
    $Parsedown->setBreaksEnabled(true);
    $Parsedown->setMarkupEscaped(true);
    $Program['notes'] = $Parsedown->text($Program['notes']);

    //Changing cpp to C++
    if($Program['lang']=='cpp'){
        $Program['lang'] = "C++";
    }elseif($Program['lang'] == 'csharp'){
        $Program['lang'] = "C#";
    }elseif($Program['lang'] == "objc"){
        $Program['lang'] = "Objective-C";
    }

    echo $GLOBALS['twig']->render('program.html', array(
        'title' => $Program['title'].' in '.ucfirst($Program['lang']),
        'program' => $Program,
        'alt_available' => $Alt_size,
        'alts' => $Alt,
        'random' => $random));


});


#Program Download
$app->get('/:language/:url_id/download',function($language,$url_id){

if(!empty($_SERVER['HTTP_USER_AGENT']) and preg_match('~(bot|crawl)~i', $_SERVER['HTTP_USER_AGENT'])){
header("HTTP/1.0 404 Not Found");
exit();
}

    $Program = get_program($language,$url_id);


    $basename = basename($Program->title.$GLOBALS['EXTENSION'][$Program->lang]);
    $filedata = $Program->content;

    // THESE HEADERS ARE USED ON ALL BROWSERS
    header("Content-Type: application-x/force-download");
    header("Content-Disposition: attachment; filename=$basename");
    header("Content-length: " . (string)(strlen($filedata)));
    header("Expires: ".gmdate("D, d M Y H:i:s", mktime(date("H")+2, date("i"), date("s"), date("m"), date("d"), date("Y")))." GMT");
    header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");

    // THIS HEADER MUST BE OMITTED FOR IE 6+
    if (FALSE === strpos($_SERVER["HTTP_USER_AGENT"], 'MSIE '))
    {
        header("Cache-Control: no-cache, must-revalidate");
    }

    // THIS IS THE LAST HEADER
    header("Pragma: no-cache");

    // FLUSH THE HEADERS TO THE BROWSER
    flush();

    // CAPTURE THE FILE IN THE OUTPUT BUFFERS - WILL BE FLUSHED AT SCRIPT END
    ob_start();
    echo $filedata;


});


$app->get('/search',function(){

if(!empty($_SERVER['HTTP_USER_AGENT']) and preg_match('~(bot|crawl)~i', $_SERVER['HTTP_USER_AGENT'])){
header("HTTP/1.0 404 Not Found");
exit();
}

$search_query =  $_GET['query'];

$query = $GLOBALS['mysqli']->query("SELECT * FROM code WHERE title LIKE '%$search_query%' LIMIT 20");

$programs = array();

while($data = $query->fetch_object()){
    array_push($programs,array(
        "lang" => $data->lang,
        "title" => $data->title,
        "time" => strtotime($data->dtime),
        "link" => make_link($data->lang.'/'.$data->url_id),
        "url_id" => $data->url_id));
}

echo $GLOBALS['twig']->render('search.html', array(
        'title'=> $search_query .' - Search | CodeCry.com',
        'programs'=> $programs,
        'search_query' => $search_query));
});


$app->get('/legal/terms/',function(){
    echo $GLOBALS['twig']->render('terms.html');
});
$app->get('/legal/privacy/',function(){
    echo $GLOBALS['twig']->render('privacy.html');
});
$app->run();
