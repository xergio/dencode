<?php

require_once 'vendor/autoload.php';

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
];

$str = $result = $_GET['str'] ?? '';
$action = $_GET['action'] ?? '';
$help = 'https://www.php.net/manual/en/index.php';

if ($action == 'reset') {
    http_response_code(302);
    header("Location: /");
    die();

} elseif ($action == 'urlencode') {
    $result = rawurlencode($str)?: $str;
    $help = 'https://www.php.net/rawurlencode';

} elseif ($action == 'urldecode') {
    $result = rawurldecode($str)?: $str;
    $help = 'https://www.php.net/rawurldecode';

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
    $result = "Algorithm        Time  Len  Hash\n";
    foreach (hash_algos() as $algo) {
        $t1 = microtime(true);
        $r = hash($algo, $str, false);
        $t2 = (microtime(true) - $t1) * 1000;
        $result .= sprintf("%-15s %.3f  %3d  %s\n", $algo, $t2, strlen($r), $r);
    }

    // password_hash(PASSWORD_DEFAULT)
    $t1 = microtime(true);
    $r = password_hash($str, PASSWORD_DEFAULT);
    $t2 = (microtime(true) - $t1);
    $result .= sprintf("%-15s %.3f  %3d  %s\n", 'password_hash', $t2, strlen($r), $r);

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
        $result = 'Error: '. json_last_error_msg() ."\n\n". $str;
    } else {
        $result = json_encode($jd, JSON_PRETTY_PRINT | JSON_PRESERVE_ZERO_FRACTION);
    }
    $help = 'https://www.php.net/json';

}

?><!doctype html>
<html lang="en">
    <head>
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-70198-12"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag() { dataLayer.push(arguments) }
            gtag('js', new Date());
            gtag('config', 'UA-70198-12');
        </script>

        <meta charset="utf-8">
        <meta name="description" content="Encode and Decode strings from/to URL, Base64, HTML entities, UU, Quprint, hash, hex and more." />

        <title>Encode/Decode everything</title>
        <meta name="description" content="Encode/Decode everything, base64, url, html, charset">
        <meta name="author" content="Sergio Álvarez <xergio@gmail.com>">

        <!-- <link rel="stylesheet" href="https://unpkg.com/normalize.css@8.0.1/normalize.css"> -->
        <link rel="stylesheet" href="https://unpkg.com/spectre.css@0.5.8/dist/spectre.min.css">
        <style type="text/css">
            html, body { height: 100%; } 
            body { margin: 0; } 
            textarea.form-input { width: 100%; height: 80vh; font-family: monospace; } 
            .container { padding-top: .4rem; padding-bottom: .4rem; }
        </style>
    </head>

    <body class="bg-gray">
        <form method="get">
            <div class="container">
                <?php foreach ($buttons as $act => $label) { ?> 
                <button type="submit" name="action" value="<?=$act?>" class="btn<?=($action == $act? ' btn-primary': '')?>"><?=$label?></button>
                <?php } ?>
                <a href="/" class="btn btn-link btn-error">Reset</button>
                <a href="<?=$help?>" class="btn btn-link">Help</a>
            </div>

            <div class="container">
                <textarea name="str" class="form-input" placeholder="Paste a string here"><?=htmlentities($result, ENT_QUOTES | ENT_HTML5 | ENT_IGNORE)?></textarea>
            </div>
        </form>

        <div class="container text-right">
            <small>Contact me via <a href="mailto:correo@sergio.am">email</a> or <a href="https://twitter.com/xergio">twitter</a>. Made with pure <a href="https://php.net/">PHP</a> and <a href="https://picturepan2.github.io/spectre/">Spectre</a>. <a href="https://sergio.am/code/dencode.xrg.es">Code</a>.</small>
        </div>
    </body>
</html>
