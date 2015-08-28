<?php


namespace SlimWP;
class SlimInWp {
    function __construct() {
        add_action( 'admin_menu', array( $this, 'slim_in_wp_menu' ));
    }
    function slim_in_wp_menu() {
        add_options_page( 'Slim in WP Options','Slim in WP','manage_options','slim-in-wp', array( $this, 'slim_in_wp' ) );
    }
    function slim_in_wp() {
        // I shd also include my REDIS / MongoDB connection or some shit ?
        if(strtolower($_SERVER['REQUEST_METHOD']) == 'post'){
            global $wp_rewrite;
            update_option('slim_base_path',$_REQUEST['slim_base_path']);
            $wp_rewrite->flush_rules(true);
        }
        ?>
        <div class="wrap">
            <h1>Slim Framework</h1>
            <form action="" method="post">
                <label>Base Path <input type="text" name="slim_base_path" value="<?php echo get_option('slim_base_path','slim/api/')?>"></label>
                <input type="submit" value="Update" class="button-primary">
            </form>
            <hr />
            <form action="" method="post">
                <label>REDIS Connection <input type="text" name="slim_redis_connection" value="<?php echo get_option('slim_redis_connection','slim/api/')?>"></label>
                <input type="submit" value="Update" class="button-primary">
            </form>
            <hr />
            <form action="" method="post">
                <label>MongoDB Connection <input type="text" name="slim_mongodb_connection" value="<?php echo get_option('slim_mongodb_connection','slim/api/')?>"></label>
                <input type="submit" value="Update" class="button-primary">
            </form>

        </div>
    <?php
    }
}