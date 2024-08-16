<?php

namespace App\Services;

use App\Models\AuditItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class AuditItemService
{
    public static function createAuditItemForEvent(User $actioningUser, Model $model, string $eventText): AuditItem
    {
        $teamId = null;

        if ($model->hasAttribute('team_id')) {
            $teamId = $model->team_id;
        }

        if ($model->hasAttribute('current_team_id')) {
            $teamId = $model->current_team_id;
        }

        $auditItem                    = new AuditItem();
        $auditItem->auditable_type    = get_class($model);
        $auditItem->auditable_id      = $model->id;
        $auditItem->auditable_text    = $eventText;
        $auditItem->auditable_team_id = $teamId;
        $auditItem->actioning_user_id = $actioningUser->id;
        $auditItem->save();

        return $auditItem;
    }
}
