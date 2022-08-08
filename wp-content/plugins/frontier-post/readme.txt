=== Frontier Post ===
Contributors: finnj
Donate link: 
Tags: frontier, frontend, frontend post, frontend edit, frontier, post, widget, posts, taxonomy
Requires at least: 3.4.0
Tested up to: 5.4
Stable tag: 5.1.5
License: GPL v3 or later
 
Simple, Fast & Secure frontend management of posts - Add, Edit, Delete posts from frontend - Full featured frontend management of posts.
  
== Description ==

WordPress Frontier Post Plugin enables simple full featured management of standard posts from frontend for all user roles.

Intention of the Frontier Post plugin is to enable front end posting and editing on your blog. Allowing your users to create content easy, with no need to go into the back-end.
Editors and Administrators can use Frontier to edit posts from the frontend (Can be enabled/disabled in settings), and at the same time go to the backend for more advanced options.

Frontier Post is intentionally made simple - But it is highly configuable if you want to extend it :)

> From WP version 5,0 it is recommended to install and activate Classic Editor Plugin

= Main Features =
* Add/Edit/Delete Posts and Pages with media directly from frontend
* Create posts using PressThis, and edit them in Frontier Post
* My Posts Widget 
* My Approvals Widget
* Capabilities are aligned with Wordpress standard.
* Edit Categories / Tags / Taxonomies (dropdown, multiselect, checkbox or radio button)
* Default category per role
* Allowed categories per role
* Widget to enable post creation link on category archive pages
* Supports Wordpress Post Status Transitions (draft, pending, private & publish)
 * Approve pending post from frontend 
 * Moderation comments (will not be displayed on blog, only on edit).
* Disable Admin bar per role (Optional)
* User defined templates for forms
* Users must be logged in to post
* Multiple pages with frontier-post shortcode can be used.
* Supports external management of capabilities (tested with User Role Editor)
* frontier-post.css can be placed in template (child theme dir - [See here](http://wpfrontier.com/frontier-post-templates-css/) ), allowing for customm css rules.
* Custom Post Types
* Custom Fields - by your own code: [Documentation on custom fields](http://wpfrontier.com/frontier-post-custom-fields/)
* Documentation: [www.wpfrontier.com](http://wpfrontier.com/)

> Try the new Frontier plugin: [Frontier Query](http://wordpress.org/plugins/frontier-query/) 

= Usage = 
* Short-code [frontier-post] in a page content after install and activation of the plugin - Then review settings and capabilities
* Short code parameters  [http://wpfrontier.com/frontier-post-shortcodes/](http://wpfrontier.com/frontier-post-shortcodes/)

= Frontier plugins =
* [Frontier Post](http://wordpress.org/plugins/frontier-post/)  - Complete frontend management of posts
* [Frontier Query](http://wordpress.org/plugins/frontier-query/)  - Display lists and groupings of posts in post/pages and widgets.
* [Frontier Buttons](http://wordpress.org/plugins/frontier-buttons/)  - Control TinyMCE buttons
* [Frontier Set Featured ](http://wordpress.org/plugins/frontier-set-featured/)  - Set featured image aut. based on post images 
* [Frontier Restrict Media ](http://wordpress.org/plugins/frontier-restrict-media/)  - Restrict media access to users own media
* [Frontier Restrict Backend ](http://wordpress.org/plugins/frontier-restrict-backend/)  - Restrict access to the backend (wp-admin)





= Translations =
* Danish
* German (stefan)
* Turkish (nelanbunet)
* Russian (samaks, updated by: vitaly)
* Chinese (beezeeking)
* Spanish (Hasmin)
* Polish (Thomasz)
* French (pabaly, Thierry)
* Dutch (fredwier)
* Português-Brasil (Diones Menqui)
* Italian (Paolo De Santis)
* Arabic (EdaraPedia)

Let me know what you think, and if you have enhancement requests or problems let me know through support area

== Installation ==

1. [Install plugin](http://www.wpbeginner.com/beginners-guide/step-by-step-guide-to-install-a-wordpress-plugin-for-beginners/)
2. Activate the plugin
3. Place page "My Posts" (Created on activation) in the menu.
4: Review (and update) Frontier Post settings & capabilities

== Frequently Asked Questions ==


[http://wpfrontier.com/frontier-post-faq/](http://wpfrontier.com/frontier-post-faq/)


== Screenshots ==

1. Frontier post list
2. Add/Edit post form 
3. Frontier Post settings
4. Frontier My Posts Widget: Settings, My posts, Comments & comments excerpts (with different themes)
5. Frontier Post capabilities
6. Frontier Post advanced settings
7. My Approvals Widget

== Changelog ==

== 5.1.5 ==
* Action frontier_post_list_query, changed to Filter: frontier_post_list_query,
* Action: frontier_post_list_bottom in frontier_post_list_bottom.php was not executed, ip was the _top action being executed twice
* CSS additions to support Frontier Post Extension demo plugin

== 5.1.2 ==
* Added shortcodes: frontier_list_sort, frontier_list_sort_order
* New action: frontier_post_list_query, fires just before list query is executed to allow manipulation of query 
* Clone Post: Title and text screw up fixed
* New Action: frontier_post_clone_past_insert, Action fires after insert of cloned post
* Tested with wp version 5.4
* Check if Classic Editor plugin is installed, warning if not and WP version is 5 or later
* Stick post flag added to lists
* Added Warning in settings if plugin Classic Editor is not activated (for Frontier Post to fuction optimal)

= 5.0.3 =
* Wordpress 5.x support and php 7.2 support (tested with WP 5.2.2 & php 7.2.2)
** Fix Function create_function() is deprecated error in widgets

= 4.4.1 =
* Updated Filter: frontier_post_mod_cmt_update  - added parameter: $fp_moderation_comments_old

= 4.4.0 =
* Updated translation header to be compatible with wordpress org polygot.
* 2 new filters:
** frontier_post_msg_output (Enable manipulation of output message $tmp_msg)
** Filter: frontier_post_mod_cmt_update 
* 5 new Actions
** frontier_post_custom_msg (executes just after add/update messages are output)
** frontier_post_form_standard_tax
** Action: frontier_post_list_top (Action fires before displaying posts)
** Action: frontier_post_list_record (Action fires before each post record) 
** Action: frontier_post_list_botttom (Action fires after displaying posts) 
* Removed redundant space on cancel button in frontier-approve-post.php



= 4.3.7 =
* Tested with WP 4.9 
* Updated Russian translation
* Updated French translation


= 4.3.5 =
* Fixed issue with Frontier Author role not added or removed
* Added Simple Featured Image, based on simple fileupload (No call to WP media library).
* Export & Import of settings (Beta)
* Fixed error: Undefined variable: frontier_edit_text_before....
* Added div wrapper to set div id from shortcode - Ex: frontier_myid="test44" will result in div ids:
** Lists: frontier-post-list-test44
** Forms: frontier-post-form-test44


= 4.3.3 =
* Fixed misplaced text: "Number of tags to edit on the input form" in admin settings
* Moderation timestamp formated same as lists, frontier Post settings
* Added additional identifiers to featured image html.
* Added wp actions for quickpost: frontier_post_form_quickpost & frontier_post_form_quickpost_top
* Add option to make titel a required field
* Updated text for no posts in list format
* Updated German translation

= 4.3.0 =
* Multiple date format can now be selected for lists (and widgets)
* Fix keep setting on uninstall: Saved settings were not used on reinstall
* Russian translation updated (Thanks to vitaly)
* If post status is private and title or content is empty, post status will not be changed - warning message still showed.
* Fixed issue where inline add form didn't reset (showed last post edited)
* added additional CSS identifiers to list .frontier-post-new-list-actions .frontier-post-new-list-fields 
* Fixed issue with parameter frontier_list_form, not being processed
* .pot file updated (translation)

= 4.2.0 =
* Error message when deleting post (frontier_delete_post.php line 159) fixed

= 4.1.8 =
* Fixed issue with Editor Tabs on some Themes
* Tested with WP 4.7
* My Approvals widget: Edit/Delete select display option (Before, After or None)

= 4.1.6 =
* Tested with WP 4.6
* New parameter: frontier_force_quickpost="true" will force display of quickpost form and hide buttons
* New frontier_mode value: quickpost - ex: frontier_mode=""quickpost", will only display quickpost form (no list)
* frontier_cat_id can be set in url - ex: http://yoursite/my-posts/?task=new&frontier_cat_id=5
* Fixed: Quickpost did not respect excluded categories


= 4.1.4 =
* Fixed: Undefined variable: tmp_selected in /volume1/web/wpbeta/wp-content/plugins/frontier-post/forms/frontier_post_form_quickpost.php on line 74
* Fixed: un-intended debug log output in function frontier_get_tax_lists(

= 4.1.3 =
* Fixed: Status heading was not displayed correctly on simple list. 


= 4.1.2 =
* Added capability frontier_post_can_clone, so clone can be disabled per user role. 

= 4.1.1 =
* Fixed issue with simple list
* Fixed issue where single select for categories didn't hide excluded categories.

= 4.1.0 =
* Publish button also shown on post create (if enabled in settings)
* Added access level to My Posts Widget
* Added Quickpost, to post directly from list of posts without screen reload
* Added possibility to clone posts.
* New advanced option: Allow empty content in posts
* Pagination css added.
* Media scripts not pre loaded, now only loaded when frontier post is preparred 
* Arabic translation added (Thanks: mahfoofgmail)

= 4.0.4 =
* Remove quotes from frontier_add_post_type

= 4.0.3 =
* Added shortcode fp-capability, to display logged in users capabilities (admin can select user to display).
* Changed logic validating post type on add post, to be consistent.

= 4.0.2 =
* Fixed issue with preview link missing parameter, when using simple List.
* Removed debug messages when deleting cache

= 4.0.1 =
* Fixed issue with delete link icon when page containing frontier-post shortcode was using frontier-mode="add"

= 4.0.0 =
* My Posts widget
 * Added Post type to Frontier My Posts Widget (optional)
 * Added edit icon to Frontier My Posts Widget (optional)
 * Added delete icon to Frontier My Posts Widget (optional)
 * Added draft posts to Frontier My Posts Widget (optional)
 * Added pending posts to Frontier My Posts Widget (optional)
* My Approvals widget
 * Added list of draft posts to Frontier My Approvals Widget (optional)
 * Added list of pending posts to Frontier My My Approvals Widget (optional)
 * Added option for date and author info for draft and pending poststo Frontier My My Approvals Widget
* Changed Delete link on edit form to button
* Improved cache reset, on post status change and deletion
* Added Custom delete text in advanced setting to allow display of custom text warning user that a delete is in progress
* Corrected delete validation check
* Added url parameter frontier_forum_post to allow custom add ling: your-site/my-posts/?task=new&frontier_add_post_type=my_post_type
* Removed Post status legend, if post statues is hidden on edit form.
* added delete button on edit form, and option in setings to show/hide the Delete button
* added shortcode parameter frontier_list_pending_posts: frontier_list_pending_posts="treue" will show draft posts 
* added option for page with draft posts
* Fixed add link for custom post types


= 3.8.7.2 =
* Fixed get_term_link( $term ) in wp_get_post_terms() returning WP_error, causing fatal error

= 3.8.7 =
* Fixed that Show publish option in Frontier Post settings did not save. 

= 3.8.6 =
* Added taxonomies (and links) to post footer on list. 
* Added link to comments on list
* Hide private posts for all admins - Only author can see them

= 3.8.5 =
* CSS change to frontier_post_form_standard: 
 * #user_post_excerpt with changed to from width to min-width to allow for dynamic resizing
 * additional class added to each taxonomy to allow CSS styling for each area - ex: fieldset.frontier_post_fieldset_tax_category
* Fixed problem where load of tinyMCE plugin wordcount could conflict with other plugin loads
* Fixed problem where Super Admins always are redirected to Frontier Post edit form as opposed to the backend.
 
= 3.8.1 =
* added action: frontier_post_form_standard_top to allow insertion of html in top of form (between status and TinyMCE editor
* added post_status: future to queried status in frontier-list-posts.php
* New function: frontier_post_display_links (To display frontier links in Frontier Query)
* Tested with wp 4.4.1

= 3.8.0 =
* Fixed issue where Frontier Post did not send email (get_settings was deprecated, replaced with fp_get_option)
* Added option to approve pending posts from list
* Added Publish Button on edit form (to allow approval of pending post).
* Fixed PressThis integration (From WP version 4.3, PressThis uses Ajax til generate call to standard editor)
* POT file updated
* Italian translation (Paolo De Santis)
* Fixed problem where if a user disabled "Can page", the edit page was removed from both frontend and admin bar instead of linking to backend.

= 3.7.5 =
* Fixed alignement of icons on list CSS: frontier-post-list-icon  display: inline !important
* Fixed: Replace deprecated function post_permalink() with get_permalink(): frontier_my_post_widget.php & frontier_post_form_list.php
* Fixed: Saved taxonomies not showing on edit form


= 3.7.3 =
* Added possibility to use tinymce wordcount, as standard wordpress wordcount does not work in frontend from version 4.3 (advanced settings)
* New shortcode parameters: 
 * frontier_pagination: If set to false pagination will not be displayed - ex: frontier_pagination="false"
 * frontier_ppp: Will override post per page setting: ex frontier_ppp=5
 * frontier_user_status: List of status to be displayed in list - ex: frontier_user_status="draft,pending"
* Fixed edit link of pages in frontend


= 3.7.0 =
* Updated widgets to support PHP 5 constructor (WP 4.3 requirement)
* Added support for PressThis - Link: Edit in standard editor redirects to Frontier Post
* Enabled selection of post types that can contain the frontier post short code.

= 3.6.1 =
* Fixed allowed categories. 
* Changed default setting from caching: 30 mins to disabled.

= 3.6.0 =
* Fixed spelling error in my posts widget 
* Added cashing of lists in add/edit form (Cache interval can be set in advanced option, defaults to 30 min)
* New list layout: Simple List / List / Exerpt / Full Post
* Warning message for empty title & content made more visible (div id="frontier-post-alert").
* Default category is not prefilled, but set on save if no category has been selected.
* Added versioning to frontier-post.css to ensure updates.
* Updated transition logic to avoid discrepancy in pagination links (url parameters removed).

= 3.5.6 =
* Fixed single select for categories (wp_dropdown_categories(): selected input integer instead of array of integers)

= 3.5.5 =
* Remove single & double quotes from post type name in function fp_get_posttype_label_singular
* Changed if ( !is_page(get_the_id()) ) To: if ( $post->post_type != 'page' ) 
* Option in General Setting to hide Add New Post on the list
* New shortcode: frontier_edit_form - Values standard, simple, old (this way edit form layout can be selected in shortcode)
* New shortcode:  frontier_editor_height - Vaue: number (pixels) ex:  frontier_editor_height=100
* Allow users with the neccessary capbilities to edit & delete private posts (edit_private_posts & delete_private_posts), will be editors and admins
* Force save to post_status=draft first, if published directly to align with Wordpress standard (and align to hook draft_to_publish)
* Changed admin option name List Capabilities to Debug Info and added Post DB content breakdown
* Fixed upgrade check for default settings


= 3.5.0 =
* Widgets (My Approvals & My Posts) are now being cached for better performance.
 * Cache time can be set (or disabled) in widget settings, default cache time: 15 minutes.
* Fixed misspelled multible to multiple
* Cancel button: added id="frontier-post-cancel" to allow css styling
* Changed post validation check, so check for age only is done for published posts (a user can always change peding & draft posts)
* Post Moderation:
 * Widget my approvals now visible for editors (in addtion to administrators (checks for capability edit_others_posts)
 * Added new short code parameter: frontier_list_pending_posts, will list post status with status = pending, only valid for editors & admins.
 * Link to pending posts page added to general settings
 * My approvals widget will now link to pending posts page if this is set in settings.
 * Editors & Administrators can enter moderation comments on edit form. Author of post can also enter moderation comments
 * Moderation comments are implemented using post meta data, fields are prefixed with "_" so comments won't be shown on to other users.
* New shortcode paratmeter: frontier_add_link_text to allow override of Create New Post link text on list form.
* Added link to documentation on settings pages

= 3.4.5 =
* Added icons for edit/delete/view in list view. Must be enabled in general settings. Own icons can be placed in template folder.
* Added new action: frontier_post_form_standard
* Tags: Number of tags displayed can be set in advanced options + Tags can now be transformed (upper case/lower case/ First letter) - Advanced settings.
 * Forms updated: frontier_post_form_standard.php
* Changes to frontier-post.css
 * Fixed issue where entries in frontier-post.css wasn't closed properly
 * added: frontier-post-taxonomies
 * added: frontier-post-list-icon-comments
 * added: frontier-post-list-icon
* Validation: Set status to draft if title or content is empty.
* pot file (translation) updated 
* Fixed filter: frontier_post_pre_update

= 3.4.1 =
* Fixed issue where admin bar was shown until advanced settings were saved

= 3.4.0 =
* Added  add/update/delete message also when just saving post.
* German translation added, thanks: tomcatchriss
* frontier_post_output_msg() added to following forms to display message on add/update:
 * frontier_post_form_standard.php
 * frontier_post_form_page.php
 * frontier_post_form_simple.php
 * frontier_post_form_old.php
 * frontier_post_form_preview.php
* New advanced seeting to disable control of admin bar (disable control if conflict with other plugin)

= 3.3.9 =
* Fixed: add/update/delete was not displayed even if Show add/update/delete messages was checked.

= 3.3.8 =
* Turkish translation added, thanks: nelanbunet
* Tested up to: 4.1.1

= 3.3.6 =
* Added to translation, po file updated
* Missing /div added in  frontier_post_form_list.php file

= 3.3.5 =
* NEW: Support for custom taxonomies (no coding necessary)
* NEW: Support for custom post types (no coding necessary)
* NEW: Support for custom fields using template forms, filters and actions.
* Settings has be re-organized
* Added custom login text under advanced option
* Changed to use get_stylesheet_directory_uri() instead of bloginfo functions that is manipulated by WPML
* Moved tags, featured image & excerpt to fieldset layout in new form
* Migration of old settings added
* Activation script updated
* Uninstall script update - clean up of old entries on options table
* Added select of form in advanced option (standard/simple/old)
* Changed css to support fieldset in safari and chrome
* Restrict frontier-post shortcode to pages
* Re-introduced output buffering
* updated frontier_post_tax_form.php with float left fieldsets

= 3.0.7 =
* Fixed capability messages not showing

= 3.0.6 =
* Fixed link to post on frontier_list_form, missing double quotes
* Fixed single category dropdown did not respect excluded categories.

= 3.0.5 =
* Support for custom post types in lists, and in forms using template forms, no support for custom fields. 
* Added 2 new shortcode parameters: frontier_add_post_type & frontier_list_post_types
 * Example usage: [frontier-post frontier_add_post_type="page" frontier_list_post_types="post,page"]

= 3.0.5 =
* Changed _wpnonce name and action to frontier post specific to resolve possible conflict
* Added "add_args" => false  to pagination var in frontier_list_form.php due to wp 4.1 bug (trac ticket 30831) 
* Added div tags to columns in frontier_list_form.php to allow custom css rules
* Updated frontier_cann_add, frontier_can_edit & frontier_can_delete functions to ensure access works correctly
* Added shortcode parameters: frontier_list_text_before & frontier_edit_text_before to display text on forms before shortcode output
* Updated example forms accordingly
* Fixed loading of css if placed in active theme dir (your-active-theme/plugins/frontier-post/)

= 3.0.2 =
* Fixed issue with users being able to publish without necessary capability
* Fixed: Allow users with wordpress standard capability (Admins & editors) edit_other_posts to edit other users post from frontend
* Fixed: Allow users with wordpress standard capability Admins & editors) delete_other_posts to delete other users post from frontend


= 3.0.1 =
* Fixed issue with Save and preview - Call changed to: include_once(frontier_load_form("frontier_post_preview_form.php"))

= 3.0.0 =
* Multiple categories can now be used in shortcode parms - use double quotes around comma separated list
* Re-designed page transition logic. Removed output buffering (ob_start() etc), and changed it to a flow in php.
* Added preview page due to new transition logic not allowing redirects to standard preview page
* Added option to hide page title for certain pages.
* Added message on the frontend for add/update/delete - Must be enabled in settings.
* Tested with 4.1
* Added option to show IDs for categories in the admin panel list
* Removed post status column from list posts if short code parameter: frontier_list_all_posts="true"
* Removed count of users posts text from list posts if short code parameter: frontier_list_all_posts="true"
* Added option for editor height (default 300)
* Fixed return from delete post, so returned to calling list page
* New function: frontier_post_wp_editor_args to allow change of editor options
* Call to wp_editor in frontier_form to use new function, to enable config of editor in templates
* Added hidden field post_categories to frontier_form.php to keep categories if category field is removed from form
* Updated logic for set capabilities, and disabled capability set on plugin activate, if external management of capabilities is enabled
* Added integration with Frontier Buttons calling function: theme_advanced_buttons1
* Disable submit buttons individually in settings
* If user has capability "delete_other_posts" (Administrators & Editors) always allow them allow them to delete posts from frontend.
* If user has capability "edit_others_posts" (Administrators & Editors) always allow them allow them to edit posts from frontend.
* If all posts are being displayed in frontier-list, show author instead of category
* Cmt heading in frontier list replaced by comment icon as heading was confusing.
* frontier_post.css will be loaded from template directory if present
* Added shortcode parameter [frontier-post frontier_list_all_posts="frontier_list_all_posts"] - Will list all published posts, not only from current user, can be combined with frontier_list_cat_id 
* Added shortcode parameter [frontier-post frontier_return_text="Save & return to category"] - Will change text on Save & Return button
* Added shortcode parameter [frontier-post frontier_list_cat_id=7] to allow for the list of the users post to be limited to one category
* Cleaned frontier_form.php, added switch for category display type
* Changed HTML output to functions for multi and checkbox
* Added category in shortcode ex:  [frontier-post frontier_cat_id=7]
* Enabled support for capabilties can be managed from other plugin (User Role Editor)
* Added widget New post from Category - The widget can be added to a category page, and will take the category from that page


= 2.6.1 =
* Removed .container (added in 2.6.0) from css as it might conflict 

= 2.6.0 =
* Added option for categories as checkbox list
* Fixed issue, Post status dropdown didnt show correct status.
* Added function frontier_tax_list() to prepare support for taxonomies

= 2.5.5 =
* Fixed My Approvals Widget & My Posts Widget - Logical values (checkbox) did not save

= 2.5.4 =
* NEW option: Default post status
* Option: Allow users to change status from Published - Fixed and works as designed
* Corrected error where mce buttons didnt work for WP versions prior to WP 3.9

= 2.5.1 =
* tinyMCE editor buttons moved to separate plugin Frontier Buttons from Wordpress version 3.9
* Dutch translation

= 2.1.2 =
* Support for Private posts
* New setting: Allow users to change status from Published
* Redirect to frontier list post page after login (thanks: newtonsongbird)
* Fixed: Frontier Edit now respects max days set in Frontier settings

= 2.1.0 =
* Short code parameters:
 * frontier_mode: Option to set frontier_mode=add using this parameter will enable to show add form directly in page - Usage: [frontier-post frontier_mode=add]
 * frontier_parent_cat_id: Option only to show child categories of the parent category in dropdowns - Usage: [frontier-post frontier_parent_cat_id=7]
 * Combined: [frontier-post mode=add frontier_parent_cat_id=7] where 7 is the category id
* option to show link to login page after message: Please login - Link used: wp_login_url()

= 2.0.7 =
* Images was not properly attached to post, fixed
* Featured image need the post to be saved once to work, fixed
 * User still needs to press save to view featured image

= 2.0.5 =
* Wordcount added (TinyMCE plugin, you need to enable custom editor buttons)
* French translation added (thanks to pabaly)

= 2.0.4 =
* Template forms: Forms can be copied (and changed) to theme folder - See FAQ
* Option: Exclude categories by ID from dropdowns on form
* Option: Email to list of emails on post for approval
* Option: Email to Author when post is approved (Pending to Publish)
* Save button on Frontier edit form (so user can save post and stay on form)
* Submit button: New setting to decide if user is taken to My Posts or to the actual post when a new post is submitted or edited. 
* Featured Image support.


= 1.6.2 =
* Translation fixes (Thanks: Thomasz Bednarek)
* Updated translations: Danish, Spanish, Polish & Russian)
* Added suggested buttons for editor in settings page.
* frontier_fix_list.php removed

= 1.6.0 =
* Temp version to be able correct the post_data issue (frontier_fix_list.php).

= 1.5.9 =
* Fixed issue where post_status was set to display value instead of value, meaning post was updated with translated value. Posts still in db, but does not show up in WP

= 1.5.7 =
* Bug: Post status changed to draft if post status was not selectable (as with a published post), hidden input field added to hold post_status
* Preview link added to My Posts list for posts that are not published (Link to unpublished posts was removed in 1.5.1)

= 1.5.6 =
* New buttons on editor: Smileys, search & replace and table control
* Frontend Author role added (Same capabilities as Author, makes it possible to distinguish between Author and Frontend Author) 
* Bug in My Posts fixed (comments from post showing), wp_reset_postdata() added in end of frontier_list.php
* Spanish Translation (hasmin)

= 1.5.1 =
* Option to hide admin bar
* Default category per role
* Only redirect edit to frontend for standard post type (not pages and custom post types)
* Du not show dropdown for status with only 1 option, only show value
* Added missing closing tags for ul and div in my approvals widget 

= 1.4.9 =
* Issue with svn, new tag created

= 1.4.8 =
* New Editor options for frontend editing - Full, Simple-Visual, Simple-Html or Text-Only
* Category: Multi-select, dropdown or hidden
* Media upload can be disabled per role
* Drafts: Can be restricted so user have to submit for approval

= 1.3.3 =
* Fixed security issue with add new post
* Chinese translation
* Russian translation

= 1.3.2 =
* Fixed hardcoded urls in My Approvals widget

= 1.3 =
* Supports Wordpress Post Status Transitions (draft/pending/publish)
* New Widget: My Approvals

= 1.2.2 =
* Fixed error in user_post_list query

= 1.2.1 =
* Fixed error in user_post_list query

= 1.2 =
* New My Posts Widget
* Added multi select for Categories
* Added support for Excerpts (Can be enabled/disabled in settings)
* Added support for Tags (Can be enabled/disabled in settings)
* Improved media upload

= 1.1.2 =
* Fixed upgrade problem

= 1.1.1 =
* Danish translation added

= 1.1 =
* Added check for comments on edit and delete based on settings
* Added support for excerpts (Can be enabled/disabled in settings)
* Added role-based capabilities
* Added ability to use Frontier Post edit directly from post using standard edit link
* Added link to page containing page in shortcode

= 1.0.1 =
* Added pagination to list of authors posts.

= 1.0.0 =
* Initial release


== Upgrade Notice ==

None