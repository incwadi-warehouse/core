<?php

namespace App\Tests\Service\Search;

use App\Service\Search\OrderBy;
use App\Service\Search\Validator;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;

class OrderByTest extends TestCase
{
    public function testOrderBy()
    {
        $validator = $this->getMockBuilder(Validator::class)
            ->disableOriginalConstructor()
            ->getMock();
        $validator->method('isValidField')
            ->willReturn(true);

        $qb = $this->getMockBuilder(QueryBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();
        $qb->method('orderBy')
            ->willReturn($qb);

        $orderBy = new OrderBy($validator);
        $orderBy->setFields(['test']);

        $this->assertInstanceOf(
            QueryBuilder::class,
            $orderBy->orderBy(
                $qb,
                ['field' => 'test', 'direction' => 'asc']
            )
        );
        $this->assertNull($orderBy->orderBy($qb, []));
        $this->assertEquals(
            'asc',
            $orderBy->getDirection(['direction' => 'asc'])
        );
        $this->assertEquals(
            'desc',
            $orderBy->getDirection(['direction' => 'desc'])
        );
        $this->assertEquals(
            'asc',
            $orderBy->getDirection([])
        );
        $this->assertEquals(
            'asc',
            $orderBy->getDirection(['direction' => 'test'])
        );
    }
}
