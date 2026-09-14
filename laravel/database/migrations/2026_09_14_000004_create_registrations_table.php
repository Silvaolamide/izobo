<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void { Schema::create('registrations', function (Blueprint $table) { $table->id(); $table->string('full_name'); $table->string('phone', 30); $table->string('email')->nullable(); $table->foreignId('state_id')->constrained()->restrictOnDelete(); $table->foreignId('lga_id')->constrained('lgas')->restrictOnDelete(); $table->foreignId('ward_id')->constrained('wards')->restrictOnDelete(); $table->string('interest'); $table->boolean('consent')->default(false); $table->timestamps(); $table->index(['lga_id','ward_id']); $table->index('created_at'); }); }
    public function down(): void { Schema::dropIfExists('registrations'); }
};
