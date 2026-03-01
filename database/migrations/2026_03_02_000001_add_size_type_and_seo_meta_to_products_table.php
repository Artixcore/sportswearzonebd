<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('size_type', 32)->default('standard')->after('main_image_path');
            $table->string('meta_title', 255)->nullable()->after('size_type');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->string('meta_keywords', 512)->nullable()->after('meta_description');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['size_type', 'meta_title', 'meta_description', 'meta_keywords']);
        });
    }
};
