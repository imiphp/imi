<?php

namespace app;

class Secrets extends \Github\Api\Repository\Actions\Secrets
{
    /**
     * @see https://docs.github.com/en/rest/reference/actions#get-a-repository-public-key
     *
     * @param string $username
     * @param string $repository
     *
     * @return array|string
     */
    public function publicKey(string $username, string $repository)
    {
        return $this->get('/repos/' . rawurlencode($username) . '/' . rawurlencode($repository) . '/actions/secrets/public-key');
    }
}
