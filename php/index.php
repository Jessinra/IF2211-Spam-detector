<html>
    <head>
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="css/bootstrap.min.css">

        <!-- jQuery library -->
        <script src="js/jquery.min.js"></script>

        <!-- Popper JS -->
        <script src="js/popper.min.js"></script>

        <!-- Latest compiled JavaScript -->
        <script src="js/bootstrap.min.js"></script> 
    </head>

    <body>

        <nav class="navbar navbar-expand-sm bg-dark navbar-dark">
            <!-- Brand/logo -->
            <a class="navbar-brand" href="#">Stima Tugas Besar The Last</a>

            <!-- Links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="#">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">About</a>
                </li>

            </ul>
        </nav>

        <div class="container " id="kolomBesar">
            <div class="row">
                <div class="col-4">
                    <form action="/stimaweb/twitter.php?first=true" method="get" target="_blank">
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



                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form> 
                </div>
                <div class="col">
                    <button type="button" class="btn btn-primary">Primary</button>
                </div>
            </div>
        </div>
    </body>
</html>