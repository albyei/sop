<?php
/**
 * @var \App\View\AppView $this
 */
?>
<style>
    .mobile-login-wrap {
        --paper:#FAF9F5;
        --paper-dim:#F1EFE7;
        --ink:#1E211C;
        --ink-soft:#6B7066;
        --line:#D8D4C8;
        --card:#FFFFFF;
        --green:#2F6F4F;
        --green-dark:#245A3E;
        --red:#C1272D;
        --red-bg:#FBE9E9;
        --safe-bottom: env(safe-area-inset-bottom, 0px);
        --safe-top: env(safe-area-inset-top, 0px);
        
        font-family: 'Inter', sans-serif;
        color: var(--ink);
        display: flex; flex-direction: column; justify-content: center;
        padding: 32px 24px calc(24px + var(--safe-bottom));
        padding-top: calc(48px + var(--safe-top));
        min-height: 100vh;
        background: var(--paper);
    }
    
    .mobile-login-wrap .brand-block { text-align: center; margin-bottom: 28px; }
    .mobile-login-wrap .brand-mark {
        width: 56px; height: 56px; border-radius: 16px;
        background: var(--green); color: #fff;
        display: flex; align-items: center; justify-content: center;
        font-family: 'Archivo Black', sans-serif; font-size: 1.4rem;
        margin: 0 auto 14px;
    }
    .mobile-login-wrap .brand-title { font-family: 'Archivo Black', sans-serif; font-size: 1.3rem; }
    .mobile-login-wrap .brand-sub {
        font-family: 'Inter', sans-serif; font-weight: 500; font-size: 0.78rem;
        letter-spacing: 0.1em; text-transform: uppercase; color: var(--ink-soft);
        margin-top: 5px;
    }
    
    .mobile-login-wrap .login-card {
        background: var(--card); border: 1px solid var(--line);
        border-radius: 20px; padding: 24px 20px;
    }
    .mobile-login-wrap .login-heading { font-weight: 700; font-size: 1.05rem; margin-bottom: 4px; }
    .mobile-login-wrap .login-subheading { font-size: 0.85rem; color: var(--ink-soft); margin-bottom: 20px; }
    
    .mobile-login-wrap .field { margin-bottom: 14px; }
    .mobile-login-wrap .field label { display: block; font-weight: 600; font-size: 0.8rem; margin-bottom: 6px; }
    .mobile-login-wrap .field-input-wrap { position: relative; }
    .mobile-login-wrap .field-input-wrap svg {
        position: absolute; left: 13px; top: 50%; transform: translateY(-50%);
        width: 17px; height: 17px; color: var(--ink-soft);
    }
    .mobile-login-wrap input.field-input {
        width: 100%; background: var(--paper-dim);
        border: 1.5px solid transparent; border-radius: 12px;
        padding: 12px 14px 12px 38px;
        font-family: 'Inter', sans-serif; font-size: 0.92rem; color: var(--ink);
        outline: none; box-sizing: border-box;
    }
    .mobile-login-wrap input.field-input:focus { border-color: var(--green); background: var(--card); }
    .mobile-login-wrap input.field-input::placeholder { color: var(--ink-soft); }
    
    .mobile-login-wrap .toggle-visibility {
        position: absolute; right: 6px; top: 50%; transform: translateY(-50%);
        border: none; background: transparent;
        color: var(--ink-soft); font-weight: 600; font-size: 0.72rem;
        padding: 6px 8px; cursor: pointer;
    }
    
    .mobile-login-wrap .login-btn-mobile {
        width: 100%; background: var(--green); color: #fff;
        border: none; border-radius: 999px;
        font-family: 'Inter', sans-serif; font-weight: 700; font-size: 0.95rem;
        padding: 14px 10px; margin-top: 6px; cursor: pointer;
        box-shadow: 0 4px 14px rgba(47,111,79,0.3);
        transition: transform .1s ease, opacity .15s ease;
    }
    .mobile-login-wrap .login-btn-mobile:active { transform: scale(0.98); }
    
    .mobile-login-wrap .demo-note {
        text-align: center; font-family: 'IBM Plex Mono', monospace;
        font-size: 0.72rem; color: var(--ink-soft); margin-top: 18px;
        padding: 10px 12px; background: var(--paper-dim); border-radius: 10px;
    }
    .mobile-login-wrap .footer-note {
        text-align: center; font-size: 0.72rem; color: var(--ink-soft); margin-top: 20px;
    }
</style>

<div class="mobile-login-wrap">
    <div class="brand-block">
        <div class="brand-mark">W</div>
        <div class="brand-title">Warung Kita</div>
        <div class="brand-sub">Point of Sale</div>
    </div>

    <div class="login-card">
        <div class="login-heading">Welcome back</div>
        <div class="login-subheading">Log in with your staff account to continue.</div>

        <?= $this->Form->create(null, ['id' => 'mobileLoginForm']) ?>
        <div class="field" id="usernameField">
            <label for="mobile-username">Username or Employee ID</label>
            <div class="field-input-wrap">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                <?= $this->Form->text('username', ['id' => 'mobile-username', 'class' => 'field-input', 'required' => true, 'placeholder' => 'e.g. citra.suryani']) ?>
            </div>
        </div>

        <div class="field" id="passwordField">
            <label for="mobile-password">Password</label>
            <div class="field-input-wrap">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="10" width="16" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
                <?= $this->Form->password('password', ['id' => 'mobile-password', 'class' => 'field-input', 'required' => true, 'placeholder' => 'Enter your password']) ?>
                <button type="button" class="toggle-visibility" id="togglePw">SHOW</button>
            </div>
        </div>

        <button type="submit" class="login-btn-mobile" id="mobileLoginBtn">Log In</button>
        <?= $this->Form->end() ?>
    </div>

    <div class="footer-note">Warung Kita POS &middot; v1.0</div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const togglePw = document.getElementById("togglePw");
        if (togglePw) {
            togglePw.addEventListener("click", () => {
                const passwordInput = document.getElementById("mobile-password");
                const isPw = passwordInput.type === "password";
                passwordInput.type = isPw ? "text" : "password";
                togglePw.textContent = isPw ? "HIDE" : "SHOW";
            });
        }
    });
</script>
