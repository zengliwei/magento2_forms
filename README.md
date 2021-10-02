# Customized forms management for Magento 2

Help to create and handle customized forms, show as widgets.

## Installation

1. Execute this command to download the module through composer:<br>
   `composer require CrazyCat_Forms`

2. Execute these commands to enable the modules:<br>
   `php bin/magento module:enable CrazyCat_Base`<br>
   `php bin/magento module:enable CrazyCat_Forms`

3. Execute these commands to update database, recompile and flush cache:<br>
   `php bin/magento setup:upgrade`<br>
   `php bin/magento setup:di:compile`<br>
   `php bin/magento cache:flush`

## How to use

1. Create a template file under CrazyCat_Forms folder of theme directory, for example:
   `[root]/app/design/frontend/[Vendor]/[theme]/CrazyCat_Forms/templates/forms/inquiry.phtml`<br>

2. Create a form through admin panel: `CONTENT / Forms / Forms > Add New Form`<br>
   - Name - Form name to show in form list, post record list and widget management
   - Identifier - Unique key of the form
   - Template - Template path like `CrazyCat_Forms::forms/inquiry.phtml`

3. Create a widget through widget management or insert one through WYSIWYG editor
