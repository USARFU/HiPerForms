<?php

/**
 * @file
 * RESTfm configuration file.
 * 4.0.9/20170106-2095449
 */

$config = array();

$config['settings'] = array (
    // If we are located in a subdirectory off the web root, set this.
    // Must also be configured in .htaccess if using Apache web server.
    // No trailing slash!
    'baseURI' => '/RESTfm',

    // List of formats we will converse in.
    // Comment out unneeded formats.
    'formats' => array (
        //'example',  // Commented out format example.
        'dict',     // RESTfmSync transfer format.
        'fmpxml',   // FileMaker FMPXMLRESULT Grammar compliant.
        'html',     // Handy for testing, not so useful in production.
        'json',     // JavaScript Object Notation.
        'simple',   // A simple to parse format, spec. in simple_export.xslt
        'txt',      // Handy for testing, not so useful in production.
        'xml',      // Extensible Markup Language.
    ),

    // Render formats nicely.
    // Improves readability of the native json and xml formats at the expense
    // of increased data size. Increased processing overhead for json with
    // PHP < 5.4.0. Not recommended in production.
    'formatNicely'  => TRUE,

    // Enforce SSL access for clients connecting to RESTfm.
    // Should also be configured in .htaccess if using Apache web server.
    // Should also be configured in web.config if using IIS web server.
    // Do not set this TRUE until after the report page (report.php) states
    // that RESTfm is fully functional when accessed via http and https.
    'SSLOnly' => TRUE,

    // Enforce strict SSL certificate checking when RESTfm is connecting to
    // the FileMaker Server Web Publishing Engine back-end. This setting is
    // relevant only when the database hostspec is using https.
    // Check http://www.restfm.com/restfm-manual/install/ssl-troubleshooting
    // for further details.
    'strictSSLCertsFMS' => TRUE,

    // Enforce strict SSL certificate checking for RESTfm connecting to
    // itself when executing the diagnostics report page (report.php). This
    // setting is used only in determining if clients are able to connect to
    // the RESTfm front-end.
    // Check http://www.restfm.com/restfm-manual/install/ssl-troubleshooting
    // for further details.
    'strictSSLCertsReport' => TRUE,

    // Respond 403 Forbidden on 401 Unauthorized.
    // Makes browser side applications run nicer when HTTP basic authentication
    // fails. Stops the browser popping up a Username/Password dialogue,
    // allowing the developer to handle usernames and passwords in JavaScript.
    // Note: Setting is ignored for html and txt formats.
    'forbiddenOnUnauthorized' => TRUE,

    // Dump raw received data, parsed received data, and response data to a
    // generated subdirectory (restfmdump.xxxxxxx) of the php.ini configured
    // temporary directory. WARNING: This is a verbose diagnostic aid, it will
    // generate a new subdirectory for every single HTTP request!
    'dumpData' => FALSE,

    // Diagnostic reporting.
    // This is enabled by default to assist in initial configuration.
    // Should be disabled once deployed to improve performance, and prevent
    // leakage of privileged information.
    'diagnostics' => FALSE,
);

$config['database'] = array (
    // FileMaker Server HTTP URL.
    // If server is localhost, hostspec should be http://127.0.0.1
    // not http://localhost for speed reasons according to
    // FileMaker/conf/filemaker-api.php
    // It is not necessary to use https with 127.0.0.1
    //'hostspec' => 'http://example.com',
    //'hostspec' => 'https://example.com',
    //'hostspec' => 'http://example.com:8081',
    'hostspec' => 'https://hiperrugby.org',

    // Default username and password if none supplied in query or no API key
    // supplied. May be empty string for "guest" access.
    // Only applies if useDefaultAuthentication is TRUE.
    'useDefaultAuthentication' => FALSE,
    'defaultUsername' => 'exampleuser',
    'defaultPassword' => 'examplepass',
);

/*
 * EXPERIMENTAL:
 * Optional list of database names that map to DSNs as supported by PHP's PDO
 * interface drivers.
 * http://php.net/manual/en/pdo.drivers.php
 */
$config['databasePDOMap'] = array(
    // 'example1' => 'mysql:host=127.0.0.1;dbname=testdb',
    // 'example2' => 'sqlite:/export/databases/testdb.sqlite3',
);

/*
 * List of API keys associated with a username and password.
 */
$config['keys'] = array (
    //'EXAMPLEKEY' => array ('exampleuser', 'examplepass'),
    'X8mjwH1Q7O3F2ErA' => array ('CMS', 'oamoawo76_5aulx2'),
);

/*
 * List of allowed origins for cross-site HTTP requests.
 * https://developer.mozilla.org/en-US/docs/HTTP_access_control
 *
 * It is not necessary to set these for most installations. Only web
 * applications being served from a different domain to RESTfm will need
 * this.
 */
$config['allowed_origins'] = array (
    // 'http://example.com',    // Example origin domain.
    // '*',                     // An origin of '*' (wildcard) will match
                                // all domains. WARNING: This is probably not
                                // what you want.
);
