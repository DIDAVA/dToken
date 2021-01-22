<?php

class dToken {
  private string $secret;
  public string $token;
  public object $payload;
  public bool $isLogged;

  public function __construct() {
    $this->isLogged = false;
    $this->secret = '128643cf384ff7aeb47bfac787bc7f7b8f262ffc5d996fe59d32944d7dfde1f9';
    $pattern = '/^(Bearer )[\w\/+-]+={0,2}.[\w\/+-]+={0,2}$/';
    $headers = apache_request_headers();
    if (!empty($headers) && array_key_exists('Authorization', $headers) && preg_match($pattern, $headers['Authorization']) == 1) {
      $this->token = str_replace( 'Bearer ', '', $headers['Authorization'] );
      $t = explode( '.', $this->token );
      $signature = base64_decode( $t[1] );
      $hash = hash( 'sha256', $t[0].$this->secret, true );
      if (strcmp($signature, $hash) == 0) {
        $this->payload = json_decode( base64_decode( $t[0] ) );
        if (!empty($this->payload) && !empty($this->payload->iss) && $this->payload->iss == 'dToken' && !empty($this->payload->exp)) {
          if ( $this->payload->exp >= time() ) $this->update();
        }
      }
    }
  }

  private function update() {
    $now = time();
    $this->payload->iss = 'dToken';
    $this->payload->iat = $now;
    $this->payload->exp = $now + 3600;
    $payload = base64_encode( json_encode( $this->payload ) );
    $signature = base64_encode( hash( 'sha256', $payload.$this->secret, true ) );
    $this->token = $payload . '.' . $signature;
    header( 'Authorization: Bearer '.$this->token );
    $this->isLogged = true;
  }

  public function setPayload( object $payload ) {
    $this->payload = $payload;
    $this->update();
  }
}