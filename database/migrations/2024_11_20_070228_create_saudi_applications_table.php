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
        Schema::create('saudi_applications', function (Blueprint $table) {
            $table->id();
            $table->string("coName");
            $table->string("coAddress");
            $table->string("regNB");
            $table->string("activity");
            $table->string("empNB");
            $table->string("cPerson");
            $table->string("cEmail");
            $table->string("cPhone");
            $table->string("remark", 3000)->nullable();
            $table->string("qualCertif");
            $table->string("prodReg");
            $table->string("facCertif");
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
        Schema::dropIfExists('saudi_applications');
    }
};
