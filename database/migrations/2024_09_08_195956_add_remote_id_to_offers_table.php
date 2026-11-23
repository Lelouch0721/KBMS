<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->unsignedBigInteger('remote_id')->nullable();
            $table->foreign('remote_id')->references('remote_id')->on('remotes')->onDelete('cascade');
        });
    }
    
    public function down()
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->dropForeign(['remote_id']);
            $table->dropColumn('remote_id');
        });
    }
    
};
