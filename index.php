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

print_r([
  'token' => $token->token ?? null,
  'Payload' => $token->payload ?? null,
  'isLogged' => $token->isLogged ? 'Yes' : 'No',
  'Headers' => 'Check the response headers from your browser'
]);

echo '</pre>';

