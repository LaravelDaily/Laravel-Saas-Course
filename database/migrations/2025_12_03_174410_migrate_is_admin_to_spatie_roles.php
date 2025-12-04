<?php

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        foreach (RoleEnum::cases() as $roleEnum) {
            Role::firstOrCreate(['name' => $roleEnum->value, 'guard_name' => 'web']);
        }

        User::query()->each(function (User $user) {
            $roleName = $user->is_admin ? RoleEnum::Admin->value : RoleEnum::User->value;
            $user->assignRole($roleName);
        });

        Schema::table('invitations', function (Blueprint $table) {
            $table->string('role')->default(RoleEnum::User->value)->after('email');
        });

        DB::table('invitations')->where('is_admin', true)->update(['role' => RoleEnum::Admin->value]);
        DB::table('invitations')->where('is_admin', false)->update(['role' => RoleEnum::User->value]);

        Schema::table('invitations', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('organization_id');
        });

        User::query()->each(function (User $user) {
            $user->is_admin = $user->hasRole(RoleEnum::Admin->value);
            $user->save();
        });

        Schema::table('invitations', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('email');
        });

        DB::table('invitations')->where('role', RoleEnum::Admin->value)->update(['is_admin' => true]);
        DB::table('invitations')->where('role', '!=', RoleEnum::Admin->value)->update(['is_admin' => false]);

        Schema::table('invitations', function (Blueprint $table) {
            $table->dropColumn('role');
        });

        foreach (RoleEnum::cases() as $roleEnum) {
            Role::where('name', $roleEnum->value)->delete();
        }
    }
};
