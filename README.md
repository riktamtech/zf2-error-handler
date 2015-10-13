Zf2ErrorHandler
===============

Installation
------------
1) Add module to your application
```bash
composer require "riktamtech/zf2-error-handler:dev-master"
```

2) Enable it in your application.config.php file.

```json
<?php
return array(
    'modules' => array(
        // ...
        'Zf2ErrorHandler',
    ),
    // ...
);
```

3) Copy config/zf2-error-handler.local.php.dist file to your application config/autoload folder. And remove .dist from filename.


