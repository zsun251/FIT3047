<?php
declare(strict_types=1);

namespace App\Policy;

use App\Policy\Resource\Page;
use Authorization\IdentityInterface;

/**
 * PagePolicy
 *
 * Handles authorization for non-entity pages such as dashboards.
 *
 * This policy is used when the resource is an instance of:
 * App\Policy\Resource\Page
 *
 * It evaluates access based on:
 *  - area (e.g. dashboard)
 *  - section (e.g. patient, staff)
 *
 * Business rules are delegated to UserAccess helper.
 */
class PagePolicy
{
    /**
     * Check if a user can access a page.
     *
     * This is the main authorization entry point.
     *
     * Rules:
     * - dashboard.patient → only patients
     * - dashboard.staff → staff (admin + clinician)
     * - default → denied
     *
     * @param IdentityInterface $user Authenticated user identity
     * @param Page $page Page resource being accessed
     * @return bool True if access is allowed
     */
    public function canAccess(IdentityInterface $user, Page $page): bool
    {
        return match ($page->area) {
            'dashboard' => match ($page->section) {
                'patient' => UserAccess::isPatient($user),
                'staff' => UserAccess::isStaff($user),
                default => false,
            },
            default => false,
        };
    }
}
