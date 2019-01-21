<?php

namespace XIVDB\Routes\AppDashboard;

use Symfony\Component\HttpFoundation\Request;

//
// Home
//
trait AppDevBlog
{
    protected function _devblog()
    {
		//
		// DevBlog route
		//
        $this->route('/devblog', 'GET|POST', function(Request $request)
        {
            // Must be an admin to view
            $this->mustBeAdmin();

            // get database
            $dbs = $this->getModule('database');

            // if saving an existing post
            if ($request->get('edit') && $request->get('save'))
            {
                $dbs->QueryBuilder
                    ->update('site_devblog')
                    ->set('subject', ':subject')
                    ->bind('subject', $request->get('subject'))
                    ->set('text', ':text')
                    ->bind('text', $request->get('text'))
                    ->where('id = :id')
                    ->bind('id', $request->get('edit'));

                $this->getModule('session')->add('success', 'Blog post has been Updated');

                $dbs->execute();
                $saved = true;
            }
            // if saving a new post
            else if ($request->get('save'))
            {
                $dbs->QueryBuilder
                    ->insert('site_devblog')
                    ->schema(['subject', 'text'])
                    ->values([':subject', ':text'])
                    ->bind('subject', $request->get('subject'))
                    ->bind('text', $request->get('text'));

                $this->getModule('session')->add('success', 'Blog post has been saved');

                $id = $dbs->get()->id();
                return $this->redirect('/devblog?edit='. $id);
            }
            // if publishing
            else if ($request->get('edit') && $request->get('publish'))
            {
                $dbs->QueryBuilder
                    ->update('site_devblog')
                    ->set('published', 1)
                    ->where('id = :id')
                    ->bind('id', $request->get('edit'));

                $this->getModule('session')->add('success', 'Blog post has been published');

                $dbs->execute();
                $saved = true;
            }
            // if un-publishing
            else if ($request->get('edit') && $request->get('unpublish'))
            {
                $dbs->QueryBuilder
                    ->update('site_devblog')
                    ->set('published', 0)
                    ->where('id = :id')
                    ->bind('id', $request->get('edit'));

                $this->getModule('session')->add('success', 'Blog post has been un-published');

                $dbs->execute();
                $saved = true;
            }
            // if deleting a post
            else if ($request->get('edit') && $request->get('delete'))
            {
                $dbs->QueryBuilder
                    ->delete('site_devblog')
                    ->where('id = :id')
                    ->bind('id', $request->get('edit'));

                $this->getModule('session')->add('success', 'Blog post has been deleted');

                $dbs->execute();
                return $this->redirect('/devblog');
            }

            // get all posts
            $dbs->QueryBuilder->select('*')->from('site_devblog')->order('id', 'desc');
            $posts = $dbs->get()->all();

            // get editing
            if ($editId = $request->get('edit')) {
                $dbs->QueryBuilder->select('*')->from('site_devblog')->where('id = :id')->bind('id', $editId);
                $editing = $dbs->get()->one();
            }

            return $this->respond('Dashboard/DevBlog/index.html.twig', [
                'posts' => $posts,
                'editing' => isset($editing) ? $editing : null,
                'saved' => isset($saved) ? $saved : false,
                'published' => isset($published) ? $published : false,
            ]);
        });

        //
		// DevBlog uploader handler
		//
        $this->route('/devblog/upload', 'GET|POST', function(Request $request)
        {
            $this->mustBeAdmin();
            $file = $request->files->get('file');

            // rename file
            $filename = time() . substr(md5($file->getClientOriginalName()), 0, 8) .'.'. pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
            $file->move(ROOT_UPLOADS, $filename);

            return $this->json([
                'filelink' => '/up/'. $filename,
            ]);
        });
    }
}
