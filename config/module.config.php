<?php

return array(
    'view_manager' => array(
        'display_not_found_reason' => false,
        'display_exceptions' => false,
        'not_found_template'       => 'error/404',
        'template_map' => array(
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/500'               => __DIR__ . '/../view/error/500.phtml',
        ),
        'template_path_stack' => array(
            'zf2-error-handler' => __DIR__ . '/../view',
        ),
    ),
);
