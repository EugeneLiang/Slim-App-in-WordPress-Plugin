<?php
/**
 * Plugin Name: Slim in WP
 * Description: Slim in WP
 * Version: 1.0
 * Author: Eugene Liang
 * License: GPLv2
 */

// I also need things like AWS SES and so on.
require 'vendor/autoload.php';

use \Slim\Extras\Middleware\CsrfGuard;

$directory =  plugin_dir_path(__FILE__);
$assets = plugin_dir_url(__FILE__)."/assets";
$loader = new Twig_Loader_Filesystem($directory);
$twig = new Twig_Environment($loader);
$twig->addExtension(new Twig_Extensions_Extension_I18n());



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
        $slim->config('debug', true);
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
add_action('slim_mapping', function($slim) use ( $twig, $assets) {
    $slim->get('/api/wget', function() use ($slim, $twig, $assets) {
      // this api pings something outside of this app. -> still gonna work.
      
      $command = "wget -p -k http://www.baidu.com 2>&1";
      $output = shell_exec($command);
      var_dump($output);
      //echo "hi2";
    });    

    // http://182.92.157.126/screen/shot.php?url= 

    $slim->get('/api/external/grabber', function() use ($slim) {
      $request = $slim->request();
      $something = $request->params('url');
      $theURL = "http://182.92.157.126/screen/shot.php?url=http://".$something;
      $REQUEST = Requests::get($theURL);
      echo "http://182.92.157.126/screen/shot.php?url=".$something;
    });

    $slim->get('/api/test', function() use ($slim, $twig, $assets) {
      //echo $directory;
      $data = array(
        'user'=>'testuser',
        'test'=>'hahahahha',
        'static_url'=>$assets
      );
      echo $twig->render('views/index.php', $data);
    });
});