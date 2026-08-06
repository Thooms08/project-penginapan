<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('others', function (Blueprint $table) {
            $table->text('about_en')->nullable()->after('about');
            $table->text('privacy_policy_en')->nullable()->after('privacy_policy');
            $table->text('terms_conditions_en')->nullable()->after('terms_conditions');
        });
    }

    public function down(): void
    {
        Schema::table('others', function (Blueprint $table) {
            $table->dropColumn(['about_en', 'privacy_policy_en', 'terms_conditions_en']);
        });
    }
};
