<?php

namespace Th3Mouk\FullCalendarBundle\Tests\Entity;

use Th3Mouk\FullCalendarBundle\Entity\Event;

class EventEntityTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructBasic()
    {
        $beginDatetime = new \DateTime('2012-01-01 00:00:00');
        $endDatetime = new \DateTime('2012-01-01 02:00:00');
        $eventTitle = 'Test Title 1';

        $eventMock = $this->getMockBuilder('Th3Mouk\FullCalendarBundle\Entity\Event')
            ->setConstructorArgs(array($eventTitle, $beginDatetime, $endDatetime))
            ->setMethods(null)
            ->getMock();

        $entityArray = $eventMock->toArray();

        $arrayCheck = array(
            'start' => date("Y-m-d\TH:i:sP", strtotime('2012-01-01 00:00:00')),
            'end' => date("Y-m-d\TH:i:sP", strtotime('2012-01-01 02:00:00')),
            'title' => 'Test Title 1',
            'allDay' => false,
        );

        $this->assertEquals($entityArray, $arrayCheck);
    }

    public function testConstructAllDay()
    {
        $beginDatetime = new \DateTime('2012-01-01 00:00:00');
        $endDatetime = new \DateTime('2012-01-01 02:00:00');
        $eventTitle = 'Test Title 1';

        $eventMock = $this->getMockBuilder('Th3Mouk\FullCalendarBundle\Entity\Event')
            ->setConstructorArgs(array($eventTitle, $beginDatetime, $endDatetime, true))
            ->setMethods(null)
            ->getMock();

        $entityArray = $eventMock->toArray();

        $arrayCheck = array(
            'start' => date("Y-m-d\TH:i:sP", strtotime('2012-01-01 00:00:00')),
            'end' => date("Y-m-d\TH:i:sP", strtotime('2012-01-01 02:00:00')),
            'title' => 'Test Title 1',
            'allDay' => true,
        );

        $this->assertEquals($entityArray, $arrayCheck);
    }

    public function testNonStandardFields()
    {
        $event = new Event('Test', new \DateTime('2012-01-01 00:00:00'), new \DateTime('2012-01-01 01:00:00'));
        $event->addField('description', 'Event descriptions');

        $expectedArray = array(
            'title' => 'Test',
            'start' => date("Y-m-d\TH:i:sP", strtotime('2012-01-01 00:00:00')),
            'end' => date("Y-m-d\TH:i:sP", strtotime('2012-01-01 01:00:00')),
            'allDay' => false,
            'description' => 'Event descriptions',
        );

        $this->assertEquals($event->toArray(), $expectedArray);
    }
}
