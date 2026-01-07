<?php

namespace App\Providers;

use App\Models\Athlete;
use App\Models\Evaluation;
use App\Models\EvaluationTemplate;
use App\Models\Organization;
use App\Models\Report;
use App\Policies\AthletePolicy;
use App\Policies\EvaluationPolicy;
use App\Policies\EvaluationTemplatePolicy;
use App\Policies\OrganizationPolicy;
use App\Policies\ReportPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Organization::class => OrganizationPolicy::class,
        Athlete::class => AthletePolicy::class,
        EvaluationTemplate::class => EvaluationTemplatePolicy::class,
        Evaluation::class => EvaluationPolicy::class,
        Report::class => ReportPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
