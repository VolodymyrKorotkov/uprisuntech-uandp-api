<?php

namespace App\Enum;

enum EntityCrudVoteAttributeEnum: string
{
    case VIEW_ATTRIBUTE = 'EntityCrudIsGrantedListener.View';
    case EDIT_ATTRIBUTE = 'EntityCrudIsGrantedListener.Edit';
    case CREATE_ATTRIBUTE = 'EntityCrudIsGrantedListener.Create';
    case REMOVE_ATTRIBUTE = 'EntityCrudIsGrantedListener.Remove';

    public function isSaveAttribute(): bool
    {
        return $this === self::CREATE_ATTRIBUTE || $this === self::EDIT_ATTRIBUTE;
    }
}
