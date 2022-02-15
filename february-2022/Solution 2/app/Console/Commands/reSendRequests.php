<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\RequestsHandler;

class reSendRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'resend:requests';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        RequestsHandler::reSendRequests();
        return 0;
    }
}
