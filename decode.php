<?php



/*
 * OAuthwo Samples - Procedures to digest a received token  
 */

//SERVER SPECIFIC INFORMATION

//This server's specific secret shared with authorization server
$secret = "b497477a761bb3d154ce8612d92caz";
//Certificate location - where this server stores the Authorization server's 
//public key, used to digital sign verification
$cert_location='/var/www/jwtoolkit/lib/pubkey.pem';

//MADE UP INFORMATION

//This is the token the Resource Server receives.
$encrypted_token = "eyJhbGciOiJkaXIiLCJlbmMiOiJBMjU2Q0JDIiwidHlwIjoiSldUIiwiaXYiOiJZOXF2KzVDNnUyZ2tNUzZlT0ltMHV4TzVFU0djcGNpT1BiK1VqUHdoaTJZPSJ9..gTmdrfLY7S6E6FYbckH+jykwzMHoqXb6d+2G6zXYZonR0Cj4JizR+xmzkFsufvJ1aVpHd9kTe8F+54fZF+M+1yRR0JE6wxXH8BPnx5i\/kls3TGhMNnI\/20zuy++MvLJwBEFS+gMu6saIlorvtTOOqfDG6A5G8XYy553OZqbkUOf3DOQ80A\/+Dh8PwjLGM3XTciMH2r\/jUcOuibG+GK6t9ul7OR0Z38tZQqF9WLi3Ga4+POFY\/PLg8vzLvqEFGATCsZa67CKRWxV0oW3vUOvEvtvgV+vnh3JZt7y9w6Lc1Z6gEMWaK9UBw+brMHZva04uYZ0mFUeq+RyJYw\/jHkvPVIhbOTSaqInTd0FtCxg+RYw6TIkk8HT4sdzVN6HvmGjj0ZQoe6f8zRNNARJE\/SmVCi4F\/Ll1zpWHgDgnsmz6rXdr19LsVE6y8d1RJqucr03JOIaOWKkNb2X0zsTlegJrIk8laLdw5oIfzo1B50ccZdzNC06s2mE7aRLzVBV+g2+G";


//PROCEDURES

//We have a JWE
//first of all explode it
$tt = explode(".", $encrypted_token);
//first the header, json decoding it
$ec_header = json_decode(base64_decode($tt[0]), true);
//then, from the header, retrieve the initialization vector(CBC)
$iv = base64_decode($ec_header['iv']);
//retrieve the cyphertext
$ciphertext = base64_decode($tt[2]);
//decyphering it
$signed_token = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $secret, $ciphertext, MCRYPT_MODE_CBC, $iv);

//Now we have a JWS
//explode it
$ss = explode(".", $signed_token);
//Actually, in this specific case, we can ignore the header
//$header = json_decode(base64_decode($ss[0]), true);
//But we have to retrieve the payload
$payload = base64_decode($ss[1]);
//And the signature
$sign = base64_decode($ss[2]);
//now, we retrieve the AS public key
$fp = fopen($cert_location, "r");
$pub_key = fread($fp, 8192);
fclose($fp);
$pubkeyid = openssl_get_publickey($pub_key);
//Now we can verify our signature
$ok = openssl_verify($ss[0] . "." . $ss[1], $sign, $pubkeyid, "sha256");
if ($ok == 1) {
    //quite good, we can print out the token payload!
    echo "good\n";
    echo print_r($payload,true)."\n";
} elseif ($ok == 0) {
    echo "incorrect signature\n";
} else {
    echo "ugly, error checking signature\n";
}
// free the key from memory
openssl_free_key($pubkeyid);



?>


<code>
    

/*
 * OAuthwo Samples - Procedures to digest a received token  
 */

//SERVER SPECIFIC INFORMATION

//This server's specific secret shared with authorization server
$secret = "b497477a761bb3d154ce8612d92caz";
//Certificate location - where this server stores the Authorization server's 
//public key, used to digital sign verification
$cert_location='/var/www/jwtoolkit/lib/pubkey.pem';

//MADE UP INFORMATION

//This is the token the Resource Server receives.
$encrypted_token = "eyJhbGciOiJkaXIiLCJlbmMiOiJBMjU2Q0JDIiwidHlwIjoiSldUIiwiaXYiOiJZOXF2KzVDNnUyZ2tNUzZlT0ltMHV4TzVFU0djcGNpT1BiK1VqUHdoaTJZPSJ9..gTmdrfLY7S6E6FYbckH+jykwzMHoqXb6d+2G6zXYZonR0Cj4JizR+xmzkFsufvJ1aVpHd9kTe8F+54fZF+M+1yRR0JE6wxXH8BPnx5i\/kls3TGhMNnI\/20zuy++MvLJwBEFS+gMu6saIlorvtTOOqfDG6A5G8XYy553OZqbkUOf3DOQ80A\/+Dh8PwjLGM3XTciMH2r\/jUcOuibG+GK6t9ul7OR0Z38tZQqF9WLi3Ga4+POFY\/PLg8vzLvqEFGATCsZa67CKRWxV0oW3vUOvEvtvgV+vnh3JZt7y9w6Lc1Z6gEMWaK9UBw+brMHZva04uYZ0mFUeq+RyJYw\/jHkvPVIhbOTSaqInTd0FtCxg+RYw6TIkk8HT4sdzVN6HvmGjj0ZQoe6f8zRNNARJE\/SmVCi4F\/Ll1zpWHgDgnsmz6rXdr19LsVE6y8d1RJqucr03JOIaOWKkNb2X0zsTlegJrIk8laLdw5oIfzo1B50ccZdzNC06s2mE7aRLzVBV+g2+G";


//PROCEDURES

//We have a JWE
//first of all explode it
$tt = explode(".", $encrypted_token);
//first the header, json decoding it
$ec_header = json_decode(base64_decode($tt[0]), true);
//then, from the header, retrieve the initialization vector(CBC)
$iv = base64_decode($ec_header['iv']);
//retrieve the cyphertext
$ciphertext = base64_decode($tt[2]);
//decyphering it
$signed_token = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $secret, $ciphertext, MCRYPT_MODE_CBC, $iv);

//Now we have a JWS
//explode it
$ss = explode(".", $signed_token);
//Actually, in this specific case, we can ignore the header
//$header = json_decode(base64_decode($ss[0]), true);
//But we have to retrieve the payload
$payload = base64_decode($ss[1]);
//And the signature
$sign = base64_decode($ss[2]);
//now, we retrieve the AS public key
$fp = fopen($cert_location, "r");
$pub_key = fread($fp, 8192);
fclose($fp);
$pubkeyid = openssl_get_publickey($pub_key);
//Now we can verify our signature
$ok = openssl_verify($ss[0] . "." . $ss[1], $sign, $pubkeyid, "sha256");
if ($ok == 1) {
    //quite good, we can print out the token payload!
    echo "good\n";
    echo print_r($payload,true)."\n";
} elseif ($ok == 0) {
    echo "incorrect signature\n";
} else {
    echo "ugly, error checking signature\n";
}
// free the key from memory
openssl_free_key($pubkeyid);

</code>