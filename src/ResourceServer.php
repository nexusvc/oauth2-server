<?php
/**
 * OAuth 2.0 Resource Server
 *
 * @package     league/oauth2-server
 * @author      Alex Bilbie <hello@alexbilbie.com>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 * @link        https://github.com/thephpleague/oauth2-server
 */

namespace Nexusvc\OAuth2\Server;

use Nexusvc\OAuth2\Server\Entity\AccessTokenEntity;
use Nexusvc\OAuth2\Server\Exception\AccessDeniedException;
use Nexusvc\OAuth2\Server\Exception\InvalidRequestException;
use Nexusvc\OAuth2\Server\Storage\AccessTokenInterface;
use Nexusvc\OAuth2\Server\Storage\ClientInterface;
use Nexusvc\OAuth2\Server\Storage\ScopeInterface;
use Nexusvc\OAuth2\Server\Storage\SessionInterface;
use Nexusvc\OAuth2\Server\TokenType\Bearer;
use Nexusvc\OAuth2\Server\TokenType\MAC;

/**
 * OAuth 2.0 Resource Server
 */
class ResourceServer extends AbstractServer
{
    /**
     * The access token
     *
     * @var \Nexusvc\OAuth2\Server\Entity\AccessTokenEntity
     */
    protected $accessToken;

    /**
     * The query string key which is used by clients to present the access token (default: access_token)
     *
     * @var string
     */
    protected $tokenKey = 'access_token';

    /**
     * Initialise the resource server
     *
     * @param \Nexusvc\OAuth2\Server\Storage\SessionInterface     $sessionStorage
     * @param \Nexusvc\OAuth2\Server\Storage\AccessTokenInterface $accessTokenStorage
     * @param \Nexusvc\OAuth2\Server\Storage\ClientInterface      $clientStorage
     * @param \Nexusvc\OAuth2\Server\Storage\ScopeInterface       $scopeStorage
     *
     * @return self
     */
    public function __construct(
        SessionInterface $sessionStorage,
        AccessTokenInterface $accessTokenStorage,
        ClientInterface $clientStorage,
        ScopeInterface $scopeStorage
    ) {
        $this->setSessionStorage($sessionStorage);
        $this->setAccessTokenStorage($accessTokenStorage);
        $this->setClientStorage($clientStorage);
        $this->setScopeStorage($scopeStorage);

        // Set Bearer as the default token type
        $this->setTokenType(new Bearer());

        parent::__construct();

        return $this;
    }

    /**
     * Sets the query string key for the access token.
     *
     * @param string $key The new query string key
     *
     * @return self
     */
    public function setIdKey($key)
    {
        $this->tokenKey = $key;

        return $this;
    }

    /**
     * Gets the access token
     *
     * @return \Nexusvc\OAuth2\Server\Entity\AccessTokenEntity
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * Checks if the access token is valid or not
     *
     * @param bool                                                $headerOnly Limit Access Token to Authorization header
     * @param \Nexusvc\OAuth2\Server\Entity\AccessTokenEntity|null $accessToken Access Token
     *
     * @throws \Nexusvc\OAuth2\Server\Exception\AccessDeniedException
     * @throws \Nexusvc\OAuth2\Server\Exception\InvalidRequestException
     *
     * @return bool
     */
    public function isValidRequest($headerOnly = true, $accessToken = null)
    {
        $accessTokenString = ($accessToken !== null)
                                ? $accessToken
                                : $this->determineAccessToken($headerOnly);

        // Set the access token
        $this->accessToken = $this->getAccessTokenStorage()->get($accessTokenString);

        // Ensure the access token exists
        if (!$this->accessToken instanceof AccessTokenEntity) {
            throw new AccessDeniedException();
        }

        // Check the access token hasn't expired
        // Ensure the auth code hasn't expired
        if ($this->accessToken->isExpired() === true) {
            throw new AccessDeniedException();
        }

        return true;
    }

    /**
     * Reads in the access token from the headers
     *
     * @param bool $headerOnly Limit Access Token to Authorization header
     *
     * @throws \Nexusvc\OAuth2\Server\Exception\InvalidRequestException Thrown if there is no access token presented
     *
     * @return string
     */
    public function determineAccessToken($headerOnly = false)
    {
	$authHeader = $this->getRequest()->headers->get('Authorization');

        if (!empty($authHeader)) {
            $accessToken = $this->getTokenType()->determineAccessTokenInHeader($this->getRequest());
        } elseif ($headerOnly === false && (! $this->getTokenType() instanceof MAC)) {
            $accessToken = ($this->getRequest()->server->get('REQUEST_METHOD') === 'GET')
                                ? $this->getRequest()->query->get($this->tokenKey)
                                : $this->getRequest()->request->get($this->tokenKey);
        }

        if (empty($accessToken)) {
            throw new InvalidRequestException('access token');
        }

        return $accessToken;
    }
}
