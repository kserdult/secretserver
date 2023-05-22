<?php
/*
Secret: <input type="text" name="secret" /> <br />
                Expire after X views: <input type="number" name="expview" /> <br />
                Time To Live (min): <input type="number" name="ttl" /> <br />
                <?php
                    echo "<input type='hidden' name='hash' value='$hash'/>"
*/
class SecretPost extends Controller {
    //Handling post request via HTML (using form)
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
}

?>