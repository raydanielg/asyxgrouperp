<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Phase 8:
     * - grns.warehouse_id — receiving warehouse for stock posting
     * - grn_items.product_id + lpo_items.product_id — reliable product mapping
     *   (NULL = unmapped legacy item → cannot post stock, LPO qty still applies)
     * - activate-contracts permission for contract activation endpoint
     */
    public function up(): void
    {
        Schema::table('grns', function (Blueprint $table) {
            $table->foreignId('warehouse_id')->nullable()->after('lpo_id')
                ->constrained('warehouses')->nullOnDelete();
        });

        Schema::table('grn_items', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->after('lpo_item_id')
                ->constrained('products')->nullOnDelete();
            $table->index('product_id');
        });

        Schema::table('lpo_items', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->after('lpo_id')
                ->constrained('products')->nullOnDelete();
            $table->index('product_id');
        });

        // Contract activation permission — grant to roles holding
        // create-crm-contracts so existing contract managers keep access.
        $id = DB::table('permissions')->where('name', 'activate-contracts')->value('id');
        if (!$id) {
            $id = DB::table('permissions')->insertGetId([
                'name' => 'activate-contracts', 'label' => 'activate-contracts',
                'module' => 'Contracts', 'created_at' => now(), 'updated_at' => now(),
            ]);
        }
        $roles = DB::table('role_permission')
            ->join('permissions', 'permissions.id', '=', 'role_permission.permission_id')
            ->where('permissions.name', 'create-crm-contracts')
            ->pluck('role_permission.role_id');
        foreach ($roles as $roleId) {
            if (!DB::table('role_permission')->where('role_id', $roleId)->where('permission_id', $id)->exists()) {
                DB::table('role_permission')->insert([
                    'role_id' => $roleId, 'permission_id' => $id,
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('grns', function (Blueprint $table) {
            $table->dropForeign(['warehouse_id']);
            $table->dropColumn('warehouse_id');
        });
        Schema::table('grn_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropIndex(['product_id']);
            $table->dropColumn('product_id');
        });
        Schema::table('lpo_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropIndex(['product_id']);
            $table->dropColumn('product_id');
        });
    }
};
