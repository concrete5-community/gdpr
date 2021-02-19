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

            'Users' => [
                'info' => t('Records are deleted in the %s class by deleting the User entity.', 'UserInfo'),
            ],
            'Files' => [
                'info' => t('The uID will be set to NULL when a user is deleted. See the %s class. Use the Orphaned Files tool to reassign or delete those files.', 'File Entity'),
            ],
            'ConfigStore' => [
                'fixed' => false,
                'info' => t('This table is not yet handled by version %s of concrete5. You may be able to update your C5 installation to fix this problem. See also %s.',
                    '8.4.0RC4',
                    'https://github.com/concrete5/concrete5/issues/6676'
                )
            ],
            'ConversationSubscriptions' => [
                'fixed' => false,
                'info' => t('This table is not yet handled by version %s of concrete5. You may be able to update your C5 installation to fix this problem. See also %s.',
                    '8.4.0RC4',
                    'https://github.com/concrete5/concrete5/issues/6676'
                )
            ],
            'DownloadStatistics' => [
                'fixed' => false,
                'info' => t('This table is not yet handled by version %s of concrete5. You may be able to update your C5 installation to fix this problem. See also %s.',
                    '8.4.0RC4',
                    'https://github.com/concrete5/concrete5/issues/6676'
                )
            ],
            'FileSets' => [
                'fixed' => false,
                'info' => t('This table is not yet handled by version %s of concrete5. You may be able to update your C5 installation to fix this problem. See also %s.',
                    '8.4.0RC4',
                    'https://github.com/concrete5/concrete5/issues/6676'
                )
            ],
            'PermissionAccessEntityUsers' => [
                'fixed' => false,
                'info' => t('This table is not yet handled by version %s of concrete5. You may be able to update your C5 installation to fix this problem. See also %s.',
                    '8.4.0RC4',
                    'https://github.com/concrete5/concrete5/issues/6676'
                )
            ],
            'NotificationAlerts' => [
                'fixed' => false,
                'info' => t('This table is not yet handled by version %s of concrete5. You may be able to update your C5 installation to fix this problem. See also %s.',
                    '8.4.0RC4',
                    'https://github.com/concrete5/concrete5/issues/6676'
                )
            ],
            'authTypeConcreteCookieMap' => [
                'fixed' => false,
                'info' => t('This table is not yet handled by version %s of concrete5. You may be able to update your C5 installation to fix this problem. See also %s.',
                    '8.4.0RC4',
                    'https://github.com/concrete5/concrete5/issues/6676'
                )
            ],
        ],
    ],

    // The whitelist contains tables that are actually false positives.
    'whitelist' => [
        // The miUsername and miEmail are used to connect with an SMTP server.
        // Not really personal information per se.
        'MailImporters',
    ],
];
