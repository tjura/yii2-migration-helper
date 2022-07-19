<?php

namespace tjura\migration\enums;

enum AvailableCommandsEnum: string
{
    case CREATE_TABLE = '1';
    case DROP_TABLE = '2';
    case ADD_COLUMN = '3';
    case DROP_COLUMN = '4';
    case ADD_JUNCTION_TABLE = '5';
    case REDO_LAST = '6';
    case DOWN_LAST = '7';
    case CREATE_EMPTY_MIGRATION_FILE = '8';
    case UP = '9';

    public static function getLabels(): array
    {
        $result = [];
        foreach (self::cases() as $enum) {
            $result[$enum->value] = $enum->name;
        }

        return $result;
    }
}