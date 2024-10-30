=== Plugin Name ===
Contributors: integrate-whatsapp-chat
Donate link: https://www.whatsappapi.in
Tags: whatsapp messsage, send whatsapp messsage, whatsapp api, verify whatsapp number
Requires at least: 3.0.1
Tested up to: 3.4
Requires PHP: 5.2.4
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Send OTP, Alert, Notification, Invoice, Booking Information, Image (.png or .jpg), PDF file directly on customer WhatsApp.

== Description ==

How to use?
 
Step 1 - Generate your api key & pair whatsapp number on https://www.whatsappapi.in

Step 2 - Fill up details above form

Step 3 - Use below function from anywhere to message on whatsapp

Send Text Message - whatsappMessage($country_code='91',$number = '987654****',$message = 'Order Notification on WhatsApp');

Send Image - whatsappMessage($country_code='91',$number = '987654****',$message = 'https://www.whatsappapi.in/front-assets/img/logo.png',$is_file = true);

Send PDF - whatsappMessage($country_code='91',$number = '987654****',$message = 'https://www.whatsappapi.in/dummy.pdf',$is_file = true);

