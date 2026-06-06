<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'customer.view',
            'customer.create',
            'customer.update',
            'product.view',
            'product.create',
            'product.update',
            'quotation.view',
            'quotation.create',
            'quotation.update',
            'quotation.convert',
            'sales-order.view',
            'sales-order.create',
            'sales-order.update',
            'invoice.view',
            'invoice.create',
            'invoice.update',
            'payment.view',
            'payment.create',
            'payment.update',
            'report.view',
            'stock.view',
            'stock.adjust',
            'warehouse.view',
            'warehouse.create',
            'warehouse.update',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum']);
        }

        $admin = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'sanctum']);
        $manager = Role::firstOrCreate(['name' => 'Manager', 'guard_name' => 'sanctum']);
        $sales = Role::firstOrCreate(['name' => 'Sales', 'guard_name' => 'sanctum']);
        $warehouse = Role::firstOrCreate(['name' => 'Warehouse', 'guard_name' => 'sanctum']);
        $finance = Role::firstOrCreate(['name' => 'Finance', 'guard_name' => 'sanctum']);

        $admin->syncPermissions(Permission::all());

        $manager->syncPermissions([
            'customer.view',
            'product.view',
            'quotation.view',
            'quotation.convert',
            'sales-order.view',
            'invoice.view',
            'payment.view',
            'report.view',
            'stock.view',
            'warehouse.view',
        ]);

        $sales->syncPermissions([
            'customer.view',
            'product.view',
            'quotation.view',
            'quotation.create',
            'quotation.update',
            'quotation.convert',
            'sales-order.view',
            'sales-order.create',
            'sales-order.update',
        ]);

        $warehouse->syncPermissions([
            'product.view',
            'stock.view',
            'stock.adjust',
            'warehouse.view',
            'warehouse.create',
            'warehouse.update',
        ]);

        $finance->syncPermissions([
            'invoice.view',
            'invoice.create',
            'invoice.update',
            'payment.view',
            'payment.create',
            'payment.update',
            'customer.view',
            'report.view',
        ]);
    }
}
