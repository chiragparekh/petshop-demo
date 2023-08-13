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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->timestamp('last_login_at')->nullable();
            $table->tinyInteger('is_marketing')->default(0)->after('id');
            $table->string('phone_number');
            $table->string('address');
            $table->uuid('avatar')->nullable()->after('id');
            $table->tinyInteger('is_admin')->default(0)->after('id');
            $table->string('last_name')->after('id');
            $table->string('first_name')->after('id');
            $table->uuid('uuid')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name');

            $table->dropColumn('last_login_at');
            $table->dropColumn('is_marketing');
            $table->dropColumn('phone_number');
            $table->dropColumn('address');
            $table->dropColumn('avatar');
            $table->dropColumn('is_admin');
            $table->dropColumn('last_name');
            $table->dropColumn('first_name');
            $table->dropColumn('uuid');
        });
    }
};
