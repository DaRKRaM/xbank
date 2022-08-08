=== Frontend Admin for ACF - Add and edit posts, pages, users and more all from the frontend ===
Contributors: shabti
Tags: elementor, acf, acf form, frontend editing
Requires at least: 4.6
Tested up to: 5.9.1
Stable tag: 3.7.11
Donate link: https://paypal.me/KaplanWebDev
Requires PHP: 5.6.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

An ACF extension that allows you to easily display frontend forms for your users so that they can edit content by themselves from the frontend.

== Description ==

An ACF extension that allows you to easily display frontend forms for your users so that they can edit content by themselves from the frontend. 

This plugin needs to have Advanced Custom Fields installed and activated. You can create awesome forms with our form builder to allow users to save custom meta data to pages, posts, users, and more. Then use our Gutenberg block or shortcode to easily display the form for your users.  

So, what can this plugin do for you?

== FREE Features ==

1. No Coding Required
Give the end user the best content managment experience without having to know code. It’s all ready to go right here. 

2. Edit Posts 
Let your users edit posts from the frontend of their site without having to access the WordPress dashboard. 

3. Add Posts 
Let your users publish new posts from the frontend using the “new post” widget

4. Delete Posts 
Let your users delete or trash posts from the frontend using the “trash button” widget

5. Edit User Profile
Allow users to edit their user data easily from the frontend.

6. User Registration Form
Allow new users to register to your site with a built in user registration form! You can even hide the WordPress dashboard from these new users.

7. Hide Admin Area 
Pick and chose which users have acess to the WordPress admin area.

8. Configure Permissions
Choose who sees your form based on user role or by specific users.

9. Modal Popup 
Display the form in a modal window that opens when clicking a button so that it won’t take up any space on your pages.


== PRO Features ==

1. Edit Global Options 
If you have global data – like header and footer data – you can create an options page using ACF and let your users edit from the frontend.

2. Limit Submits
Prevent all or specific users from submitting the form more than a number of times.

3. Send Emails 
Set emails to be sent and map the ACF form data to display in the email fields such as the email address, the from address, subject, and message. 

4. Style Tab
Use Elementor to style the form and as well the buttons. 

5. Multi Step Forms 
Make your forms more engaging by adding multiple steps.

6. Stripe and Paypal 
Accept payments through Stripe or Paypal upon form submission. 

7. Woocommerce Intergration 
Easily add Woocomerce products from the frontend.
 

Purchase your copy here at the official website: [Frontend Admin website](https://www.frontendform.com/)


== Useful Links ==
Appreciate what we're doing? Want to stay updated with new features? Give us a like and follow us on our facebook page: 
[ACF Frontend Facebook page](https://www.facebook.com/acffrontend/)

The Pro version has even more cool features. Check it out at the official website:
[ACF Frontend website](https://www.frontendform.com/)

Check out our other plugin, which let's you dynamically query your posts more easily: 
[Advanced Post Queries for Elementor](https://wordpress.org/plugins/advanced-post-queries/)

[Advanced Custom Fields Pro](https://www.advancedcustomfields.com/pro/)

== Installation ==

1. Make sure both Advanced Custom Fields is installed and activated. 
2. Upload the plugin files to the `/wp-content/plugins/acf-frontend-form-elements` directory, or install the plugin through the WordPress plugins screen directly.
3. Activate the plugin through the 'Plugins' screen in WordPress
4. Create a form under Frontend Admin > forms.
5. Choose the desired form type. 
6. Configure the fields permisions, display, and other settings as you please.
7. Copy and paste the shortcode on any page. You can also use our Gutenberg block.
8. You should now see a form on the frontend for editing a post, adding a post, editing or adding a user, and more.


== Tutorials ==  

= Installating Frontend Admin =
https://www.youtube.com/watch?v=Qio9iHzpMLo

= How to create a form for frontend data submission =
https://www.youtube.com/watch?v=7vrW8hx5jlE



== Frequently Asked Questions ==

= Can I send emails through this form? =

You can use a "action hook" to send emails when this form is registered: <a href="https://www.frontendadmin.com/frontend_admin/save_post/">frontend_admin/save_post</a>

If you purchase our pro version, you will be able to configure this from the widget without any code. You will be able to send any number of emails upon form submission. 

= Can I let users set post categories through this form? =

Yes. Simply add a taxonomy field and set the taxonomy type to Category




== Changelog ==
 = 3.7.11 - 27-07-2022 =
 * Placed "form type" select directly in form builder page

 = 3.7.10 - 20-07-2022 =
 * Fixed Woo attributes not saving properly
 * Optimized Mailchimp action
 * Changed functions prefix from "acf_frontend" to "feadmin"  
 * Fixed Elementor submit button alignment

 = 3.7.9 - 14-07-2022 =  
 * Fixed delete button not redirecting properly
 * Fixed success message not showing at top of page

 = 3.7.8 - 06-07-2022 =  
 * Added Mailchimp Status field to allow user to choose whether or not to sign up to mailing list
 * Fixed post to edit not showing "new post" option if no posts are found in the query
 * Fixed neccesary scripts not enqueued if admin is not logged in 
 * Removed deprecated "documents" folder
 
 = 3.7.7 - 30-06-2022 = 
 * Fixed admin css not loading in min file
 * Fixed Save Custom Fields to option not saving to correctly in legacy Elemntor widgets  

 = 3.7.6 - 28-06-2022 = 
 * Fixed plugin icon svg issue
 * Fixed modal button calling create form modal  

 = 3.7.5 - 27-06-2022 = 
 * Fixed dynamic value shortcodes 
 * Fixed delete button redirect broken

 = 3.7.4 - 26-06-2022 = 
 * Added solution for cache plugin breaking acf js object
 * Changed plugin name from ACF Frontend to Frontend Admin for ACF 
 * Added presets to form builder: 
         Delete Post Button
         Post Status Button
         Delete Product Button
         Product Status Button
 * Added Favicon field

 = 3.7.0 - 15-06-2022 = 
 * Added option to choose Frontend Form in Elementor widgets
 * Lite deprecated all Elementor widgets for new users except for Frontend Form (if you are already using these widgets, they will continue to function)
 * Added assets for form builder

 = 3.6.6 - 15-06-2022 = 
 * Fixed issue with multi step product forms 
 * Tweeked multi step forms to allow fields before steps that will show always
 * Fixed step tabs to only show when fields show
 * Fixed issue with multiple "ACF Fields" instances in one form 

 = 3.6.4 - 10-06-2022 = 
 * Moved form builder section tabs to the side of form settings
 * Added User Website field type

 = 3.6.3 - 09-06-2022 = 
 * Added Frontend Submissions Gutenberg block
 * Fixed Gutenberg block not loading form options
 * Fixed dynamic options in url query option not being generated properly
 * Added "Save Form Submissions" on form level

 = 3.6.1 - 07-06-2022 = 
 * Added Mailchimp integration 
 * Deprecated URL parameters setting in actions tab. Parameters can be placed directly in the url
 * Fixed taxonomy field to show "add new" button under field instead of transparent icon

 = 3.5.9 - 01-06-2022 = 
 * Fixed ACF Fields in multi step showing

 = 3.5.8 - 31-05-2022 = 
 * Fixed post author permissions error in modal window
 * Fixed post_to_edit current user filter not working
 * Fixed ACF Fields always showing full width

 = 3.5.7 - 30-05-2022 = 
 * Added "Post to Edit" field type to allow users to easily choose which post to edit with a dropdown
 * Added "Product to Edit" field type to allow users to easily choose which product to edit with a dropdown
 * Added option to change the "frontend-dashboard" slug
 * Fixed url query post not loading field values

 = 3.5.6 - 26-05-2022 = 
 * Fixed User email field not updating when edited
 * Fixed username editing logs user out

 = 3.5.5 - 25-05-2022 = 
 * Fixed submissions shortcode not showing new submissions

 = 3.5.4 - 25-05-2022 = 
 * Fixed edit form links not showing in admin bar in certain themes
 * Fixed shortcodes using form keys not working
 * Added preview button to form edit page

 = 3.5.3 - 24-05-2022 = 
 * Added Frontend Admin edit posts to admin toolbar
 * Added public preview for forms removing the need to create a dedicated page
 * Fixed Elementor modal window styles not working due to recent change to the css classes
 * Added link to form in submissions table
 * Fixed user field values not being loaded when using url query

= 3.5.2 - 18-05-2022 =
 * Fixed hidden modal button
 * Fixed default value not including shortcodes on submit
 * Added Frontend Admin quick access to admin toolbar

= 3.5.1 - 16-05-2022 =
 * Fixed ACF Fields not saving in admin form builder
 * Fixed "confirm password" field not hiding initially in multi step form

= 3.5.0 - 15-05-2022 =
 * Added submissions shortcode to forms: [acf_frontend submissions="{form id}"]
 * Removed bulk add fields option in form builder

= 3.4.15 - 09-05-2022 = 
 * Fixed multi step error in Elementor widgets

= 3.4.14 - 08-05-2022 =
 * Fixed verification email being sent when not activated
 * Fixed hidden field removing value in edit forms

= 3.4.13 - 04-05-2022 =
 * Fixed fields showing in wrong steps
 * Fixed acf fields width option in Elementor

= 3.4.12 - 03-05-2022 =
 * Fixed ACF fields issue when duplicating Frontend From Elementor widget

= 3.4.11 - 02-05-2022 =
 * Fixed multi step issue with acf fields 
 * Removed unecessary wrapper around ACF Fields causing fields to show on one line
 
= 3.4.9 - 02-05-2022 =
 * Fixed woo product forms not saving
 * Fixed status field appearing as checkbox instead of radio

= 3.4.8 - 01-05-2022 =
 * Fixed validation error when using ACF fields in forms
 * Fixed fields duplicating on submissions approval page

= 3.4.7 - 28-04-2022 =
 * Fixed form skipping validation if js error exists
 * Fixed [acf:field_name] shortcodes not returning empty if no value is generated 
 * Fixed form validating fields that are displayed as hidden

= 3.4.6 - 26-04-2022 =
 * Fixed [acf:field_name] shortcodes not working properly

= 3.4.5 - 14-04-2022 =
 * Added option to validate each step's fields
 * Fixed missing "local avatar" dynamic fields in Elementor
 * Added Option to disable submissions to dedicated database table

= 3.4.3 - 14-04-2022 =
 * Added Woocommerce Order actions: "New Order Form" and "Edit Order Form"
 * Changed CSS classes to avoid conflicts: 
    "modal" -> "fea-modal"
    "modal-inner" -> "fea-modal-inner"
    "modal-content" -> "fea-modal-content"
 * Fixed permissions message not showing for non logged in users    

= 3.4.2 - 11-04-2022 =
 * Fixed email verification creates duplicate entries

= 3.4.1 - 08-04-2022 =
 * Fixed user setting in permissions tab not loading users.
 * Fixed taxonomy field loading as "related terms"

= 3.4.0 - 06-04-2022 =
 * Added option to limit submissions in form builder
 * Moved permissions settings into a repeating setting for better rule configurations

= 3.3.49 - 05-04-2022 =
 * Fixed issue with editing specific post, user, or term

= 3.3.48 - 03-04-2022 =
 * Fixed default values not loading

= 3.3.47 - 31-03-2022 =
 * Fixed forms saving html entities instead of plain text
 
= 3.3.46 - 31-03-2022 =
 * Fixed attributes not working in Elementor modal

= 3.3.45 - 30-03-2022 =
 * Fixed modal button reloads form on every click
 * Fixed multiple modal windows breaking page markup
 * Changed the following css classes for page loading clarity: 
    .frontend-admin-edit-button -> .modal-button
    .frontend-admin-edit-button-container -> .modal-button-container

= 3.3.44 - 29-03-2022 =
 * Fixed add image button not displaying if text is changed
 
= 3.3.43 - 25-03-2022 =
 * Fixed emails not sending when submitting multi step forms

= 3.3.42 - 24-03-2022 =
 * Fixed multi steps display errors
 * Fixed "Add Image Button" styles not working
 * Added Modal Button Styles to all Elementor widgets 

= 3.3.41 - 23-03-2022 =
 * Added ability to display dynamic values on frontend
 * Added option to edit terms from related terms field
 * Fixed edit specific post not working 
 * Fixed edit specific user not working 
 * Fixed issue with multi valued fields in Admin Submission Approval 

= 3.3.40 - 17-03-2022 =
 * Fixed delete redirect url not working
 * Added option to save specific fields to different data types

 = 3.3.39 - 14-03-2022 =
 * Fixed issue with product images field

= 3.3.38 - 14-03-2022 =
 * Added option to choose fields in nested relationship form
 * Soft deprecated form title display, use message field from now on
 * Fixed delete button redirect issue
 * Tweeked default form success messages to match form type
 * Added Elementor style options for modal button

= 3.3.37 - 09-03-2022 =
 * Fixed image field's browser upload
 * Fixed color picker and time picker breaking in multi step 
 * Added filter to prevent submissions from saving

= 3.3.36 - 08-03-2022 =
 * Added multi step feature to admin form builder

= 3.3.35 - 04-03-2022 =
 * Fixed file and image based fields breaking when button text changed

= 3.3.34 - 03-03-2022 =
 * Fixed addon installer

= 3.3.33 - 28-02-2022 =
 * Updated Freemius SDK

= 3.3.32 - 22-02-2022 =
 * Fixed product author field
 * Fixed dynamic permissions option in edit button widget
 * Fixed ACF fields not showing up within a group field
 * Fixed JS not working after first step in multi step
 * Fixed required mark not showing up after first step

= 3.3.31 - 21-02-2022 =
 * Fixed column field
 * Fixed shipping attributes error
 * Added user role options to admin form builder
 * Added validation to user role field

= 3.3.30 - 17-02-2022 =
 * Fixed delete button icons not showing

= 3.3.29 - 16-02-2022 =
 * Fixed default colors for the delete button 
 * Fixed woocommerce shiping fields not showing up and not saving correctly

= 3.3.28 - 10-02-2022 =
 * Fixed submission approval to edit data rather than add new data
 * Localized strings used in js files

= 3.3.27 - 10-02-2022 =
 * Fixed multi step validation error

= 3.3.26 - 06-02-2022 =
 * Fixed conflict with multi step form and repeater fields

= 3.3.25 - 27-01-2022 =
 * Fixed bug in woo delete product button
 
= 3.3.24 - 27-01-2022 =
 * Fixed bug in woo attributes field

= 3.3.23 - 25-01-2022 =
 * Fixed js error 'otherSteps undefined'
 * Moved plugin folder into "main" for development purposes
 * Restored missing submit button wrapper with class of "fea-submit-buttons"
 * Fixed submit button floating to top right of the form when field widths are less than 100%
 * Fixed local avatar setting
 * Fixed conditional logic not working across multiple steps

= 3.3.22 - 24-01-2022 =
 * Added [post:author] shortcode to dynamic tags on form submit
 * Fixed multi step validating fields in upcoming steps
 * Fixed setcookie being called after headers are sent

= 3.3.20 - 21-01-2022 =
 * Fixed review option not opening review page

= 3.3.19 - 20-01-2022 =
 * Added submit button styling options in Oxygen integration
 * Fixed missing fields in multi step form
 * Fixed Elementor styles for delete button
 * Fixed styles for labels applying to checkbox labels as well

= 3.3.18 - 19-01-2022 =
 * Fixed multi step form navigation issue

= 3.3.17 - 18-01-2022 =
 * Fixed wp uploader not working in modal window
 * Fixed delete button being followed by update button

= 3.3.16 - 17-01-2022 =
 * Fixed price field not displaying
 * Fixed url query editing current page if no object id passed in form
 * Added post status and product status as default fields in form builder

= 3.3.15 - 16-01-2022 =
 * Fixed form submit button hidden when tabs are used
 * Fixed tab display issue

= 3.3.14 - 12-01-2022 =
 * Fixed variations field not saving data
 * Fixed post author field not showing options

= 3.3.13 - 10-01-2022 =
 * Fixed error when navigating between steps on multi step form
 * Fixed delete button not redirecting after deleting data
 * Fixed issue when bulk deleting of submissions
 * Fixed form tabs display
 * Fixed email verification sending after each step of multi step form
 * Fixed form title missing
 * Fixed error with missing function in ACF Fields field
 * Fixed hidden submit button issue
 * Improved delete data widgets confirmation message
 * Added Delete Product Widget

= 3.3.10 - 05-01-2022 =
 * Fixed issue with form actions including emails and webhooks
 * Fixed multi step form not navigating to other tabs 
 * Added validation to user email field when "set as username" is active

= 3.3.9 - 30-12-2021 =
 * Fixed missing 'user_regitered' column

= 3.3.8 - 30-12-2021 =
 * Fixed error with user password not saving
 * Optimized process of saving user data in the database
 * Added settings for Attributes, Variations, Download Files fields to form builder

= 3.3.7 - 28-12-2021 =
 * Fixed forms showing on post edit page
 * Fixed new user form not saving data properly, including username and password
 * Fixed form builder presets not working
 * Fixed conflict with Oxygen

= 3.3.6 - 27-12-2021 =
 * Fixed issue with Woocmmerce fields: attributes and variations
 * Fixed conditional logic of Woocommerce fields

= 3.3.5 - 26-12-2021 =
 * Fixed 'undefined' error occuring with uploading images without permissions
 * Improved ux for multi step form

= 3.3.4 - 25-12-2021 =
 * Minified necessary scripts
 * Changed css classes that start with acff to frontend-admin

= 3.3.3 - 22-12-2021 =
 * Fixed multi step form user experience
 * Tabs don't use ajax when "link to step" is enabled

= 3.3.2 - 20-12-2021 =
 * Changed submissions database name

= 3.3.1 - 19-12-2021 =
 * Removed link to support forum in wp dashboard

= 3.2.21 - 17-12-2021 = 
 * Fixed user password not being saved on registration
 * Fixed acf field custom classes not showing in elementor widgets
 * Fixed css rule messing with repeater table display

= 3.2.20 - 14-12-2021 = 
 * Fixed password field updating password even when button is not pressed

= 3.2.19 - 13-12-2021 = 
 * Fixed false Trojan error in acff-min.js

= 3.2.18 - 09-12-2021 = 
 * Added request for plugin review after 10 submissions, 100 submissions, and 1000 submissions
 * Fixed upgrade notice to dismiss without page reload

= 3.2.17 - 2021-12-08 = 
 * Fixed submit button field not rendering
 * Fixed display name field index error
 * Fixed confirm password field showing in submission approvals

= 3.2.16 =
 * Added missing user password options in form builder
 * Fixed post author being set to admin upon submission approval
 * Fixed password not being saved upon aubmission approval. Password field does not appear in the submission form.

= 3.2.15 =
 * Fixed force edit user password editing the password even if blank
 * Fixed sale price field validation preventing submission even if the sale price is lower than regular price
 * Fixed post title set as slug option not working

= 3.2.14 =
 * Fixed fields being created from scratch instead of updated on page load in Elementor
 * Fixed ACF field styles bug in Elementor

= 3.2.12 =
 * Fixed bug causing all "add new" buttons to open form popup

= 3.2.11 =
 * Disabled media filters that were causing files to not show in media library 

= 3.2.10 =
 * Fixed form types popup not working when no forms exist

= 3.2.9 =
 * Added modal window to form builder
 * Fixed hidden field option in Elementor
 * Fixed Specific Post option

= 3.2.8 =
 * Fixed wp native post fields not saving data do to a change in Advanced Custom Fields

= 3.2.7 =
 * Fixed email verification sending when not selected under submission requirments

= 3.2.6 =
 * Added post status and product status fields

= 3.2.5 =
 * Changed payments addon installation form to post with page reload
 * Added styling options to the Frontend Form Oxygen element

= 3.2.4 =
 * Added button to install and activate the payments module
 * Added option to require email verification on form submission  

= 3.2.3 =
 * Added Email Verification option to submission requirements
 * Fixed trial text
 * Added contributor

= 3.2.2 =
 * Added display mode option to all field types. Option are Edit, Read Only, or Hidden
 * Added duplicate post and duplicate product options to form builder
 * Removed "addons" page in wp admin

= 3.2.1 =
 * Added ACF fields option to frontend form builder

= 3.2.0 =
 * Added form builder preset options

= 3.1.27 =
 * All ACFF field now use the field type as the field name

= 3.1.26 =
 * Fixed styles for specific fields
 * Fixed duplicate post form not reloading

= 3.1.25 =
 * Fixed taxonomy terms not loading selected terms

= 3.1.24 =
 * Added bulk add fields option to form builder

= 3.1.23 =
 * Fixed referer redirect in delete button

= 3.1.22 =
 * Fixed taxonomy fields loading random default value 

= 3.1.21 =
 * Fixed delete button not redirecting 

= 3.1.20 = 
 * Fixed limit submits not updating on post delete
 * Fixed multi step forms not going to next step
 * Improved form editing by deleting cookies that saved test submissions

= 3.1.19 =
 * Fixed forms not limiting submissions 

= 3.1.18 =
 * Fixed field groups not showing in Elementor widgets

= 3.1.17 =
 * Fixed issue with term name on edit form

= 3.1.16 =
 * Replaced conditional logic with custom js for the form builder settings
 * Fixed user bio field issue 

= 3.1.15 =
 * Restyled form builder admin page 

= 3.1.14 =
 * Added option to dynamically show field groups in frontend forms
 * Fixed submissions approval

= 3.1.13 = 
 * Fixed delete button issue

= 3.1.12 = 
 * Fixed conflict with Events Manager plugin

= 3.1.11 = 
 * Fixed issue with cross sell fields not showing
 * Fixed SKU field not changing label

= 3.1.10 = 
 * Fixed woocommerce fields not updating correctly

= 3.1.9 = 
 * Fixed issue with woocommerce integration

= 3.1.8 = 
 * Added "No Permissions Message" option in Frontend Form screen

= 3.1.7 = 
 * Divided product fields into categories
 * Fixed steps not changing on tabs

= 3.1.6 =
 * Fixed username edit not working
 * Fixed edit passwords validaing even when edit button isn't clicked 
 * Fixed term forms not saving data
 * Fixed post author field not saving entry

= 3.1.5 =
 * Fixed issue with updating user and terms field  

= 3.1.4 =
 * Added webhooks action to forms
 * Fixed Frontend admin options not saving

= 3.1.3 =
 * Fixed multi step form cookies issue
 * Fixed images not saving properly
 * Fixed submissions not showing submitted images

= 3.1.2 =
 * Fixed permissions settings 
 * Fixed delete button redirect issue when custom url is blank. From now on, it defaults to current page

= 3.1.1 =
 * Added submissions table to capture submissions before they are approved
 * Added option to require admin approval before data is saved

= 3.0.39 =
 * Added ajax no page reload option to form builder

= 3.0.38 =
 * Fixed "no success message" option not working when using ajax
 * Fixed post status option disappearing when "Draft" is selected

= 3.0.37 =
 * Added option "Allow Unfiltered HTML" to allow users to insert HTML from the front end (not recommended)
 * Fixed duplicate product form not copying taxonomy terms
 * Added better support for WP multi-site 

= 3.0.36 =
 * Fixed issue with product price

= 3.0.35 =
 * Fixed issue with new products not saving when using attributes

= 3.0.34 =
 * Fixed recaptcha field

= 3.0.33 =
 * Fixed acf_frontend/save_post hook
 * Fixed default value issue with elementor dynamic tags

= 3.0.32 =
 * Added cookie supprt to multi step forms so that data doesn;t get lost on page reload.
 * Uploaded images get added to media library only after the form is submitted
 * Fixed ACF Forntend admin settings not updating correctly

= 3.0.31 =
 * Added default permissions by role value for Frontend form
 * Fixed image field not attaching on edit post form

= 3.0.30 =
 * Fixed issue with taxonomy field conditional logic
 * Fixed issue with file upload for logged out users

= 3.0.29 =
 * Fixed issue with permissions "by role" selection
 * Fixed taxonomy field loading page terms instead of post terms

= 3.0.28 =
 * Fixed dynmaic default value feature 
 * Added option to filter products by user ids in linked product fields

= 3.0.25 =
 * Fixed terms not loading properly

= 3.0.24 =
 * Fixed backend validation error
 * Added gutenberg Category for Frontend blocks

= 3.0.23 =
 * Fixed site logo field
 * Fixed nested columns
 * Optimized form submission

= 3.0.22 =
 * Fixed form validation error

= 3.0.21 =
 * Fixed product title and descriptions fields not saving values

= 3.0.18 =
 * Fixed posts not saving if "title" field not in form

= 3.0.17 =
 * Fixed success message not showing on external pages

= 3.0.16 =
 * Fixed basic uploader not uploading images or files without media library
 * Added action hooks for after each data type is saved upon form submit

= 3.0.14 =
 * Fixed relationship with add/edit post form template not saving data since version 3

= 3.0.13 =
 * Fixed broken prepend and append not showing properly
 * Fixed recaptcha field
 * Moved all scripts out of includes folder 
 * Fixed issue with response to form within relationship field

= 3.0.12 =
 * Fixed Post Content field not working on backend
 * Fixed username not saving
 * Fixed duplicate default fields on new user form widget
 * Optimized add new post process

= 3.0.10 =
 * Fixed duplicate default fields on duplicate post form widget

= 3.0.9 =
 * Fixed user custom fields not saving since version 3.0.6
 * Fixed empty success message box appearing when show success message is turned off

= 3.0.8 =
 * Fixed missing files error

= 3.0.7 =
 * Added call to migrate ACF Frontend Elementor widgets data to latest version

= 3.0.6 =
 * Username change logs add user. Added automatic login for such a case
 * Added check to avoid success message showing on page reload
 * On Form screen, field labels are automatically populated when changed if empty

= 3.0.5 =
 * Fixed issue with product variations
 * Fixed issue with form validation

= 3.0.3 =
 * ACF Frontend Forms can now add and update multiple data types with just one form: posts, users, taxonomies, products, and site options 
 * ACF Frontend no requires Elementor to display Frontend admin forms. You can now create forms using the new form builder under the ACF Frontend tab.
 * Added Duplicate Product Widget
 * Removed need for url parameters on page reload
 * Fixed ACF custom fields loading data form multiple object types
 * Added all post fields as default in new post, edit post widget, and duplicate post widget 
 * Added all product fields as default in new product, edit product widget, and duplicate product widget 
 * Added all user fields as default in new user, edit user widget
 * Added all term fields as default in new term, edit term widget

= 2.11.8 =
 * Fixed username and password fields

= 2.11.7 =
 * Fixed issue with limit submissions when status is pending

= 2.11.6 =
 * Fixed issue with limit submissions count with new posts

= 2.11.5 =
 * Fixed issues with emails not sending when using Elementor
 * Fixed form shortcode display issue
 * Added email feature to ACF Frontend Forms
 * Added option to change error message

= 2.11.4 =
 * Fixed ux issue: multi step form staying at bottom of the page when moving to next step
 * Fixed ux issue: form within modal window staying at the bottom of the modal when validation error thrown
 * Fixed form shortcodes not rendering values in redirect settings

= 2.11.3 =
 * Multi step only counts submission at the last step
 * Multi step only publishes posts on last step unless set otherwise
 * Fixed issue with nested ACF relationship forms not adding new posts to the selected list. 
 * Fixed taxonomy field loading terms related to current page when adding a new post

= 2.11.2 =
 * Fixed error with multi step form

= 2.11.1 =
 * Fixed issue with Payments module addon

= 2.10.22 =
 * Fixed issue with content field not loading content in the admin dashboard
 * Fixed issue with spinning wheel stoping to circle before page reload
 * Fixed "Add Image" button style tab not having any effect.
 * Added shortcode for all form fields "[all_fields]"

= 2.10.20 =
 * Fixed error with delete button text hover color
 * Fixed issue with Frontend Form shortcodes not displaying fields properly

= 2.10.19 =
 * Added support for downloadable product
 * Added support for virtual products
 * Added additional validation on form actions
 * Fixed delete button error
 * Fixed "Duplicate Post" Widget missing "Specific Post" option

= 2.10.18 =
 * Fixed relationship add post feature within repeater/flexible content fields
 * Fixed scripts loading even on all pages even whe form does not appear
 * "Dynamic selection" for permissions now uses multi-valued as well as single-valued ACF user fields to control who can see the form
 * Fixed annoying editor at the top of Elementor editors
 * Fixed submit button showing even when a submit button field is used

= 2.10.17 =
 * Added term slug field
 * Added term description field
 * Fixed term name validation error
 * Fixed post date field loading wrong time

= 2.10.16 =
 * Fixed wysiwyg not loading as post content field 
 * Fixed issue with ACF fields not loading data properly

= 2.10.14 =
 * Added option to remove "edit password" button on edit user form

= 2.10.13 =
 * Fixed bug with variations attributes

= 2.10.12 =
 * Fixed issue with post type field

= 2.10.11 =
 * Fixed issue with delete post button permissions 
 * Fixed Elementor form template not showing form widgets
 * Fixed issue with relationship and add/edit custom post types feature

= 2.10.10 = 
 * Fixed issue with role field 
 * Fixed issue with field prepare 
 * Added Product field types to ACF
 * Added option to enable/disable add/edit products on linked products fields

= 2.10.9 = 
 * Fixed issue woth conditions of product fields

= 2.10.8 = 
 * Added Grouped Product type field
 * Added Upsells field
 * Added Cross-sells field 
 * Moved product field conditional logic to prepare field function rather than saving in database

= 2.10.7 = 
 * Added toggle arrow to variation rows 
 * Enabled variable price field by default
 * Fixed attributes custom terms field
 * Fixed attributes and variations fields ui issues
 * Added ability to show form based on user capabilities

= 2.10.5 = 
 * Fixed cache errors with prodct slug and product sku files
 * Fixed featured image display and attachment issue

= 2.10.4 = 
 * Fixed error with permissions

= 2.10.3 = 
 * Added Oxygen integration - phase 1 
 * Fixed Post Author dynamic permissions
 * Fixed All Users dynamic permissions
 * Added "Submit Button" field

= 2.10.2 = 
 * Fixed issue with featured image field

= 2.10.1 =
 * Fixed Multi step redeirection issue
 * Fixed delete button widget permissions issue
 * Deprecated delete button inside edit post form. From now on, use the Trash Button widget
 * Added ACF field types: Featured Image and Site Logo 

= 2.10.0 =
 * Seperated Payments module to seperate addon in order to make the core pugin lighter

= 2.9.16 =
 * Added Gutenberg block to make disaplying the forms easier
 * Added permissions to global forms
 * Fixed bug with payments form

= 2.9.15 =
 * Removed dependency on Elementor
 * Added form post type with shortcodes

= 2.9.13 =
 * Fixed Relationship Edit Post feature creating duplicate posts
 * Fixed error when updating product type

= 2.9.12 =
 * Fixed issue with "login as new user" in multi step
 * Fixed limit submission not updating when post is deleted
 * Fixed limit submission hiding form even in the Elementor preview

= 2.9.11 =
 * Fixed media attachment to post issue
 * Fixed Freemius SDK issue causing conflicts with other plugins using Freemius

= 2.9.10 =
 * Fixed default user role setting not working
 * Fixed column field layout issues
 * Fixed stock quantity field not updating the stock status
 * Added conditional logic to stock fields

= 2.9.9 =
 * Added External Url field and Button text field to product forms
 * Added default main image for new products

= 2.9.8 =
 * Fixed missing float setting
 * Fixed username not being set from email when setting is turned on
 * Adding "save prepend" and "save append" feature
 * Fixed username required even when turned off

= 2.9.7 =
 * Fixed "sale price" showing only on variable type products
 * Added tax class field and tax status field
 * Added Elementor support to ACF group fields 

= 2.9.6 =
 * Fixed css issue with relationship edit icon
 * Fixed relationship add/edit form reloading page

= 2.9.5 =
 * Fixed error not showing empty variations field

= 2.9.4 =
 * Fixed taxonomy field not saving properly

= 2.9.3 =
 * Fixed issues with variations saving new products
 * Fixed ACF Frontend admin options not saving
 * Fixed local avatar not showing image fields

= 2.9.2 =
 * Improved the end user ux of the attributes field
 * Improved the end user ux of the variations field
 * Added full control over all the text in the attributes field
 * Added full control over all the text in the variables field
 * Added "reassign users" option to the delete users button
 * Fixed delete buttons error

= 2.9.1 =
 * Added support for variable products
 * Added variation field to edit product and new product forms
 * Added "prepend" option to text based fields
 * Added "append" option to text based fields
 * Added "minimum" option to number based fields
 * Added "maxinimum" option to number based fields
 * Fixed conflict with ACF Extended

= 2.8.29 =
 * Added option to edit "Attributes" field inner fields 

= 2.8.28 =
* Fixed edit term form not loading term id in loop
* Fixed product delete button error

= 2.8.27 =
 * Fixed submit spinner showing twice

= 2.8.26 =
 * Fixed pdf not uploading to gallery field
 * Added "Delete Term" widget
 * Added "Delete User" widget
 * Fixed relationship edit post post icon adding post to selected list 

= 2.8.25 =
 * Added Woocomerce Product Attributes field
 * Added Woocomerce Product Type field
 * Fixed "add new term" option only appearing to editors and up
 * Changed "add new term" from icon to button 
 * Added random string to modal windows so that the ids will be unique no matter what

= 2.8.24 =
 * Fixed issue with Relationship field "add post" feature
 * Fixed broken clone fields display

= 2.8.23 =
 * Fixed edit post form changing post type

= 2.8.21 =
 * Fixed issue with multi step form "overwrite action settings" feature
 * Fixed multi step form issue submitting form when last step is visited before previous steps are filled 

= 2.8.20 =
 * Added default terms option to categoies, tags, and taxonomy fields

= 2.8.19 =
 * Improved ACF relationship add/edit feature to allow unlimited layers of posts within posts

= 2.8.18 =
 * Added "Save revision" feature to edit post form

= 2.8.17 =
 * Changed new post form "post type" option to show all post types, including private opens
 * Fixed gallery upload issue when logged out
 * Multi step forms now update $GLOBALS['admin_form'] when clicking on step tabs

= 2.8.16 =
 * Added Edit Current Author option to User Edit Form
 * Fixed conflict with ACF Multi Lingual

= 2.8.15 =
 * Fixed form calling validation twice
 * Added "Duplicate Post Form" Elementor widget
 * Imporoved preview of forms that lack permissions 

= 2.8.13 =
 * Fixed "post url" redirect option
 * Added step index as hidden field in multi step form 

= 2.8.12 =
 * Added Local Json support for frontend ACF fields
 * Fixed Javascript error
 * Fixed Payment collection feature

= 2.8.11 =
 * Fixed js 404 issue

= 2.8.10 =
 * Fixed foreach error

= 2.8.9 =
 * Added delete success message option
 * Added save draft success message option
 * Fixed error in form submit
 * Fixed default post date off
 * Fixed plugin classes structure
 * Freemius SDK update

= 2.8.8 =
 * Fixed WPML integration error
 * Fixed multi step errors
 * Fixed missing draft buttons
 * Save as Draft and load draft features now work without page reload
 * Fixed delete button not redirecting
 * Fixed post title saving as slug when both options are set

= 2.8.6 =
 * Fixed error with gallery field when using basic uploader
 * Added js filter to username field to accept only lowercase latin letters, digits, @, and .  

= 2.8.5 =
 * Fixed true/false field not showing checkbox

= 2.8.4 =
 * Fixed issue with "post author" permissions showing to other users
 * Fixed error with shortcodes when fields lack value

= 2.8.3 =
 * Fixed multi step forms tab navigation issue
 * Fixed multi step error with message fields

= 2.8.2 =
 * Fixed basic uploader
 * Fixed js modal error
 * Added loading bar when uploading images using the basic uploader

= 2.8.1 =
 * Fixed "dynamic" permissions settings
 * Fixed issue with loading repeater field

= 2.8.0 =
 * Added no reload between steps on multi step forms
 * Fixed bug with relationship field Add/Edit posts option
 * Fixed issues with Product images field
 * Fixed featured image shortcodes
 * Added product slug field

= 2.7.35 =
 * Fixed issue with multi step form 
 * Fixed issue with defined function get_user_field()

= 2.7.34 =
 * Fixed form not clearing after adding post
 * Fixed field groups not showing 

= 2.7.33 =
 * Added width, margin, and padding options to all field types
 * Added Column field type
 * Fixed issue with Save Drafts option

= 2.7.32 =
 * Fixed bug with new post and new user forms not showing up

= 2.7.31 =
 * Fixed error with editing display name option
 * Fixed permissions error on edit user form

= 2.7.30 =
 * Fixed email field not passing validation when required

= 2.7.29 =
 * Fixed gallery images not saving with basic upload
 * Fixed text fields showing default html "required" errors
 * Added "New Term Form" widget

= 2.7.28 =
 * Fixed image uploading not working for logged out users or contributor role
 * Fixed multiple instances of same html ids on the same page
 * Added options to change the upload button text in Image-based fields  
 
= 2.7.27 =
 * Fixed message field disappearing

= 2.7.26 =
 * Fixed error with edit password button

= 2.7.25 =
 * Fixed issue with redirect
 * Fixed issue with script loading too early

= 2.7.24 =
 * Fixed edit post form to keeps status by default
 * Fixed missing password strength meter file

= 2.7.23 =
 * Added "form container width" option to relationship field
 * Fixed permissions not working on user edit form
 * Removed border on mobile multi step form
 * Fixed multi step form being hidden on mobile
 * Fixed product images field not showing
 * Fixed featured images not "attaching" to posts

= 2.7.21 =
 * Fixed bug that was changing post title upon editing without a post tile field

= 2.7.20 =
 * Fixed featured image and Woo main image not saving when using basic input
 * Fixed saved draft in last step of multi step form redirecting
 * Fixed "frontend only" fields disappearing when using Ajax submit 

= 2.7.19 =
 * Fixed Save Draft feature validating required fields
 * Fixed draft not saving if no title inserted

= 2.7.18 =
 * Removed closing /div that was breaking page layouts when multi step forms were used
 * Fixed form not submitting on second ajax submission
 * Fixed title field "post title" feature not saving as slug

= 2.7.17 =
 * Fixed Password strength error in popups and modals

= 2.7.16 =
 * Fixed relationship field "add post" feature

= 2.7.15 =
 * Fixed "[user:email]" shortcode
 * Added "[user:role]" shortcode

= 2.7.13 =
 * Fixed issue preventing field data not displaying Elementor dynamic data

= 2.7.12 =
 * Fixed issue with form not clearing

= 2.7.11 =
 * Fixed product categories field checkbox appearance option
 * Fixed taxonomy field checkbox appearance option
 * Fixed ACF Frontend settings not saving

= 2.7.10 =
 * Fixed drafts saving as published
 * Fixed saved drafts not changing draft status

= 2.7.9 =
 * Made submit button blur on submit

= 2.7.8 =
 * Fixed issue with multi step functionality

= 2.7.6 =
 * Fixed issue in relationship field Add Post feature not saving the new posts
 * Fixed issue in relationship field with post types
 * Added a default post type field to the "Fields" widget in the Form template
 * Fixed issue with modal window removing content when clicking the X
 * Important: Deprecated the default title and default featured image settings as they can be set using hidden post title and featured image fields 

= 2.7.5 =
 * Fixed bug preventing author, subscribers, and logged out users from submitting
 * Fixed bug with User shortcodes on post forms
 * Fixed issue in relationship field adding post when the edit icon is clicked

= 2.7.4 =
 * Fixed bug with the password meter

= 2.7.3 =
 * Fixed bug with display name field

= 2.7.2 =
 * Added option to add and edit posts from a relationship field
 * Added option to choose Elementor form template for the add and edit posts form
 * Added option to filter posts/pages/cpts in a relationship field based on a specific author or the current user's posts/pages/cpts
 * Fixed bug with drafts list
 * Dates on drafts now display in time and date format from the the wp dashboard settings page
 * Fixed "set as post title" conflict with "set as post slug" when used together in a text field. Now it displays the Title and updates the slug as well.
 * Added "post type" field to add post and edit post forms. 
 * Fixed issue with default title not showing dynamic values correctly
 * Added option to add dynamic value shortcodes in the custom redirect url
 * Added feadmin_get_esc_attrs for backward compatibilty of older ACF versions
 * Restructured the form submission to process data faster
 * Fixed the preview redirect url setting
 * Fixed email meta data repeating Time 

= 2.6.20 =
* Fixed bug with Saved Drafts list
* Extended modal window option to all widgets
* Extended modal styles to free version

= 2.6.19 =
* Fixed bug with "Clear Form" setting

= 2.6.18 =
* Fixed issue with Add Product form

= 2.6.17 =
* Fixed issue with multi step add post form
* Fixed issue with modal closing on submit

= 2.6.16 =
* Added a more intuitive UI to password strength and match meters
* Added option to leave modal window open on submit
* Added edit password button to password field in edit user form
* Fixed redirect issues with new posts
* Fixed redirect when using acf_form()

= 2.6.15 =
* Fixed white screen error

= 2.6.14 =
* Added dynamic pricing option to the credit card form
* Added Slug field
* Added post Date field
* Added Post Author field
* Fixed Role field labels for custom roles
* Added Post Order Menu field
* Added option to show form data in update message by using field shortcodes (ex. [acf:field_name] )
* Fixed conflict with Anywhere Elementor

= 2.6.12 =
* Fixed multi step form render bug

= 2.6.11 =
* Fixed bug with edit user form
* Added option to add default featured image

= 2.6.10 =
* Fixed errors with multi step form

= 2.6.9 =
* Fixed issue with edit post form settings not saving
* Fixed issue with two ajax forms on same page

= 2.6.8 =
* Fixed ajax errors when updating Elementor
* Added Ajax success message when creating a new post in Pro

= 2.6.7 =
* Fixed issue with email user shortcodes
* Fixed issue with post status in multi step forms

= 2.6.6 =
* Fixed dynamic default value on new posts

= 2.6.5 =
* Fixed error

= 2.6.4 =
* Fixed issue that prevented title value from loading

= 2.6.3 =
* Fixed pot translation files
* Added option to hide admin area by role 
* Added option to disable the option to hide admin area by user
* Added option to redirect users from WP dashboard to any url 

= 2.6.2 =
* Fixed multi step conditional logic error
* Fixed email content error
* Added dynamic default value option to title field

= 2.6.1 =
* Fixed email shortcodes
* Fixed bug preventing submitting forms that don't require payment

= 2.6.0 =
* Added Stripe and Paypal Credit Card options for taking payments for new post submissions
* Added option to show success message

= 2.5.41 =
* Fixed Display Name field not saving
* Fixed Wyswyg styling errors

= 2.5.40 =
* Fixed repeater rows author filtering rows display by author
* Fixed H1 tag appearing on all pages

= 2.5.39 =
* Fixed bug with multi step new post form
* Added option to filter repeater rows based on row author

= 2.5.37 =
* Fixed bug not showing field labels on live page 

= 2.5.36 =
* Fixed field default value, placeholder and label not showing dynamic value

= 2.5.35 =
* Added default value option to text based fields
* Added read only option to text based fields
* Added disabled option to all fields
* Added option to hide a field from view
* Fixed Taxonomy fields not loading

= 2.5.34 =
* Added option to leave message for users who are not allowed to view form
* Added permissions options to steps

= 2.5.33 =
* Limited free 7 day trial notice to the ACF Frontend settings page
* Added option to choose which post gets deleted or trashed by the Trash Button
* Added more redirect options upon deleting or trashing a post/product 

= 2.5.32 =
* Fixed post drafts not publishing issue
* Fixed issue with post draft validation

= 2.5.31 =
* Fixed Anywhere Elementor conflict

= 2.5.30 =
* Fixed missing field groups issue
* Fixed missing taxonomy fields

= 2.5.29 =
* Fixed custom taxonomy field issue
* Fixed message field issue

= 2.5.28 =
* Fixed missing file error

= 2.5.27 =
* Fixed error with multi step new products form

= 2.5.26 =
* Fixed error on Elementor Pro single templates 

= 2.5.25 =
* Added option to allow users to update their usernames. Warning: this can affect your urls and their SEO ratings
* Added option to hide success message
* Added option to allow user manager to edit other user profiles
* Fixed bug with message field not showing on frontend
* Fixed bug with title structure

= 2.5.24 =
* Fixed Pro trial message not being dismissed
* Fixed multisite error

= 2.5.23 =
* Added missing "new post" in main action
* Fixed bug in new post form submission

= 2.5.21 =
* Added a styling tab to each field with margin, padding and width styles
* Added placeholder option for text-based fields
* Added option to add default display name
* Added option to add default username
* Fixed some bugs with the registration form


= 2.5.20 =
* Fixed previous step password fields causing validation errors
* Fixed text editor appearing on non Elementor pages

= 2.5.19 =
* Fixed confirm password field validation issue
* Added option to save User Email as Username

= 2.5.18 =
* Fixed field width issue on RTL sites

= 2.5.17 =
* Added option to display taxonomy, categories and tags fields in dropdowns or radio buttons
* Added option to add new terms in taxonomy, categories and tags fields 

= 2.5.16 =
* Fixed issue with user role field
* Fixed issue with user email sending notice when registering

= 2.5.15 =
* Fixed issue with Ajax validation
* Fixed missing submit spinner

= 2.5.14 =
* Fixed issue with last step of multi step form not redirecting
* Added all Woocommerce product inventory fields
* Added user nickname and user display name fields

= 2.5.13 =
* Fixed issue with duplicate fields saving in database
* Fixed product images field not saving images to product images 
* Added better support for Elementor popups

= 2.5.11 =
* Added ACF conditional logic support to multi step forms 
* Fixed last step of multi step forms not redirecting to custom url 
* Fixed user fields not loading saved value 
* Fixed taxonomy field type had no taxonomy selection

= 2.5.10 =
* Moved Local Avatar and Uploads Privacy settings to ACF Frontend admin page
* Added dynamic tag for user local avatars to replace the default gravatar ( requires Elementor Pro )
* Fixed two lines appearing after field groups
* Fixed issue that prevented trashing posts of custom type rather than deleting them

= 2.5.9 =
* Fixed error showing hidden input

= 2.5.8 =
* Fixed issue with multi step created new posts each step

= 2.5.7 =
* Fixed error with migration query

= 2.5.5 =
* Added option to trash posts instead of deleting them in the delete post button
* Optimized the migration of old widget setting to the widget settings
* Fixed repeating field groups issue
* Fixed field group tab issue

= 2.5.4 =
* Fixed error when saving widget in template
* Fixed step tabs

= 2.5.3 =
* Fixed Google Maps issue

= 2.5.2 =
* WARNING: this updates the database. Please back up your database before updating
* Restructured the fields selection so that all of the fields are in one place in the editor. Now you can reorder the fields
* Restructered the multi step option so that it is more similar to Elementor's multi step option
* Seperated confirm password and password strength check 
* Added option to add content inside the form
* Added multi step option to all the form widgets

= 2.4.19 =
* Fixed issue with excluding fields option

= 2.4.18 =
* Fixed issue with duplicating fields in latest update

= 2.4.16 =
* Added option to display choice fields and image fields to comments list widget 

= 2.4.15 =
* Fixed Url not showing right on localhost in the new Edit Button widget 

= 2.4.14 =
* Added Edit Button and Delete Button widgets to single template

= 2.4.13 =
* Fixed Comments List widget bugs
* Fixed label and instruction spacing styles not working

= 2.4.12 =
* Added Comments List widget in Pro

= 2.4.11 =
* Removed default value from the default post title option

= 2.4.9 =
* Added no page reload option in pro (still in development)

= 2.4.8 =
* Fixed bug when adding post and editing on same page
* Added Default title and default featrured image options to post and product actions/widgets
* Added some more frontend Woocommerce fields

= 2.4.7 =
* Added New Comment action and widget in pro

= 2.4.6 =
* Added Site Options, Add and Edit Product widgets in pro

= 2.4.5 =
* Added Edit User and New User Widgets
* Added ability to add url parameters to the redirect
* Added option to preview redirect
* Limited the editing privilage to post author when Post to Edit is set to URL Query in Edit Post action 
* Fixed the update message showing in all posts 

= 2.4.3 =
* Fixed widget bug

= 2.4.2 =
* Added "Edit Post Form" widget
* Added "New Post Form" widget
* Added redirect and icon options to Delete Post Button option and widget
* Added icon option to modal button

= 2.4.1 =
* Fixed issue with Modal button

= 2.4.0 =
* Added Paypal option to forms in pro (BETA)
* Added Paypal button widget (BETA)
* Added Category for Widgets

= 2.3.35 =
* Added option to either clear new post from form or edit it

= 2.3.34 =
* Fixed bug with custom password

= 2.3.33 =
* Fixed Confirm Password field errors
* Fixed error with multi step fields

= 2.3.31 =
* Fixed bug that was reloading page instead of redirecting users to new post when "new post url" was selected 

= 2.3.30 =
* Fixed error trying to call product action functions when Woocomerce not installed

= 2.3.28 =
* Fixed error with custom price fields
* Fixed error with Woocomerce categories and tags
* Added responsive width to all built-in fields
* Added Google reCaptcha field in Pro
* Tweaked the page reload on new posts and users to load the newly added post data in the form

= 2.3.27 =
* Fixed error with site tagline field

= 2.3.26 =
* Added Happy files integration to ACF file field, image field, and gallery field.
* Added custom "product images" field on frontend for Woocommerce users
* Added custom "product sale price" field on frontend for Woocommerce users
* Added option to edit a post based on a URL query
* Fixed issue with "title structure" option on new submissions
* Fixed issue that was creating two users during the multi step form

= 2.3.23 =
* Added option to exclude field from field groups selection for faster setup
* Added option to add default field groups faster setup

= 2.3.22 
* Fixed product categories field was loading WP categories
* Custom title structure was not saving on initial submission

= 2.3.21 =
* Tweak: Added automatic login option to registration form
* Added custom structure to title field
* Added custom post slug option for posts
* Added custom product price option to number fields
* Added New Product and Edit Product actions in pro. Woocommerce integration phase one.
* Fixed edit user action to be able to choose whether or not to require passwords
* Fixed validation error on default fields
* Fixed Site Title field not saving

= 2.3.20 =
* Fixed ACF tabs not working

= 2.3.19 =
* Fixed multi step previous button, which was activating the field validations
* Fixed confirm password bug not validating properly

= 2.3.17 =
* Fixed multi step tabs not showing properly on vertical align

= 2.3.16 =
* Fixed error of post titls not saving as slug for CPT
* Fixed error of not saving username

= 2.3.15 =
* Fixed error: Custom Post data fields were loading and saving the wrong data

= 2.3.14 = 
* Fixed error in multi form

= 2.3.13 = 
* Added options to default fields
* Removed custom post and user field selection from the widget settings
* Fixed some JS errors

= 2.3.7 = 
* Added Stripe action
* Added a Delete Post Button widget
* Added option to remove border between fields
* Optimized the assets to load only where needed

= 2.3.6 = 
* Fix: There was a bug on the edit user action that we squashed
* Tweak: Added Custom Labels for the built in user fields

= 2.3.5 = 
* Added Tabs Position option: side or top
* Added multi step tabs navigation to preview
* Emails in multi step form were sending in first step even when not specified
* Email user shorcodes fixed

= 2.3.1 = 
* Important: Changed the email shortcodes to be written without a underscore before it. For example: 'post_title' instead of '_post_title'
* Important: Changed the default site option fields to be deactivated by default. Activate them in the options tab if they are needed 
* Fix: multi step forms were not saving new posts properly 
* Tweak: Added validation to username to block illegal characters 
* Tweak: Added option to force strong password and confirm
* Tweak: Repositioned the save draft button and added styling options for it
* Tweak: Added email shortcode support for featured image and post url as well as fields with multiple values
* Tweak: Added option to show custom content or nothing for reached limit
* Coming soon: Stripe
* Coming soon: Woocomerce


= 2.2.15 = 
* Fix: multi step forms not showing some of the default user fields and not submitting to next step

= 2.2.14 = 
* Fix: Multi forms and custom labels were not working together
* Fix: Messages were always on edit screen

= 2.2.13 =  
* Fixed the Elementor select controls in the widget to comply with Elementor 2.9
* Fixed conflict with free Elementor version caused by autocomplete controls by replacing them with text field for inserting post, user, and term ids for Elementor free version only
* Fixed bug in query which was showing all drafts in the draft selection


= 2.2.12 = 
* Tweak: Added custom label options to default post fields so that you don't need to create a whole ACF field just to change the label. Available for post actions in free version and for edit options action in pro. Coming soon for user actions.
* Fix: fixed bug that was saving posts as drafts when save as draft was turned off
* Fix: fixed bug that was preventing saved drafts from showing when adding a post of a custom type

= 2.2.11 = 
* Fixed bug preventing featured images from saving on certain custom post types since 2.2.10
* Removed default ACF options page. Will return as option in 2.3
* Tweak: limited drafts shown in new post form to those submitted by author
* Tweak: fixed the save progress toggle to always show before submit button

= 2.2.10 = 
* Please note: Changed "set as title field" in ACF text field settings to save data from admin dashboard as well as frontend
* Please note: Changed "set as content field" in ACF wysiwig and textarea field settings to save data from admin dashboard as well as frontend
* Please note: Changed "set as excerpt field" in ACF textarea field settings to save data from admin dashboard as well as frontend
* Added a plugin admin page
* Optimized the widget in the Elementor editor by loading posts, users, and terms selections' values with autocomplete
* Added option to let users save a draft on new post action
* Added option to let users edit saved draft on new post action
* Added ACF options to allow you to create custom featured image field and to create read-only text fields
* Added default site option fields to allow users to edit site title, tagline, and logo in pro
* Added popover for email shortcodes in the editor in pro

= 2.2.9 = 
* Added default user fields: First Name, Last Name, and Biography
* Fixed bug in ACF settings when switching field type
* Fixed a bug with the ACF field selections
* Added option to create new post with many steps
* Added styles for steps in pro
* Added styles for ACF icons in pro
* Added more options to multi step in pro
 
= 2.2.8 = 
Changed required mark to show by default
* Fixed bug preventing the form from showing when "all users" is chosen in the permissions tab
* Fixed bug preventing field group names from showing in fields selection

= 2.2.7 = 
* Added hook that hides all default ACF Frontend fields from dynamic tags
Returned option to hide admin area to backend user forms
* Fixed permissions bug preventing form from appearing when first added

= 2.2.6 = 
* Fixed error with ACF image fields

= 2.2.5 = 
* Fixed bug in the logged in users setting in the permissions tab

= 2.2.4 = 
* Added tags setting to post actions
* Added post status setting to edit post action

= 2.2.3 = 
* Added default post categories and tags fields
* Added label display options
* Added styles for modal window, messages, fields, labels, and add more buttons in pro

= 2.2.2 = 
* Fixed error in dynamic selection setting in permissions tab

= 2.2.1 = 
* Added Freemius opt-in so that we can use shared data to make this plugin freaking awesome!
* Added promos for pro features.
* Added user auto-populate options and local ACF avatar




== Upgrade Notice ==





