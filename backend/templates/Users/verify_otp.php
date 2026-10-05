<?php
/**
 * @var \App\View\AppView $this
 * @var string $email
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
        letter-spacing: 4px;
        font-weight: 700;
        text-align: center;
    }
    .mobile-login-wrap input.field-input:focus { border-color: var(--green); background: var(--card); }
    .mobile-login-wrap input.field-input::placeholder { color: var(--ink-soft); letter-spacing: 2px; font-weight: normal; }
    
    .mobile-login-wrap .login-btn-mobile {
        width: 100%; background: var(--green); color: #fff;
        border: none; border-radius: 999px;
        font-family: 'Inter', sans-serif; font-weight: 700; font-size: 0.95rem;
        padding: 14px 10px; margin-top: 6px; cursor: pointer;
        box-shadow: 0 4px 14px rgba(47,111,79,0.3);
        transition: transform .1s ease, opacity .15s ease;
    }
    .mobile-login-wrap .login-btn-mobile:active { transform: scale(0.98); }
    
    .mobile-login-wrap .footer-note {
        text-align: center; font-size: 0.72rem; color: var(--ink-soft); margin-top: 20px;
    }

    .resend-box { margin-top: 20px; text-align: center; font-size: 0.85rem; color: var(--ink-soft); }
    .resend-box form { display: inline; }
    .resend-btn { background: none; border: none; color: var(--green); font-weight: 600; cursor: pointer; text-decoration: none; padding: 0; font-family: 'Inter', sans-serif;}
    .resend-btn:disabled { color: var(--ink-soft); cursor: not-allowed; }
</style>

<div class="mobile-login-wrap">
    <div class="brand-block">
        <div class="brand-mark">W</div>
        <div class="brand-title">Warung Kita</div>
        <div class="brand-sub">Point of Sale</div>
    </div>

    <div class="login-card">
        <div class="login-heading">Verification Code</div>
        <div class="login-subheading">We have sent a 6-digit OTP code to <strong><?= h($email) ?></strong>.</div>

        <?= $this->Form->create(null, ['url' => ['action' => 'verifyOtp'], 'id' => 'verifyForm']) ?>
        <div class="field" id="otpField">
            <label for="mobile-otp">Enter OTP Code</label>
            <div class="field-input-wrap">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                <?= $this->Form->text('otp', ['id' => 'mobile-otp', 'class' => 'field-input', 'required' => true, 'placeholder' => '• • • • • •', 'maxlength' => 6, 'autocomplete' => 'one-time-code', 'inputmode' => 'numeric', 'pattern' => '[0-9]*']) ?>
            </div>
        </div>

        <button type="submit" class="login-btn-mobile">Verifikasi</button>
        <?= $this->Form->end() ?>

        <div class="resend-box" id="resendBox">
            Kirim ulang OTP dalam <span id="countdown">45</span> detik
        </div>
        
        <div class="resend-box" id="resendFormBox" style="display: none;">
            <?= $this->Form->create(null, ['url' => ['action' => 'requestOtp']]) ?>
            <?= $this->Form->hidden('email', ['value' => $email]) ?>
            Belum menerima kode? <button type="submit" class="resend-btn">Kirim Ulang</button>
            <?= $this->Form->end() ?>
        </div>

        <div style="margin-top: 16px; text-align: center;">
            <a href="<?= $this->Url->build(['action' => 'loginOtp']) ?>" style="font-family: 'Inter', sans-serif; font-size: 0.85rem; font-weight: 600; color: var(--ink-soft); text-decoration: none;">
                Ganti Email
            </a>
        </div>
    </div>

    <div class="footer-note">Warung Kita POS &middot; v1.0</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let timeLeft = 45; // Resend cooldown display
    const countdownEl = document.getElementById('countdown');
    const resendBox = document.getElementById('resendBox');
    const resendFormBox = document.getElementById('resendFormBox');

    const timer = setInterval(() => {
        timeLeft--;
        if(countdownEl) countdownEl.textContent = timeLeft;
        if(timeLeft <= 0) {
            clearInterval(timer);
            if(resendBox) resendBox.style.display = 'none';
            if(resendFormBox) resendFormBox.style.display = 'block';
        }
    }, 1000);
});
</script>
