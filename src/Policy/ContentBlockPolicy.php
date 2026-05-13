<?php
declare(strict_types=1);

namespace App\Policy;

use Authorization\IdentityInterface;
use ContentBlocks\Model\Entity\ContentBlock;

class ContentBlockPolicy
{
    /**
     * List content blocks
     */
    public function canIndex(IdentityInterface $user, ContentBlock $contentBlock): bool
    {
        return UserAccess::isAdmin($user);
    }

    /**
     * View content block
     */
    public function canView(IdentityInterface $user, ContentBlock $contentBlock): bool
    {
        return UserAccess::isAdmin($user);
    }

    /**
     * Add content block
     */
    public function canAdd(IdentityInterface $user, ContentBlock $contentBlock): bool
    {
        return UserAccess::isAdmin($user);
    }

    /**
     * Edit content block
     */
    public function canEdit(IdentityInterface $user, ContentBlock $contentBlock): bool
    {
        return UserAccess::isAdmin($user);
    }

    /**
     * Delete content block
     */
    public function canDelete(IdentityInterface $user, ContentBlock $contentBlock): bool
    {
        return UserAccess::isAdmin($user);
    }

    /**
     * Restore previous content block value
     */
    public function canRestore(IdentityInterface $user, ContentBlock $contentBlock): bool
    {
        return UserAccess::isAdmin($user);
    }

    /**
     * Upload content block image
     */
    public function canUpload(IdentityInterface $user, ContentBlock $contentBlock): bool
    {
        return UserAccess::isAdmin($user);
    }
}
