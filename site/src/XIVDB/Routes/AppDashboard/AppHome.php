<?php

namespace XIVDB\Routes\AppDashboard;

use Symfony\Component\HttpFoundation\Request;
use Intervention\Image\ImageManagerStatic as Image;
use XIVDB\Apps\Community\Comments;
use XIVDB\Apps\Community\Screenshots;
use XIVDB\Apps\Content\ContentDB;
use XIVDB\Apps\Database\Database;
use XIVDB\Apps\Users\Users;

//
// Home
//
trait AppHome
{
    protected function _home()
    {
		//
		// Home route
		//
        $this->route('/', 'GET', function(Request $request)
        {
            $this->mustBeModerator();
            return $this->respond('Dashboard/Home/index.html.twig');
        });

        //
		// Home route
		//
        $this->route('/cms', 'GET', function(Request $request)
        {
            $this->mustBeAdmin();

            /** @var Database $dbs */
            $dbs = $this->getDatabase();
            /** @var Comments $commentService */
            $commentService = $this->getModule('comments');
            /** @var Screenshots $screenshotService */
            $screenshotService = $this->getModule('screenshots');
            /** @var Users $userService */
            $userService = $this->getModule('users');

            // -------------------------------------------------------------

            if ($request->get('deleteComment')) {
                $dbs->QueryBuilder
                    ->delete('content_comments')
                    ->where('id = :id')
                    ->bind('id', $request->get('deleteComment'));

                $dbs->execute();
                $this->getModule('session')->add('success',
                    sprintf('Comment %s deleted', $request->get('deleteComment')));
            }

            if ($request->get('deleteAllComments')) {
                $dbs->QueryBuilder
                    ->delete('content_comments')
                    ->where('member = :member')
                    ->bind('member', $request->get('deleteAllComments'));

                $dbs->execute();
                $this->getModule('session')->add('success',
                    sprintf('Deleted all comments by user: %s', $request->get('deleteAllComments')));
            }

            if ($request->get('deleteScreenshot')) {
                $dbs->QueryBuilder
                    ->delete('content_screenshots')
                    ->where('id = :id')
                    ->bind('id', $request->get('deleteScreenshot'));

                $dbs->execute();
                $this->getModule('session')->add('success',
                    sprintf('Screenshot %s deleted', $request->get('deleteScreenshot')));
            }

            // -------------------------------------------------------------

            $comments = $commentService->getRecent(50, $request->get('search'));
            $screenshots = $screenshotService->getRecent(50, $request->get('id'));

            //print_r($screenshots);die;

            return $this->respond('Dashboard/Home/cms.html.twig',[
                'comments' => $comments,
                'screenshots' => $screenshots,
            ]);
        });

        $this->route('/images', 'GET', function(Request $request)
        {
            die;
            $template = 'Dashboard/Home/img.html.twig';
            $contentIds = array_flip(ContentDB::$contentIds);

            $dbs = $this->getModule('database');
            $dbs->QueryBuilder
                ->select('*')
                ->from('content_screenshots')
                ->where('processed = 0')
                ->limit(0,1);

            $image = $dbs->get()->one();
            if (!$image) {
                die('No images left');
            }

            // get content folder
            $id = $image['id'];
            $cid = $image['content'];
            $folder = isset($contentIds[$cid]) ? $contentIds[$cid] : false;
            if (!$folder) {
                $dbs->QueryBuilder->update('content_screenshots')->set('processed', '1')->set('log', ':res')->bind(':res', 'Content folder not found for: '. $cid)->where('id = '. $id);
                $dbs->execute();
                return $this->respond($template, [ 'img' => $image ]);
            }

            // set folder and filename
            $folder = ROOT_WEB .'/screenshots/'. $folder .'/'. $image['uniq'] .'/';
            $filename = $image['filename'];
            $newfilename = str_ireplace('.png', '.jpg', $filename);

            // check if folder exists
            if (!is_dir($folder)) {
                $dbs->QueryBuilder->update('content_screenshots')->set('processed', '1')->set('log', ':res')->bind(':res', 'Folder not found: '. $folder)->where('id = '. $id);
                $dbs->execute();
                return $this->respond($template, [ 'img' => $image ]);
            }

            // check image exists
            if (!file_exists($folder.$filename)) {
                $dbs->QueryBuilder->update('content_screenshots')->set('processed', '1')->set('log', ':res')->bind(':res', 'File does not exist: '. $folder.$filename)->where('id = '. $id);
                $dbs->execute();
                return $this->respond($template, [ 'img' => $image ]);
            }

            // delete any others
            @unlink($folder.'gallery_'.$filename);
            @unlink($folder.'small_'.$filename);
            @unlink($folder.'thumb_'.$filename);

            // make image
            $img = Image::make($folder.$filename);
            $img->save($folder.$newfilename, 75);
            $img->resize(null, 250, function ($constraint) {
                $constraint->aspectRatio();
            })->save($folder.'small_'.$newfilename, 75);

            $dbs->QueryBuilder->update('content_screenshots')->set('processed', '1')->set('log', ':res')->bind(':res', 'Success')->where('id = '. $id);
            $dbs->execute();
            return $this->respond($template, [ 'img' => $image ]);
        });
    }
}
