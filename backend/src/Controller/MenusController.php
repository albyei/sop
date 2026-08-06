<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Menus Controller
 *
 * @property \App\Model\Table\MenusTable $Menus
 */
class MenusController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->Menus->find()
            ->contain(['Branches']);

        $identity = $this->Authentication->getIdentity();
        if ($identity && $identity->branch_id) {
            $query->where(['Menus.branch_id' => $identity->branch_id]);
        }

        if ($this->request->is('json')) {
            // API request from POS
            $query->where(['Menus.is_active' => true]);
            
            $category = $this->request->getQuery('category');
            if (!empty($category) && $category !== 'ALL') {
                $query->where(['Menus.category' => $category]);
            }

            $this->paginate = ['limit' => 24]; // 24 items per page for grid
            try {
                $menus = $this->paginate($query);
            } catch (\Cake\Http\Exception\NotFoundException $e) {
                $menus = [];
            }
            
            // Get all distinct categories for the filter tabs
            $catQuery = $this->Menus->find()
                ->select(['category'])
                ->where(['is_active' => true])
                ->whereNotNull('category')
                ->where(['category !=' => ''])
                ->distinct(['category']);
            if ($identity && $identity->branch_id) {
                $catQuery->where(['Menus.branch_id' => $identity->branch_id]);
            }
            $categories = $catQuery->all()->extract('category')->toArray();
            
            $paging = $this->request->getAttribute('paging');
            $this->set([
                'menus' => $menus,
                'categories' => array_values(array_unique($categories)),
                'paging' => $paging['Menus'] ?? null
            ]);
            $this->viewBuilder()->setOption('serialize', ['menus', 'categories', 'paging']);
        } else {
            // HTML request from Admin
            $menus = $this->paginate($query);
            $this->set(compact('menus'));
            $this->viewBuilder()->setOption('serialize', ['menus']);
        }
    }

    /**
     * View method
     *
     * @param string|null $id Menu id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $menu = $this->Menus->get($id, contain: ['Branches', 'TransactionItems']);
        $this->set(compact('menu'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $menu = $this->Menus->newEmptyEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            if (isset($data['price'])) {
                $data['price'] = preg_replace('/[^0-9]/', '', (string)$data['price']);
            }
            $menu = $this->Menus->patchEntity($menu, $data);
            if ($this->Menus->save($menu)) {
                $this->Flash->success(__('The menu has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The menu could not be saved. Please, try again.'));
        }
        $branches = $this->Menus->Branches->find('list', limit: 200)->all();
        $this->set(compact('menu', 'branches'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Menu id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $menu = $this->Menus->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            if (isset($data['price'])) {
                $data['price'] = preg_replace('/[^0-9]/', '', (string)$data['price']);
            }
            $menu = $this->Menus->patchEntity($menu, $data);
            if ($this->Menus->save($menu)) {
                $this->Flash->success(__('The menu has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The menu could not be saved. Please, try again.'));
        }
        $branches = $this->Menus->Branches->find('list', limit: 200)->all();
        $this->set(compact('menu', 'branches'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Menu id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $menu = $this->Menus->get($id);
        if ($this->Menus->delete($menu)) {
            $this->Flash->success(__('The menu has been deleted.'));
        } else {
            $this->Flash->error(__('The menu could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
