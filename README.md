phpGsis 
==============

A collection of classes for painless consuming of various GSIS SOAP services.

`\Gsis\VatDetails` for requesting VAT information    
`\Gsis\a39` for getting the compliance status for Article 39a (Άρθρο 39α)

Installation
------------
- Minumum PHP 7.2
- Requires the [SOAP extension](https://www.php.net/manual/en/book.soap.php) to be already installed
- Ιnstall [Composer](http://getcomposer.org/).
- Then install using the following command:
    ```sh
    composer require marios88/php-gsis
    ```
        
Configuration
-------------

You are required register with Gsis before using most of their services.  
***Note that 39a and Vat details require different credentials*** ( [how to create them (Greek)](https://www.aade.gr/sites/default/files/2018-07/eidikoi_kwdikoi_FAQs.pdf) )

Examples
---
```php
<?php

use Gsis\VatDetails;
use Gsis\a39;

require __DIR__.'/vendor/autoload.php';

$gsis = new a39('myusername1','mypassword1','myafm1');
var_dump($gsis->fetchBasic('otherafm1'));

$gsis = new VatDetails('myusername2','mypassword2','myafm1');
var_dump($gsis->fetchBasic('otherafm2'));
```

Release History
---
* 0.1.0  
  * Initial Release

Notes
---
This was insipired by the work of [dspinellis/greek-vat-data](https://github.com/dspinellis/greek-vat-data)  
[Gsis 39a Service](https://www.aade.gr/epiheiriseis/forologikes-ypiresies/fpa/yperesia-arthroy-39a)  
[Gsis 39a Service Developer Manual (in greek)](https://www.aade.gr/dl_assets/39afpa/developer_guide_aade39afpaV1.0.pdf)  

Licence
---
MIT