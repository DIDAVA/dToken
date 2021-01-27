<?php
declare(strict_types = 1);
include './class/dToken.php';
$dToken = new dToken();

// Update Token
$dToken->update( (object) [
  'name' => 'Farshad Javan',
  'company' => 'DIDAVA',
  'email' => 'farshad.javan@gmail.com'
]);
echo 'Token: ' . $dToken . PHP_EOL;

// Pause for 1 second
sleep( 1 );

// Load from string
$token = $dToken->value;
$dToken->fromString( $token );
echo 'Token: ' . $dToken . PHP_EOL;