<?php

namespace App\Tests\Service\Search;

use App\Service\Search\Validator;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
    public function testValidator()
    {
        $validator = new Validator();
        $validator->setFields(['test1']);

        $this->assertTrue($validator->isValidField('test1'));
        $this->assertFalse($validator->isValidField('test2'));

        $this->assertTrue($validator->isValidOperator('eq'));
        $this->assertFalse($validator->isValidOperator('test'));
    }
}
