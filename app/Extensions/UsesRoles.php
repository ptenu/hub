<?php

namespace App\Extensions;

use App\Models\Branch;

trait UsesRoles {
    // This basically only works for the Contact model, adding as a trait
    // for reasons of code separation, rather than re-use.

    public function hasRole(string $role, Branch $branch = null)
    {
        $methodName = 'check' . ucfirst($role) . 'Role';
        if (!method_exists($this, $methodName)) {
            return false;
        }

        try {
            return $this->$methodName($branch);
        } catch (\Exception) {
            return false;
        }
    }

    public function checkGuestRole(Branch $branch)
    {
        return auth()->guest();
    }

    public function checkUserRole(Branch $branch)
    {
        return auth()->check();
    }

}
