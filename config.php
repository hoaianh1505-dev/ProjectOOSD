<?php
define('SERVERNAME', 'localhost');
define('USERNAME', 'root');
define('PASSWORD', '');
define('DBNAME', 'the_bloom_studio');

// Load secrets from local file (not committed)
$local = __DIR__ . '/config.local.php';
if (file_exists($local)) {
    require_once $local;
} else {
    // Safe defaults (empty) so repo can be pushed & run without secrets
    define("SMTP_USERNAME", "");
    define("SMTP_SECRET", "");
    define("SMTP_HOST", "smtp.gmail.com");
    define("SHOP_OWNER", "");

    define("GOOGLE_RECAPTCHA_SITE", "");
    define("GOOGLE_RECAPTCHA_SECRET", "");

    define("GOOGLE_CLIENT_ID", "");
    define("GOOGLE_CLIENT_SECRET", "");
}
