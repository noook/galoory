<?php

namespace App\Google;

use Google_Client;
use Google_Service_Gmail;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class GmailAuthenticator {

    private string $credentialsPath;

    public function __construct(ParameterBagInterface $bag)
    {
        $this->credentialsPath = $bag->get('gmail_credentials');
    }

    public function getClient(): Google_Client {
        $client = new Google_Client();
        $client->setApplicationName('Galoory');
        $client->setScopes([
            Google_Service_Gmail::GMAIL_SEND,
        ]);
        $client->setAuthConfig($this->credentialsPath);
        $client->setAccessType('offline');

        $credentialsPath = dirname($this->credentialsPath) . '/gmail-token.json';
        if (file_exists($credentialsPath)) {
            $accessToken = json_decode(file_get_contents($credentialsPath), true);
        } else {
            $authUrl = $client->createAuthUrl();
            printf("Open the following link in your browser:\n%s\n", $authUrl);
            print 'Enter verification code: ';
            $authCode = trim(fgets(STDIN));

            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

            file_put_contents($credentialsPath, json_encode($accessToken, JSON_PRETTY_PRINT));
            printf("Credentials saved to %s\n", $credentialsPath);
        }
        $client->setAccessToken($accessToken);

        if ($client->isAccessTokenExpired()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            file_put_contents($credentialsPath, json_encode($client->getAccessToken(), JSON_PRETTY_PRINT));
        }

        return $client;
    }
}
