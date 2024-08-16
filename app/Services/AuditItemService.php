<?php

namespace App\Services;

use App\Models\AuditItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class AuditItemService
{
    public static function createAuditItemForEvent(Model $model, string $eventText, ?int $teamId = null): AuditItem
    {

        $auditItem                    = new AuditItem();
        $auditItem->auditable_type    = get_class($model);
        $auditItem->auditable_id      = $model->id;
        $auditItem->auditable_text    = $eventText;
        $auditItem->auditable_team_id = $teamId;
        $auditItem->save();

        return $auditItem;
    }
}
