<?php

namespace XIVDB\Routes\AppDashboard;
use Symfony\Component\HttpFoundation\Request;

//
// Dev Tasks
//
trait AppDevTasks
{
    protected function _devtasks()
    {
        // List dev tasks
        $this->route('/devtasks', 'GET|POST', function(Request $request)
        {
            $this->mustBeAdmin();
            $dbs = $this->getModule('database');
            $list = $dbs->QueryBuilder->select('*')->from('site_devtasks')->order('priority', 'desc');

            return $this->respond('Dashboard/DevTasks/index.html.twig', [
                'tasks' => $dbs->get()->all(),
            ]);
        });

        // Edit/Create dev tasks
        $this->route('/devtasks/task', 'GET|POST', function(Request $request)
        {
            $this->mustBeAdmin();
            $dbs = $this->getModule('database');

            // if submit
            if ($request->get('edit') && $request->get('text'))
            {
                $dbs->QueryBuilder
                    ->update('site_devtasks')
                    ->set('text', ':text')
                    ->set('title', ':title')
                    ->set('priority', ':priority')
                    ->set('completed', ':completed')
                    ->where('id = :id')
                    ->bind('text', $request->get('text'))
                    ->bind('title', $request->get('title'))
                    ->bind('priority', $request->get('priority'))
                    ->bind('completed', $request->get('completed'))
                    ->bind('id', $request->get('edit'));

                $this->getModule('session')->add('success', 'Updated devtask');
                $dbs->execute();
            }
            else if ($request->get('text'))
            {
                $dbs->QueryBuilder
                    ->insert('site_devtasks')
                    ->schema(['text', 'title', 'priority', 'completed'])
                    ->values([':text', ':title', ':priority', ':completed'])
                    ->bind('text', $request->get('text'))
                    ->bind('title', $request->get('title'))
                    ->bind('priority', $request->get('priority'))
                    ->bind('completed', $request->get('completed'));

                $this->getModule('session')->add('success', 'Dev task saved');

                $id = $dbs->get()->id();
                return $this->redirect('/devtasks/task?edit='. $id);
            }

            if ($request->get('edit')) {
                $dbs->QueryBuilder->select('*')->from('site_devtasks')->where('id = :id')->bind('id', $request->get('edit'));
                $editing = $dbs->get()->one();
            }

            return $this->respond('Dashboard/DevTasks/edit.html.twig', [
                'editing' => isset($editing) ? $editing : null,
            ]);
        });

        // List dev tasks
        $this->route('/devtasks/view/{id}', 'GET|POST', function(Request $request, $id)
        {
            $this->mustBeAdmin();
            $dbs = $this->getModule('database');

            $dbs->QueryBuilder->select('*')->from('site_devtasks')->where('id = :id')->bind('id', $id);

            return $this->respond('Dashboard/DevTasks/view.html.twig', [
                'task' => $dbs->get()->one(),
            ]);
        });
    }
}
