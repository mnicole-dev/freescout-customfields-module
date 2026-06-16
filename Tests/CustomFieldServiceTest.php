<?php

namespace Modules\CustomFields\Tests;

use Modules\CustomFields\Services\CustomFieldService;
use PHPUnit\Framework\TestCase;

class CustomFieldServiceTest extends TestCase
{
    public function test_singleline_trims_and_empties_to_null(): void
    {
        $this->assertSame('hello', CustomFieldService::serialize('singleline', '  hello '));
        $this->assertNull(CustomFieldService::serialize('singleline', '   '));
        $this->assertSame('hello', CustomFieldService::deserialize('singleline', 'hello'));
        $this->assertNull(CustomFieldService::deserialize('singleline', null));
    }

    public function test_number_accepts_numeric_only(): void
    {
        $this->assertSame('42', CustomFieldService::serialize('number', '42'));
        $this->assertSame('3.5', CustomFieldService::serialize('number', '3.5'));
        $this->assertNull(CustomFieldService::serialize('number', 'abc'));
        $this->assertNull(CustomFieldService::serialize('number', ''));
    }

    public function test_date_accepts_valid_iso_only(): void
    {
        $this->assertSame('2026-06-16', CustomFieldService::serialize('date', '2026-06-16'));
        $this->assertNull(CustomFieldService::serialize('date', '16/06/2026'));
        $this->assertNull(CustomFieldService::serialize('date', '2026-13-40'));
    }

    public function test_multiselect_and_tags_roundtrip_json(): void
    {
        $this->assertSame('["a","b"]', CustomFieldService::serialize('multiselect', ['a', ' b ', '']));
        $this->assertSame('["x","y"]', CustomFieldService::serialize('tags', 'x, y ,'));
        $this->assertNull(CustomFieldService::serialize('tags', []));
        $this->assertSame(['a', 'b'], CustomFieldService::deserialize('multiselect', '["a","b"]'));
        $this->assertSame([], CustomFieldService::deserialize('tags', null));
    }

    public function test_parse_options_splits_lines(): void
    {
        $this->assertSame(['Low', 'High'], CustomFieldService::parseOptions("Low\r\n High \n\nHigh"));
        $this->assertSame([], CustomFieldService::parseOptions(null));
    }

    public function test_is_multi_value(): void
    {
        $this->assertTrue(CustomFieldService::isMultiValue('multiselect'));
        $this->assertTrue(CustomFieldService::isMultiValue('tags'));
        $this->assertFalse(CustomFieldService::isMultiValue('dropdown'));
    }
}
