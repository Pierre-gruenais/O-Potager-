<?php

namespace App\DataFixtures\Provider;

class AppProvider
{
    
    /**
     * available roles
     * @var array
     */
    private $roles = [
            "ROLE_USER","ROLE_ADMIN"
    ];
    
    /**
     * Get a role
     */
    public function role()
    {
        return $this->roles[array_rand($this->roles)];
    }
    
}
