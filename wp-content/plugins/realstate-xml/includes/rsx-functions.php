<?php
/*
 * Add my new menu to the Admin Control Panel
 */
 
// Hook the 'admin_menu' action hook, run the function named 'rsx_Admin_Link()'
add_action( 'admin_menu', 'rsx_Admin_Link' );
 
// Add a new top level menu link to the ACP
function rsx_Admin_Link()
{
      add_menu_page(
       'Real State XML', // Title of the page
        'Real State XML', // Text to show on the menu link
        'manage_options', // Capability requirement to see the link
		  'sample-page',
        'includes/rsx-home.php' // The 'slug' - file to display when clicking the link
    );
}




