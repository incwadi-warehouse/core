<?php

namespace App\Tests\Service\Search;

use App\Service\Search\Filter;
use App\Service\Search\Validator;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Expr\Comparison;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;

class FilterTest extends TestCase
{
    public function testFilter()
    {
        $validator = $this->getMockBuilder(Validator::class)
            ->disableOriginalConstructor()
            ->getMock();
        $validator->method('isValidField')
            ->willReturn(true);
        $validator->method('isValidOperator')
            ->willReturn(true);

        $eq = $this->getMockBuilder(Comparison::class)
            ->disableOriginalConstructor()
            ->getMock();

        $expr = $this->getMockBuilder(Expr::class)
            ->disableOriginalConstructor()
            ->getMock();
        $expr->method('eq')
            ->willReturn($eq);

        $qb = $this->getMockBuilder(QueryBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();
        $qb->method('expr')
            ->willReturn($expr);

        $filter = new Filter($validator);
        $filter->setFields(['test']);

        $this->assertInstanceOf(
            Comparison::class,
            $filter->filter(
                $qb,
                ['field' => 'test', 'operator' => 'eq', 'value' => 'value']
            )
        );
    }
}
