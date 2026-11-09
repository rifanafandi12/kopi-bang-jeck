<?php

namespace App\Enums;

enum OrderEventType: string
{
    case QrScanned = 'qr_scanned';
    case DataDiriSubmitted = 'data_diri_submitted';
    case FirstItemAdded = 'first_item_added';
    case OrderConfirmed = 'order_confirmed';
    case AcceptedByCashier = 'accepted_by_cashier';
    case Preparing = 'preparing';
    case Ready = 'ready';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case PaymentInitiated = 'payment_initiated';
    case PaymentPaid = 'payment_paid';
    case PaymentFailed = 'payment_failed';
    case PaymentExpired = 'payment_expired';
}
