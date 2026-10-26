<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Request;

class AuditLogger
{
    /**
     * Log an audit event.
     */
    public static function log(?int $userId, string $action, ?string $ipAddress = null): void
    {
        AuditLog::create([
            'user_id' => $userId,
            'action' => $action,
            'ip_address' => $ipAddress ?? Request::ip(),
            'logged_at' => now(),
        ]);
    }

    /**
     * Log a login event.
     */
    public static function logLogin(int $userId): void
    {
        self::log($userId, 'login');
    }

    /**
     * Log a failed login event.
     */
    public static function logFailedLogin(?int $userId = null): void
    {
        self::log($userId, 'failed_login');
    }

    /**
     * Log a lockout event.
     */
    public static function logLockout(int $userId): void
    {
        self::log($userId, 'account_lockout');
    }

    /**
     * Log a registration event.
     */
    public static function logRegistration(int $userId): void
    {
        self::log($userId, 'registration');
    }

    /**
     * Log a payment event.
     */
    public static function logPayment(int $userId, string $status): void
    {
        self::log($userId, "payment_{$status}");
    }

    /**
     * Log a delivery status change.
     */
    public static function logDeliveryUpdate(int $userId, string $status): void
    {
        self::log($userId, "delivery_update_{$status}");
    }

    /**
     * Log a seller verification event.
     */
    public static function logSellerVerification(int $adminId, string $status): void
    {
        self::log($adminId, "seller_verification_{$status}");
    }

    /**
     * Log a user suspension event.
     */
    public static function logUserSuspension(int $adminId): void
    {
        self::log($adminId, 'user_suspension');
    }

    /**
     * Log a user reactivation event.
     */
    public static function logUserReactivation(int $adminId): void
    {
        self::log($adminId, 'user_reactivation');
    }

    /**
     * Log an access denied event.
     */
    public static function logAccessDenied(?int $userId = null): void
    {
        self::log($userId, 'access_denied');
    }
}
