# Conversify Script Manager

This plugin enables easy integration with Conversify.

## Features

## Installation

### Manual Installation

 * Download the extension
 * Unzip the file
 * Create a folder {Magento 2 root}/app/code/Conversify/ScriptManager
 * Copy the content from the unzip folder

### Using Composer

```
composer config repositories.conversify-scriptmanager git https://github.com/conversify/magento2-scriptmanager
composer require conversify/magento2-scriptmanager
```

### Enable Conversify (from {Magento root} folder)

 * php -f bin/magento module:enable --clear-static-content Conversify_ScriptManager
 * php -f bin/magento setup:upgrade

### Configure

Log into your Magento 2 Admin, then go to Stores -> Configuration -> Conversify -> Conversify Script Manager and enter your api key.

