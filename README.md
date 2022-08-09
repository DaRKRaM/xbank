XBANK v1
--------

A simple virtual Wallet made in Wordpress.

Theme:
Using the custom theme "xbank" a child of hello-elementor.

Plugins:
PODS: Creation of "Dinero" content type.
Creation of extra field for User. (Money -> balance of user)
Ultimate Member: memberships login and account.

Plugin Xbank:
Custom plugin created to show the deposits history, and Save the Deposits (deposits are a custom post type called "Dinero")
You can choose Add or Withdraw.

Pages:
Widthdraws are managing on page: 
Post-Process (theme xbank/page-post-process.php)

Custom Json:
file (theme xbank/inc/json.php)
Creation of choosed fields to show on a JSON file the REST API.
https://www.aijer.org/wp-json/bank/v1/dinero



REST API
(default)
https://www.aijer.org/wp-json/wp/v2/dinero


