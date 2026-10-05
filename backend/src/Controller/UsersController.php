<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{
    /**
     * Initialize controller
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->Authentication->allowUnauthenticated(['login', 'add', 'loginOtp', 'requestOtp', 'verifyOtp']);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->Users->find()
            ->contain(['Branches']);
        $users = $this->paginate($query);

        $this->set(compact('users'));
    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $user = $this->Users->get($id, contain: ['Branches', 'Transactions']);
        $this->set(compact('user'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $user = $this->Users->newEmptyEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $branches = $this->Users->Branches->find('list', limit: 200)->all();
        $this->set(compact('user', 'branches'));
    }

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $user = $this->Users->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $branches = $this->Users->Branches->find('list', limit: 200)->all();
        $this->set(compact('user', 'branches'));
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('The user has been deleted.'));
        } else {
            $this->Flash->error(__('The user could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function login()
    {
        $this->request->allowMethod(['get', 'post']);

        $result = $this->Authentication->getResult();
        if ($result && $result->isValid()) {
            $this->Flash->success(__('Login successful'));
            $user = $result->getData();
            
            // Standard redirect back or to role-based default
            $target = $this->Authentication->getLoginRedirect() ?? [
                'controller' => $user->role === 'admin' ? 'Transactions' : 'Pages',
                'action' => $user->role === 'admin' ? 'index' : 'pos'
            ];
            
            return $this->redirect($target);
        }

        if ($this->request->is('post')) {
            $this->Flash->error(__('Invalid username or password'));
        }
    }

    public function loginOtp()
    {
        $this->request->allowMethod(['get']);
        // Renders templates/Users/login_otp.php
    }

    public function requestOtp()
    {
        $this->request->allowMethod(['post']);
        $email = $this->request->getData('email');

        if (empty($email)) {
            $this->Flash->error(__('Please enter your email.'));
            return $this->redirect(['action' => 'loginOtp']);
        }

        $otpService = new \App\Service\OtpService();
        $result = $otpService->generateAndSendOtp($email);

        if (!$result['success']) {
            $this->Flash->error(__($result['message']));
            return $this->redirect(['action' => 'loginOtp']);
        }

        $this->request->getSession()->write('Otp.email', $email);
        $this->Flash->success(__($result['message']));
        return $this->redirect(['action' => 'verifyOtp']);
    }

    public function verifyOtp()
    {
        $this->request->allowMethod(['get', 'post']);
        $email = $this->request->getSession()->read('Otp.email');

        if (!$email) {
            $this->Flash->error(__('No active OTP session found. Please request again.'));
            return $this->redirect(['action' => 'loginOtp']);
        }

        if ($this->request->is('post')) {
            $otp = $this->request->getData('otp');
            $otpService = new \App\Service\OtpService();
            $result = $otpService->verifyOtp($email, $otp);

            if ($result['success']) {
                $user = $result['user'];
                
                // Clear session
                $this->request->getSession()->delete('Otp.email');
                
                // Programmatic Login using CakePHP Authentication
                $this->Authentication->setIdentity($user);
                $this->Flash->success(__($result['message']));

                $target = $this->Authentication->getLoginRedirect() ?? [
                    'controller' => $user->role === 'admin' ? 'Transactions' : 'Pages',
                    'action' => $user->role === 'admin' ? 'index' : 'pos'
                ];
                
                return $this->redirect($target);
            } else {
                $this->Flash->error(__($result['message']));
            }
        }
        
        $this->set(compact('email'));
    }

    /**
     * Logout method
     *
     * @return \Cake\Http\Response|null|void Redirects to login.
     */
    public function logout()
    {
        $result = $this->Authentication->getResult();
        if ($result->isValid()) {
            $this->Authentication->logout();
            return $this->redirect(['controller' => 'Users', 'action' => 'login']);
        }
    }
}
