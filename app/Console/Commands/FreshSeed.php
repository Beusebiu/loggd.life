<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FreshSeed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fresh-seed {--force : Force the operation in production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fresh migrate and seed the database with test data (50 complete profiles + test user)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (app()->environment('production') && ! $this->option('force')) {
            $this->error('This command cannot run in production without --force flag');

            return 1;
        }

        $this->warn('⚠️  This will destroy all data in your database!');

        if (! $this->confirm('Are you sure you want to continue?')) {
            $this->info('Operation cancelled.');

            return 0;
        }

        $this->info('🔄 Running fresh migrations...');
        $this->call('migrate:fresh');

        $this->info('🌱 Seeding database with comprehensive test data...');
        $this->call('db:seed');

        $this->newLine();
        $this->info('✅ Database refreshed successfully!');
        $this->newLine();
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->line('  📧 Test User: test@loggd.life');
        $this->line('  🔑 Password: password123');
        $this->line('  👥 Additional: 50 diverse test profiles (user1-user50@loggd.test)');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->newLine();

        return 0;
    }
}
