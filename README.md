# Slim in WordPress
A Slim Php app in a WordPress Plugin

## This is an experiment using Php Slim Framework within a WordPress Plugin.
This is meant as a complimentary experiment to https://github.com/EugeneLiang/Php-Slim-App-in-WordPress. I thought that since Php Slim can be used in a Theme, why not a plugin ?


## Usage
- Copy and paste this entire folder into /plugins folder
- activate this plugin
- go to WordPress settings and use any of the pretty urls.



## Main Idea
The main idea here is to use Slim as a way to expose RESTful APIs from the plugin.

The default endpoint is http://yourdomain.com/slim/api, but you can change the default endpoint if you want to via the WordPress admin.

You can also use other databases such as MongoDB. Using MongoDB in this case is slightly different from your usual use case:
- you will first need to declare the use of MongoDB first
- to import the MongoDB object, you will do import it as the add_action function:
```
add_action('slim_mapping', function($slim) use ($MongoUser, $twig, $assets) {
    // your api endpoints here
});
```


That's all for now.