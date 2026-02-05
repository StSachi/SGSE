<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'papel')) {
                $table->string('papel', 30)->default('CLIENTE')->after('password');
            }

            if (!Schema::hasColumn('users', 'ativo')) {
                $table->boolean('ativo')->default(true)->after('papel');
            }
        });

        // Só atualiza se a coluna existir (extra-safe)
        if (Schema::hasColumn('users', 'papel')) {
            DB::table('users')->whereNull('papel')->update(['papel' => 'CLIENTE']);
        }

        if (Schema::hasColumn('users', 'ativo')) {
            DB::table('users')->whereNull('ativo')->update(['ativo' => 1]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'ativo')) {
                $table->dropColumn('ativo');
            }
            if (Schema::hasColumn('users', 'papel')) {
                $table->dropColumn('papel');
            }
        });
    }
};
