<?php
/**
 * Copyright (c) 2019 Keira Dueck <sylae@calref.net>
 * Use of this source code is governed by the MIT license, which
 * can be found in the LICENSE file.
 */

namespace PRT;


use Carbon\Carbon;
use CharlotteDunois\Collect\Collection;
use RangeException;

class PIRTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var PIR
     */
    public $pir;

    public function __construct()
    {
        parent::__construct();

        $nowDate = Carbon::now("America/Los_Angeles")->setTimezone("America/Los_Angeles")->setDate(2011, 7, 14);
        Carbon::setTestNow($nowDate);
        $this->pir = new PIR(0x1234, new Carbon("2010-01-01"), "XXX");
    }

    public function testHasRef()
    {
        $ref = new PIR(0x4567, new Carbon("2010-01-01"), "XXX");
        $this->assertFalse($this->pir->hasRef($ref));
        $this->pir->addRef($ref);
        $this->assertTrue($this->pir->hasRef($ref));
    }

    public function test__constructValid()
    {
        $this->assertInstanceOf(PIR::class, new PIR(0x1134, new Carbon("2010-01-01"), "XXX"));
    }

    public function test__constructInvalidIDLow()
    {
        $this->expectException(RangeException::class);
        new PIR(-1, new Carbon("2010-01-01"), "XXX");
    }

    public function test__constructInvalidIDHigh()
    {
        $this->expectException(RangeException::class);
        new PIR(0x10000, new Carbon("2010-01-01"), "XXX");
    }

    public function test__constructInvalidDate()
    {
        $this->expectException(RangeException::class);
        new PIR(0x1134, new Carbon("1980-01-01"), "XXX");
    }

    public function test__constructInvalidDeptLow()
    {
        $this->expectException(RangeException::class);
        new PIR(0x1134, new Carbon("2010-01-01"), "XX");
    }

    public function test__constructInvalidDeptHigh()
    {
        $this->expectException(RangeException::class);
        new PIR(0x1134, new Carbon("2010-01-01"), "XXXX");
    }

    public function testPirDB()
    {
        $this->assertInstanceOf(Collection::class, PIR::pirDB());
    }

    public function testGetSortNumber()
    {
        $lessID = new PIR(0x1233, new Carbon("2010-01-01"), "XXX");
        $moreID = new PIR(0x1235, new Carbon("2010-01-01"), "XXX");
        $lessDate = new PIR(0x1234, new Carbon("2009-01-01"), "XXX");
        $moreDate = new PIR(0x1234, new Carbon("2011-01-01"), "XXX");
        $this->assertGreaterThan($lessID->getSortNumber(), $this->pir->getSortNumber());
        $this->assertGreaterThan($lessDate->getSortNumber(), $this->pir->getSortNumber());
        $this->assertLessThan($moreID->getSortNumber(), $this->pir->getSortNumber());
        $this->assertLessThan($moreDate->getSortNumber(), $this->pir->getSortNumber());
    }

    public function testGetRefs()
    {
        $this->assertInstanceOf(Collection::class, $this->pir->getRefs());
        $ref = new PIR(0x1000, new Carbon("2010-01-01"), "XXX");
        $this->pir->addRef($ref);
        $this->assertInstanceOf(Collection::class, $this->pir->getRefs($ref));
    }

    public function testGetTag()
    {
        $this->assertEquals("XXX-2010-1234", $this->pir->getTag());
    }

    public function testGetLongTag()
    {
        $this->assertEquals("XXX-2010-1234 2010-01-01", $this->pir->getLongTag());
    }

    public function testAddRef()
    {
        $ref = new PIR(0x1235, new Carbon("2010-01-01"), "XXX");
        $this->assertFalse($this->pir->hasRef($ref));
        $this->pir->addRef($ref);
        $this->assertTrue($this->pir->hasRef($ref));
    }
}
