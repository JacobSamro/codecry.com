<?php




$GLOBALS['EXTENSION'] = array(
    'java' => '.java',
    'cpp' => '.cpp',
    'c' => '.c',
    'python' => '.py',
    'ruby' => '.rb',
    'php' => '.php',
    'javascript' => '.js'
);


$GLOBALS['mysqli'] = mysqli_init();
$GLOBALS['mysqli']->options(MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, true);
$GLOBALS['mysqli']->ssl_set(NULL, NULL, "/etc/ssl/certs/ca-certificates.crt", NULL, NULL);

if(!$GLOBALS['mysqli']->real_connect($host, $username, $password, $database)) {
    die("Connection error: " . $GLOBALS['mysqli']->connect_error);
}

if ($GLOBALS['mysqli']->connect_error) {
    die("Connection failed: " . $GLOBALS['mysqli']->connect_error);
}


function make_link($raw)
{
    return $GLOBALS['BASE_URL'] . $raw;
}

function get_program_from_language($lang, $offset)
{


    $query = $GLOBALS['mysqli']->query("SELECT * FROM code  WHERE lang='$lang' ORDER BY dtime DESC LIMIT $offset,10");

    $stack = array();

    while ($data = $query->fetch_object()) {

        array_push($stack, array(
            "title" => $data->title,
            "author" => $data->author,
            "time" => strtotime($data->dtime),
            "lang" => $data->lang,
            "link" => make_link($data->lang . '/' . $data->url_id),
            "lang_legal" => toFullName($data->lang)
        )
        );

    }

    return $stack;
}

function recent_programs($offset)
{

$query=$GLOBALS['mysqli']->query("SELECT * FROM code  ORDER BY dtime DESC LIMIT $offset,10");

    $stack = array();

    while ($data = $query->fetch_object()) {

        array_push($stack, array(
            "title" => $data->title,
            "author" => $data->author,
            "time" => strtotime($data->dtime),
            "lang" => $data->lang,
            "link" => make_link($data->lang . '/' . $data->url_id),
            "lang_legal" => toFullName($data->lang)
        )
        );

    }
    return $stack;

}

function get_program_from_id($id)
{

    $query = $GLOBALS['mysqli']->query("SELECT content,lang FROM code WHERE id=$id");
    $data = $query->fetch_object();
    return $data;

}

function get_program($lang, $url)
{

    $query = $GLOBALS['mysqli']->query("SELECT * FROM code WHERE lang='$lang' AND url_id='$url'");
    $data = $query->fetch_object();
    $data->enc_content = $data->content;
    $data->content = htmlspecialchars_decode($data->content, ENT_QUOTES);
    $data->notes = htmlspecialchars_decode($data->notes, ENT_QUOTES);
    $data->lang_legal = toFullName($data->lang);
    return $data;
}

function code_num_stats()
{

    $query = $GLOBALS['mysqli']->query("SELECT lang,COUNT(lang) AS count  FROM code GROUP BY lang");

    $DStore = array();

    while ($data = $query->fetch_object()) {

        $DStore[$data->lang] = $data->count;
    }
    return $DStore;

}

function livesearch($keyword, $BASE_URL)
{


    $query = $GLOBALS['mysqli']->query("SELECT * FROM code  WHERE title LIKE '%$keyword%' LIMIT 10;");

    $dataOUT = "<ul>";
    while ($data = $query->fetch_object()) {
        $dataOUT .= "<li><a href='https://code.samro.in/" . $data->lang . "/" . $data->url_id . "' >" . $data->title . " in " . toFullName($data->lang) . "</a></li>";
    }

    return $dataOUT . "</ul>";

}


function totalPrograms()
{
    return $GLOBALS['mysqli']->query("SELECT COUNT(*) AS TOTAL FROM code")->fetch_object()->TOTAL;
}

function totalProgramsInLanguage($lang)
{
    return $GLOBALS['mysqli']->query("SELECT COUNT(*) AS TOTAL FROM code WHERE lang='$lang'")->fetch_object()->TOTAL;
}


function alt_available($url_id)
{

    $query = $GLOBALS['mysqli']->query("SELECT lang FROM code  WHERE url_id = '$url_id'");

    $DStore = array();

    while ($data = $query->fetch_object()) {

        array_push($DStore, $data->lang);

    }

    return $DStore;
}

function random_programs()
{

    $query = $GLOBALS['mysqli']->query("SELECT * FROM code ORDER BY RAND() LIMIT 10");

    $stack = array();

    while ($data = $query->fetch_object()) {

        array_push($stack, array(
            'lang' => $data->lang,
            'title' => $data->title,
            'url_id' => $data->url_id,
            'lang_legal' => toFullName($data->lang)
        )
        );

    }

    return $stack;

}

/* Programs by Language Page Meta Description */

$GLOBALS['description'] = array(
    "c" => "C Code Snippets : C - the most popular general purpose programming language developed by Dennis Ritchie at  Bell Labs",
    "cpp" => "C++ Code Snippets : C++ - Object Oriented successor of C, designed by Bjarne Stroustrup at Bell Labs",
    "python" => "Python Code Snippets : Python - Dynamic, interpreted, interactive, object-oriented programming language with good code readability, designed by Guido van Rossum",
    "perl" => "Perl Code Snippets : Perl - high-level, general-purpose, interpreted, dynamic programming language especially designed for text processing, developed by Larry Wall",
    "php" => "PHP Code Snippets : PHP - Server scripting language, and a powerful tool for making dynamic web pages, designed by Rasmus Lerdorf",
    "ocaml" => "OCaml Code Snippets : OCaml is a general purpose programming language with an emphasis on expressiveness and safety. ",
    "clojure" => "Clojure Code Snippets : Clojure - general-purpose programming language with an emphasis on functional programming, created by Rich Hickey",
    "ruby" => "Ruby Code Snippets : Ruby - A dynamic, open source programming language with a focus on simplicity and productivity, designed by Yukihiro Matsumoto",
    "javascript" => "JavaScript Code Snippets : JavaScript - high-level, dynamic, untyped, and interpreted programming language, designed by Brendan Eich",
    "objc" => "Objective-C Code Snippets : Objective-C - general-purpose, object-oriented programming language, designed by Brad Cox and Tom Love",
    "swift" => "Swift Code Snippets : Swift - general-purpose, multi-paradigm, compiled programming language, designed by Chris Lattner and Apple Inc.",
    "go" => "GO Code Snippets : Go is an open source programming language created at Google in 2007 by Robert Griesemer, Rob Pike, and Ken Thompson",
    "nodejs" => "Node.JS Code Snippets : Node.JS - Event-driven I/O server-side JavaScript environment based on V8, designed by Ryan Dahl",
    "android" => "Android Sample Projects : Android - mobile operating system based on the Linux kernel, developed by Google",
    "ios" => "iOS Sample projects : iOS - mobile operating system created and developed by Apple Inc. and distributed exclusively for Apple hardware",
    "csharp" => "C# Code Snippets : C# - simple, modern, general-purpose, object-oriented programming language developed by Microsoft within its .NET initiative led by Anders Hejlsberg",
    "java" => ""
);

/* Programs by Language Page Meta Description */

$GLOBALS['keywords'] = array(
    "c" => "c programs, programs in c, c programs site, c, c basics, c tutorial, c code",
    "cpp" => "c++ programs, programs in c++, c++ programs site, c++, c++ basics, c++ tutorial, c++ code",
    "python" => "python programs, programs in python, python programs site, python, python basics, python tutorial, python scripts, python codes, python2 programs, python3 programs",
    "perl" => "perl programs, programs in perl, perl programs site, perl, perl basics, perl tutorial, perl scripts, perl codes",
    "php" => "php scripts, codes in php, php coding site, php, php basics, php tutorial, php scripts, php codes, php snippets, php programs",
    "ocaml" => "ocaml scripts, codes in ocaml, ocaml coding site, ocaml, ocaml basics, ocaml tutorial, ocaml scripts, ocaml codes, ocaml snippets, ocaml programs",
    "clojure" => "clojure scripts, codes in clojure, clojure coding site, clojure, clojure basics, clojure tutorial, clojure scripts, clojure codes, clojure snippets, clojure programs",
    "ruby" => "ruby scripts, codes in ruby, ruby coding site, ruby, ruby basics, ruby tutorial, ruby scripts, ruby codes, ruby snippets, ruby programs",
    "javascript" => "javascript scripts, codes in javascript, javascript coding site, javascript, javascript basics, javascript tutorial, javascript scripts, javascript codes, javascript snippets, javascript programs",
    "objc" => "Objctive C scripts, codes in Objctive C, Objctive C coding site, Objctive C, Objctive C basics, Objctive C tutorial, Objctive C scripts, Objctive C codes, Objctive C snippets, Objctive C programs, Objctive-C",
    "swift" => "swift scripts, codes in swift, swift coding site, swift, swift basics, swift tutorial, swift scripts, swift codes, swift snippets, swift programs",
    "go" => "go scripts, codes in go, go coding site, go, go basics, go tutorial, go scripts, go codes, go snippets, go programs",
    "nodejs" => "nodejs scripts, codes in nodejs, nodejs coding site, nodejs, nodejs basics, nodejs tutorial, nodejs scripts, nodejs codes, nodejs snippets, nodejs programs",
    "android" => "android scripts, codes in android, android coding site, android, android basics, android tutorial, android scripts, android codes, android snippets, android programs",
    "ios" => "ios scripts, codes in ios, ios coding site, ios, ios basics, ios tutorial, ios scripts, ios codes, ios snippets, ios programs",
    "csharp" => "c# scripts, codes in c#, c# coding site, c#, c# basics, c# tutorial, c# scripts, c# codes, c# snippets, c# programs",
    "java" => ""
);

function toFullName($lang)
{

    switch ($lang) {

        case 'c':
            return 'C';
        case 'cpp':
            return 'C++';
        case 'python':
            return 'Python';
        case 'php':
            return 'PHP';
        case 'ruby':
            return 'Ruby';
        case 'perl':
            return 'Perl';
        case 'java':
            return 'Java';
        case 'javascript':
            return 'JavaScript';
        case 'csharp':
            return 'C#';
        case 'objc':
            return 'Objective-C';
        case 'swift':
            return 'Swift';
        case 'ocaml':
            return 'OCaml';
        case 'go':
            return 'GO';
        case 'clojure':
            return 'Clojure';
        case 'nodejs':
            return 'Node.JS';
        case 'android':
            return 'Android';

        default:
            return 0;

    }

}