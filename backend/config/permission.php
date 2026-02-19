<?php

return [
    'models' => [
        'permission' => Spatie\Permission\Models\Permission::class,
        'role' => Spatie\Permission\Models\Role::class,
    ],

    'table_names' => [
        'roles' => 'roles',
        'permissions' => 'permissions',
        'model_has_permissions' => 'model_has_permissions',
        'model_has_roles' => 'model_has_roles',
        'role_has_permissions' => 'role_has_permissions',
    ],

    'column_names' => [
        'roles_table' => 'name',
        'permissions_table' => 'name',
        'model_morph_key' => 'model_id',
    ],

    'register_permission_check_method' => true,

    'register_role_check_method' => true,

    'scopes' => [],

    'providers' => [
        Spatie\Permission\PermissionServiceProvider::class,
    ],

    'enforce_for_namespaced_controllers' => false,

    'default_guard_name' => 'web',

    'role_names' => [
        'admin' => 'Administrator',
        'teacher' => 'Teacher',
        'student' => 'Student',
        'parent' => 'Parent',
    ],

    'permission_names' => [
        'create_users' => 'Create Users',
        'edit_users' => 'Edit Users',
        'delete_users' => 'Delete Users',
        'manage_classes' => 'Manage Classes',
        'manage_subjects' => 'Manage Subjects',
        'manage_sessions' => 'Manage Sessions',
        'create_assessments' => 'Create Assessments',
        'grade_assessments' => 'Grade Assessments',
    ],
];
