<?php

namespace App\Listeners;

use App\Events\ActionCreated;
use App\Events\ActionUpdated;
use App\Events\ActionDeleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;
use App\TokenStore\TokenCache;
use App\TimeZones\TimeZones;
use Carbon\Carbon;

class ActionEventSubscriber
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle action created events.
     */
    public function handleActionCreated(ActionCreated $event) {
        $graph = $this->getGraph();

        // Build the event
        $newEvent = [
            'subject' => $event->action->name,
            // 'attendees' => $attendees,
            'start' => [
                'dateTime' => Carbon::parse($event->action->datedebut)->format(\DateTimeInterface::ISO8601),
                'timeZone' => session('userTimeZone')
            ],
            'end' => [
                'dateTime' => Carbon::parse($event->action->datefin)->format(\DateTimeInterface::ISO8601),
                'timeZone' => session('userTimeZone')
            ],
            'body' => [
                'content' => $event->action->description,
                'contentType' => 'text'
            ],
            'reminderMinutesBeforeStart'=> 15,
            'importance'=> 'high'
        ];        
        // POST /me/events
        $response = $graph->createRequest('POST', '/me/events')
            ->attachBody($newEvent)
            ->setReturnType(Model\Event::class)
            ->execute();        
        $event->action->outlook_event_id = $response->getId();
        $event->action->save();
    }
 
    /**
     * Handle action updated events.
     */
    public function handleActionUpdated(ActionUpdated $event) {
        $graph = $this->getGraph();
        
        // Update the event
        $editEvent = [
            'subject' => $event->action->name,
            // 'attendees' => $attendees,
            'start' => [
                'dateTime' => Carbon::parse($event->action->datedebut)->format(\DateTimeInterface::ISO8601),
                'timeZone' => session('userTimeZone')
            ],
            'end' => [
                'dateTime' => Carbon::parse($event->action->datefin)->format(\DateTimeInterface::ISO8601),
                'timeZone' => session('userTimeZone')
            ],
            'body' => [
                'content' => $event->action->description,
                'contentType' => 'text'
            ],
        ];        
        // POST /me/events
        $graph->createRequest('PATCH', '/me/events/'.$event->action->outlook_event_id)
            ->attachBody($editEvent)
            ->setReturnType(Model\Event::class)
            ->execute();
    }

    /**
     * Handle action deleted events.
     */
    public function handleActionDeleted(ActionUpdated $event) {
        $graph = $this->getGraph();
        
        // POST /me/events/{id}
        $graph->createRequest('DELETE', '/me/events/'.$event->action->outlook_event_id)
                ->execute();
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     * @return void
     */
    public function subscribe($events)
    {
        $events->listen(
            ActionCreated::class,
            [ActionEventSubscriber::class, 'handleActionCreated']
        );
 
        $events->listen(
            ActionUpdated::class,
            [ActionEventSubscriber::class, 'handleActionUpdated']
        );

        $events->listen(
            ActionDeleted::class,
            [ActionEventSubscriber::class, 'handleActionDeleted']
        );
    }
    
    private function getGraph(): Graph {
      // Get the access token from the cache
      $tokenCache = new TokenCache();
      $accessToken = $tokenCache->getAccessToken();
  
      // Create a Graph client
      $graph = new Graph();
      $graph->setAccessToken($accessToken);
      return $graph;
    }    
}
