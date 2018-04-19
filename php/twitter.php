<?php

    require "vendor/autoload.php";
    require "constantKey.php";
    use Abraham\TwitterOAuth\TwitterOAuth;
    use GuzzleHttp\Exception\GuzzleException;
    use GuzzleHttp\Client;


    //Cek masukan 
    // echo $_GET['first'];
    // echo $_GET['keyword'];
    // echo $_GET['spam'];
    // echo $_GET['result_type'];
    // echo $_GET['algo'];





    $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);
    // $content = $connection->get("search/tweets",["q"=>"jokowi","lang"=>"id","result_type"=>"popular"]);
    // echo json_encode($content),"\n";


    //Lanjut
    $isFirst = $_GET['first']=='true';
    $keyword = $_GET['keyword'];
    $result_type = $_GET['result_type'];
    $algoritma = $_GET['algo'];
    $lang = 'id';
    $spam_keywords = explode(",", $_GET['spam']);
    if($isFirst){
        //First Request
        $content = $connection->get("search/tweets",[
            "q"=>$keyword,
            "lang"=>$lang,
            "result_type"=>$result_type
            ]);
    } else{
        //Next Request
        $maxid = $_GET['maxid'];
        $include_entities = $_GET['include_entities'];
        $content = $connection->get("search/tweets",[
            "q"=>$keyword,
            "lang"=>$lang,
            "result_type"=>$result_type,
            "max_id"=>$maxid,
            "include_entities"=>$include_entities
            ]);
    }

    //print_r($content);
    //$content['spam_keywords'] = $spam_keywords;
    $array = json_decode(json_encode($content), true);
    $array['spam_keywords'] = $spam_keywords;
    $array['algoritma'] = $algoritma;
    
    $client = new Client([
        'headers' => [ 'Content-Type' => 'application/json' ]
    ]);
    
    $response = $client->post('http://localhost:5000/compute',
        ['body' => json_encode($array)]
    );


    echo var_export($response->getBody()->getContents(), true);

    //echo json_encode($content);
    // $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);
    // $content = $connection->get("search/tweets",
    // ["q"=>"jokowi",
    // "lang"=>"id",
    // "result_type"=>"popular",
    // "max_id" =>"985835199316410367",
    // "include_entities" => 1
    // ]);
    // echo json_encode($content),"\n";
    // $url = "?max_id=983365180674293759&q=jokowi&lang=id&include_entities=1&result_type=popular";
    // // $query_str = parse_url($url, PHP_URL_QUERY);
    // $query_str = parse_url($url, PHP_URL_QUERY);
    // parse_str($query_str, $query_params);
    // // print_r($query_params);



?>