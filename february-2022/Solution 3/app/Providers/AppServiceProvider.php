<?php

namespace App\Providers;

use App\Jobs\SendEmail;
use App\Repositories\Eloquent\EmailRequestRepositoryEloquent;
use App\Repositories\EmailRequestRepository;
use App\Services\Impl\EmailRequestServiceImpl;
use App\Services\EmailRequestService;
use App\Services\Impl\SendEmailJobServiceImpl;
use App\Services\Impl\WebhookServiceImpl;
use App\Services\SendEmailJobService;
use App\Services\WebhookService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerRepositories();
        $this->registerServices();
        $this->registerDIForJobs();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    private function registerRepositories()
    {
         $this->app->bind(EmailRequestRepository::class, EmailRequestRepositoryEloquent::class);
    }

    private function registerServices()
    {
         $this->app->bind(EmailRequestService::class, EmailRequestServiceImpl::class);
         $this->app->bind(SendEmailJobService::class, SendEmailJobServiceImpl::class);
         $this->app->bind(WebhookService::class, WebhookServiceImpl::class);
    }

    private function registerDIForJobs()
    {
        $this->app->bindMethod([SendEmail::class, 'handle'], function ($job, $app) {
            return $job->handle($app->make(SendEmailJobService::class));
        });
    }
}
