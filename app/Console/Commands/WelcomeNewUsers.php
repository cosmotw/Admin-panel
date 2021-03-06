<?php

namespace App\Console\Commands;

use \App\User;
use App\Services\EmailService;
use Illuminate\Console\Command;

class WelcomeNewUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:newusers {--queue=default}';

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
    public function __construct(EmailService $emailService)
    {
        parent::__construct();

        $this->emailService = $emailService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(User $user)
    {
        $usersData = $user->signedUpThisWeek();

        $totalUnits = $usersData->count();
        $this->output->progressStart($totalUnits);
        $usersData->each(function ($user) {
            $this->emailService->welcomeNewUsers($user);
            $this->output->progressAdvance();
        });
        $this->output->progressFinish();

        $this->info('Email send success.');
    }
}
