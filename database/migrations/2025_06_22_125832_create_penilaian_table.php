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
        Schema::create('penilaian', function (Blueprint $table) {
            $table->id(); // BIGINT PRIMARY KEY AUTO_INCREMENT
            
            // Foreign Keys
            $table->foreignId('periode_id')
                  ->constrained('periode_penilaian')
                  ->onDelete('cascade')
                  ->comment('FK ke periode_penilaian.id');
                  
            $table->unsignedBigInteger('penilai_id_user')
                  ->comment('FK ke user.id (yang mengisi)');
                  
            $table->unsignedBigInteger('dinilai_id_user')
                  ->comment('FK ke user.id (yang dinilai)');
            
            // Foreign key constraints
            $table->foreign('penilai_id_user')->references('id')->on('user')->onDelete('cascade');
            $table->foreign('dinilai_id_user')->references('id')->on('user')->onDelete('cascade');
            
            // Data Penilaian
            $table->decimal('total_nilai', 5, 2)
                  ->nullable()
                  ->comment('Rata-rata akhir (opsional)');
                  
            $table->text('komentar')
                  ->nullable()
                  ->comment('Komentar umum (opsional)');
                  
            $table->enum('status', ['belum_diisi', 'selesai'])
                  ->default('belum_diisi')
                  ->comment('Status pengisian penilaian');
                  
            $table->dateTime('tanggal_penilaian')
                  ->nullable()
                  ->comment('Diisi saat penilai submit');
            
            $table->timestamps(); // created_at dan updated_at
            
            // Indexes untuk performa query
            $table->index('periode_id');
            $table->index('penilai_id_user');
            $table->index('dinilai_id_user');
            $table->index('status');
            $table->index('tanggal_penilaian');
            
            // Unique constraint untuk mencegah duplikasi penilaian
            // Satu penilai hanya bisa menilai satu orang sekali dalam satu periode
            $table->unique(['periode_id', 'penilai_id_user', 'dinilai_id_user'], 'unique_penilaian');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penilaian');
    }
};