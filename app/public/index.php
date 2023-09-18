<?php

declare(strict_types=1);

$start_time = microtime(true);

require_once '../vendor/autoload.php';

// action => label
$buttons = [
    'urlencode' => 'URL Encode',
    'urldecode' => 'URL Decode',
    'b64encode' => 'Base64 Encode',
    'b64decode' => 'Base64 Decode',
    'htmlencode' => 'HTML Encode',
    'htmldecode' => 'HTML Decode',
    'uuencode' => 'UU Encode',
    'uudecode' => 'UU Decode',
    'qprintencode' => 'Qprint Encode',
    'qprintdecode' => 'Qprint Decode',
    'hash' => 'Hash',
    'hex' => 'Hex',
    'prettyjson' => 'Pretty JSON',
    'prettyxml' => 'Pretty XML',
    'serializedtojson' => 'PHP Serialized to JSON',
    'openssl_genrsa' => 'OpenSSL genrsa',
];

$str = $result = $_POST['str'] ?? $_GET['str'] ?? '';
$action = $_POST['action'] ?? $_GET['action'] ?? '';
$plain = str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'text/plain');
$help = 'https://www.php.net/manual/';
$jsonEncodeOptions = JSON_PRETTY_PRINT | JSON_PRESERVE_ZERO_FRACTION | JSON_UNESCAPED_SLASHES;

if ($action == 'reset') {
    http_response_code(302);
    header("Location: /");
    exit;

} elseif ($action == 'urlencode') {
    $result = urlencode($str)?: $str;
    $help = 'https://www.php.net/urlencode';

} elseif ($action == 'urldecode') {
    $result = urldecode($str)?: $str;
    $help = 'https://www.php.net/urldecode';

} elseif ($action == 'b64encode') {
    $result = base64_encode($str)?: $str;
    $help = 'https://www.php.net/base64_encode';

} elseif ($action == 'b64decode') {
    $result = base64_decode($str)?: $str;
    $help = 'https://www.php.net/base64_decode';

} elseif ($action == 'htmlencode') {
    $result = htmlentities($str, ENT_QUOTES | ENT_HTML5)?: $str;
    $help = 'https://www.php.net/htmlentities';

} elseif ($action == 'htmldecode') {
    $result = html_entity_decode($str, ENT_QUOTES | ENT_HTML5)?: $str;
    $help = 'https://www.php.net/html_entity_decode';

} elseif ($action == 'uuencode') {
    $result = convert_uuencode($str)?: $str;
    $help = 'https://www.php.net/convert_uuencode';

} elseif ($action == 'uudecode') {
    $result = convert_uudecode($str)?: $str;
    $help = 'https://www.php.net/convert_uudecode';

} elseif ($action == 'hash') {
    $result = "Algorithm        Time  Len  Hash\n\nhash()\n";
    foreach (hash_algos() as $algo) {
        $t1 = microtime(true);
        $r = hash($algo, $str, false);
        $t2 = (microtime(true) - $t1) * 1000;
        $result .= sprintf("%-15s %.3f  %3d  %s\n", $algo, $t2, strlen($r), $r);
    }

    $result .= "\npassword_hash()\n";
    foreach (password_algos() as $algo) {
        $t1 = microtime(true);
        $r = password_hash($str, $algo);
        $t2 = (microtime(true) - $t1);
        $result .= sprintf("%-15s %.3f  %3d  %s\n", $algo, $t2, strlen($r), $r);
    }

    $result = trim($result);
    $help = 'https://www.php.net/hash';

} elseif ($action == 'hex') {
    $dumper = new Clue\Hexdump\Hexdump();
    $result = $dumper->dump($str);
    $help = 'https://github.com/clue/php-hexdump';

} elseif ($action == 'qprintencode') {
    $result = quoted_printable_encode($str)?: $str;
    $help = 'https://www.php.net/quoted_printable_encode';

} elseif ($action == 'qprintdecode') {
    $result = quoted_printable_decode($str)?: $str;
    $help = 'https://www.php.net/quoted_printable_decode';

} elseif ($action == 'prettyjson') {
    $jd = json_decode($str);
    if (JSON_ERROR_NONE !== json_last_error()) {
        $result = 'Input text couldn\'t be decoded: '."\n\n".json_last_error_msg();
    } else {
        $result = json_encode($jd, $jsonEncodeOptions);
    }
    $help = 'https://www.php.net/json';

} elseif ($action == 'prettyxml') {
    $dom = new DOMDocument('1.0');
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    @$dom->loadXML($str);

    $result = @$dom->saveXML();
    $help = 'https://www.php.net/DOMDocument';

} elseif ($action == 'serializedtojson') {
    $stdClass = preg_replace('~O:[0-9]+:"[^"]+"~', 'O:8:"stdClass"', $str);
    $unserialized = unserialize($stdClass);
    if ($unserialized === false) {
        $result = 'Input text couldn\'t be unserialized: '."\n\n".(error_get_last()['message'] ?? 'Unknown error.');
    } else {
        $result = json_encode($unserialized, $jsonEncodeOptions);
    }
    $help = 'https://www.php.net/serialize';

} elseif ($action == 'openssl_genrsa') {
    $passphrase = strlen($str) > 0 ? hash('sha256', $str) : bin2hex(openssl_random_pseudo_bytes(32));
    $options = ['private_key_bits' => 2048, 'encrypt_key' => true, 'encrypt_key_cipher' => OPENSSL_CIPHER_AES_128_CBC];
    $key = openssl_pkey_new($options);
    openssl_pkey_export($key, $pkey, $passphrase);

    $result = "Passphrase: " . $passphrase . "\n\n" . $pkey . "\n" . openssl_pkey_get_details($key)['key'];

    $help = 'https://www.php.net/openssl_pkey_new';

} else {
    $str = '';
    $action = '';
    $help = 'https://www.php.net/';
}

if ($plain) {
    header('Content-Type: text/plain;charset=UTF-8');
    die($result);
}

?><!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Decode/Encode/Hash/Pretty/etc</title>
        <meta name="author" content="Sergio Álvarez <correo@sergio.am>">
        <meta name="description" content="Encode and Decode strings from/to URL, Base64, HTML entities, UU, Qprint, hash, hex and more." />

        <link rel="apple-touch-icon" sizes="180x180" href="/img/favicon.io/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/img/favicon.io/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/img/favicon.io/favicon-16x16.png">
        <link rel="manifest" href="/img/favicon.io/site.webmanifest">

        <link rel="stylesheet" href="/css/pico.min.css">
        <link rel="stylesheet" href="/css/custom.css">
    </head>

    <body class="container-fluid">
        <form method="get" id="form" action="/">
            <main class="layout">
                <div>
                    <?php foreach ($buttons as $act => $label) { ?> 
                    <button type="submit" name="action" value="<?=$act?>" class="<?=($action == $act? ' contrast': '')?>"><?=$label?></button>
                    <?php } ?>
                    <a href="/" role="button" class="secondary">Reset</a>
                    <a href="<?=$help?>" role="button" class="secondary">Help</a>
                    <hr />
                    <p><small><a href="mailto:dencode@sergio.am">contact</a> · <a href="https://sergio.am/code/dencode.xrg.es">source code</a> · <?php printf("%.6f", (microtime(true) - $start_time)); ?>s</small></p>
                </div>

                <div>
                    <textarea name="str" id="str" class="form-input" placeholder="Paste a string here"><?=htmlentities((string)$result, ENT_QUOTES | ENT_HTML5 | ENT_IGNORE)?></textarea>
                </div>
            </main>
        </form>

        <script src="/js/form.js"></script>
    </body>
</html>
