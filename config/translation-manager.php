<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Routes group config
    |--------------------------------------------------------------------------
    |
    | The default group settings for the elFinder routes.
    |
    */
    'route'          => [
        'prefix'     => 'admin/translations',
        'middleware' => ['web', 'auth'],
    ],

    /**
     * Enable deletion of translations
     *
     * @type boolean
     */
    'delete_enabled' => true,

    /**
     * Exclude specific groups from Laravel Translation Manager.
     * This is useful if, for example, you want to avoid editing the official Laravel language files.
     *
     * @type array
     *
     *    array(
     *        'pagination',
     *        'reminders',
     *        'validation',
     *        'package::main'
     *    )
     */
    'exclude_groups' => [],

    /**
     * Exclude specific vendor packages from Laravel Translation Manager.
     * This is useful if, for example, you want to avoid editing the package language files.
     *
     * @type array
     *
     *  array(
     *      'translator-manager',
     *  )
     */
    'exclude_packages' => array(),

    /**
     * Exclude specific languages from Laravel Translation Manager.
     *
     * @type array
     *
     *    array(
     *        'fr',
     *        'de',
     *    )
     */
    'exclude_langs'  => [],

    /**
     * Export translations with keys output alphabetically.
     */
    'sort_keys '     => false,

    'trans_functions' => [
        'trans',
        'trans_choice',
        'Lang::get',
        'Lang::choice',
        'Lang::trans',
        'Lang::transChoice',
        '@lang',
        '@choice',
        '__',
        '$trans.get',
    ],

    'language_dirs' => [
        'workbench' => [
            'root' => '/Modules/{package}',
            'files' => 'Resources/lang/{locale}/{group}',
            'include' => '*',
            'vars' => [
                '{vendor}' => 'module',
            ],
        ],
    ],

];
