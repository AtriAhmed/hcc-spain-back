<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('applies', function (Blueprint $table) {
            // Add the new tel column (assuming same type as phone was, usually string)
            $table->string('tel')->nullable()->after('email');
        });

        // Copy data from phone → tel
        DB::table('applies')->update([
            'tel' => DB::raw('phone')
        ]);

        // Remove the old column
        Schema::table('applies', function (Blueprint $table) {
            $table->dropColumn('phone');
        });
    }

    public function down(): void
    {
        Schema::table('applies', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
        });

        DB::table('applies')->update([
            'phone' => DB::raw('tel')
        ]);

        Schema::table('applies', function (Blueprint $table) {
            $table->dropColumn('tel');
        });
    }
};
