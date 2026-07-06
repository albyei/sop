<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Enum\TransactionStatus;
use Cake\Log\Log;

/**
 * Transactions Controller
 *
 * @property \App\Model\Table\TransactionsTable $Transactions
 */
class TransactionsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->Transactions->find()
            ->contain(['Branches', 'Users', 'TransactionItems' => ['Menus']])
            ->order(['Transactions.created' => 'DESC']);
            
        $status = $this->request->getQuery('status');
        if ($status && $status !== 'all') {
            $query->where(['Transactions.status' => $status]);
        }
        
        $search = $this->request->getQuery('search');
        if ($search) {
            // Include $search directly since CakePHP uses bound parameters for array conditions
            $conditions = ['Transactions.customer_name LIKE' => "%$search%"];
            if (is_numeric($search)) {
                $conditions['Transactions.id'] = (int)$search;
            }
            $query->where(['OR' => $conditions]);
        }
            
        $transactions = $this->paginate($query);

        // Fetch counts for the tabs
        $statusCounts = [
            'all' => $this->Transactions->find()->count(),
            'pending' => $this->Transactions->find()->where(['status' => 'pending'])->count(),
            'paid' => $this->Transactions->find()->where(['status' => 'paid'])->count(),
        ];

        $this->set(compact('transactions', 'statusCounts'));
    }

    /**
     * View method
     *
     * @param string|null $id Transaction id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $transaction = $this->Transactions->get($id, contain: ['Branches', 'Users', 'TransactionItems' => ['Menus']]);
        $this->set(compact('transaction'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $transaction = $this->Transactions->newEmptyEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            // Fallback for user_id if not set by frontend
            $identity = $this->Authentication->getIdentity();
            if ($identity) {
                $data['user_id'] = $data['user_id'] ?? $identity->id;
                $data['branch_id'] = $data['branch_id'] ?? $identity->branch_id;
                // Fallback for users (like superadmin) who might not have a branch_id assigned
                if (empty($data['branch_id'])) {
                    $data['branch_id'] = 1;
                }
            }

            // Recalculate prices and totals to prevent client-side tampering
            if (!empty($data['transaction_items']) && is_array($data['transaction_items'])) {
                $menusTable = $this->fetchTable('Menus');
                $calculatedTotal = 0;
                
                foreach ($data['transaction_items'] as &$item) {
                    if (!empty($item['menu_id'])) {
                        $menu = $menusTable->get($item['menu_id']);
                        $qty = (int)($item['qty'] ?? 1);
                        
                        $item['price'] = $menu->price;
                        $item['subtotal'] = $menu->price * $qty;
                        $calculatedTotal += $item['subtotal'];
                    }
                }
                
                $data['total'] = $calculatedTotal;
                
                // Adjust change_amount based on actual calculated total for POS payments
                if (($data['status'] ?? '') === TransactionStatus::PAID->value && isset($data['paid_amount'])) {
                    $paidAmount = (int)$data['paid_amount'];
                    if ($paidAmount >= $calculatedTotal) {
                        $data['change_amount'] = $paidAmount - $calculatedTotal;
                    } else {
                        // Prevent saving as paid if paid_amount is insufficient for actual total
                        $data['status'] = TransactionStatus::PENDING->value;
                        $data['change_amount'] = 0;
                    }
                }
            }

            $transaction = $this->Transactions->patchEntity($transaction, $data, [
                'associated' => ['TransactionItems']
            ]);
            
            $isPaid = ($transaction->status === TransactionStatus::PAID->value);

            if ($this->Transactions->save($transaction)) {
                $userId = $identity ? $identity->id : 'Guest';
                $actionStr = $isPaid ? 'paid and completed' : 'created as pending';
                Log::info(sprintf('Transaction #%s was %s by User ID %s. Total: Rp %s', $transaction->id, $actionStr, $userId, $transaction->total), ['scope' => ['transactions']]);

                if ($this->request->is('json')) {
                    $this->set(['success' => true, 'transaction' => $transaction]);
                    $this->viewBuilder()->setOption('serialize', ['success', 'transaction']);
                    return;
                }
                $this->Flash->success(__('The transaction has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            if ($this->request->is('json')) {
                $this->set(['success' => false, 'errors' => $transaction->getErrors()]);
                $this->viewBuilder()->setOption('serialize', ['success', 'errors']);
                return;
            }
            $this->Flash->error(__('The transaction could not be saved. Please, try again.'));
        }
        $branches = $this->Transactions->Branches->find('list', limit: 200)->all();
        $users = $this->Transactions->Users->find('list', limit: 200)->all();
        $this->set(compact('transaction', 'branches', 'users'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Transaction id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $transaction = $this->Transactions->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $transaction = $this->Transactions->patchEntity($transaction, $this->request->getData());
            if ($this->Transactions->save($transaction)) {
                $this->Flash->success(__('The transaction has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The transaction could not be saved. Please, try again.'));
        }
        $branches = $this->Transactions->Branches->find('list', limit: 200)->all();
        $users = $this->Transactions->Users->find('list', limit: 200)->all();
        $this->set(compact('transaction', 'branches', 'users'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Transaction id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $transaction = $this->Transactions->get($id);
        if ($this->Transactions->delete($transaction)) {
            $this->Flash->success(__('The transaction has been deleted.'));
        } else {
            $this->Flash->error(__('The transaction could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
