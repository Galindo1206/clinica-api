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
        Schema::table('settings', function (Blueprint $table) {
            $table->string('secondary_color')->default('#111827')->after('primary_color');

            $table->text('footer_text')->nullable()->after('secondary_color');

            $table->boolean('show_qr')->default(false)->after('footer_text');
            $table->boolean('show_signature')->default(false)->after('show_qr');
            $table->boolean('show_cmp')->default(true)->after('show_signature');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            //
        });
    }
};
