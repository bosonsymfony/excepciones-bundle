<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace UCI\Boson\ExcepcionesBundle\Tests\Monolog\Formatter;

use UCI\Boson\ExcepcionesBundle\Monolog\Formatter\ExcepcionesFormatter;

class ExcepcionesFormatterTest extends \PHPUnit_Framework_TestCase
{
    public function testDefFormatWithString()
    {
        $formatter = new ExcepcionesFormatter(null, 'Y-m-d');
        $message = $formatter->format(array(
            'level_name' => 'WARNING',
            'channel' => 'log',
            'context' => array(),
            'message' => 'foo',
            'datetime' => new \DateTime,
            'extra' => array(),
        ));

        $string = "###############################################################\n###############################################################\n";
        $string = $string."[".date("Y-m-d")."] log.WARNING: foo  []\n";


        $this->assertEquals($string, $message);
    }

    public function testDefFormatWithArrayContext()
    {
        $formatter = new ExcepcionesFormatter(null, 'Y-m-d');
        $message = $formatter->format(array(
            'level_name' => 'ERROR',
            'channel' => 'meh',
            'message' => 'foo',
            'datetime' => new \DateTime,
            'extra' => array(),
            'context' => array(
                'foo' => 'bar',
                'baz' => 'qux',
                'bool' => false,
                'null' => null
            )
        ));

        $string = "###############################################################\n###############################################################\n";
        $string = $string."[".date("Y-m-d")."] meh.ERROR: foo   foo: bar\n"."  baz: qux\n"."  bool: \n"."  null: \n"." []\n";

        $this->assertEquals($string, $message);
    }

    }

class TestFoo
{
    public $foo = 'foo';
}

class TestBar
{
    public function __toString()
    {
        return 'bar';
    }
}
