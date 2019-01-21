<?php

/**
 * LibraLanguages
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractLibra;


class LibraLanguages
{
    private $log = [];

    public function init()
    {
        $this->db();

        $strings = $this->get(sprintf(FILE_LIBRA_LANGUAGE, 'en'));
        $strings_de = $this->get(sprintf(FILE_LIBRA_LANGUAGE, 'de'));
        $strings_fr = $this->get(sprintf(FILE_LIBRA_LANGUAGE, 'fr'));
        $strings_ja = $this->get(sprintf(FILE_LIBRA_LANGUAGE, 'ja'));

        die;

        $insert = [];
        foreach($strings as $id => $string)
        {
            $de = $strings_de[$id];
            $fr = $strings_fr[$id];
            $ja = $strings_ja[$id];

            if (is_array($string) || is_array($de) || is_array($fr) || is_array($ja)) {
                continue;
            }

            $new = [
                'id'=> $id,
                'text_ja' => addslashes($ja),
                'text_en' => addslashes($string),
                'text_de' => addslashes($de),
                'text_fr' => addslashes($fr),
            ];

            $insert[] = $new;

            if (count($insert) == 500)
            {
                $this->insert($insert);
                $insert = [];
            }
        }

        $this->insert($insert);

        die;
    }

    private function insert($data)
    {
        $this->qb->insert('db_languages')->data($data)->duplicate();
        $this->database->sql($this->qb);
    }

    private function toArray($xmlstring)
    {
        $xml = simplexml_load_string($xmlstring);
        $json = json_encode($xml);
        $array = json_decode($json,TRUE);

        return $array['string'];
    }
}
