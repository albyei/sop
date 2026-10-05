<?php
declare(strict_types=1);

namespace App\Service;

use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\I18n\DateTime;
use Cake\Mailer\Mailer;
use Cake\Cache\Cache;
use Cake\Log\Log;
use Authentication\PasswordHasher\DefaultPasswordHasher;
use Exception;

class OtpService
{
    use LocatorAwareTrait;

    protected $LoginOtps;
    protected $Users;

    public function __construct()
    {
        $this->LoginOtps = $this->fetchTable('LoginOtps');
        $this->Users = $this->fetchTable('Users');
    }

    /**
     * @return array ['success' => bool, 'message' => string]
     */
    public function generateAndSendOtp(string $email): array
    {
        // 1. Check Cooldown / Rate Limit
        $remaining = $this->getCooldownRemaining($email);
        if ($remaining > 0) {
            return ['success' => false, 'message' => 'Please wait ' . $remaining . ' seconds before requesting another OTP.'];
        }

        // 2. Find Active User
        $user = $this->Users->find()
            ->where(['email' => $email, 'is_active' => true])
            ->first();

        // 3. Security constraint: Don't enumerate accounts. 
        // Always set cache and return generic success even if user doesn't exist
        $this->markRequest($email);

        if (!$user) {
            Log::info("OTP requested for non-existent or inactive email: $email");
            return ['success' => true, 'message' => 'If your email is registered and active, an OTP has been sent.'];
        }

        // 4. Generate 6 digit OTP securely
        $otpPlain = (string)random_int(100000, 999999);
        $hasher = new DefaultPasswordHasher();
        $otpHash = $hasher->hash($otpPlain);

        // 5. Save to database
        $loginOtp = $this->LoginOtps->newEmptyEntity();
        $loginOtp->user_id = $user->id;
        $loginOtp->otp_hash = $otpHash;
        $loginOtp->expires_at = (new DateTime())->addMinutes(5);
        $loginOtp->attempts = 0;

        if (!$this->LoginOtps->save($loginOtp)) {
            Log::error("Failed to save OTP for user_id: {$user->id}");
            return ['success' => false, 'message' => 'Internal server error occurred while processing OTP.'];
        }

        // 6. Send Email
        try {
            $mailer = new Mailer('default');
            $mailer->setTo($email)
                ->setSubject('Login Verification Code')
                ->deliver(
                    "Login Verification Code\n\n" .
                    "Your verification code is:\n\n" .
                    $otpPlain . "\n\n" .
                    "This code will expire in 5 minutes.\n\n" .
                    "If you did not request this code, you can ignore this email."
                );
            Log::info("OTP successfully sent to user_id: {$user->id}");
        } catch (Exception $e) {
            Log::error("Failed to send OTP email to user_id: {$user->id}. Error: " . $e->getMessage());
            // Since we can't send email, we might fail, but for enumeration we shouldn't reveal why it failed.
            // But we must let the legitimate user know.
            return ['success' => false, 'message' => 'Failed to send email. Please check configuration.'];
        }

        return ['success' => true, 'message' => 'If your email is registered and active, an OTP has been sent.'];
    }

    /**
     * @return array ['success' => bool, 'message' => string, 'user' => \App\Model\Entity\User|null]
     */
    public function verifyOtp(string $email, string $otpPlain): array
    {
        $user = $this->Users->find()
            ->where(['email' => $email, 'is_active' => true])
            ->first();

        if (!$user) {
            return ['success' => false, 'message' => 'Invalid or expired OTP.'];
        }

        // Find the latest unused OTP for this user
        $loginOtp = $this->LoginOtps->find()
            ->where(['user_id' => $user->id, 'used_at IS' => null])
            ->orderBy(['created' => 'DESC'])
            ->first();

        if (!$loginOtp) {
            return ['success' => false, 'message' => 'No active OTP found.'];
        }

        if ($loginOtp->attempts >= 5) {
            Log::warning("Max OTP attempts reached for user_id: {$user->id}");
            return ['success' => false, 'message' => 'Maximum attempts reached. Please request a new OTP.'];
        }

        $now = new DateTime();
        if ($loginOtp->expires_at < $now) {
            Log::info("Expired OTP attempted for user_id: {$user->id}");
            return ['success' => false, 'message' => 'OTP has expired.'];
        }

        $hasher = new DefaultPasswordHasher();
        if (!$hasher->check($otpPlain, $loginOtp->otp_hash)) {
            $loginOtp->attempts += 1;
            $this->LoginOtps->save($loginOtp);
            Log::info("Failed OTP verification for user_id: {$user->id}");
            return ['success' => false, 'message' => 'Invalid or expired OTP.'];
        }

        // Success
        $loginOtp->used_at = $now;
        $this->LoginOtps->save($loginOtp);
        Log::info("Successful OTP verification for user_id: {$user->id}");

        return ['success' => true, 'message' => 'OTP verified successfully.', 'user' => $user];
    }
    
    public function getCooldownRemaining(string $email): int
    {
        $cacheKey = 'otp_request_' . md5($email);
        $lastRequest = Cache::read($cacheKey, 'default');
        if ($lastRequest) {
            $elapsed = time() - (int)$lastRequest;
            if ($elapsed < 60) {
                return 60 - $elapsed;
            } else {
                // Expired manually since default cache might not have 60s TTL
                Cache::delete($cacheKey, 'default');
            }
        }
        return 0;
    }

    public function markRequest(string $email): void
    {
        $cacheKey = 'otp_request_' . md5($email);
        Cache::write($cacheKey, time(), 'default');
    }
}
