<?php

class GoogleAuth
{

    private $redirectUrl = SITE_URL . 'ajax?c=googleAuth&a=google-auth-redirect-handler';
    private $clientCredentials;

    private $authValidated;
    private $accessToken;

    protected $client;

    protected $scope = [];

    public function __construct()
    {
        $this->client = new Google\Client();
    }

    public function authInit()
    {
        $this->client->setAuthConfig($this->clientCredentials);
        $this->client->addScope($this->scope);

        if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
            $this->client->setAccessToken($_SESSION['access_token']);
            if ($this->client->isAccessTokenExpired()) {
                return $this->authRedirectHandler();
            }
            $this->setAuthValidated(true);
            $this->setAccessToken($_SESSION['access_token']);
            return [
                'success' => true,
            ];
        } else {
            return $this->authRedirectHandler();
        }
    }

    public function authRedirectHandler()
    {
        $this->client->setAuthConfig($this->clientCredentials);
        $this->client->setRedirectUri($this->redirectUrl);
        $this->client->addScope($this->scope);

        if (!isset($_GET['code'])) {
            $auth_url = $this->client->createAuthUrl();
            return [
                'success' => false,
                'code' => 'auth_url',
                'auth_url' => filter_var($auth_url, FILTER_SANITIZE_URL)
            ];
        } else {
            $this->client->fetchAccessTokenWithAuthCode($_GET['code']);
            $_SESSION['access_token'] = $this->client->getAccessToken();
            $this->setAuthValidated(true);
            $this->setAccessToken($_SESSION['access_token']);
            return [
                'success' => true,
            ];
        }
    }

    /**
     * @param mixed $accessToken
     */
    public function setAccessToken($accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @return mixed
     */
    public function getAuthValidated()
    {
        return $this->authValidated;
    }

    /**
     * @param mixed $authValidated
     */
    public function setAuthValidated($authValidated): void
    {
        $this->authValidated = $authValidated;
    }

    /**
     * @return mixed
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @param array $scope
     */
    public function setScope(array $scope): void
    {
        $this->scope = $scope;
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * @param string $redirectUrl
     */
    public function setRedirectUrl(string $redirectUrl): void
    {
        $this->redirectUrl = $redirectUrl;
    }

    /**
     * @param string $clientCredentials
     */
    public function setClientCredentials(string $clientCredentials): void
    {
        $this->clientCredentials = $clientCredentials;
    }
}