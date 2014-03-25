=== Web Payment Software Payment Gateway for WooCommerce ===
Contributors: Dualcube
Donate link: http://dualcube.com/
Tags: gateway, sales, sell, shop, shopping, store, cart, checkout, commerce, e-commerce, web payment, wordpress ecommerce, shopping cart, woocommerce, credit card, extension
Requires at least: 3.6
Tested up to: 3.8.1
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A simple, affordable solution for your business to accept payments online through Woocommerce.

== Description ==

Web Payment Software™ is a product and service of Mountain Media. Mountain Media has been serving the eCommerce development marketplace since 1998. 

As a compliment to Mountain Commerce, the company’s full-featured eCommerce platform, Mountain Media has developed a payment gateway for use with WooCommerce with an added twist. Not only does Web Payment Software™ function as a traditional payment gateway, it also offers a virtual terminal as well as a hosted solution for constructing and managing “payment pages.” This flexible new system is great for accepting online invoice payments, donations or event registrations or as an alternative to PayPal. 

Mountain Media is a PCI DSS compliant hosting provider which ensures that your transactions will be protected by the toughest data security standards and technology available. Contact a payment consultant today at (877) 583-0300 for a free consultation.

= Feedback =
All we want is some love. If you did not like this plugin or if it is buggy, please give us a shout and we will be happy to fix the issue/add the feature. If you indeed liked it, please leave a 5/5 rating.  
In case you feel compelled to rate this plugin less than 5 stars - please do mention the reason and we will add or change options and fix bugs. It's very unpleasant to see silent low rates. For more information and instructions on this plugin please visit www.web-payment-software.com.


== Installation ==

= Installing The Payment Gateway Plugin =
1. Download the plugin zip file.
2. Login to your WordPress Admin. Click on Plugins | Add New from the left hand menu.
3. Click on the “Upload” option, then click “Choose File” to select the zip file from your computer. Once selected, press “OK” and press the "Install Now" button.
4. Activate the plugin.
5. Open the settings page for WooCommerce and click the "Payment Gateways" or "Checkout" tab.
6. Click on the sub tab for "Web Payment Software".
7. Configure your Web Payment Software Payment Gateway settings. See below for details.

**Note: This plugin requires that you have an SSL certificate installed and active on your site. Also 'Force SSL' option should be active on Woocommerce.**



= Obtain Credentials from Web Payment Software Payment Gateway = 
If you are a merchant looking to accept credit card payments online then click on this [link](https://www.web-payment-software.com/application.php "Web Payment Software Payment Gateway").

To accept payments online, you will need to apply for a payment gateway account and a merchant account.



= Connect to WooCommerce =
To configure the plugin, go to **WooCommerce > Settings** from the left hand menu, then the top tab “Payment Gateways” or "Checkout". You should see "Web Payment Software" as an option at the top of the screen. You can select the radio button next to this option to make it the default gateway.

* **Enable/Disable**  – check the box to enable Web Payment Software.
* **Title**  – allows you to determine what your customers will see this payment option as on the checkout page.
* **Description**  – controls the message that appears under the payment fields on the checkout page. Here you can list the types of cards you accept.
* **Merchant ID** – enter the API merchant ID you created in your Web Payment Software account.
* **Merchant Key** – enter the API merchant key you created
* **Transaction Mode** – select the sale method you prefer – your options are: ‘Authorize Only’ or ‘Authorize & Mark. ‘Authorize Only’ will authorize the customer’s card for the purchase amount only. ‘Authorize & Mark’ will authorize the customer’s card and collect funds.
* **Set Order Status For Mark** - select which order status to use for "Mark" transaction type.
* **Set Order Status For Void** – select which order status to use for "Mark" transaction type.
* **Host** – URL for Web Payment Software gateway processor.
* **Accepted Cards** - List of credit cards that your website will use.
* **CVV** - check the box to require customers to enter their credit card CVV code.
* **Web Payment Software Test Mode** - Enable Test / Sandbox mode for testing transaction. *Note: This option must be unchecked in the live site to collect online payments.*
* **Logging** -  Enable logs for payment gateway requests and responses.
* **Save Changes**.


== Frequently Asked Questions ==

= Does this plugin work with newest WP version and also older versions? =
Yes, this plugin works really fine up to WordPress 3.8.1! It is also compatible with older WordPress versions, down to 3.6.

= I have Woocommerce 1.X, does the plugin works with this? =
No, the plugin is compatible for Woocommerce versions 2.X and above.

= Is the payment gateway secure ? =
Yes, this payment gateway only works when the woocommerce force SSL is activated and you have a SSL certificate installed and active on your server.

= Do I need a merchant account before I can use the Web Payment Software gateway plugin? =
Yes. In order to use this plugin you will need a merchant services account. Web Payment Software offers merchant accounts. For more information, please visit: [http://www.web-payment-software.com/](http://www.web-payment-software.com/ "Web Payment Software"). If you already have a merchant account set up, chances are our gateway will integrate with it. Send us an email: [support@mountainmedia.com](mailto:support@mountainmedia.com "support@mountainmedia.com") or call our office: 1.877.583.0300 to find out more.

= What is the cost for the gateway plugin? =
This plugin is a FREE download, however it does have monthly and per transaction costs. For detailed Pricings, click on this [link](http://www.web-payment-software.com/Pricing-c6.html "Pricing"). For more information: [support@mountainmedia.com](mailto:support@mountainmedia.com "support@mountainmedia.com")

= Web Payment Software is not showing up as a payment method - help! =
For security purposes, this plugin requires an active SSL connection via a secure https page to view this option on your payment pages.



== Screenshots ==

1. In order to ensure secure transactions force SSL(HTTPS) is required on the checkout page.
2. Mandatory details include Merchant ID and Merchant Key
3. Easily accept credit card payments on your own site.


==Changelog==

= 1.0.0 =
* Initial release.


==Upgrade Notice==

Have fun.
