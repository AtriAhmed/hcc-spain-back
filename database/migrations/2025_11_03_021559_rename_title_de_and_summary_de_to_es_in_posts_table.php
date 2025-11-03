<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            // Drop old German fields
            if (Schema::hasColumn('posts', 'title_de')) {
                $table->dropColumn('title_de');
            }
            if (Schema::hasColumn('posts', 'summary_de')) {
                $table->dropColumn('summary_de');
            }

            // Create new Spanish fields
            $table->string('title_es')->nullable()->after('title_ar');
            $table->text('summary_es')->nullable()->after('summary_ar');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            // Recreate German fields in case of rollback
            $table->string('title_de')->nullable()->after('title_ar');
            $table->text('summary_de')->nullable()->after('summary_ar');

            // Remove Spanish fields
            $table->dropColumn('title_es');
            $table->dropColumn('summary_es');
        });
    }
};
