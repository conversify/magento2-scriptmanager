# Conversify Script Manager

This plugin enables easy integration with [Conversify](https://www.conversify.com).

## Features

## 1 Installation

### a) Manual Installation

 * Download the extension
 * Create a folder {Magento 2 root}/app/code/Conversify/ScriptManager
 * Extract the extension in the newly created folder

### b) Using Composer
Register the repository with composer:
```bash
composer config repositories.conversify-scriptmanager git https://github.com/conversify/magento2-scriptmanager
composer require conversify/magento2-scriptmanager
```

## 2 Enable Conversify
Run the following in the Magento root folder:
```bash
php -f bin/magento module:enable --clear-static-content Conversify_ScriptManager
php -f bin/magento setup:upgrade
```

## 3 Configure

Log into your Magento 2 Admin, then go to Stores -> Configuration -> Conversify -> Conversify Script Manager and enter your API key. An API key can be obtained by creating an account at [Conversify](https://www.conversify.com).
