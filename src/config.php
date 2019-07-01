<?php
return [
    'domain' => env('QINIU_DOMAIN', ""),
    'callback' => env('QINIU_CALLBACK', ''),
    'callback_host' => env('QINIU_CALLBACK_HOST', ''),
    'access_key' => env('QINIU_ACCESS_KEY', ''),
    'secret_key' => env('QINIU_SECRET_KEY', ''),
    'bucket' => env('QINIU_BUCKET', ''),
    'insert_only' => env('QINIU_INSERT_ONLY', 1),
    'file_type' => env('QINIU_FILE_TYPE', 1),
];