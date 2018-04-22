<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <title>Twitter Spam Detector</title>

        <!-- Bootstrap CSS CDN -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <!-- Our Custom CSS -->
        <link rel="stylesheet" href="css/style.css">
        <!-- Scrollbar Custom CSS -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.min.css">

    </head>
    <body>

        <div class="wrapper">
            <!-- Sidebar Holder -->
            <nav id="sidebar">
                <div class="sidebar-header">
                    <h3>Twitter Spam Detector</h3>
                </div>

                <ul class="list-unstyled components">
                    <div class="container" id="kolomBesar">
                        <div class="row">
                            <div class="col-xs-3">
                                <form action="index.php" method="get">
                                    <input type="hidden" name="first" value="true">
                                    <div class="form-group">
                                        <label for="keyword">Input Keyword Search : </label>
                                        <input type="text" class="form-control" id="keyword" name="keyword">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="spam">Input Spam Word :</label>
                                        <input type="text" class="form-control" id="spam" name="spam">
                                    </div>
                                    <div class="form-group">
                                        <label for="result_type">Select tweet type : </label>
                                        <select class="form-control" id="result_type" name="result_type">
                                            <option>popular</option>
                                            <option>recent</option>
                                            <option>mixed</option>
                                        </select>
                                    </div> 

                                    <div class="form-group">
                                        <label for="algo">Select algorithm : </label>
                                        <select class="form-control" id="algo" name="algo">
                                            <option>KMP</option>
                                            <option>BoyerMoore</option>
                                            <option>Regex</option>
                                        </select>
                                    </div> 

                                    <ul class="list-unstyled CTAs">
                                        <input type="submit" class="btn btn-default submit-btn" value="Submit">
                                    </ul>
                                </form> 
                            </div>
                        </div>
                    </div>
                </ul>


            </nav>

            <!-- Page Content Holder -->
            <div id="content">

                <nav class="navbar navbar-default">
                    <div class="container-fluid">

                        <div class="navbar-header">
                            <button type="button" id="sidebarCollapse" class="btn btn-info navbar-btn">
                                <i class="glyphicon glyphicon-align-left"></i>
                                <span>Search Keyword</span>
                            </button>
                        </div>

                        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                            <ul class="nav navbar-nav navbar-right">
                                <li><a>Tugas Besar Stima oleh </a></li>
                                <li><a href="#" target="_blank">Jessin</a></li>
                                <li><a href="#" target="_blank">Wildan</a></li>
                                <li><a href="#" target="_blank">Hafizh</a></li>
                            </ul>
                        </div>
                    </div>
                </nav>
                <?php

                    require "vendor/autoload.php";
                    require "constantKey.php";
                    use Abraham\TwitterOAuth\TwitterOAuth;
                    use GuzzleHttp\Exception\GuzzleException;
                    use GuzzleHttp\Client;

                    if (isset($_GET["spam"]) && !empty($_GET["spam"])) {

                        $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);

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
                            //echo 'next<br>';
                            $maxid = $_GET['max_id'];
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
                        $array = json_decode(json_encode($content), true);
                        $array['spam_keywords'] = $spam_keywords;
                        $array['algoritma'] = $algoritma;
                        

                        //Fase Request ke python flask
                        $client = new Client([
                            'headers' => [ 'Content-Type' => 'application/json' ]
                        ]);
                        
                        $response = $client->post('http://localhost:5000/compute',
                            ['body' => json_encode($array)]
                        );

                        $arrayHasilAlgoritma = json_decode($response->getBody()->getContents(),true);
                        if (array_key_exists("next_results",$arrayHasilAlgoritma['search_metadata'])){
                            $urlNext = $arrayHasilAlgoritma['search_metadata']['next_results'];
                            $query_str = parse_url($urlNext, PHP_URL_QUERY);
                            parse_str($query_str, $query_params);
                            $arrayHasilAlgoritma['max_id'] = $query_params['max_id'];
                            $arrayHasilAlgoritma['include_entities'] = $query_params['include_entities'];
                            $arrayHasilAlgoritma['result_type'] = $query_params['result_type'];
                            $arrayHasilAlgoritma['q'] = $keyword;

                            
                        }
                        drawTweets($arrayHasilAlgoritma);

                    }

                    function drawTweets($arrays) {
                        foreach($arrays['statuses'] as $stats) {
                            $text = $stats['text'];
                            $img = $stats['user']['profile_image_url'];
                            $time = $stats['created_at'];
                            $name = $stats['user']['screen_name'];
                            if (empty($stats['spam_occurrence'])) {
                                $tweet = '
                                        <div class="message-avatar">
                                            <img src="'. $img .'" alt="">
                                        </div>
                                        <div class="message-body">
                                            <div class="message-body-heading">
                                                <h5>@'. $name .'<span class="unread">Not Spam</span></h5>
                                                <span>'. $time .'</span>
                                            </div>
                                            <p>'. $text .'</p>
                                        </div>
                                        <div class="line"></div>';
                                echo $tweet;
                            } else {
                                $pembuka = '<kbd>';
                                $penutup = '</kbd>';
                                $textBaru = '';
                                $indexAwal = 0;
                                //Pemrosesan text baru highliting
                                for($i=0;$i<count($stats['spam_occurrence']);$i++){
                                    $textBaru = $textBaru.substr($text,$indexAwal,$stats['spam_occurrence'][$i][0]-1-$indexAwal+1);
                                    $textBaru = $textBaru.$pembuka.substr($text,$stats['spam_occurrence'][$i][0],$stats['spam_occurrence'][$i][1]-$stats['spam_occurrence'][$i][0]).$penutup;
                                    $indexAwal = $stats['spam_occurrence'][$i][1];
                                }

                                if($indexAwal<strlen($text)){
                                    $textBaru = $textBaru.substr($text,$indexAwal,strlen($text)-1-$indexAwal+1);
                                }
                                $tweet = '
                                        <div class="message-avatar">
                                            <img src="'. $img .'" alt="">
                                        </div>
                                        <div class="message-body">
                                            <div class="message-body-heading">
                                                <h5>@'. $name .'<span class="important">Spam</span></h5>
                                                <span>'. $time .'</span>
                                            </div>
                                            <p>'. $textBaru .'</p>
                                        </div>
                                    <div class="line"></div>';
                                echo $tweet; 
                            }
                        }  
                        $tweet='';
                        if(array_key_exists("next_results",$arrays['search_metadata'])){
                            $tweet = $tweet.'<a href="'.'index.php?first=false&keyword='.$_GET['keyword'].'&max_id='.$arrays['max_id'].'&include_entities='.$arrays['include_entities'].'&spam='.$_GET['spam'].'&result_type='. $_GET['result_type'].'&algo='. $_GET['algo'].
                            '" class="btn btn-primary btn-lg active" role="button" aria-pressed="true">Next Result</a>';
                        }

                        echo $tweet;
                    }
                ?>
            </div>
        </div>

        <!-- jQuery CDN -->
        <script src="https://code.jquery.com/jquery-1.12.0.min.js"></script>
        <!-- Bootstrap Js CDN -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <!-- jQuery Custom Scroller CDN -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.concat.min.js"></script>
                    
        <script src="js/script.js"></script>
    </body>
</html>
