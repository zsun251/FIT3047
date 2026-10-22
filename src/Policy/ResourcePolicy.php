<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Resource;
use Authorization\IdentityInterface;

class ResourcePolicy
{
    private function isOwner(IdentityInterface $user, Resource $resource): bool
    {
        return UserAccess::isClinician($user) &&
               $resource->clinician_id === UserAccess::getClinicianId($user);
    }

    /**
     * Staff index — all staff can see the full list
     */
    public function canIndex(IdentityInterface $user, Resource $resource): bool
    {
        return UserAccess::isStaff($user);
    }

    /**
     * Staff detail view — admin sees all; clinician sees only their own
     */
    public function canView(IdentityInterface $user, Resource $resource): bool
    {
        return UserAccess::isAdmin($user) || $this->isOwner($user, $resource);
    }

    /**
     * Add — admin (selects author) or clinician (auto-assigned)
     */
    public function canAdd(IdentityInterface $user, Resource $resource): bool
    {
        return UserAccess::isStaff($user);
    }

    /**
     * Edit — admin can edit any; clinician only their own
     */
    public function canEdit(IdentityInterface $user, Resource $resource): bool
    {
        return UserAccess::isAdmin($user) || $this->isOwner($user, $resource);
    }

    /**
     * Delete — admin can delete any; clinician only their own
     */
    public function canDelete(IdentityInterface $user, Resource $resource): bool
    {
        return UserAccess::isAdmin($user) || $this->isOwner($user, $resource);
    }

    /**
     * Patient-facing read — staff and patients
     */
    public function canRead(IdentityInterface $user, Resource $resource): bool
    {
        return UserAccess::isStaff($user) || UserAccess::isPatient($user);
    }
}
