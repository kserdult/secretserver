<?php
    //handling routes
    Route::set('secret', function(){
        if (isset($_SERVER['HTTP_ACCEPT'])) {
            $acceptHeader = $_SERVER['HTTP_ACCEPT'];
            $url = $_SERVER['REQUEST_URI'];
            $hash = explode('/v1/secret/', $url);
            // Check the desired response format
            if (strpos($acceptHeader, 'text/html') !== false) {
                if (count($hash)>1){
                    Secret::CreateView('Secret');
                    Secret::getSecret($hash[1]);
                }
                else {
                    Secret::CreateView('PostSecret');
                }
            } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
                Secret::Response($hash);
            } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
                Secret::POSTReq($hash);
            }
        } else {
            // Accept header not present
            echo 'Accept header not found';
        }
    });
    Route::set('index.php', function(){
        Index::CreateView('Index');
    });    
    Route::set('secretpost', function(){
        SecretPost::CreateView('SecretPost');
        SecretPost::postSecret();
    });
    ?>