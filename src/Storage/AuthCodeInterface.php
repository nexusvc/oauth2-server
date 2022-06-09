<?php
/**
 * OAuth 2.0 Auth code storage interface
 *
 * @package     league/oauth2-server
 * @author      Alex Bilbie <hello@alexbilbie.com>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 * @link        https://github.com/thephpleague/oauth2-server
 */

namespace Nexusvc\OAuth2\Server\Storage;

use Nexusvc\OAuth2\Server\Entity\AuthCodeEntity;
use Nexusvc\OAuth2\Server\Entity\ScopeEntity;

/**
 * Auth code storage interface
 */
interface AuthCodeInterface extends StorageInterface
{
    /**
     * Get the auth code
     *
     * @param string $code
     *
     * @return \Nexusvc\OAuth2\Server\Entity\AuthCodeEntity | null
     */
    public function get($code);

    /**
     * Create an auth code.
     *
     * @param string  $token       The token ID
     * @param integer $expireTime  Token expire time
     * @param integer $sessionId   Session identifier
     * @param string  $redirectUri Client redirect uri
     *
     * @return void
     */
    public function create($token, $expireTime, $sessionId, $redirectUri);

    /**
     * Get the scopes for an access token
     *
     * @param \Nexusvc\OAuth2\Server\Entity\AuthCodeEntity $token The auth code
     *
     * @return \Nexusvc\OAuth2\Server\Entity\ScopeEntity[] Array of \Nexusvc\OAuth2\Server\Entity\ScopeEntity
     */
    public function getScopes(AuthCodeEntity $token);

    /**
     * Associate a scope with an acess token
     *
     * @param \Nexusvc\OAuth2\Server\Entity\AuthCodeEntity $token The auth code
     * @param \Nexusvc\OAuth2\Server\Entity\ScopeEntity    $scope The scope
     *
     * @return void
     */
    public function associateScope(AuthCodeEntity $token, ScopeEntity $scope);

    /**
     * Delete an access token
     *
     * @param \Nexusvc\OAuth2\Server\Entity\AuthCodeEntity $token The access token to delete
     *
     * @return void
     */
    public function delete(AuthCodeEntity $token);
}
