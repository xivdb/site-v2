<?php

namespace XIVDB\Apps\Community;

//
// Comments organize trait
//
trait CommentsOrganize
{
	//
	// Format comments
	//
	private function format()
    {
        foreach($this->data as $i => $post)
        {
            // if post is deleted, force deleted text
            // else parse it
            if ($post['deleted']) {
                $post['text'] = '<span class="comment-deleted">[deleted]</span>';
            } else {
                $post['text'] = htmlentities($post['text']);

                // store an original (for editing)
                // and format a new one
                $post['original'] = $post['text'];

                // Markdown parse
                $post['text'] = $this->markdown($post['text']);
            }

			// Get patch
			$post['patch'] = $this->getPostPatch($post['time']);
            $this->data[$i] = $post;
        }
    }

	//
    // Get patch of a post
    //
    private function getPostPatch($time)
    {
        $cmtdate = strtotime($time);
        $patch = null;

        foreach($this->patchlist as $patch) {
            $patchdate = strtotime($patch['date']);
            $patch = $patch;

            // When the patch is less than comment, finish
            if ($patchdate < $cmtdate) {
                break;
            }
        }

        return $patch['command'];
    }
}
