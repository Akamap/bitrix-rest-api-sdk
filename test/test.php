<?php

require_once __DIR__ . '/../src/BitrixRestApi.php';

$b24connection = new Akamap\BitrixRestApi\Connection(
    'portal.loc',
    'webhook',
    'z09bm05g4x4zq2pn',
    1
);

print_r($b24connection->profile());