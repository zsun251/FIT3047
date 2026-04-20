<?php
declare(strict_types=1);

namespace App\Policy\Resource;

/**
 * Page resource used for authorization of non-ORM routes.
 *
 * This class represents logical application pages (e.g. dashboard sections)
 * that do not map to database entities.
 *
 * It is used by the Authorization plugin to evaluate access rules
 * for UI-level routes such as:
 *  - dashboard/patient
 *  - dashboard/staff
 *
 * Example usage:
 * ```
 * $page = new Page('dashboard', 'patient');
 * $this->Authorization->authorize($page, 'access');
 * ```
 */
class Page
{
    /**
     * Application area (e.g. dashboard, admin, public)
     */
    public string $area;

    /**
     * Section within the area (e.g. patient, staff, index)
     */
    public string $section;

    /**
     * Constructor
     *
     * @param string $area Main area of the application (e.g. dashboard)
     * @param string $section Sub-section or role-based view (default: index)
     */
    public function __construct(string $area, string $section = 'index')
    {
        $this->area = $area;
        $this->section = $section;
    }
}
