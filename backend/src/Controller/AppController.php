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
use Cake\Event\EventManager;
use Cake\Http\Exception\ForbiddenException;
use Cake\View\JsonView;

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
    public function viewClasses(): array
    {
        return [JsonView::class];
    }

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
        $this->loadComponent('Authentication.Authentication');

        // Global multi-tenancy filter (ponytail style)
        EventManager::instance()->on('Model.beforeFind', function ($event, $query, $options, $primary) {
            if (!$primary) {
                return;
            }
            $table = $event->getSubject();
            if ($table->hasField('branch_id')) {
                $identity = $this->Authentication->getIdentity();
                if ($identity && !self::isAdmin($identity)) {
                    $query->where([$table->aliasField('branch_id') => $identity->get('branch_id')]);
                }
            }
        });

        EventManager::instance()->on('Model.beforeSave', function ($event, $entity, $options) {
            $table = $event->getSubject();
            if ($table->hasField('branch_id')) {
                $identity = $this->Authentication->getIdentity();
                // Non-admins can only ever write into their own branch
                if ($identity && !self::isAdmin($identity)) {
                    $entity->branch_id = $identity->get('branch_id');
                }
            }
        });

        /*
         * Enable the following component for recommended CakePHP form protection settings.
         * see https://book.cakephp.org/5/en/controllers/components/form-protection.html
         */
        $this->loadComponent('FormProtection');
    }
    /**
     * Actions a non-admin user may reach, per controller. '*' allows every action.
     * Access is decided by controller/action only, never by response format.
     *
     * @var array<string, array<string>>
     */
    protected const NON_ADMIN_ACTIONS = [
        'Pages' => ['*'],
        'Users' => ['login', 'logout', 'loginOtp', 'requestOtp', 'verifyOtp'],
        'Transactions' => ['index', 'view', 'add', 'edit'],
        'Menus' => ['index', 'view'],
    ];

    /**
     * Whether the identity has the admin role (case-insensitive).
     *
     * @param \Authentication\IdentityInterface $identity Logged-in identity
     * @return bool
     */
    public static function isAdmin($identity): bool
    {
        return strtolower((string)$identity->get('role')) === 'admin';
    }

    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);

        $identity = $this->Authentication->getIdentity();
        if (!$identity || self::isAdmin($identity)) {
            return;
        }

        $controller = $this->request->getParam('controller');
        $action = $this->request->getParam('action');
        $allowedActions = self::NON_ADMIN_ACTIONS[$controller] ?? [];

        if (!in_array('*', $allowedActions, true) && !in_array($action, $allowedActions, true)) {
            if ($this->request->is('json')) {
                throw new ForbiddenException(__('You are not authorized to perform this action.'));
            }
            $this->Flash->error(__('You are not authorized to perform this action.'));

            return $this->redirect(['controller' => 'Pages', 'action' => 'pos']);
        }
    }
}
