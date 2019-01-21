<?php

namespace XIVDB\Apps\GameData;

//
// Handle filenames
//
trait GameDataFilenames
{
    // consts
    private $filenamePython = '%s_%s.csv';
    private $filenameExplorer = '%s.exh%s.csv';
    private $filenameSaint = '%s.csv';

    //
    // Get a specific filename
    //
    public function getSpecificFilename($type)
    {
        if ($type == 'python') {
            return sprintf($this->filenamePython, $this->name, method_exists($this, 'json') ? 'en' : '');
        }

        if ($type == 'explorer') {
            return sprintf($this->filenameExplorer, strtolower($this->name), method_exists($this, 'json') ? '_en' : '');
        }

        if ($type == 'saint') {
            return sprintf($this->filenameSaint, $this->name);
        }

        return false;
    }

    //
    // Return the json file name (false if it doesn't exist)
    //
    public function getJsonFilename($name = false)
    {
        $name = $name ? sprintf('%s.json', $name) : sprintf('%s.json', $this->name);
        $filename = ROOT_EXTRACTS . EXTRACT_PATH . EXTRACT_LISTS . $name;
        return file_exists($filename) ? $filename : false;
    }

    //
    // Return the python file name (false if it doesn't exist)
    //
    public function getPythonFilename()
    {
        $filename = ROOT_EXTRACTS . EXTRACT_PATH . EXTRACT_EXD . $this->getSpecificFilename('python');
        return file_exists($filename) ? $filename : false;
    }

    //
    // Return the explorer file name (false if it doesn't exist)
    //
    public function getExplorerFilename()
    {
        $filename = ROOT_EXTRACTS . EXTRACT_PATH . EXPLORER_EXD . $this->getSpecificFilename('explorer');
        return file_exists($filename) ? $filename : false;
    }

    //
    // Return the Saint filename (false if it doesn't exist)
    //
    public function getSaintFilename($csvname = false)
    {
        $csvname = $csvname ? sprintf($this->filenameSaint, $csvname) : $this->getSpecificFilename('saint');
        $filename = ROOT_EXTRACTS . EXTRACT_PATH . SAINT_EXD . $csvname;
        return file_exists($filename) ? $filename : false;
    }

    //
    // Return the Saint filename (false if it doesn't exist)
    //
    public function getChineseFilenames()
    {
        return [
            'cns' => ROOT_EXTRACTS . EXTRACT_PATH . CHINESE_EXD . CHINESE_CNS,
            'cnt' => ROOT_EXTRACTS . EXTRACT_PATH . CHINESE_EXD . CHINESE_CNT,
            'ko' => ROOT_EXTRACTS . EXTRACT_PATH . CHINESE_EXD . CHINESE_KO,
        ];
    }
}
