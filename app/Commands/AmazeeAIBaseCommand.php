<?php

namespace App\Commands;

use LaravelZero\Framework\Commands\Command;
use FreedomtechHosting\PolydockAmazeeAIBackendClient\Client;

abstract class AmazeeAIBaseCommand extends Command
{
    protected ?string $token = null;
    protected Client $client;

    public function __construct()
    {
        parent::__construct();
        $this->addTokenOption();
    }

    protected function addTokenOption(): void
    {
        $this->addOption('token', 't', \Symfony\Component\Console\Input\InputOption::VALUE_OPTIONAL, 'The API token to use');
    }

    protected function getToken(): string
    {
        if ($this->option('token')) {
            $this->info('Using token from command line');
            return $this->option('token');
        }

        $this->info('Using token from environment variable');
        $envToken = env('POLYDOCK_AMAZEEAI_ADMIN_TOKEN');

        if (!$envToken) {
            throw new \RuntimeException('No token provided. Use --token option or set POLYDOCK_AMAZEEAI_ADMIN_TOKEN environment variable.');
        }

        return $envToken;
    }

    protected function initializeClient(): void
    {
        $this->token = $this->getToken();
        $this->client = app()->make(Client::class, ['token' => $this->token]);
    }
} 