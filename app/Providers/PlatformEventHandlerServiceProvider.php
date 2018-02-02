<?php

namespace App\Providers;

use App\PlatformEvent;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PlatformEventHandlerServiceProvider
{
    /**
     * Platform Events to store all mutations, etc.
     * @var $events
     */
    public $events;

    /**
     * Get the platform events.
     * @return mixed
     */
    public function get()
    {
        return $this->events;
    }

    /**
     * Count of the current platform events.
     * @return int
     */
    public function count()
    {
        return count($this->events);
    }

    /**
     * @param $name
     * @param $queryKey
     * @param $queryValue
     * @param null $fromDate
     * @param null $toDate
     * @return PlatformEvent|\Illuminate\Database\Query\Builder
     */
    public function buildPlatformQuery($name, $queryKey, $queryValue, $fromDate = null, $toDate = null) {
        // Start to build the query.

        $platformQuery = PlatformEvent::whereName($name);

        if($fromDate != null && $toDate != null) {
            $platformQuery
                ->where('created_at','>=',$fromDate)
                ->where('created_at','<=',$toDate);
        }

        if($queryKey != null && $queryValue != null) {
            $platformQuery->where('json_value->'.$queryKey,$queryValue);
        }

        return $platformQuery;
    }

    /**
     * Find events via name.
     * @param $name
     * @return array
     */
    public function findEvents($name)
    {
        // Retrieve the platform_event(s) that match the passed name.
        $basePlatformEvents = PlatformEvent::whereName($name)->get();

        // Parse the events into readable array(s).
        $platformEvents = [];
        foreach ($basePlatformEvents as $platformEvent) {
            $platformEvents[] = $this->buildOutEventStructure($platformEvent);
        }

        // Set the events.
        $this->events = $platformEvents;

        // For chaining.
        return $this;
    }

    /**
     * Find events that occur within a time frame provided.
     * @param $name
     * @param $fromDate
     * @param $toDate
     * @return $this
     */
    public function findEventsInDateRange($name,$fromDate,$toDate)
    {
        // Retrieve the platform_event(s) that match the passed name.
        $basePlatformEvents = PlatformEvent::whereName($name)
            ->where('created_at','>=',$fromDate)
            ->where('created_at','<=',$toDate)
            ->get();

        // Parse the events into readable array(s).
        $platformEvents = [];
        foreach ($basePlatformEvents as $platformEvent) {
            $platformEvents[] = $this->buildOutEventStructure($platformEvent);
        }

        // Set the events.
        $this->events = $platformEvents;

        // For chaining.
        return $this;
    }

    /**
     * Find Events that occured today.
     * @param $name
     * @return $this
     */
    public function findEventsToday($name)
    {
        // Retrieve the platform_event(s) that match the passed name.
        $basePlatformEvents = PlatformEvent::whereName($name)
            ->whereDate('created_at',Carbon::now()->toDateString())
            ->get();

        // Parse the events into readable array(s).
        $platformEvents = [];
        foreach ($basePlatformEvents as $platformEvent) {
            $platformEvents[] = $this->buildOutEventStructure($platformEvent);
        }

        // Set the events.
        $this->events = $platformEvents;

        // For chaining.
        return $this;
    }

    /**
     * Chain after "findEvents" to match decoded values.
     * @param string|array $queryKey
     * @param string|int|array $queryValue
     * @return $this
     */
    public function withDecodedValue($queryKey, $queryValue = -1)
    {
        // Fetch the current platform events.
        $events = $this->events;

        // Go through the current platform event array and match events.
        $matchedEvents = [];
        foreach ($events as $event) {
            if(is_array($queryKey)) {
                $matchCount = 0;
                for($i = 0; $i <= count($queryKey) - 1; $i++) {
                    if (array_key_exists($queryKey[$i], $event["value"])) {
                        if($event["value"][$queryKey[$i]] == $queryValue[$i]) {
                            $matchCount++;
                            if($matchCount == count($queryKey)) {
                                $matchedEvents[] = $event;
                            }
                        }
                    }
                }
            } else {
                if (array_key_exists($queryKey, $event["value"])) {
                    if($queryValue != -1) {
                        // Match based off value, as well.
                        if($event["value"][$queryKey] == $queryValue) {
                            $matchedEvents[] = $event;
                        }
                    } else {
                        $matchedEvents[] = $event;
                    }
                }
            }
        }
        $this->events = $matchedEvents;
        return $this;
    }

    /**
     * Build out ID/Value assoc. array
     *
     * @param $platformEvent
     * @return array
     */
    private function buildOutEventStructure($platformEvent)
    {
        // Create the initial event structure
        $event = [
            "id" => $platformEvent->id
        ];

        // Grab the value.
        if ($platformEvent->type == "json") {
                $event["value"] = json_decode($platformEvent->json_value, 1);
        } else {
            $event["value"] = $platformEvent[$platformEvent->type . "_value"];
        }

        // Return the formatted event.
        return $event;
    }
}
