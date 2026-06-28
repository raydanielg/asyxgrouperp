<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ═══ Document Management ═══
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('document_number')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category')->nullable(); // contract, invoice, tender, hr, legal, technical
            $table->string('file_path');
            $table->string('file_type')->nullable();
            $table->bigInteger('file_size')->default(0);
            $table->string('version')->default('1.0');
            $table->string('status')->default('draft'); // draft, pending_signature, signed, archived
            $table->string('reference_type')->nullable(); // project, tender, contract, employee
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('signed_at')->nullable();
            $table->timestamps();
        });

        // ═══ E-Signatures ═══
        Schema::create('document_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->cascadeOnDelete();
            $table->foreignId('signer_id')->constrained('users')->cascadeOnDelete();
            $table->string('signer_name');
            $table->string('signer_email');
            $table->string('status')->default('pending'); // pending, signed, declined
            $table->timestamp('signed_at')->nullable();
            $table->string('signature_hash')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('decline_reason')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // ═══ Document Access Logs ═══
        Schema::create('document_access_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action'); // view, download, sign, archive
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_access_logs');
        Schema::dropIfExists('document_signatures');
        Schema::dropIfExists('documents');
    }
};
