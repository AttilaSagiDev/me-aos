# **Magento 2.0 Products by Quantity Range Extension** #


## Description ##

The extension shows products which stock quantity value are between in a specified range. The module provides a widget for inserting the block anywhere in the frontend quickly. 

The administrator can insert the widget in CMS Pages and Static Blocks, or anywhere use the Magento 2.0 Widgets menu.

The extension comes with five different default templates for showing products in the content area, or anywhere on the frontend of the store depends on the administrator choice. These five modes are displaying the products in grid or list, images and names only, names or images only mode.

The widget has plenty of options to set up before inserting, so it's easy to customize the appearance of the products. The administrator can configure to show or not the block widget title, and also customize title's text. There is the same option for the block's subtitle(short description) also.

The administrator can set to display the pager of the product list and the number of products per page. The customizable minimal and maximal stock quantity range determine which products will be displayed.

There are also options to set up the default sorting of the products(name,  price, creation date) and the maximum number of items to display.

The extension supports configurable, grouped and bundle product types  (besides simple, downloadable or virtual products) which have associated or attached simple products.

## Features ##

- The widget(s) can be placed anywhere easily on the store frontend in Magento 2.0.
- Five different default mode to show the list of the items.
- Custom block title and a short description(subtitle), and also the availability to show or not.
- Display pager or not and the number of products to show by page.
- Customizable minimal and maximal stock quantity limit range.
- Adjustable maximum items to display, sorting options by creation date, name and price in ascendant or descendant direction. 
- Administrator able to set separately to display or not configurable, grouped and bundle products. 
- Multistore support.
- Supported languages: English.

Individual module, i. e. it does not modify the standard Magento 2.0 files.
 
Support:
Magento Community Edition  2.1.x, 2.2.x

## Installation ##

** Important! Always install and test the extension in your development environment, and not on your live or production server. **
 
1. Backup Your Data 
Backup your store database and web directory. 
 
2. Clear Cache and cookies 
Clear the store cache under var/cache and all cookies for your store domain. 
 
3. Disable Compilation 
Disable Compilation, if it’s enabled.

4. Upload Files 
Unzip extension contents on your computer and navigates inside the extracted folder. Create folder app/code on your web server if you don't have it already. Using your FTP client upload the content of the directory to your store root/app/code folder.
Important! If the module contents don't include the Me/Aos directory in the zip file, you must create root/app/code/Me/Aos folder and upload the extension here.

5. Enable extension
Please use the following commands in the /bin directory of your Magento 2.0 instance:

    php magento module:enable Me_Aos

    php magento setup:upgrade 

One more time clear the cache under var/cache and var/page_cache login to Magento backend (admin panel).

## Configuration ##
 
Login to Magento backend (admin panel).  You can find the module configuration here: Stores / Configuration, in the left menu Magevolve Extensions / Products by Quantity Range.

Settings:

** Basic **

Enable Extension: Here you can enable the extension. If this value is no, all the widgets belong to this extension will not be displayed. So it's easy to turn off the displaying of the widgets in one place. 
 
** Widget Options **

Display Block Title: Select yes if you would like to show and customize block title.

The title of the Block: "Products By Quantity Range" by default, if not set.

Display Short Description: Select yes if you would like to show and customize short description(subtitle).

Short Description under Block Title: You can set a short description which will appear under the title if not empty. You can use HTML tags for formatting.

Display Page Control: Select yes if you would like to use the pager.

Number of Products per Page: 5 by default.

Products minimum quantity limit to Display: 1 by default. This value must be greater than zero.

Products maximal quantity limit to Display: 10 by default.

Number of Products to Display: 10 by default.

Available Product Listing Sort By: Please select the default sorting.

Available Direction: Please select the default sorting direction.

Show Configurable Products: Show configurable products which have associated simple products whose current quantity between the defined range.

Show Grouped Products: Show grouped products which have associated simple products whose current quantity between the defined range.

Show Bundle Products: Show bundle products which have associated simple products whose current quantity between the defined range.

Template: Please select the template to use.

Cache Lifetime (Seconds): 86400 by default, if not set. To refresh instantly, clear the Blocks HTML Output cache.

## Change Log ##

Version 1.0.1 - Feb 4, 2018
- Compatibility with Magento 2.1.x
- Compatibility with Magento 2.2.x
- Code Sniffer fixes

Version 1.0.0 - Jan 9, 2017
- Compatibility with Magento 2.0.x
- Compatibility with Magento 2.1.x

## Troubleshooting ##
 
1. After the extension installation I receive a 404 error in Stores / Configuration / Products by Quantity Range. 
Clear the store cache, browser cookies, logout from backend and login back. 
 
2. My configuration changes do not appear on the store frontend.
Clear the store cache, clear your browser cache and domain cookies and refresh the page. 
 
## Extension license ##
 
The module license description included in the Terms and Conditions:
http://magevolve.com/terms-and-conditions  
 
## Support ##
 
If you have any questions about the extension, please contact us.

## License ##

See COPYING.txt for license details.

Copyright © 2016 Magevolve Ltd. All rights reserved.