<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->enum('type', ['income', 'expense', 'both'])->default('expense');
            $table->string('icon', 50)->default('fa-tag');
        });

        $categories = [
            ['name' => 'Kiriman & Hibah', 'type' => 'income', 'icon' => 'fa-gift'],
            ['name' => 'Kebutuhan Pokok', 'type' => 'expense', 'icon' => 'fa-shopping-basket'],
            ['name' => 'Pendidikan & Pesantren', 'type' => 'expense', 'icon' => 'fa-graduation-cap'],
            ['name' => 'Sosial & Hadiah', 'type' => 'both', 'icon' => 'fa-hand-holding-heart'],
            ['name' => 'Gaya Hidup', 'type' => 'expense', 'icon' => 'fa-coffee'],
            ['name' => 'Finansial & Hutang', 'type' => 'both', 'icon' => 'fa-chart-line'],
            ['name' => 'Lainnya', 'type' => 'both', 'icon' => 'fa-tag'],
        ];

        foreach ($categories as $cat) {
            DB::table('categories')->insert($cat);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
