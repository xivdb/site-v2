<?php

namespace XIVDB\Apps\GameData;

//
// Handle storage manipulation
//
trait GameDataStorage
{
    //
    // Get the json file data
    //
    public function getJsonFileData()
    {
        if ($filename = $this->getJsonFilename()) {
            // Get JSON
            return $this->getJson($filename);
        }

        return false;
    }

    //
    // Get the CSV file data
    //
    public function getCsvFileData($prev = false)
    {
        $filename = $this->getSaintFilename();
        if ($prev) {
            $filename = str_ireplace(CURRENT_PATCH, PREVIOUS_PATCH, $filename);
        }

        // Get CSV
        return $this->getCsv($filename);
    }

    //
    // Get the selected json entry
    //
    public function getSelectedJsonEntry()
    {
        if ($data = $this->getJsonFileData()) {
            return $this->id ? $data[$this->id] : reset($data);
        }

        return false;
    }

    //
    // Get the selected json entry
    //
    public function getSelectedCsvEntry()
    {
        if ($data = $this->getCsvFileData()) {
            return $this->id ? $data[$this->id] : reset($data);
        }

        return false;
    }

    //
    // Get the selected json entry
    //
    public function getSelectedCsvEntryPreviousPatch()
    {
        if ($data = $this->getCsvFileData(true)) {
            return ($this->id && isset($data[$this->id])) ? $data[$this->id] : reset($data);
        }

        return false;
    }
}
