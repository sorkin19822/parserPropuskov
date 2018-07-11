<?php
# подключаем библиотеку
include('simple_html_dom.php');
class ParserDoor{
    private $url;
    private $user;
    private $password;
    private $firstStep;
    private $lastStep;

    function __construct($user, $password, $firstStep, $lastStep)
    {
        $this->user = $user;
        $this->password = $password;
        $this->firstStep = $firstStep;
        $this->lastStep = $lastStep;
    }

    function setFirstStep($firstStep){
        return $this->firstStep = $firstStep;
    }

    function setLastStep($lastStep){
        return $this->lastStep = $lastStep;
    }


    public function startParser(){
        $fileopen=fopen("file.txt", "a+");
        for ($i=$this->firstStep; $i<=$this->lastStep; $i++) {
            try {
                $this->url = "http://{$this->user}:{$this->password}@10.101.101.202/Event.htm?page=".$i;
                $html = file_get_html($this->url);
            } catch (Exception $e) {
                echo 'На странице: '.$this->url.' Железяка здохла, пробуем дальше....',  $e->getMessage(), "\n";
            }

            foreach ($html->find('tr') as $article) {
                $item['id'] = $article->children(0)->plaintext;
                if ($item['id'] === 'No') {
                    continue;
                }
                $item['time'] = $article->children(1)->plaintext;
                $item['door'] = $article->children(2)->plaintext;
                $item['name'] = $article->children(3)->plaintext;
                $item['passCard'] = $article->children(4)->plaintext;
                $item['action'] = $article->children(5)->plaintext;
                $str=$item['id'].'|'.$item['time'].'|'.$item['door'].'|'.$item['name'].'|'.$item['passCard'].'|'.$item['action'].PHP_EOL;
                echo $str;
                echo '<br>';
                fwrite($fileopen,$str);
            }
        }
        fclose($fileopen);
    }
}
if (!isset($_SESSION)) {
    session_start();
}
?>

<html>
<head>
    <title>!DOCTYPE</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <!-- UIkit CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-rc.6/css/uikit.min.css" />

    <!-- UIkit JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-rc.6/js/uikit.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-rc.6/js/uikit-icons.min.js"></script>

    <script
            src="http://code.jquery.com/jquery-3.3.1.min.js"
            integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
            crossorigin="anonymous"></script>
</head>
<body>
<div class="uk-flex uk-flex-between uuk-margin-remove" uk-grid style="padding-top: 50px">
    <form class="uk-margin-auto" action="/index.php" method="post">
        <div class="uk-margin uk-text-center">
            <div class="uk-inline">
                <span class="uk-form-icon" uk-icon="icon: user"></span>
                <input class="uk-input" type="text" name="user">
            </div>
        </div>

        <div class="uk-margin uk-text-center">
            <div class="uk-inline">
                <span class="uk-form-icon uk-form-icon-flip" uk-icon="icon: lock"></span>
                <input class="uk-input" type="text" name="password">
            </div>
        </div>

        <div class="uk-margin" uk-grid>
            <div class="uk-inline uk-width-1-2">
                <span class="uk-form-icon">С</span>
                <input class="uk-input uk-form-width-small" type="text" name="firstStep">
            </div>
            <div class="uk-inline uk-width-1-2">
                <span class="uk-form-icon">По &nbsp;</span>
                <input class="uk-input uk-form-width-small" type="text" name="lastStep">
            </div>
        </div>
        <p uk-margin class="uk-text-center">
            <input class="uk-button uk-button-primary" type="submit" value="Панеслася!!!">
        </p>
    </form>
</div>
<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $parser = new ParserDoor($_POST['user'],$_POST['password'],$_POST['firstStep'],$_POST['lastStep']);
    $parser->startParser();
    $_SESSION['postdata'] = $_POST;
    unset($_POST);
    exit;
}
?>
</body>
</html>