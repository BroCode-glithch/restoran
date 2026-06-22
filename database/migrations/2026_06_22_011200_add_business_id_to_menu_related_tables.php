<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBusinessIdToMenuRelatedTables extends Migration
{
    public function up()
    {
        // Intentionally left blank.
        // The menus and services business_id columns are added by the earlier migration.
    }

    public function down()
    {
        // Intentionally left blank.
    }
}
