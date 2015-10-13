Zf2ErrorHandler
===============

Installation
------------
1) Add mdouel to your application
```composer require riktamtech/zf2-error-handler


2) Enable it in your application.config.php file.

<?php
return array(
    'modules' => array(
        // ...
        'Zf2ErrorHandler',
    ),
    // ...
);

3) Copy config/zf2-error-handler.local.php.dist file to your application config/autoload folder. And remove .dist from filename.


