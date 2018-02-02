<?php

namespace App\Jobs\Truths;

use App\PlatformCheckTruthReport;

class Truth {
	/**
		@var $platformCheckTruthId
		@var $platformTaskType
		@var $endValue
	*/
	private $platformCheckTruthId, $platformTaskType, $endValue;
	/**
		Save the platform task report to the database.
	*/
	protected function saveReport()
	{
		// Create a platform task report.
		PlatformCheckTruthReport::create([
			'platform_check_truth_id' => $this->platformCheckTruthId,
			$this->platformTaskType.'_value' => $this->endValue
		]);
	}
}