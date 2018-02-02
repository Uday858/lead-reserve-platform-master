<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\PlatformCheckTruth;
use App\PlatformCheckTruthReport;
use App\PlatformCheckTruthParameter;
use \Carbon\Carbon;

class TestPlatformTruths extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'platform:test-truth';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test a truth job. Related to the `platform_check_truth` and `platform_task` tables.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Collect the truth id.
        $truthId = $this->ask('What truth do you want to test?');
        if(PlatformCheckTruth::where('id',$truthId)->exists()) { 
            // Truth exists. 
            $truthObject = $this->getTruthObject($truthId);
            $this->warn('Testing ' . $truthObject->check_name);
            
            // Generate the parameters for the truth checking process.
            $parameters = $this->buildParameterValueArray($truthObject);

            // New truth class
            $generatedTruthClass = (new $truthObject->target_class());

            // Call the handle method.
            $returnValue = call_user_func_array([$generatedTruthClass,$truthObject->target_method],array_values($parameters));

            PlatformCheckTruthReport::create([
                'platform_check_truth_id' => $truthId,
                $truthObject->return_type . '_value' => $returnValue
            ]);

            $this->info("Report Created for...");
            $this->warn("(".$truthObject->id.") ".$truthObject->check_name);
        } else {
            // Bail.
            $this->error("Can't find that check! Please try again.");
        }
    }

    /**
        Build out a parameter array.
    */
    private function buildParameterValueArray($truthObject)
    {
        // Create a return array.
        $parameters = [];

        // For every parameter within the truth object, go ahead and parse it.
        foreach($truthObject->parameters->sortBy('sort_order') as $parameter) {
            $parameters[$parameter->parameter_name] = $this->parseParameterValue($parameter->parameter_type,$this->ask($parameter->parameter_name . " - " . $parameter->parameter_type . " -- Input -> "));
        }

        // Return the parameters that have now been formatted.
        return $parameters;
    }

    /**
        Return the formatted truth object.
    */
    private function getTruthObject($truthId)
    {
        return PlatformCheckTruth::where('id',$truthId)->first();
    }

    /**
        Format and parse any value that we pass in.
    */
    private function parseParameterValue($parameterType, $parameterValue)
    {
        switch($parameterType)
        {
            case "timestamp":
                return Carbon::parse($parameterValue);
            break;
            default:
                return $parameterValue;
            break;
        }
    }
}
