<?php
/**
 * @var \App\View\AppView $this
 */
?>
<style>
    body { 
        justify-content: center; 
        align-items: center; 
    }
    .login-container {
        /* ponytail: minimal login card */
        background: var(--surface);
        padding: 2.5rem;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-md);
        width: 100%;
        max-width: 400px;
        border: 1px solid var(--border);
    }
    .login-container .logo {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        margin-bottom: 2rem;
    }
    .form-group { margin-bottom: 1.25rem; }
    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        font-size: 0.9rem;
        color: var(--text-muted);
    }
    .form-group input {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        font-size: 1rem;
        outline: none;
        transition: var(--transition);
        background: var(--bg);
        color: var(--text-main);
    }
    .form-group input::placeholder { color: var(--text-muted); }
    .form-group input:focus {
        border-color: var(--primary);
        background: var(--surface);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }
    .btn-login {
        width: 100%;
        padding: 0.875rem;
        background: var(--primary);
        color: white;
        border: none;
        border-radius: var(--radius-md);
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: var(--transition);
        margin-top: 1rem;
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.3);
    }
    .btn-login:hover {
        background: var(--primary-hover);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
    }
</style>

<div class="login-container">
    <div class="logo">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8h1a4 4 0 0 1 0 8h-1"></path><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"></path><line x1="6" y1="1" x2="6" y2="4"></line><line x1="10" y1="1" x2="10" y2="4"></line><line x1="14" y1="1" x2="14" y2="4"></line></svg>
        Cafe Cashier
    </div>

    <?= $this->Form->create() ?>
    <div class="form-group">
        <label for="username">Username</label>
        <?= $this->Form->text('username', ['id' => 'username', 'required' => true, 'placeholder' => 'Enter your username']) ?>
    </div>
    
    <div class="form-group">
        <label for="password">Password</label>
        <?= $this->Form->password('password', ['id' => 'password', 'required' => true, 'placeholder' => 'Enter your password']) ?>
    </div>
    
    <button type="submit" class="btn-login">Login</button>
    <?= $this->Form->end() ?>
</div>
