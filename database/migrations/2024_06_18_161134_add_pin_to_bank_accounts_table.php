<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('bank_accounts', function (Blueprint $table) {
        $table->string('pin')->nullable()->after('balance');
    });
}

public function down()
{
    Schema::table('bank_accounts', function (Blueprint $table) {
        $table->dropColumn('pin');
    });
}

};
