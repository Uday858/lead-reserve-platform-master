<?php

namespace App\Jobs;

use App\PlatformEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class TrackPlatformEventAsync implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var $name
     * Track the PlatformEvent name.
     */
    public $name;

    /**
     * @var $description
     * Track the PlatformEvent description.
     */
    public $description;

    /**
     * @var $data
     * Track the PlatformEvent data.
     */
    public $data; // TODO: Should be an array!

    /**
     * TrackPlatformEventAsync constructor.
     * @param $name
     * @param $description
     * @param $data
     */
    public function __construct($name, $description, $data)
    {
        $this->name = $name;
        $this->description = $description;
        $this->data = $data; // Serialized into JSON.
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        PlatformEvent::create([
            "name" => $this->name,
            "description" => $this->description,
            "type" => "json",
            "json_value" => json_encode($this->data)
        ]);
    }
}
