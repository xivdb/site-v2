<?php

namespace XIVDB\Apps\Characters;

use XIVDB\Apps\Content\ContentDB;

trait DataMinionsMounts
{
    protected function handleMinionsMounts()
    {
        $this->getAllMinionsAndMounts();
        $this->orderMinionContentData();
        $this->orderMountContentData();
        $this->groupMinionsAndMounts();
    }

    //
    // Get all the minions and mounts from the database
    //
    private function getAllMinionsAndMounts()
    {
        // get possible list
        $dbs = $this->getModule('database');
        $dbs->QueryBuilder
            ->select(['id', 'name_{lang} as name', 'icon'])
            ->from(ContentDB::MINIONS);

        $allMinions = $dbs->get()->all();

        //
        // Get all mounts
        //

        // get possible list
        $dbs = $this->getModule('database');
        $dbs->QueryBuilder
            ->select(['id', 'name_{lang} as name', 'icon'])
            ->from(ContentDB::MOUNTS)
            ->where('id != 103');

        $allMounts = $dbs->get()->all();

        //
        // Minion Stats
        //
        $minionsObtained = 0;
        $minionsTotal = count($allMinions);
        foreach($allMinions as $i => $minion) {
            $id = $minion['id'];
            $minion['obtained'] = isset($this->chardata['data']['minions'][$id]) ? $this->chardata['data']['minions'][$id] : false;

            if ($minion['obtained']) {
                $minionsObtained++;
            }

            $minion['icon'] = $this->iconize($minion['icon']);
            $minion['url'] = $this->url('minion', $minion['id'], $minion['name']);
            $this->tempdata['all_minions'][$id] = $minion;
        }

        $this->tempdata['minionsTotal'] = $minionsTotal;

        //
        // Mount Stats
        //
        $mountsObtained = 0;
        $mountsTotal = count($allMounts);
        foreach($allMounts as $i => $mount) {
            $id = $mount['id'];
            $mount['obtained'] = isset($this->chardata['data']['mounts'][$id]) ? $this->chardata['data']['mounts'][$id] : false;

            if ($mount['obtained']) {
                $mountsObtained++;
            }

            $mount['icon'] = $this->iconize($mount['icon']);
            $mount['url'] = $this->url('mount', $mount['id'], $mount['name']);
            $this->tempdata['all_mounts'][$id] = $mount;
        }

        $this->tempdata['mountsTotal'] = $mountsTotal;
    }

    //
    // Re-order minion data
    //
    protected function orderMinionContentData()
    {
        $minions = [];
        foreach($this->chardata['data']['minions'] as $i => $id) {
            if (isset($this->tempdata['all_minions'][$id])) {
                $minions[$id] = $this->tempdata['all_minions'][$id];

                // add all minions of light
                if ($id == 67) {
                    $minions[68] = $this->tempdata['all_minions'][68];
                    $minions[69] = $this->tempdata['all_minions'][69];
                    $minions[70] = $this->tempdata['all_minions'][70];
                }

                // add all wind up leaders
                if ($id == 71) {
                    $minions[72] = $this->tempdata['all_minions'][72];
                    $minions[73] = $this->tempdata['all_minions'][73];
                    $minions[74] = $this->tempdata['all_minions'][74];
                }
            }

        }

        $this->chardata['data']['minions'] = $minions;
        $this->tempdata['minionsObtained'] = count($minions);
    }

    //
    // Re-order mount data
    //
    protected function orderMountContentData()
    {
        $mounts = [];
        foreach($this->chardata['data']['mounts'] as $i => $id) {
            if (isset($this->tempdata['all_mounts'][$id])) {
                $mounts[$id] = $this->tempdata['all_mounts'][$id];
            }
        }

        $this->chardata['data']['mounts'] = $mounts;
        $this->tempdata['mountsObtained'] = count($mounts);
    }

    protected function groupMinionsAndMounts()
    {
        $this->tempdata['minions_and_mounts'] = [
            'minions' => array_keys($this->chardata['data']['minions']),
            'mounts' => array_keys($this->chardata['data']['mounts']),
        ];
    }
}
