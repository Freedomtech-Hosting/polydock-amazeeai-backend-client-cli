<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

use FreedomtechHosting\PolydockAmazeeAIBackendClient\Client;
use FreedomtechHosting\PolydockAmazeeAIBackendClient\Exception\HttpException;
use App\Enums\TokenType;

class AdminMeCommand extends AmazeeAIBaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'admin:me';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Get information about the current admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->initializeClient(TokenType::ADMIN_TOKEN);
            $response = $this->client->getMe();
            $this->table(
                ['Field', 'Value'],
                [
                    ['Email', $response['email']],
                    ['ID', $response['id']],
                    ['Is Active', $response['is_active'] ? 'Yes' : 'No'],
                    ['Is Admin', $response['is_admin'] ? 'Yes' : 'No'],
                ]
            );
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            return 1;
        }

        return 0;
    }
}
