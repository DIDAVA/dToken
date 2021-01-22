<?php

include './class/dToken.php';

$token = new dToken();

$token->setPayload( (object) [
  'email' => 'user@example.com',
  'name' => 'user full name',
  'age' => 30
]);

// TEST Headers
echo '<pre>';

echo 'Token: ' . $token . PHP_EOL;
echo 'Payload: ';
print_r( $token->payload );
echo 'isLogged: ' . ($token->isLogged ? 'Yes' : 'No') . PHP_EOL.PHP_EOL;
echo 'Check the response headers from your browser';

echo '</pre>';

