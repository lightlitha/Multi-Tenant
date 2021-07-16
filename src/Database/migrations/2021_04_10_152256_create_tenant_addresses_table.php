<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenantAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tenant_addresses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('line1');
            $table->string('line2')->nullable();
            $table->string('suburb')->nullable();
            $table->string('city');
            $table->string('zipcode');
            $table->string('country');
            $table->unsignedBigInteger('tenant_id');
            $table->foreign('tenant_id')->references('id')->on('tenants');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tenant_addresses');
    }
}
