<?php

return [
    'enable' =>  env('XHPROF_ENABLE', false),
    'hit' =>  env('XHPROF_HIT', 100),
    'storage' => env('XHPROF_STORAGE', 'sql'),
    'connection' => env('XHPROF_CONNECTION', 'performance'),
    'expire' => env('XHPROF_EXPIRE', 30),
];
