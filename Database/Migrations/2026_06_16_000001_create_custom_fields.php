<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomFields extends Migration
{
    public function up()
    {
        if (Schema::hasTable('custom_fields')) {
            return;
        }
        Schema::create('custom_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('type', 20);
            $table->text('options')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        // Non destructif : conserver les définitions à la désactivation du module.
    }
}
