<?php

namespace App\Constants;

class NotificationType
{
    // Membership
    public const MEMBER_VERIFICATION_APPROVED = 'member.verification_approved';
    public const MEMBER_VERIFICATION_REJECTED = 'member.verification_rejected';
    public const MEMBER_RESUBMITTED = 'member.resubmitted';

    // Loans
    public const LOAN_REVIEW_STARTED = 'loan.review_started';
    public const LOAN_APPROVED = 'loan.approved';
    public const LOAN_REJECTED = 'loan.rejected';
    public const LOAN_DISBURSED = 'loan.disbursed';
    public const LOAN_STATUS_CHANGED = 'loan.status_changed';

    // Savings Payments
    public const SAVINGS_PAYMENT_APPROVED = 'savings_payment.approved';
    public const SAVINGS_PAYMENT_REJECTED = 'savings_payment.rejected';

    // Loan Payments
    public const LOAN_PAYMENT_APPROVED = 'loan_payment.approved';
    public const LOAN_PAYMENT_REJECTED = 'loan_payment.rejected';

    // System / Operational
    public const SYSTEM_ALERT = 'system.alert';
    public const SAVING_DUE_REMINDER = 'saving.due_reminder';
    public const SAVINGS_WITHDRAWAL = 'savings.withdrawal';
    public const LOAN_SUBMITTED = 'loan.submitted';
    public const INSTALLMENT_PAID = 'installment.paid';
    public const CLOSING_SUBMITTED = 'cashier.closing_submitted';
    public const CLOSING_VERIFIED = 'cashier.closing_verified';

    /**
     * Get all registered notification types.
     *
     * @return list<string>
     */
    public static function all(): array
    {
        return [
            self::MEMBER_VERIFICATION_APPROVED,
            self::MEMBER_VERIFICATION_REJECTED,
            self::MEMBER_RESUBMITTED,
            self::LOAN_REVIEW_STARTED,
            self::LOAN_APPROVED,
            self::LOAN_REJECTED,
            self::LOAN_DISBURSED,
            self::LOAN_STATUS_CHANGED,
            self::SAVINGS_PAYMENT_APPROVED,
            self::SAVINGS_PAYMENT_REJECTED,
            self::LOAN_PAYMENT_APPROVED,
            self::LOAN_PAYMENT_REJECTED,
            self::SYSTEM_ALERT,
            self::SAVING_DUE_REMINDER,
            self::SAVINGS_WITHDRAWAL,
            self::LOAN_SUBMITTED,
            self::INSTALLMENT_PAID,
            self::CLOSING_SUBMITTED,
            self::CLOSING_VERIFIED,
        ];
    }

    /**
     * Determine if a notification type string is valid.
     */
    public static function isValid(string $type): bool
    {
        return in_array($type, self::all(), true);
    }
}
