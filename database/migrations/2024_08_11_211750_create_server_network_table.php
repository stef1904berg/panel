<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('server_network', function (Blueprint $table) {
            $table->unsignedInteger('server_id');
            $table->unsignedInteger('network_id');

            $table->foreign('server_id')->references('id')->on('servers');
            $table->foreign('network_id')->references('id')->on('networks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('server_network');
    }
};
