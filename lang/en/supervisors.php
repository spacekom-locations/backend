<?php

$authLang = require_once __DIR__.DIRECTORY_SEPARATOR.'supervisors'.DIRECTORY_SEPARATOR.'auth.php';

$global = [
    'not_found'  => 'The supervisor ( :user_name ) is not a registered supervisor',  
    'supervisor_created' => 'supervisor created successfully',
    'in_active_account' => 'this is not an active account please contact administrator',
    'activated_successfully' => 'Supervisor Account Activated Successfully',
    'suspended_successfully' => 'Supervisor Account Suspended Successfully',
    'updated_successfully' => 'Supervisor Data has been Updated Successfully',
    'soft_deleted_successfully' => 'Supervisor has been moved to trash',
    'permanent_deleted_successfully' => 'Supervisor has been removed permanently',
];


return $global + $authLang ;