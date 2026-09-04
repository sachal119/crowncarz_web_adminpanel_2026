<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('staff', 'name')) {
            Schema::table('staff', function (Blueprint $table) {
                $table->string('name')->nullable()->after('id');
            });
        }

        if (! Schema::hasColumn('staff', 'role')) {
            Schema::table('staff', function (Blueprint $table) {
                $table->string('role', 30)->default('collaborator')->after('password');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('staff', 'role')) {
            Schema::table('staff', function (Blueprint $table) {
                $table->dropColumn('role');
            });
        }
    }
};
