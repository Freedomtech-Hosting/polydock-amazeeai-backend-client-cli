<?php

namespace App\Commands;

use LaravelZero\Framework\Commands\Command;
use FreedomtechHosting\PolydockAmazeeAIBackendClient\Client;

abstract class AmazeeAIBaseCommand extends Command
{
    protected ?string $token = null;
    protected Client $client;
    protected string $tokenFile = '.amazeeai-user.token';
    protected bool $useUserToken = true; // Default to using user token

    public function __construct()
    {
        parent::__construct();
        $this->addTokenOption();
    }

    protected function addTokenOption(): void
    {
        $this->addOption('token', 't', \Symfony\Component\Console\Input\InputOption::VALUE_OPTIONAL, 'The API token to use');
    }

    protected function storeUserToken(string $token): void
    {
        file_put_contents($this->tokenFile, $token);
        chmod($this->tokenFile, 0600); // Secure the file
    }

    protected function getUserToken(): ?string
    {
        if (!file_exists($this->tokenFile)) {
            return null;
        }
        return trim(file_get_contents($this->tokenFile));
    }

    protected function getToken(): string
    {
        // Command line token always takes precedence
        if ($this->option('token')) {
            $this->info('Using token from command line');
            return $this->option('token');
        }

        // If using user token, try to get it from file
        if ($this->useUserToken) {
            $userToken = $this->getUserToken();
            if ($userToken) {
                $this->info('Using stored user token');
                return $userToken;
            }
        } else {
            // Fallback to environment variable
            $this->info('Using token from environment variable');
            $envToken = env('POLYDOCK_AMAZEEAI_ADMIN_TOKEN');
        }
        
        if (!$envToken) {
            throw new \RuntimeException('No token available. Please login first or provide a token via --token option or POLYDOCK_AMAZEEAI_ADMIN_TOKEN environment variable.');
        }

        return $envToken;
    }

    protected function initializeClient(bool $useUserToken = true): void
    {
        $this->useUserToken = $useUserToken;
        $this->token = $this->getToken();
        $this->client = app()->make(Client::class, ['token' => $this->token]);
    }
} 