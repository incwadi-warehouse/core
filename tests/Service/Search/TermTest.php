<?php

namespace App\Tests\Service\Search;

use App\Service\Search\Term;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Expr\Func;
use Doctrine\ORM\Query\Expr\Orx;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;

class TermTest extends TestCase
{
    public function testTerm()
    {
        $orx = $this->getMockBuilder(Orx::class)
            ->disableOriginalConstructor()
            ->getMock();

        $func = $this->getMockBuilder(Func::class)
            ->disableOriginalConstructor()
            ->getMock();

        $expr = $this->getMockBuilder(Expr::class)
            ->disableOriginalConstructor()
            ->getMock();
        $expr->method('orX')
            ->willReturn($orx);
        $expr->method('concat')
            ->willReturn($func);

        $qb = $this->getMockBuilder(QueryBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();
        $qb->method('expr')
            ->willReturn($expr);

        $term = new Term();

        $this->assertInstanceOf(Orx::class, $term->term($qb, 'term'));
        $this->assertNull($term->term($qb, null));
        $this->assertNull($term->term($qb, '%'));
        $this->assertNull($term->term($qb, '*'));
    }
}
