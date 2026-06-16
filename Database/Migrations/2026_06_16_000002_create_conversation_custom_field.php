<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConversationCustomField extends Migration
{
    public function up()
    {
        if (Schema::hasTable('conversation_custom_field')) {
            return;
        }
        Schema::create('conversation_custom_field', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('conversation_id')->index();
            $table->unsignedInteger('custom_field_id')->index();
            $table->text('value')->nullable();
            $table->timestamps();
            $table->unique(['conversation_id', 'custom_field_id']);
        });
    }

    public function down()
    {
        // Non destructif : conserver les valeurs saisies.
    }
}
