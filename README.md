CalendarBundle - jQuery fullcalendar bundle.
===============

This bundle allows you to integrate the [jQuery FullCalendar](http://arshaw.com/fullcalendar/) plugin into your Symfony2 application.

Once installed, this bundle will use event listeners to load events from any bundle in your application.

Installation
------------

Before installing, please note that this bundle has a dependency on the [FOSJsRouting](https://github.com/FriendsOfSymfony/FOSJsRoutingBundle) bundle to expose the calendar AJAX event loader route.  Please ensure that the FOSJsRouting bundle is installed and configured before continuing.

### Through Composer:

```sh
composer require adesigns/calendar-bundle
```

Register the bundle in `app/AppKernel.php`:

``` php
// app/AppKernel.php

public function registerBundles()
{
    return array(
        // ...
        new Th3Mouk\FullCalendarBundle\Th3MoukFullCalendarBundle(),
    );
}
```

Register the routing in `app/config/routing.yml`:

``` yml
# app/config/routing.yml

th3mouk_planning:
    resource: "@Th3MoukFullCalendarBundle/Controller/"
    type:     annotation
```

Publish the assets:

    $ php app/console assets:install web
    
Usage
-----

Add the required stylesheet and javascripts to your layout:

Stylesheet:    
```
<link rel="stylesheet" href="{{ asset('bundles/th3moukfullcalendar/css/fullcalendar/fullcalendar.min.css') }}" />
```    
Javascript:
```
<script type="text/javascript" src="{{ asset('bundles/th3moukfullcalendar/js/jquery/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('bundles/th3moukfullcalendar/js/moment/moment-with-locales.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('bundles/th3moukfullcalendar/js/fullcalendar/fullcalendar.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('bundles/th3moukfullcalendar/js/calendar-settings.js') }}"></script>
```    
Then, in the template where you wish to display the calendar, add the following twig:

```
{% include 'Th3MoukFullCalendarBundle::calendar.html.twig' %}
```   

Adding Events
-------------    

The best part about this bundle is that you can add events to the calendar from any part of your application.  The calendar loads events via AJAX, and dispatches an event to load calendar events from your application.

When a request is made to load events for a given start/end time, the bundle dispatches a `calendar.load_events` event.  Adding event listeners is an easy 2 step process

Create an Event Listener class in your bundle:

``` php
// src/Acme/DemoBundle/EventListener/CalendarEventListener.php  
	
namespace Acme\DemoBundle\EventListener;

use Th3Mouk\FullCalendarBundle\Event\CalendarEvent;
use Th3Mouk\FullCalendarBundle\Entity\Event;
use Doctrine\ORM\EntityManager;

class CalendarEventListener
{
	private $entityManager;
	
	public function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
	}
	
	public function loadEvents(CalendarEvent $calendarEvent)
	{
		$startDate = $calendarEvent->getStartDatetime();
		$endDate = $calendarEvent->getEndDatetime();

		// The original request so you can get filters from the calendar
        // Use the filter in your query for example

     	$request = $calendarEvent->getRequest();
        $filter = $request->get('filter');


		// load events using your custom logic here,
		// for instance, retrieving events from a repository

		$companyEvents = $this->entityManager->getRepository('AcmeDemoBundle:MyCompanyEvents')
			              ->createQueryBuilder('company_events')
			              ->where('company_events.event_datetime BETWEEN :startDate and :endDate')
			              ->setParameter('startDate', $startDate->format('Y-m-d H:i:s'))
			              ->setParameter('endDate', $endDate->format('Y-m-d H:i:s'))
			              ->getQuery()->getResult();

	    // $companyEvents and $companyEvent in this example
	    // represent entities from your database, NOT instances of Event
	    // within this bundle.
	    //
	    // Create Event instances and populate it's properties with data
	    // from your own entities/database values.
	    
		foreach($companyEvents as $companyEvent) {

		    // create an event with a start/end time, or an all day event
		    if ($companyEvent->getAllDayEvent() === false) {
		    	$event = new Event($companyEvent->getTitle(), $companyEvent->getStartDatetime(), $companyEvent->getEndDatetime());
		    } else {
		    	$event = new Event($companyEvent->getTitle(), $companyEvent->getStartDatetime(), null, true);
		    }

		    //optional calendar event settings
		    $event->setAllDay(true); // default is false, set to true if this is an all day event
		    $event->setBgColor('#FF0000'); //set the background color of the event's label
		    $event->setFgColor('#FFFFFF'); //set the foreground color of the event's label
		    $event->setUrl('http://www.google.com'); // url to send user to when event label is clicked
		    $event->setCssClass('my-custom-class'); // a custom class you may want to apply to event labels

		    //finally, add the event to the CalendarEvent for displaying on the calendar
		    $calendarEvent->addEvent($event);
		}
	}
}
```

Additional properties and customization of each event on the calendar can be found in the Entity/Event class.

Then, add the listener to your services:
``` xml
<?xml version="1.0" ?>
  <container xmlns="http://symfony.com/schema/dic/services">

    <services>
        <service id="acme.demobundle.calendar_listener" class="Acme\DemoBundle\EventListener\CalendarEventListener">
            <argument type="service" id="doctrine.orm.entity_manager" />
            <tag name="kernel.event_listener" event="calendar.load_events" method="loadEvents" />
        </service>

    </services>
  </container>
```

And that's it!  When the `Th3MoukFullCalendarBundle::calendar.html.twig` template is rendered, any events within the current month/day/year will be pulled from your application.


Extending the Calendar Javascript
-------------

You may want to customize the FullCalendar javascript to meet your applications needs.  In order to do this, you can
copy the calendar-settings.js in Resources/public/js, and modify it to fit your needs.  For instance, you can pass
custom filters to your event listeners by adding extra parameters in the eventSources method:

``` javascript
eventSources: [
        {
            url: Routing.generate('fullcalendar_loader'),
            type: 'POST',
            // A way to add custom filters to your event listeners
            data: {
                filter: 'my_custom_filter_param'
            },
            error: function() {
               //alert('There was an error while fetching Google Calendar!');
            }
        }
]
```



    
