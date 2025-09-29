<?php
// File: database/migrations/2025_09_29_000001_alter_users_add_lms_fields.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'first_name')) {
                $table->string('first_name')->nullable()->after('id');
            }

            if (!Schema::hasColumn('users', 'last_name')) {
                $table->string('last_name')->nullable()->after('first_name');
            }

            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('password');
            }

            if (!Schema::hasColumn('users', 'avatar_url')) {
                $table->string('avatar_url')->nullable()->after('phone');
            }

            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['student', 'instructor', 'admin'])->default('student')->after('avatar_url');
            }

            if (!Schema::hasColumn('users', 'status')) {
                $table->enum('status', ['active', 'suspended', 'inactive'])->default('active')->after('role');
            }

            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('status');
            }
        });

        // Attempt to backfill first_name/last_name from existing 'name' column if present
        if (Schema::hasColumn('users', 'name')) {
            DB::table('users')->select(['id', 'name'])->orderBy('id')->chunkById(500, function ($users) {
                foreach ($users as $user) {
                    $fullName = trim((string) ($user->name ?? ''));
                    $parts = preg_split('/\s+/', $fullName, 2);
                    $first = $parts[0] ?? null;
                    $last = $parts[1] ?? null;
                    DB::table('users')->where('id', $user->id)->update([
                        'first_name' => $first,
                        'last_name' => $last,
                    ]);
                }
            });

            // Make first_name/last_name not nullable and drop legacy 'name'
            Schema::table('users', function (Blueprint $table) {
                $table->string('first_name')->nullable(false)->change();
                $table->string('last_name')->nullable(false)->change();
                $table->dropColumn('name');
            });
        } else {
            // If there is no legacy 'name', still enforce not-null going forward
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'first_name')) {
                    $table->string('first_name')->nullable(false)->change();
                }
                if (Schema::hasColumn('users', 'last_name')) {
                    $table->string('last_name')->nullable(false)->change();
                }
            });
        }

        // Helpful composite indexes
        Schema::table('users', function (Blueprint $table) {
            // Add indexes if they do not already exist (DBAL lacks direct hasIndex; safe to try creating named indexes)
            try {
                $table->index(['role', 'status'], 'users_role_status_index');
            } catch (\Throwable $e) {
            }
            try {
                $table->index('email', 'users_email_index');
            } catch (\Throwable $e) {
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate legacy 'name' column to store combined names when rolling back
        if (!Schema::hasColumn('users', 'name')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('name')->nullable()->after('id');
            });

            // Combine first_name and last_name back into name
            if (Schema::hasColumn('users', 'first_name') && Schema::hasColumn('users', 'last_name')) {
                DB::table('users')->select(['id', 'first_name', 'last_name'])->orderBy('id')->chunkById(500, function ($users) {
                    foreach ($users as $user) {
                        $name = trim(((string) ($user->first_name ?? '')).' '.((string) ($user->last_name ?? '')));
                        DB::table('users')->where('id', $user->id)->update(['name' => $name]);
                    }
                });
            }
        }

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'first_name')) {
                $table->dropColumn('first_name');
            }
            if (Schema::hasColumn('users', 'last_name')) {
                $table->dropColumn('last_name');
            }
            if (Schema::hasColumn('users', 'phone')) {
                $table->dropColumn('phone');
            }
            if (Schema::hasColumn('users', 'avatar_url')) {
                $table->dropColumn('avatar_url');
            }
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
            if (Schema::hasColumn('users', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('users', 'last_login_at')) {
                $table->dropColumn('last_login_at');
            }

            // Drop extra indexes if present
            try {
                $table->dropIndex('users_role_status_index');
            } catch (\Throwable $e) {
            }
            try {
                $table->dropIndex('users_email_index');
            } catch (\Throwable $e) {
            }
        });
    }
};


