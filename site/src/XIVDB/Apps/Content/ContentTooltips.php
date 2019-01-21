<?php

namespace XIVDB\Apps\Content;

trait ContentTooltips
{
    //
    // Get tooltip for the set content
    //
    public function getTooltip()
    {
        // key and save path
        $key = sprintf('%s_%s_%s', static::TYPE, $this->id, LANGUAGE);
        $savePath = sprintf('%s/%s/', ROOT_TOOLTIPS, static::TYPE);
        if (!is_dir($savePath)) { mkdir($savePath, '0777', true); }
        $save = sprintf('%s%s.json', $savePath, $key);

        // paths
        $template = sprintf('Content/content_tooltips/tooltip_%s.twig', static::TYPE);
        $fullpath = sprintf('%s/Content/content_tooltips/tooltip_%s.twig', ROOT_VIEWS, static::TYPE);

        // if the file does not exist, end
        // or if the method tooltip does not exists, end!
        if (!file_exists($fullpath) || !method_exists($this, 'tooltip')) {
            return false;
        }

        // get tooltip data (for replacing link)
        // append on content ID!
        $data = $this->tooltip();
        $data['id'] = $this->id;
        ksort($data);

        // get the html
        $html = $this->twigit($template, [
            'content' => $this->data,
            'defines' => [
                'DEV' => DEV,
                'URL' => URL,
                'LANGUAGE' => LANGUAGE,
            ],
            'languages' => (new \XIVDB\Apps\Site\Language())->getAll(),
        ]);

        // check html is a string and exists
        if ($html && is_string($html))
        {
            // trim and remove some junk
            // fix image urls
            $html = trim($html);
            $html = preg_replace('~>\s*\n\s*<~', '><', $html);
            $html = str_ireplace("\n", null, $html);

            // this is so hacky.. remove a lot of useless white space
            $html = str_ireplace('          ', ' ', $html);
            $html = str_ireplace('         ', ' ', $html);
            $html = str_ireplace('        ', ' ', $html);
            $html = str_ireplace('       ', ' ', $html);
            $html = str_ireplace('      ', ' ', $html);
            $html = str_ireplace('     ', ' ', $html);
            $html = str_ireplace('    ', ' ', $html);
            $html = str_ireplace('   ', ' ', $html);
            $html = str_ireplace('  ', ' ', $html);

            // response
            $response = [
                'html' => $html,
                'data' => $data,
            ];

            // save html, blank errors!
            @file_put_contents($save, json_encode($response));
            return $response;
        }

        return false;
    }
}
