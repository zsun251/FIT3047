<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Symptom;
use Authorization\IdentityInterface;

class SymptomPolicy
{

    /**
     * View symptom
     */
    public function canView(IdentityInterface $user, Symptom $resource): bool
    {
        return UserAccess::isStaff($user);
    }

    /**
     * Add symptom
     */
    public function canAdd(IdentityInterface $user, Symptom $resource): bool
    {
        return UserAccess::isStaff($user);
    }

    /**
     * Edit symptom
     */
    public function canEdit(IdentityInterface $user, Symptom $resource): bool
    {
        return UserAccess::isStaff($user);
    }

    /**
     * Delete symptom
     */
    public function canDelete(IdentityInterface $user, Symptom $resource): bool
    {
        return UserAccess::isStaff($user);
    }

    /**
     * List symptoms
     */
    public function canIndex(IdentityInterface $user, Symptom $resource): bool
    {
        return UserAccess::isStaff($user);
    }
}
