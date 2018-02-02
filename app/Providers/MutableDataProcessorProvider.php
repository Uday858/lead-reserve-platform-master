<?php

namespace App\Providers;

use App\MutableDataPair;

class MutableDataProcessorProvider
{
    /**
     * @param $storageId
     * @param $newValue
     * @return bool
     */
    public function updateDataPair($storageId,$newValue)
    {
        // Retrieve storage item.
        $storageItem = MutableDataPair::whereId($storageId)->first();

        // Update the value of the storage item.
        $storageItem[$storageItem->type . "_value"] = $newValue;

        // Save the storage item.
        return $storageItem->save();
    }

    /**
     * Create a new mutable data pair.
     *
     * @param $parentId
     * @param $parentType
     * @param $value
     * @return mixed
     */
    public function createNewDataPair($parentId, $parentType, $value)
    {
        return MutableDataPair::create([
            "parent_id" => $parentId,
            "parent_type" => $parentType,
            "type" => $this->getTypeOfValue($value),
            $this->getTypeOfValue($value) . "_value" => $value
        ]);
    }

    /**
     * Return the type of any variable.
     *
     * @param $value
     * @return string
     */
    public function getType($value)
    {
        return $this->getTypeOfValue($value);
    }

    /**
     * Get the type of a variable (Structured in our 5 type system.)
     *
     * @param $value
     * @return string
     */
    private function getTypeOfValue($value)
    {
        switch (gettype($value)) {
            case "boolean":
                return "bool";
                break;
            case "integer":
                return "integer";
                break;
            case "double":
                return "float";
                break;
            case "string":
                return "string";
                break;
            default:
                return "json";
                break;
        }
    }
}
