<?php
declare(strict_types = 1);

final class Token {

  public string $value;
  public object $payload;
  public bool $isSignedIn;
  public bool $testMode;

  public function __construct() {
    $this->testMode = PHP_SAPI === 'cli';
    $this->value = '';
    $this->isSignedIn = false;
    $this->checkHeader();
  }

  protected function checkHeader(): void {
    if ( !$this->testMode ) {
      $pattern = '/^(Bearer )[\w\/+-]+={0,2}.[\w\/+-]+={0,2}$/';
      $headers = apache_request_headers();
      if (array_key_exists('Authorization', $headers) && preg_match($pattern, $headers['Authorization']) == 1) {
        $token = str_replace( 'Bearer ', '', $headers['Authorization'] );
        $this->parse( $token );
      }
    }
  }

  public function fromString( string $token ): void {
    $pattern = '/^[\w\/+-]+={0,2}.[\w\/+-]+={0,2}$/';
    if (preg_match($pattern, $token)) $this->parse( $token );
  }

  protected function parse( string $token ): void {
    $token = explode( '.', $token );
    if (strcmp($this->sign($token[0]), $token[1]) == 0) {
      $payload = json_decode( base64_decode( $token[0] ) );
      if (!empty($payload) && !empty($payload->iss) && $payload->iss == 'dToken' && !empty($payload->exp) && $payload->exp >= time()) {
        $this->update( $payload );
      }
    }
  }

  protected function sign( string $payload ): string {
    $secret = '128643cf384ff7aeb47bfac787bc7f7b8f262ffc5d996fe59d32944d7dfde1f9';
    $hash = hash( 'sha256', $payload.$secret, true );
    return base64_encode( $hash );
  }

  public function update( object $payload ): void {
    $now = time();
    $payload->iss = 'dToken';
    $payload->iat = $now;
    $payload->exp = $now + 3600;
    $b64 = base64_encode( json_encode( $payload ) );
    $this->value = $b64 . '.' . $this->sign( $b64 );
    $this->isSignedIn = true;
    if (!$this->testMode) header( 'Authorization: Bearer '.$this->value );
  }

  public function __toString(): string {
    return $this->value;
  }

}