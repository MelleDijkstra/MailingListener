<?php

// include Composer autoloader
require 'vendor/autoload.php';

// autoload classes from /classes directory
spl_autoload_register(function($class) {
   include 'classes/'.$class.'.php';
});

session_start();

$GLOBALS["db"] = DatabaseManager::Instance('mysql:host=localhost;dbname=jobapp','root',null)->getDB();

Flight::route('/', function() {
    include 'templates/header.html';
    // go to next route by returning true
    return true;
});

Flight::route('/', function(){

    include 'forms/vacancyform.html';

    $sql = "SELECT * FROM vacancies";

    echo "<h1>Vacancies</h1>";
    foreach($stmt = $GLOBALS["db"]->query($sql, PDO::FETCH_OBJ) as $vacancy) {
        echo '<h3>'.$vacancy->name.'</h3>';
        echo '<div class="content" style="width: 500px;">'.$vacancy->content.'</div>';
    }

    echo '<hr />';

    echo '<a href="subscribers/add">Add subscriber</a>';

});

Flight::route('/createjob', function() {

    try{
        if(!Validator::name(trim($_POST["name"]),null,50)) throw new Exception("Name not valid");
        if(empty($_POST["content"])) throw new Exception("Content of job vacancy can't be empty!");

        // validation complete, create database connection and create vacancy
        $stmt = $GLOBALS["db"]->prepare("INSERT INTO vacancies (name, content) VALUES (:name,:content)");
        $stmt->bindParam(':name', $_POST["name"]);
        $stmt->bindParam(':content', $_POST["content"]);

        if($stmt->execute()) {

            echo 'Vacancy created! going to send mail... <br /><a href="/">Home</a><br />';

            try{

                $stmt = $GLOBALS["db"]->query("SELECT mail FROM subscribers");
                $subscribers = $stmt->fetchAll(PDO::FETCH_OBJ);

                // send mail to subscribers
                $mail = new PHPMailer();

                //$mail->SMTPDebug = 4;

                $mail->isSMTP();
                $mail->Host = "smtp.gmail.com";
                $mail->Port = 465;
                $mail->SMTPAuth = true;
                $mail->Username = "yourmail@example.com";
                $mail->Password = "YourPasswor$";
                $mail->SMTPSecure = 'ssl';

                $mail->setFrom('mailer@example.com', 'Mailer name');
                // loop through each subscriber and add to addresses list
                // antoher option is to send a mail to separate from each other
                foreach($subscribers as $subscriber) {
                    $mail->addAddress($subscriber->mail);
                }
                $mail->isHTML(true);

                $mail->Subject = 'New job "'.$_POST["name"].'", apply now!';
                $mail->Body = include 'templates/mailtemplate.phtml';

                if(!$mail->send()) {
                    echo '<pre>Message could not be sent.<br />';
                    echo 'Mailer error: '.$mail->ErrorInfo."</pre>";
                } else {
                    echo 'Everyone is notified!<br />Mails are send, Horray!';
                }
            } catch(Exception $e) {
                echo $e->getMessage();
            }

        }

    } catch(Exception $e) {
        echo $e->getMessage();
        echo '<a href="/">Try again</a>';
    }

});

Flight::route('/subscribers(/@action)',function($action) {

    switch($action) {
        case 'add':
            include 'forms/addsubform.html';
            break;
        case 'validate':
            try{
                if(!Validator::email($_POST["email"])) throw new Exception("Email is not valid");

                $stmt = $GLOBALS["db"]->prepare("INSERT INTO subscribers (mail) VALUES (:mail)");
                $stmt->bindParam('mail', $_POST["email"]);

                if($stmt->execute()) {
                    echo 'Subscriber added to mailing list';
                } else {
                    throw new Exception('Something went wrong!');
                }

            } catch(Exception $e) {
                echo $e->getMessage();
            }
            break;
        default:
            $stmt = $GLOBALS["db"]->query("SELECT mail FROM subscribers");
            $subsribers = $stmt->fetchAll(PDO::FETCH_OBJ);
            echo '<h1>Mailing List</h1>';
            echo '<ul>';
            foreach($subsribers as $sub) {
                echo '<li>'.$sub->mail.'</li>';
            }
            echo '</ul>';
            break;
    }

});

Flight::route('/vacancies(/@id)', function($id) {

    $sql = (!is_numeric($id)) ? "SELECT * FROM vacancies" : "SELECT * FROM vacancies WHERE id = :id";

    $stmt = $GLOBALS["db"]->prepare($sql);

    if(is_numeric($id)) {
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    }

    if($stmt->execute()) {
        $vacancies = $stmt->fetchAll(PDO::FETCH_OBJ);

        if(count($vacancies) > 0) {
            foreach($vacancies as $vacancy) {
                echo '<h3>'.$vacancy->name.'</h3>';
                echo '<div class="content" style="width: 500px;">'.$vacancy->content.'</div>';
            }
        } else {
            echo 'No vacancy with number '.$id.' found';
        }
    } else {
        echo 'Something went wrong';
    }
});

// include footer before sending response
Flight::after('start', function(){
    include 'templates/footer.html';
});

Flight::start();