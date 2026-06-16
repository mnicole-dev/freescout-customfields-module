<?php

namespace Modules\CustomFields\Services;

class CustomFieldService
{
    const TYPES = ['dropdown', 'multiselect', 'singleline', 'multiline', 'tags', 'number', 'date'];
    const MULTI_TYPES = ['multiselect', 'tags'];

    public static function isMultiValue(string $type): bool
    {
        return in_array($type, self::MULTI_TYPES, true);
    }

    /** Raw form input → DB string (or null to clear). Invalid input → null, never throws. */
    public static function serialize(string $type, $input): ?string
    {
        switch ($type) {
            case 'number':
                if ($input === null || $input === '') {
                    return null;
                }
                return is_numeric($input) ? (string) (0 + $input) : null;

            case 'date':
                if (!is_string($input) || $input === '') {
                    return null;
                }
                $d = \DateTime::createFromFormat('Y-m-d', $input);
                return ($d && $d->format('Y-m-d') === $input) ? $input : null;

            case 'multiselect':
            case 'tags':
                if (is_array($input)) {
                    $items = $input;
                } elseif (is_string($input) && $input !== '') {
                    $items = explode(',', $input);
                } else {
                    $items = [];
                }
                $items = array_values(array_filter(
                    array_map(fn ($v) => trim((string) $v), $items),
                    fn ($v) => $v !== ''
                ));
                return $items ? json_encode($items) : null;

            default: // dropdown, singleline, multiline
                $s = is_string($input) ? trim($input) : '';
                return $s === '' ? null : $s;
        }
    }

    /** DB string → value for the view: array for multi types, string otherwise, null/[] if empty. */
    public static function deserialize(string $type, ?string $stored)
    {
        if ($stored === null || $stored === '') {
            return self::isMultiValue($type) ? [] : null;
        }
        if (self::isMultiValue($type)) {
            $arr = json_decode($stored, true);
            return is_array($arr) ? $arr : [];
        }
        return $stored;
    }

    /** Parse a definition's options textarea (one per line) → de-duplicated ordered array. */
    public static function parseOptions(?string $raw): array
    {
        if (!$raw) {
            return [];
        }
        $lines = array_map('trim', preg_split('/\r\n|\r|\n/', $raw));
        $out = [];
        foreach ($lines as $line) {
            if ($line !== '' && !in_array($line, $out, true)) {
                $out[] = $line;
            }
        }
        return $out;
    }

    /** A field applies to a mailbox if it targets all mailboxes, or the mailbox is in its assigned list. */
    public static function appliesToMailbox(bool $allMailboxes, int $mailboxId, array $assignedMailboxIds): bool
    {
        if ($allMailboxes) {
            return true;
        }
        return in_array($mailboxId, array_map('intval', $assignedMailboxIds), true);
    }

    /** Form selection → mailbox ids to sync into the pivot ([] when "all mailboxes"). */
    public static function normalizeMailboxSelection(bool $allMailboxes, $mailboxIds): array
    {
        if ($allMailboxes || !is_array($mailboxIds)) {
            return [];
        }
        $ids = array_map('intval', array_filter($mailboxIds, fn ($v) => $v !== '' && $v !== null));
        return array_values(array_unique($ids));
    }
}
