<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string("title_en")->nullable();
            $table->string("title_ar")->nullable();
            $table->string("title_de")->nullable(); // Added German title
            $table->string("slug");
            $table->integer("author_id");
            $table->text("summary_en")->nullable();
            $table->text("summary_ar")->nullable();
            $table->text("summary_de")->nullable(); // Added German summary
            $table->tinyInteger("status")->default(1);
            $table->string("image")->nullable();
            $table->string("keywords")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('blogs');
    }
};
