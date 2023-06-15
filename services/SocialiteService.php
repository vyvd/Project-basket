<?php


use League\OAuth2\Client\Provider\Google;
use SocialConnect\Auth\Service;
use SocialConnect\Common\HttpStack;
use SocialConnect\HttpClient\Curl;
use SocialConnect\HttpClient\RequestFactory;
use SocialConnect\HttpClient\StreamFactory;
use SocialConnect\Provider\Session\Session;

class SocialiteService
{
    public static function configProvider() {

        // You can use any HTTP client with PSR-18 compatibility
        $httpClient = new Curl();

        $httpStack = new HttpStack(
            $httpClient,
            new RequestFactory(),
            new StreamFactory()
        );

        $providers = [
            'facebook' => [
                'client_id'     => '197568709209634',
                'client_secret' => '8655f981e07a71afd66e32418ba0a253',
                'redirect'      => SITE_URL.'ajax?c=account&a=social_login_callback&provider=facebook'
            ],
            'google' => [
                'client_id'     => '264898751184-dq82h0i867h91ir9rog29et7s1kk1tc2.apps.googleusercontent.com',
                'client_secret' => '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',
                'redirect'      => SITE_URL.'ajax?c=account&a=social_login_callback&provider=google'
            ]
        ];

        $configureProviders = [
            'redirectUri' => SITE_URL.'ajax?c=account&a=social_login_callback&provider=${provider}',
            'provider' => [
                'facebook' => [
                    'applicationId' => '197568709209634',
                    'applicationSecret' => '8655f981e07a71afd66e32418ba0a253',
                    'scope' => ['public_profile,email'],
                    'options' => [
                        'identity.fields' => [
                            'name',
                            'email',
                            'picture.width(99999)'
                        ],
                    ],
                ],
                'google' => [
                    'applicationId' => '264898751184-dq82h0i867h91ir9rog29et7s1kk1tc2.apps.googleusercontent.com',
                    'applicationSecret' => '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',
                    'scope' => ['profile', 'email']
                ],
            ],
        ];

        /**
         * By default collection factory is null, in this case Auth\Service will create
         * a new instance of \SocialConnect\Auth\CollectionFactory
         * you can use custom or register another providers by CollectionFactory instance
         */

        $collectionFactory = null;

        $service = new Service(
            new HttpStack(
                $httpClient,
                new RequestFactory(),
                new StreamFactory()
            ),
            new Session(),
            $configureProviders,
            $collectionFactory
        );

        return $service;
    }

    public static function googleProvider(): Google
    {
        return new Google([
            'clientId'     => '264898751184-dq82h0i867h91ir9rog29et7s1kk1tc2.apps.googleusercontent.com',
            'clientSecret' => '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',
            'redirectUri'  => SITE_URL.'ajax?c=account&a=social_login_callback&provider=google',
        ]);
    }


    public static function authorize($provider)
    {
        $service = self::configProvider();
        $socialProvider = $service->getProvider($provider);
        return $socialProvider->makeAuthUrl();
    }



    public static function callback($providerName)
    {
        $service = self::configProvider();

        $provider = $service->getProvider($providerName);
        $accessToken = $provider->getAccessTokenByRequestParameters($_GET);

        $user = $provider->getIdentity($accessToken);

        return $user;
    }

    public static function googleAuthorize(): string
    {
        $service = self::googleProvider();
        $redirectUrl = $service->getAuthorizationUrl();
        $_SESSION['oauth2state'] = $service->getState();
        return $redirectUrl;
    }


    public static function googleCallback()
    {
        $provider = self::googleProvider();

        if (!empty($_GET['error'])) {
            $response = [
                'error' => true,
                'message' => 'Got error: ' . htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8'),
            ];
//            // Got an error, probably user denied access
//            exit('Got error: ' . htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8'));

        } elseif (empty($_GET['code'])) {

            // If we don't have an authorization code then get one
            $authUrl = $provider->getAuthorizationUrl();
            $_SESSION['oauth2state'] = $provider->getState();
            header('Location: ' . $authUrl);
            exit;

        } elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {

            // State is invalid, possible CSRF attack in progress
            unset($_SESSION['oauth2state']);

            $response = [
                'error' => true,
                'message' => 'Invalid state',
            ];

        } else {

            // Try to get an access token (using the authorization code grant)
            $token = $provider->getAccessToken('authorization_code', [
                'code' => $_GET['code']
            ]);

            // Optional: Now you have a token you can look up a users profile data
            try {

                // We got an access token, let's now get the owner details
                $ownerDetails = $provider->getResourceOwner($token);

                $response = [
                    'error' => false,
                    'id' => $ownerDetails->getId(),
                    'name' => $ownerDetails->getName(),
                    'firstname' => $ownerDetails->getFirstName(),
                    'lastname' => $ownerDetails->getLastName(),
                    'email' => $ownerDetails->getEmail(),
                    'pictureURL' => $ownerDetails->getAvatar(),
                ];

            } catch (Exception $e) {

                $response = [
                    'error' => true,
                    'message' => 'Something went wrong: ' . $e->getMessage()
                ];

            }
        }

        return (object) $response;
    }
}
?>