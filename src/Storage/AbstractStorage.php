<?php
/**
 * OAuth 2.0 abstract storage
 *
 * @package     league/oauth2-server
 * @author      Alex Bilbie <hello@alexbilbie.com>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 * @link        https://github.com/thephpleague/oauth2-server
 */

namespace Nexusvc\OAuth2\Server\Storage;

use Nexusvc\OAuth2\Server\AbstractServer;

/**
 * Abstract storage class
 */
abstract class AbstractStorage implements StorageInterface
{
    /**
     * Server
     *
     * @var \Nexusvc\OAuth2\Server\AbstractServer $server
     */
    protected $server;

    /**
     * Set the server
     *
     * @param \Nexusvc\OAuth2\Server\AbstractServer $server
     *
     * @return self
     */
    public function setServer(AbstractServer $server)
    {
        $this->server = $server;

        return $this;
    }

    /**
     * Return the server
     *
     * @return \Nexusvc\OAuth2\Server\AbstractServer
     */
    protected function getServer()
    {
        return $this->server;
    }
}
