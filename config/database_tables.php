<?php

/** @see \Concrete\Core\User\UserInfo delete method **/

return [
    'taken_care_of' => [
        'default' => [
            'SpeedAnalyzerReports' => [
                'info' => t('%s listens to the %s event.', 'Speed Analyzer', 'on_user_delete'),
            ],

            'OauthUserMap' => [
                'info' => t('Records are deleted in the %s class.', 'UserInfo'),
            ],

            'Logs' => [
                'info' => t('Records are deleted in the %s class.', 'UserInfo'),
                'c5_version' => '8.4.0', // https://github.com/concrete5/concrete5/commit/177429e792c471faa8c3225d20431279930c0bd0
            ],

            'UserSearchIndexAttributes' => [
                'info' => t('Records are deleted in the %s class.', 'UserInfo'),
            ],

            'UserGroups' => [
                'info' => t('Records are deleted in the %s class.', 'UserInfo'),
            ],

            'UserValidationHashes' => [
                'info' => t('Records are deleted in the %s class.', 'UserInfo'),
            ],

            'Piles' => [
                'info' => t('Records are deleted in the %s class.', 'UserInfo'),
            ],

            'Blocks' => [
                'info' => t('Records are deleted in the %s class.', 'UserInfo'),
            ],

            'Pages' => [
                'info' => t('Records are deleted in the %s class.', 'UserInfo'),
            ],
        ],
    ],
];
