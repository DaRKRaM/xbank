XBANK v1
--------

A simple virtual Wallet made in Wordpress.

Theme:
Using the custom theme "xbank" a child of hello-elementor.

Plugins:
PODS: Creation of "Dinero" Content Type.
Creation of extra field for Users. (Field "money" -> balance of user)

Ultimate Member: memberships login and account.

Plugin Xbank:
Custom plugin created to show the account statement, and to manage Deposits and withdraws.

Pages:
HOME - Contains the Account Statement and the form for adding or withdraw funds.
Add funds or withdraws creates a new posts of dinero custom content type)

REVOKE (there is and option to delete posts (revoke a transfer) of the dinero custom content type by user) on page: 
Post-Process (theme xbank/page-post-process.php)

Custom Json:
file (theme xbank/inc/json.php)
Creation of choosed fields to show on a JSON file the REST API. (more fields can be added)
https://www.aijer.org/wp-json/bank/v1/dinero



REST API
(default)
https://www.aijer.org/wp-json/wp/v2/dinero


