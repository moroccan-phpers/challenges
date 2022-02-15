<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\DeliveryServiceHandler;

class webhoock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'minute:webhook';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'process mails';

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
        DeliveryServiceHandler::webhook();
        echo "checked!";
        return 0;
    }
}
