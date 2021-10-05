# Customized forms management for Magento 2

Help to create and handle customized forms, show as widgets.

## Installation

1. Execute this command to download the module through composer:<br>
   `composer require crazycat/module-magento2-forms`

2. Execute these commands to enable the modules:<br>
   `php bin/magento module:enable CrazyCat_Base`<br>
   `php bin/magento module:enable CrazyCat_Forms`

3. Execute these commands to update database, recompile and flush cache:<br>
   `php bin/magento setup:upgrade`<br>
   `php bin/magento setup:di:compile`<br>
   `php bin/magento cache:flush`

## How to use

### Create a Form

1. Create a template file under CrazyCat_Forms folder of theme directory, for example:
   `[root]/app/design/frontend/[Vendor]/[theme]/CrazyCat_Forms/templates/forms/inquiry.phtml`<br>

2. Create a form through admin panel: `CONTENT / Forms / Forms > Add New Form`<br>
    - Form Name - Form name to show in form list, post record list and widget management
    - Identifier - Unique key of the form
    - Email Sender - To select a sender from the list of which options are same with store config email identities
    - Recipients - Recipients emails, separated by comma
    - Renderer - To render elements through customize a template or configure elements
    - Template - Template path like `CrazyCat_Forms::forms/inquiry.phtml`
    - Elements - Configure elements

3. Create a `Form` widget through widget management or insert one through WYSIWYG editor to show in storefront

### Form Template

Here is an example of email template:

```php
<?php
use CrazyCat\Forms\Block\Widget\Form;

/** @var Form $block */
$blockId = $block->getJsId();
?>
<form method="post"
      action="<?= $block->escapeUrl($block->getFormAction()) ?>"
      data-mage-init='{"validation": {}}'>
    <?= $block->getHiddenInputHtml(); /* @noEscape */ ?>
    <fieldset class="fieldset">
        <div class="field required">
            <label for="<?= $blockId /* @noEscape */ ?>-firstname" class="label">
                <?= $block->escapeHtml(__('Firstname')) ?>
            </label>
            <div class="control">
                <input id="<?= $blockId /* @noEscape */ ?>-firstname" type="text" name="data[Firstname]"
                       class="input-text required-entry"/>
            </div>
        </div>
        <div class="field required">
            <label for="<?= $blockId /* @noEscape */ ?>-lastname" class="label">
                <?= $block->escapeHtml(__('Lastname')) ?>
            </label>
            <div class="control">
                <input id="<?= $blockId /* @noEscape */ ?>-lastname" type="text" name="data[Lastname]"
                       class="input-text required-entry"/>
            </div>
        </div>
        <div class="field required">
            <label for="<?= $blockId /* @noEscape */ ?>-email" class="label">
                <?= $block->escapeHtml(__('Email')) ?>
            </label>
            <div class="control">
                <input id="<?= $blockId /* @noEscape */ ?>-email" type="text" name="data[Email]"
                       class="input-text required-entry validate-email"/>
            </div>
        </div>
    </fieldset>
    <div class="actions-toolbar">
        <button type="submit" class="action primary">
            <span><?= $block->escapeHtml(__('Submit')) ?></span>
        </button>
    </div>
</form>
```

Several key points need to be noted:

- Form method must be `post`
- Form action must be `$block->escapeUrl($block->getFormAction())`
- Hidden fields are required: `$block->getHiddenInputHtml()`
- Name of post elements must be wrapped with `data[]`

### Form Elements

Using Configure Elements as renderer means that elements in the block will be rendered with **UI components**.

The element management is a drag and drop box, it provides some common types of frontend model and validation to choose.

**Configuration** column of the elements is to add JavaScript settings for related UI component.

### CAPTCHA

Administrator is able to enable the CAPTCHA through switching this setting in admin panel:<br>
`STORES / Settings / Configuration > SECURITY / Google reCAPTCHA Storefront > Storefront / Enable for Customized Forms`

And fill in `Google API Website Key` and `Google API Secret Key` of the related service.
