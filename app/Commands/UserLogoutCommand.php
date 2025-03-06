<?php

namespace App\Commands;

use App\Enums\TokenType;

class UserLogoutCommand extends AmazeeAIBaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'user:logout';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Logout and clear stored credentials';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            // First verify the current token is valid
            $this->initializeClient(TokenType::USER_TOKEN);
            
            // Clear the stored token
            $this->clearUserToken();
            
            $this->info('Successfully logged out');
            return 0;
        } catch (\Exception $e) {
            // If there's no token or other error, just clear the token file
            $this->clearUserToken();
            $this->info('Local credentials cleared');
            return 0;
        }
    }
} 