<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Controller\Controller;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link https://book.cakephp.org/5/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{


    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('FormProtection');`
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Flash');

        /*
         * Enable the following component for recommended CakePHP form protection settings.
         * see https://book.cakephp.org/5/en/controllers/components/form-protection.html
         */
        //$this->loadComponent('FormProtection');

        $this->loadComponent('Authentication.Authentication');
        $this->loadComponent('Authorization.Authorization');
    }


    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);

        $authIdentity = $this->Authentication->getIdentity();

        if ($authIdentity) {
            $userId = $authIdentity->getIdentifier();

            // Reload user with associations
            $usersTable = $this->fetchTable('Users');
            $userWithAssociations = $usersTable->get($userId, [
                'contain' => ['Patients', 'Clinicians', 'Admins']
            ]);

            // Create Authentication\Identity with the updated user entity
            $newAuthIdentity = new \Authentication\Identity($userWithAssociations);

            // Update Authentication identity
            $this->Authentication->setIdentity($newAuthIdentity);

            // Get the authorization service from the request attribute
            $authService = $this->request->getAttribute('authorization');

            if ($authService) {
                // Create new Authorization Identity wrapper with the Authentication Identity
                $newAuthzIdentity = new \Authorization\Identity($authService, $newAuthIdentity);

                // Replace the identity in the request
                $this->request = $this->request->withAttribute('identity', $newAuthzIdentity);
            }
        }
    }
}
