<?php
/**
 * Date: 7/30/13
 * Time: 10:20 AM
 * Plugin Name: Slim in WP
 * Description: Slim in WP
 * Version: 1.0
 * Author: Eugene Liang
 * License: GPLv2
 */

// I also need things like AWS SES and so on.
require 'vendor/autoload.php';



use \Slim\Extras\Middleware\CsrfGuard;
$directory =  get_template_directory()."/app";
$assets = get_template_directory_uri();
$loader = new Twig_Loader_Filesystem($directory);
$twig = new Twig_Environment($loader);
$twig->addExtension(new Twig_Extensions_Extension_I18n());


$mongo = new MongoClient("mongodb://localhost/?journal=true&w=1&wTimeoutMS=20000");
$mongodb = $mongo->react_blog;
$MongoUser = $mongodb->users;

// i shd also have some redis shit here.
require_once 'SlimInWp.php';
new \SlimWP\SlimInWp();

add_filter('rewrite_rules_array', function ($rules) {
    $new_rules = array(
        '('.get_option('slim_base_path','slim/api/').')' => 'index.php',
    );
    $rules = $new_rules + $rules;
    return $rules;
});

add_action('init', function () {
    if (strstr($_SERVER['REQUEST_URI'], get_option('slim_base_path','slim/api/'))) {
        // database connections


        $slim = new \Slim\Slim();
        $haha = "this is funny";
        do_action('slim_mapping',$slim);
        // probably can instantiate Redis and some shit here

        $slim->run();
        exit;
    }
});



// so the things here will provide for 
// all RESTFul apis will be within here.
// generally covers external APIs that has to connect from some other shit
add_action('slim_mapping', function($slim) use ($MongoUser, $twig, $assets) {
    $haha = "laughing text u know";

    $slim->get('/api/internal/user/:u',function($user) use ($slim, $haha, $MongoUser){
        //printf("User is %s",$user); 
        //printf($haha);
        //echo "\n haha-->";
        $slim->contentType('application/json');
        $posts = $MongoUser->find();
        $data = iterator_to_array($posts);
        echo json_encode($data); 
    });

    // user related may not be required.
    $slim->post('/api/internal/user/:action', function($action) use ($slim, $MongoUser) {
        // printf("User is %s",$user); 
        // # nice technique : http://stackoverflow.com/questions/19068363/storing-and-retrieving-an-array-in-a-php-cookie
        $slim->response()->headers->set('Content-Type', 'application/json');
        if ($action == "create") {
            // create a new user + session it
            $_user = array(
              '_id'=>uniqid(),
              'email'=>$slim->request()->post('email'),
              'password'=>$slim->request()->post('password')
            );
            $new_user = $MongoUser->save($_user);
            if ($new_user) {
              // redirect to login page
              $dataArray = array('user' => $new_user,'message'=>"User created sucessfully");
              $response = json_encode($dataArray);
              echo $response;
            }
            else {
              $slim->halt(401, "User sign up fail.");
            }
        }
        else {

            $check_user = $MongoUser->findOne(array(
                '$and' => array(
                    array('email' => $slim->request()->post('email')),
                    array('password' => $slim->request()->post('password'))
                )
            ));

            if ($check_user) {
              // set the session here
              if (!isset($_SESSION)) {
                session_start();
              }
              # nice technique : http://stackoverflow.com/questions/19068363/storing-and-retrieving-an-array-in-a-php-cookie
              $dataArray = array('user' => $new_user,'message'=>"User created sucessfully");
              $response = json_encode($dataArray);
              echo $response;
            }
            else {
              $slim->halt(401, "Credentials incorrect or user dont exist.");
            }
        }

    
    });
    $slim->get('/api/internal/group/:u', function($group){
        printf("Group is haha %s", $group);           
    });
    $slim->get('/api/internal/pings/:u', function($group){
        printf("Group is haha %s", $group);           
    });
    $slim->get('/api/internal/search/:u', function($group) use ($slim, $mongodb) {
        $slim->response()->headers->set('Content-Type', 'application/json');
        $request = $slim->request();
        $params = $request->params('paramName');
        $dataArray = array('id' => $id, 'somethingElse' => $somethingElse, 'group'=>$group, 'param'=>$params);
        $response = json_encode($dataArray);
        printf($response);
    });



    $slim->get('/api/external/something/:u', function($something) use ($slim, $mongodb) {
      // this api pings something outside of this app. -> still gonna work.
    });

    $slim->get('/api/sampleapp', function() use ($slim, $twig, $assets) {
      // this api pings something outside of this app. -> still gonna work.
      $data = array(
        'user'=>'nada',
        'test'=>'haha',
        'static_url'=>$assets,
      );
      echo $twig->render('views/index.php', $data);
    });

});