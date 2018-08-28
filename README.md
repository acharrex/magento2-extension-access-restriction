# Shopigo AccessRestriction extension

Magento 2 "AccessRestriction" extension adds a feature which allow to deny access to some pages of Magento by displaying an 404 error page.

## Installation

### Get the source code

#### Composer method

- Switch to your Magento project root
- Run `composer require shopigo/module-access-restriction=dev-master`

#### Download repository method

- Download file https://github.com/shopgio/module-access-restriction/archive/master.zip
- Switch to your Magento project root
- Create folder `app/code/Shopigo/AccessRestriction`
- Extract zip into path

### Enable extension

- Switch to your Magento project root
- Run `php bin/magento module:enable Shopigo_AccessRestriction` to enable the extension
- Run `php bin/magento setup:upgrade` to make sure that the module is properly registered
- If you are not in developer mode, run `php bin/magento setup:static-content:deploy`

## How to use it

- Log in the Magento back-office
- Go to the menu "Stores > Configuration > General > General > Access Restrictions"
- Configure your rules

Enjoy!