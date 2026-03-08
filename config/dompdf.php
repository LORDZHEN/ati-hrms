<?php

/**
 * config/dompdf.php
 *
 * Published via: php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"
 *
 * Key settings for ATI-HRMS on Windows/XAMPP:
 *  - isHtml5ParserEnabled  → correctly parses data URI src attributes in <img>
 *  - isRemoteEnabled       → allows base64 data URIs to be processed
 *  - isLocalFileEnabled    → allows file:/// URIs if needed
 *  - enable_php            → keep false for security
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Paper size and orientation
    |--------------------------------------------------------------------------
    */
    'default_paper_size'        => 'a4',
    'default_paper_orientation' => 'portrait',

    /*
    |--------------------------------------------------------------------------
    | Default font
    |--------------------------------------------------------------------------
    */
    'default_font'              => 'Arial',

    /*
    |--------------------------------------------------------------------------
    | DPI for PDF rendering
    |--------------------------------------------------------------------------
    */
    'dpi'                       => 96,

    /*
    |--------------------------------------------------------------------------
    | Font directory — where DomPDF stores/finds embedded fonts
    |--------------------------------------------------------------------------
    */
    'font_dir'                  => storage_path('fonts/'),
    'font_cache'                => storage_path('fonts/'),

    /*
    |--------------------------------------------------------------------------
    | Temporary directory for DomPDF image cache
    |--------------------------------------------------------------------------
    */
    'temp_dir'                  => sys_get_temp_dir(),

    /*
    |--------------------------------------------------------------------------
    | Allow remote URLs (required for data: URIs to work correctly)
    |--------------------------------------------------------------------------
    */
    'isRemoteEnabled'           => true,

    /*
    |--------------------------------------------------------------------------
    | Allow local file:/// URIs
    |--------------------------------------------------------------------------
    */
    'isLocalFileEnabled'        => true,

    /*
    |--------------------------------------------------------------------------
    | HTML5 parser — REQUIRED for data URI src attributes in <img> tags
    | to be correctly recognised and embedded in the PDF.
    | Without this, DomPDF uses its legacy parser which misreads data URIs.
    |--------------------------------------------------------------------------
    */
    'isHtml5ParserEnabled'      => true,

    /*
    |--------------------------------------------------------------------------
    | PHP execution inside Blade — keep false for security
    |--------------------------------------------------------------------------
    */
    'enable_php'                => false,

    /*
    |--------------------------------------------------------------------------
    | Chroot — restrict DomPDF file access to the public directory
    |--------------------------------------------------------------------------
    */
    'chroot'                    => null, // set per-call via setOptions(['chroot' => public_path()])

    /*
    |--------------------------------------------------------------------------
    | Logging verbosity (0 = silent, useful in production)
    |--------------------------------------------------------------------------
    */
    'log_output_file'           => null,

    /*
    |--------------------------------------------------------------------------
    | IMPORTANT — do not change these unless you know what you're doing
    |--------------------------------------------------------------------------
    */
    'pdfBackend'                => 'CPDF',
    'pdflibLicense'             => '',
    'admin_username'            => 'user',
    'admin_password'            => 'password',

    'options' => [
        'font_dir'              => storage_path('fonts/'),
        'font_cache'            => storage_path('fonts/'),
        'temp_dir'              => sys_get_temp_dir(),
        'chroot'                => realpath(base_path()),
        'allowed_remote_hosts'  => null,
        'log_output_file'       => null,
        'isPhpEnabled'          => false,
        'isRemoteEnabled'       => true,
        'isLocalFileEnabled'    => true,
        'isHtml5ParserEnabled'  => true,  // ← CRITICAL for data URI images
        'isAutoProtocol'        => true,
        'isFontSubsettingEnabled' => true,
        'defaultMediaType'      => 'screen',
        'defaultPaperSize'      => 'a4',
        'defaultPaperOrientation' => 'portrait',
        'defaultFont'           => 'Arial',
        'dpi'                   => 96,
        'fontHeightRatio'       => 1.1,
        'isJavascriptEnabled'   => false,
        'enable_php'            => false,
    ],

];
