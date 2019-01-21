<?php

namespace XIVDB\Apps\LibraData;

trait LibraDatabaseHandler
{
    /**
     * Sorts an array by a column within the array,
     * main use is databases results
     *
     * @param $data - the data to resort
     * @param $column - the column to use for sorting
     * @return Array - the new array!
     */
    public function sortDataByColumn($data, $column)
    {
        $newData = [];
        foreach($data as $k => $v)
        {
            $newKey = $v[$column];
            $newData[$newKey] = $this->removeNumericIndexes($v);
        }

        ksort($newData);
        return $newData;
    }

    /**
     * Removes numeric indexes, for some reason sqlite
     * returns both table column names and an index list,
     * so this helps strip index's and reduce data duplication
     *
     * @param $data - the array to remove from
     * @return Array - the new array, without numeric indexes!
     */
    public function removeNumericIndexes($data)
    {
        foreach($data as $k => $v)
            if (is_numeric($k))
                unset($data[$k]);

        return $data;
    }
}
