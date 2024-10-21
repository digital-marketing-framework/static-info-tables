<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Digital Marketing Framework - Static Info Tables',
    'description' => 'Provides standard value maps from static_info_tables to the Digital Marketing Framework.',
    'category' => 'be',
    'author_email' => 'info@mediatis.de',
    'author_company' => 'Mediatis AG',
    'state' => 'stable',
    'version' => '2.2.0',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-12.4.99',
            'dmf_core' => '2.0.0-2.99.99',
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
];
