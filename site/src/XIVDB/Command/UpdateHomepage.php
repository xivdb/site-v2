<?php

namespace XIVDB\Command;

use Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\Console\Input\InputInterface;

use Knp\Command\Command;
use XIVDB\Apps\Services\XIVSync\XIVSync;

class UpdateHomepage extends Command
{
    private $output;
    private $AppHandler;

    protected function configure()
    {
        $this
            ->setName("UpdateHomepage")
            ->setDescription("Updates the home page json data from XIVSync.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->AppHandler = new \XIVDB\Apps\AppHandler();

        $xivsync = $this->AppHandler->getModule('xivsync');

        // is sync online?
        if (!$xivsync->isOnline()) {
            return $output->writeln('XIVSync is offline');
        }

        // get SE dev posts
        $this->getDevPosts($xivsync, $input, $output);

        // get lodestone data
        $this->getLodestoneData($xivsync, $input, $output);

        // Finished!
        $output->writeln('Complete!');
    }

    //
    // Get lodestone data and save it to lodestone.json
    //
    private function getLodestoneData($xivsync, $input, $output)
    {
        $saveto = ROOT_WEB .'/assets/lodestone.json';
        $data = [];

        // getBanners
        $output->writeln('getBanners');
        $data['banners'] = $xivsync->getBanners()['data']['entries'];

        // getTopics
        $output->writeln('getTopics');
        $data['topics'] = $xivsync->getTopics()['data']['entries'];

        // getNotices
        $output->writeln('getNotices');
        $data['notices'] = $xivsync->getNotices()['data']['entries'];

        // getMaintenance
        $output->writeln('getMaintenance');
        $data['maintenance'] = $xivsync->getMaintenance()['data']['entries'];

        // getStatus
        $output->writeln('getStatus');
        $data['status'] = $xivsync->getStatus()['data']['entries'];

        // getCommunity
        $output->writeln('getCommunity');
        $data['community'] = [];

        // getEvents
        $output->writeln('getEvents');
        $data['events'] = [];

        // getPopularPosts
        $output->writeln('getPopularPosts');
        $data['popularposts'] = [];

        if ($data['topics']) {
            file_put_contents($saveto, json_encode($data));
            return $output->writeln('Saved to: '. $saveto);
        }

        $output->writeln('Failed to get dev posts. $data[topics] is empty');
        print_r($xivsync->errors);
    }

    //
    // Get dev posts and save to dev tracker assets
    //
    private function getDevPosts($xivsync, $input, $output)
    {
        $output->writeln('Getting dev posts ...');
        $saveto = ROOT_WEB .'/assets/devtracker.json';

        // Get dev posts
        if ($data = $xivsync->getDevPosts()) {
            $data = json_encode($data['data']);

            if ($data && strlen($data) > 100) {
                file_put_contents($saveto, $data);
                return $output->writeln('Saved to: '. $saveto);
            }
        }

        $output->writeln('Failed to get dev posts. $data is empty');
        print_r($xivsync->errors);
    }
}
