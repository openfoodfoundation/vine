<?php

namespace App\Enums;

enum VoucherSetMerchantTeamApprovalRequestStatus: string
{
    case READY    = 'ready';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
}
