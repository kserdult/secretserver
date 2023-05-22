<?php class Secret extends Controller {
    //General post request
    public static function postSecret() {
        try{
            if (self::getQuery('SELECT count(hash) FROM `secret` WHERE HASH= "'.$_POST['hash'].'";')[0][0]<1){
                self::getQuery('INSERT INTO `secret` (`id`, `secret`, `expireAfterViews`, `expireAfter`, `createdAt`, `hash`) VALUES (NULL,"'.$_POST['secret'].'", "'.$_POST['expview'].'", "'.$_POST['ttl'].'", current_timestamp(), "'.$_POST['hash'].'");');
                echo 'Secret successfuly uploaded! with the following hash: "'.$_POST['hash'].'"';
            }
            else {
                echo "Secret is already added with this hash, please reload the previous page and try again!";
            }
        }catch (PDOException $e){
            echo $e->getMessage();
        }
        return true;
    }
    //Get a secret for a given hash
    public static function getSecret($hash) {
        if ($hash=="") self::getRandomSecret();
        $data = self::getQuery('SELECT secret FROM secret WHERE hash="'. $hash .'" LIMIT 1');
        if($data){
            self::Delete();
            self::setExpAfterViews($hash);
            echo $data[0][0];
            return $data[0][0];
        }
        else {
            if ($hash=="") {
                echo "There are no secrets";
                return 0;
            }
            echo "There is no secret with this hash!";
            return "There is no secret with this hash!";
        }
    }

    //Requesting a random hash
    private static function getRandomSecret(){
        $hash = self::getQuery("SELECT hash FROM secret order by RAND() LIMIT 1");
        if ($hash) header('Location:/v1/secret/'.$hash[0][0].'');
    }

    //Handling GET requests from non-html requests
    public static function Response($hash){
        $acceptHeader = $_SERVER['HTTP_ACCEPT'];
        self::Delete();
        self::setExpAfterViews($hash[1]);
        $data = self::getQuery('SELECT secret, createdAt, createdAt + INTERVAL expireAfter MINUTE, expireAfterViews FROM secret WHERE hash="'. $hash[1] .'" LIMIT 1');
        //Checking if header is XML
        if (strpos($acceptHeader, 'application/xml') !== false || strpos($acceptHeader, 'text/xml') !== false) {
            if ($data) {
                // Create a new DOMDocument
                $xmlDoc = new DOMDocument('1.0', 'UTF-8');
            
                // Create the root element
                $root = $xmlDoc->createElement('Secret');
                $xmlDoc->appendChild($root);
            
                // Add hash element
                $hash2 = $xmlDoc->createElement('hash');
                $hash2->appendChild($xmlDoc->createTextNode($hash[1]));
                $root->appendChild($hash2);
            
                // Add secretText element
                $secretText = $xmlDoc->createElement('secretText');
                $secretText->appendChild($xmlDoc->createTextNode($data[0][0]));
                $root->appendChild($secretText);
            
                // Add createdAt element
                $createdAt = $xmlDoc->createElement('createdAt');
                $createdAt->appendChild($xmlDoc->createTextNode($data[0][1]));
                $root->appendChild($createdAt);
            
                // Add expiresAt element
                $expiresAt = $xmlDoc->createElement('expiresAt');
                $expiresAt->appendChild($xmlDoc->createTextNode($data[0][2]));
                $root->appendChild($expiresAt);
            
                // Add remainingViews element
                $remainingViews = $xmlDoc->createElement('remainingViews');
                $remainingViews->appendChild($xmlDoc->createTextNode($data[0][3]));
                $root->appendChild($remainingViews);
            
                // Set the content type header to specify XML response
                header('Content-Type: application/xml');
            
                // Output the XML response
                echo $xmlDoc->saveXML();
            } else {
                // Create a new DOMDocument
                $xmlDoc = new DOMDocument('1.0', 'UTF-8');
            
                // Create the root element
                $root = $xmlDoc->createElement('response');
                $xmlDoc->appendChild($root);
            
                // Add error message element
                $error = $xmlDoc->createElement('error');
                $error->appendChild($xmlDoc->createTextNode('Secret not found'));
                $root->appendChild($error);
            
                // Set the content type header to specify XML response
                header('Content-Type: application/xml');
            
                // Output the XML response
                echo $xmlDoc->saveXML();
            }
        }
        //Checking if header is JSON
        else if (strpos($acceptHeader, 'application/json') !==false) {
            if($data){
                $responseData = array(
                    "hash"=> $hash[1],
                    "secretText"=> $data[0][0],
                    "createdAt"=> $data[0][1],
                    "expiresAt"=> $data[0][2],
                    "remainingViews"=> $data[0][3]
                );

                // Convert the array to JSON
                $jsonResponse = json_encode($responseData);

                // Set the content type header to specify JSON response
                header('Content-Type: application/json');

                // Output the JSON response
                echo $jsonResponse;
            }
            else {
                http_response_code(404);
                header('Content-Type: application/json');

                // Output the JSON response
                echo "Secret not found";
            }
        }
    }
    //Handling POST requests from non-html requests
    public static function POSTReq(){
        $acceptHeader = $_SERVER['HTTP_ACCEPT'];
        function rndString(){
            return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(10/strlen($x)) )),1, 10);
        }
        //Checking if request is XML
        if (strpos($acceptHeader, 'application/xml') !== false || strpos($acceptHeader, 'text/xml') !== false) {
            if ($_POST['secret']&&$_POST['expireAfterViews']&&$_POST['expireAfterViews']>0&&$_POST['expireAfter']>=0) {
                $hash= rndString();
                self::getQuery('INSERT INTO `secret` (`id`, `secret`, `expireAfterViews`, `expireAfter`, `createdAt`, `hash`) VALUES (NULL,"'.$_POST['secret'].'", "'.$_POST['expireAfterViews'].'", "'.$_POST['expireAfter'].'", current_timestamp(), "'.$hash.'");');
                $data = self::getQuery('SELECT createdAt, createdAt + INTERVAL expireAfter MINUTE FROM secret WHERE hash="'. $hash .'" LIMIT 1');
                // Create a new DOMDocument
                $xmlDoc = new DOMDocument('1.0', 'UTF-8');
            
                // Create the root element
                $root = $xmlDoc->createElement('Secret');
                $xmlDoc->appendChild($root);
            
                // Add hash element
                $hash2 = $xmlDoc->createElement('hash');
                $hash2->appendChild($xmlDoc->createTextNode($hash));
                $root->appendChild($hash2);
            
                // Add secretText element
                $secretText = $xmlDoc->createElement('secretText');
                $secretText->appendChild($xmlDoc->createTextNode($_POST['secret']));
                $root->appendChild($secretText);
            
                // Add createdAt element
                $createdAt = $xmlDoc->createElement('createdAt');
                $createdAt->appendChild($xmlDoc->createTextNode($data[0][0]));
                $root->appendChild($createdAt);
            
                // Add expiresAt element
                $expiresAt = $xmlDoc->createElement('expiresAt');
                $expiresAt->appendChild($xmlDoc->createTextNode($data[0][1]));
                $root->appendChild($expiresAt);
            
                // Add remainingViews element
                $remainingViews = $xmlDoc->createElement('remainingViews');
                $remainingViews->appendChild($xmlDoc->createTextNode($_POST['expireAfterViews']));
                $root->appendChild($remainingViews);
            
                // Set the content type header to specify XML response
                header('Content-Type: application/xml');
            
                // Output the XML response
                echo $xmlDoc->saveXML();
            } else {
                // Create a new DOMDocument
                $xmlDoc = new DOMDocument('1.0', 'UTF-8');
            
                // Create the root element
                $root = $xmlDoc->createElement('response');
                $xmlDoc->appendChild($root);
            
                // Add error message element
                $error = $xmlDoc->createElement('error');
                $error->appendChild($xmlDoc->createTextNode('Secret not found'));
                $root->appendChild($error);
            
                // Set the content type header to specify XML response
                header('Content-Type: application/xml');
            
                // Output the XML response
                echo $xmlDoc->saveXML();
            }
        }
        //Checking if request is JSON
        else if (strpos($acceptHeader, 'application/json') !==false) {
            if ($_POST['secret']&&$_POST['expireAfterViews']&&$_POST['expireAfterViews']>0&&$_POST['expireAfter']>=0) {
                $hash= rndString();
                self::getQuery('INSERT INTO `secret` (`id`, `secret`, `expireAfterViews`, `expireAfter`, `createdAt`, `hash`) VALUES (NULL,"'.$_POST['secret'].'", "'.$_POST['expireAfterViews'].'", "'.$_POST['expireAfter'].'", current_timestamp(), "'.$hash.'");');
                $data = self::getQuery('SELECT createdAt, createdAt + INTERVAL expireAfter MINUTE FROM secret WHERE hash="'. $hash .'" LIMIT 1');
                $responseData = array(
                    "hash"=> $hash,
                    "secretText"=> $_POST['secret'],
                    "createdAt"=> $data[0][0],
                    "expiresAt"=> $data[0][1],
                    "remainingViews"=> $_POST['expireAfterViews']
                );
                // Convert the array to JSON
                $jsonResponse = json_encode($responseData);
                // Set the content type header to specify JSON response
                header('Content-Type: application/json');
                // Output the JSON response
                echo $jsonResponse;
            }
            else {
                http_response_code(405);
                header('Content-Type: application/json');
                // Output the JSON response
                echo "Invalid input";
            }
        }      
    }
} ?>