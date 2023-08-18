<?php

/** @see \Concrete\Core\User\UserInfo::delete **/

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
                'c5_version' => '8.4.0', // https://github.com/concretecms/concretecms/commit/177429e792c471faa8c3225d20431279930c0bd0
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
                'fixed' => true,
                'info' => t('Records are deleted in the %s class.', 'UserInfo'),
                'c5_version' => '8.4.2', // https://github.com/concretecms/concretecms/commit/f948e4a1927bf44215d774bf3b7463688cc08538
            ],
            'ConversationSubscriptions' => [
                'fixed' => true,
                'info' => t('Records are deleted in the %s class.', 'UserInfo'),
                'c5_version' => '8.4.2', // https://github.com/concretecms/concretecms/commit/f948e4a1927bf44215d774bf3b7463688cc08538
            ],
            'DownloadStatistics' => [
                'fixed' => true,
                'info' => t('Records are deleted in the %s class.', 'UserInfo'),
                'c5_version' => '8.4.2', // https://github.com/concretecms/concretecms/commit/f948e4a1927bf44215d774bf3b7463688cc08538
            ],
            'FileSets' => [
                'fixed' => true,
                'info' => t('Records are deleted in the %s class.', 'UserInfo'),
                'c5_version' => '8.4.2', // https://github.com/concretecms/concretecms/commit/f948e4a1927bf44215d774bf3b7463688cc08538
            ],
            'PermissionAccessEntityUsers' => [
                'fixed' => true,
                'info' => t('Records are deleted in the %s class.', 'UserInfo'),
                'c5_version' => '8.4.2', // https://github.com/concretecms/concretecms/commit/f948e4a1927bf44215d774bf3b7463688cc08538
            ],
            'authTypeConcreteCookieMap' => [
                'fixed' => true,
                'info' => t('Records are deleted in the %s class.', 'UserInfo'),
                'c5_version' => '8.4.2', // https://github.com/concretecms/concretecms/commit/f948e4a1927bf44215d774bf3b7463688cc08538
            ],
            'NotificationAlerts' => [
                'fixed' => false,
                'info' => t('This table is not yet handled by version %s of Concrete CMS. You may be able to update your C5 installation to fix this problem. See also %s.',
                    '8.4.0RC4',
                    'https://github.com/concretecms/concretecms/issues/6676'
                )
            ],
        ],
    ],

    // The whitelist contains tables that are actually false positives.
    'whitelist' => [
        // The miUsername and miEmail are used to connect with an SMTP server.
        // Not really personal information per se.
        'MailImporters',

        // The columnsPhone column indicates how many images should be shown
        // on a mobile phone. It doesn't contain a phone number.
        'btSimpleGallery',
    ],
];
