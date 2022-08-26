<?php

namespace App\TokenStore;
use League\OAuth2\Client\Provider\GenericProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class TokenCache {
  public function storeTokens($accessToken) {
    $token_data = [
      'accessToken'   => $accessToken->access_token,
      'tokenExpires'  => $accessToken->expires_in,
    ];
    Storage::put('token.json', json_encode($token_data));
  }

  public function clearTokens() {

  }

  // <GetAccessTokenSnippet>
  public function getAccessToken() {
    // Check if tokens exist
    if (File::exists(storage_path('app\token.json'))) {
      $token = json_decode(File::get(storage_path('app\token.json')));
      // Check if token is expired
      //Get current time + 5 minutes (to allow for time differences)
      $now = time() + 300;
      if ($token->tokenExpires <= $now) {
        // Token is expired (or very close to it)
        // so let's refresh
        try {
          $guzzle = new \GuzzleHttp\Client();
          $url = config('azure.authority').config('azure.tokenEndpoint');
          $token = json_decode($guzzle->post($url, [
              'form_params' => [
                  'client_id'     => config('azure.appId'),
                  'client_secret' => config('azure.appSecret'),
                  'scope'         => 'https://graph.microsoft.com/.default',
                  'grant_type'    => 'client_credentials',
              ],
          ])->getBody()->getContents());
          // Store the new values
          $this->updateTokens($token);
  
          return $token->access_token;
        }
        catch ( IdentityProviderException $e) {
          return '';
        }
      }
    }else{
      try {
        $guzzle = new \GuzzleHttp\Client();
        $url = config('azure.authority').config('azure.tokenEndpoint');
        $token = json_decode($guzzle->post($url, [
            'form_params' => [
                'client_id'     => config('azure.appId'),
                'client_secret' => config('azure.appSecret'),
                'scope'         => 'https://graph.microsoft.com/.default',
                'grant_type'    => 'client_credentials',
            ],
        ])->getBody()->getContents());
        // Store the new values
        $this->storeTokens($token);

        return $token->access_token;
      }
      catch ( IdentityProviderException $e) {
        return '';
      }
    }

    // Token is still valid, just return it
    return $token->tokenExpires;
  }
  // </GetAccessTokenSnippet>

  // <UpdateTokensSnippet>
  public function updateTokens($accessToken) {
    Storage::delete('token.json');
    $token_data = [
      'accessToken'   => $accessToken->access_token,
      'tokenExpires'  => $accessToken->expires_in,
    ];
    Storage::put('token.json', json_encode($token_data));
  }
  // </UpdateTokensSnippet>
}
