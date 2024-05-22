<?php
return array (
  'waf_open' => '0',
  'waf' => 
  array (
    0 => '\\.\\./',
    1 => '\\<\\?',
    2 => '\\s*or\\s+.*=.*',
    3 => 'select([\\s\\S]*?)(from|limit)',
    4 => '(?:(union([\\s\\S]*?)select))',
    5 => 'having|updatexml|extractvalue',
    6 => 'sleep\\((\\s*)(\\d*)(\\s*)\\)',
    7 => 'benchmark\\((.*)\\,(.*)\\)',
    8 => 'base64_decode\\(',
    9 => '(?:from\\W+information_schema\\W)',
    10 => '(?:(?:current_)user|database|schema|connection_id)\\s*\\(',
    11 => '(?:etc\\/\\W*passwd)',
    12 => 'into(\\s+)+(?:dump|out)file\\s*',
    13 => 'group\\s+by.+\\(',
    14 => '(?:define|eval|file_get_contents|include|require|require_once|shell_exec|phpinfo|system|passthru|preg_\\w+|execute|echo|print|print_r|var_dump|(fp)open|alert|showmodaldialog)\\(',
    15 => '(gopher|doc|php|glob|file|phar|zlib|ftp|ldap|dict|ogg|data)\\:\\/',
    16 => '\\$_(GET|post|cookie|files|session|env|phplib|GLOBALS|SERVER)\\[',
    17 => '\\<(iframe|script|body|layer|div|meta|style|base|object|input)',
    18 => '(onmouseover|onerror|onload|onclick)\\=',
    19 => '\\|\\|.*(?:ls|pwd|whoami|ll|ifconfog|ipconfig|&&|chmod|cd|mkdir|rmdir|cp|mv)',
  ),
);
?>