<?php 
// 
// we need to add crossorigin in order for the error handler to work on
// the dev server

wp_register_script($self->dashboardID, '');
wp_enqueue_script($self->dashboardID );
add_action('admin_print_footer_scripts', function() {
    ?>
        <script crossorigin src="http://localhost:3112/static/js/bundle.js<?php echo esc_attr('?v='.time()) ?>"></script>
        <script crossorigin src="http://localhost:3112/static/js/vendors~main.chunk.js<?php echo esc_attr('?v='.time()) ?>"></script>
        <script crossorigin src="http://localhost:3112/static/js/main.chunk.js<?php echo esc_attr('?v='.time()) ?>"></script>
        <script>
            window.lodash = _.noConflict();
        </script>
    <?php 
}, 1000);
