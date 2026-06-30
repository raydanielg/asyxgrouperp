<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('documents')) {
            Schema::table('documents', function (Blueprint $table) {
                if (!Schema::hasColumn('documents', 'project_id')) {
                    $table->foreignId('project_id')->nullable()->after('reference_id')->constrained()->nullOnDelete();
                }
                if (!Schema::hasColumn('documents', 'tags')) {
                    $table->string('tags')->nullable()->after('category');
                }
                if (!Schema::hasColumn('documents', 'is_confidential')) {
                    $table->boolean('is_confidential')->default(false)->after('status');
                }
                if (!Schema::hasColumn('documents', 'expiry_date')) {
                    $table->date('expiry_date')->nullable()->after('signed_at');
                }
                if (!Schema::hasColumn('documents', 'parent_document_id')) {
                    $table->foreignId('parent_document_id')->nullable()->after('id')->constrained('documents')->nullOnDelete();
                }
            });
        }

        if (!Schema::hasTable('document_categories')) {
            Schema::create('document_categories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('icon')->nullable();
                $table->string('color')->default('gray');
                $table->text('description')->nullable();
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('document_categories')) {
            Schema::dropIfExists('document_categories');
        }

        if (Schema::hasTable('documents')) {
            Schema::table('documents', function (Blueprint $table) {
                if (Schema::hasColumn('documents', 'parent_document_id')) {
                    $table->dropConstrainedForeignId('parent_document_id');
                }
                if (Schema::hasColumn('documents', 'expiry_date')) {
                    $table->dropColumn('expiry_date');
                }
                if (Schema::hasColumn('documents', 'is_confidential')) {
                    $table->dropColumn('is_confidential');
                }
                if (Schema::hasColumn('documents', 'tags')) {
                    $table->dropColumn('tags');
                }
                if (Schema::hasColumn('documents', 'project_id')) {
                    $table->dropConstrainedForeignId('project_id');
                }
            });
        }
    }
};
