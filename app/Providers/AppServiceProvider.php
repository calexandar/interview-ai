<?php

namespace App\Providers;

use App\AI\Contracts\InterviewAI;
use App\AI\InterviewAIProvider;
use App\Listeners\LogAiInvocation;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Laravel\Ai\Events\AgentFailed;
use Laravel\Ai\Events\AgentPrompted;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(InterviewAI::class, InterviewAIProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->registerAiObservability();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }

    /**
     * Records provider, model, token usage and latency for every AI call, and a
     * categorized failure when one occurs. Prompt bodies and candidate content
     * are never logged.
     */
    private function registerAiObservability(): void
    {
        Event::listen(AgentPrompted::class, [LogAiInvocation::class, 'handlePrompted']);
        Event::listen(AgentFailed::class, [LogAiInvocation::class, 'handleFailed']);
    }
}
