<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddMailboxTargetingToCustomFields extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('custom_fields', 'all_mailboxes')) {
            Schema::table('custom_fields', function (Blueprint $table) {
                $table->boolean('all_mailboxes')->default(false);
            });
            // Champs existants : conserver le comportement global (visibles partout).
            DB::table('custom_fields')->update(['all_mailboxes' => 1]);
        }

        if (!Schema::hasTable('custom_field_mailbox')) {
            Schema::create('custom_field_mailbox', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('custom_field_id')->index();
                $table->unsignedInteger('mailbox_id')->index();
                $table->unique(['custom_field_id', 'mailbox_id']);
            });
        }
    }

    public function down()
    {
        // Non destructif : on conserve la colonne et le pivot.
    }
}
