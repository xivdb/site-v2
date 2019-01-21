<?php

namespace XIVDB\Apps\Community;

//
// Comments replies trait
//
trait CommentsReplies
{
    private $temp;
    private $replies;

    /**
     * Sort data by replies
     */
    private function sortForReplies()
    {
        $this->temp = [];
        $this->replies = [];

        // sort comments into original/replies
        $this->initialSort();

        // recurrsively loop through them
        $this->temp = $this->recurrsiveSort($this->temp);

        // set data to the new temp
        $this->data = $this->temp;
    }

    /**
     * Initial sort to split the replies with the non replies
     */
    private function initialSort()
    {
        foreach($this->data as $id => $post)
        {
            // if its a reply or not
            if ($post['reply_to']) {
                $this->replies[$post['reply_to']][$post['id']] = $post;
            } else {
                $post['replies'] = [];
                $this->temp[$post['id']] = $post;
            }
        }
    }

    /**
     * Recurrsive sort function
     *
     * @param $data - the data to loop through
     * @return $data - the data recurrsively looped and sorted
     */
    private function recurrsiveSort($data)
    {
        foreach($data as $id => $post) {
            if (isset($this->replies[$id])) {
                $replies = $this->replies[$id];
                unset($this->replies[$id]);
                $data[$id]['replies'] = $this->recurrsiveSort($replies);
            }
        }

        // sort
        usort($data, function($a, $b) {
            return strcmp($b['time'], $a['time']);
        });

        return $data;
    }
}
