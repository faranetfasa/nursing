<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Manager references live in a separate migration because they point at `users`,
 * which is created after the organisation tables.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organizations', function (Blueprint $table): void {
            $table->foreignId('manager_id')->nullable()->after('is_active')
                ->constrained('users')->cascadeOnUpdate()->nullOnDelete();
        });

        Schema::table('branches', function (Blueprint $table): void {
            $table->foreignId('manager_id')->nullable()->after('is_active')
                ->constrained('users')->cascadeOnUpdate()->nullOnDelete();
        });

        Schema::table('departments', function (Blueprint $table): void {
            $table->foreignId('manager_id')->nullable()->after('is_active')
                ->constrained('users')->cascadeOnUpdate()->nullOnDelete();
        });
    }

    public function down(): void
    {
        foreach (['organizations', 'branches', 'departments'] as $name) {
            Schema::table($name, function (Blueprint $table): void {
                $table->dropConstrainedForeignId('manager_id');
            });
        }
    }
};
