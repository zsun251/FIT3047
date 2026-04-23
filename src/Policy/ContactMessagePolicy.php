<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\ContactMessage;
use Authorization\IdentityInterface;

class ContactMessagePolicy
{

    /**
     * View contact message
     */
    public function canView(IdentityInterface $user, ContactMessage $resource): bool
    {
        return UserAccess::isStaff($user);
    }

    /**
     * Add contact message
     */
    public function canAdd(IdentityInterface $user, ContactMessage $resource): bool
    {
        return UserAccess::isStaff($user);
    }

    /**
     * Edit contact message
     */
    public function canEdit(IdentityInterface $user, ContactMessage $resource): bool
    {
        return UserAccess::isStaff($user);
    }

    /**
     * Delete contact message
     */
    public function canDelete(IdentityInterface $user, ContactMessage $resource): bool
    {
        return UserAccess::isStaff($user);
    }

    /**
     * List contact messages
     */
    public function canIndex(IdentityInterface $user, ContactMessage $resource): bool
    {
        return UserAccess::isStaff($user);
    }
}
