<?php
namespace Solveda\LinkedIn\Model;

class LinkedInData
{
    protected $_clientId;
    protected $_clientSecret;
    protected $_redirectUri;

    public function __construct(
        \Solveda\LinkedIn\Helper\Data $helper
    ) {
        $this->_clientId = $helper->getLinkedInClientId();
        $this->_clientSecret = $helper->getLinkedInClientSecret();
        $this->_redirectUri = $helper->getLinkedInRedirectUri();
    }

    public function getAccessToken($code)
    {
        $url = 'https://www.linkedin.com/oauth/v2/accessToken';
        $params = [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $this->_redirectUri,
            'client_id' => $this->_clientId,
            'client_secret' => $this->_clientSecret,
        ];

        return $this->_apiCall($url, $params, 'POST');
    }

    public function getUserData($accessToken)
    {
        $url = 'https://api.linkedin.com/v2/me';
        $params = [
            'oauth2_access_token' => $accessToken,
            'projection' => '(id,localizedFirstName,localizedLastName,emailAddress,gender)',
        ];

        $headerArr = ['Authorization: Bearer ' . $accessToken];
        return $this->_apiCall($url, $params, 'GET', $headerArr);
    }

    protected function _apiCall($url, $params, $method = 'GET', $headerArr = [])
    {
        $ch = curl_init();

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        } elseif ($method === 'GET') {
            $url .= '?' . http_build_query($params);
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headerArr);

        $response = curl_exec($ch);

        if ($response === false) {
            throw new \Exception(curl_error($ch));
        }

        curl_close($ch);

        return $response;
    }
}
