<?php

namespace XIVDB\Apps\Community;

//
// Comments class
//
class Screenshots extends \XIVDB\Apps\AppHandler
{
	use ScreenshotsTally;
	use ScreenshotsFiles;

	const TABLE = 'content_screenshots';
	const TABLE_MEMBERS = 'members';

	protected $dbs;
	protected $data;

	protected $columnsScreenshots = [
		'id', 'uniq', 'content', 'time','member',
		'pinned', 'deleted', 'filename',
	];

	protected $columnsMembers = [
		'id as member', 'username', 'lodestone_id', 'character_name',
		'character_server', 'character_avatar', 'star', 'approved',
	];

	function __construct()
	{
		$this->dbs = $this->getModule('database');
	}

	//
	// Get comments for a piece of content
	//
	public function get($uniqueId, $contentId)
	{
		$this->dbs
			->QueryBuilder
			->select()
			->from(self::TABLE)
			->addColumns([ self::TABLE => $this->columnsScreenshots ])
			->addColumns([ self::TABLE_MEMBERS => $this->columnsMembers ])
			->join([ self::TABLE => 'member' ], [ self::TABLE_MEMBERS => 'id' ])
			->where([
				self::TABLE .'.uniq = :uid',
				self::TABLE .'.content = :cid',
				self::TABLE .'.deleted = 0',
			])
			->bind('uid', $uniqueId)
			->bind('cid', $contentId)
			->order('time', 'desc')
			->limit(0,500);

		$this->data = $this->dbs->get()->all();

		// Set image paths
		foreach($this->data as $i => $image) {
			// get content text
            $contentText = array_search($image['content'], \XIVDB\Apps\Content\ContentDB::$contentIds);
            $image['content_text'] = $contentText;

            $folder = '/screenshots/'. $contentText .'/'. $image['uniq'] .'/';
            $image['folder'] = $folder;
            $image['file'] = $folder . $image['filename'];
            $image['small'] = $folder .'small_'. $image['filename'];
            $this->data[$i] = $image;
        }

		return $this->data;
	}

    /**
     * Get the latest screenshot for a specific content piece
     *
     * @param $uniqueId
     * @param $contentId
     */
    public function getLatestScreenshot($uniqueId, $contentId)
    {
        $data = $this->get($uniqueId, $contentId);
        return reset($data);
    }

	//
	// Get screenshots for a specific user id
	//
	public function getViaUserId($userId)
	{
		$this->dbs
			->QueryBuilder
			->select()
			->from(self::TABLE)
			->addColumns([ self::TABLE => $this->columnsScreenshots ])
			->where([
				self::TABLE .'.member = :member',
				self::TABLE .'.deleted = 0',
			])
			->bind('member', $userId)
			->order('time', 'desc')
			->limit(0,500);

		$this->data = $this->dbs->get()->all();

		// Set image paths
		foreach($this->data as $i => $image) {
			// get content text
            $contentText = array_search($image['content'], \XIVDB\Apps\Content\ContentDB::$contentIds);
            $image['content_text'] = $contentText;

			// create paths
            $folder = '/screenshots/'. $contentText .'/'. $image['uniq'] .'/';
            $image['folder'] = $folder;
            $image['file'] = $folder . $image['filename'];
            $image['small'] = $folder .'small_'. $image['filename'];
            $this->data[$i] = $image;
        }

		return $this->data;
	}

	//
	// Delete an image
	//
	public function delete($request)
	{
		if (!$this->getUser()) {
			return [false, 'User is not logged in'];
		}

		// get
        $cid = $request->get('cid');
        $uid = $request->get('uid');
        $screenshotId = $request->get('screenshotId');

		// validate ...
        if (!$cid || !$uid || !$screenshotId) {
            return [false, 'Empty submitted data'];
        }

		// get the screenshot
		$this->dbs
			->QueryBuilder
			->select('*', false)
			->from(self::TABLE)
			->where([
				'id = :id',
				'member = :mid'
			])
			->bind('id', $screenshotId)
			->bind(':mid', $this->getUser()->id)
			->limit(0,1);

		$post = $this->dbs->get()->one();
		if (!$post) {
            return [false, 'Invalid post access'];
        }

		// update screenshot
		$this->dbs
			->QueryBuilder
			->update(self::TABLE)
			->set([
				'deleted' => '1',
				'pinned' => '0'
			])
			->where([
				'id = :id',
				'member = :mid',
			])
			->bind('id', $screenshotId)
			->bind(':mid', $this->getUser()->id);

		$this->dbs->execute();
		return [true];
	}

	//
	// Get the total screenshots for some piece of content
	//
	public function getTotal($uniqueId, $contentId)
	{
		$this->dbs
			->QueryBuilder
			->total()
			->from(self::TABLE)
			->where([
				self::TABLE .'.uniq = :uid',
				self::TABLE .'.content = :cid',
				self::TABLE .'.deleted = 0',
			])
			->bind('uid', $uniqueId)
			->bind('cid', $contentId);

		return $this->dbs->get()->total();
	}

	//
	// Get the most recent screenshots
	//
	public function getRecent($total = 30, $id = false)
	{
		$this->dbs
			->QueryBuilder
			->select('*', false)
			->from(self::TABLE)
			->where([
				'deleted = 0'
			])
			->order('time', 'desc')
			->limit(0, $total);

		if (trim($id)) {
		    $this->dbs
                ->QueryBuilder
                ->where('id = :id')
                ->bind('id', $id);
        }

		$screenshots = $this->dbs->get()->all();

		$users = $this->getModule('users');
		$content = $this->getModule('content');

		// append user onto screenshots
		foreach($screenshots as $i => $ss) {
			$screenshots[$i]['member'] = $users->get($ss['member']);
			$screenshots[$i]['time'] = $this->getModule('moment', $ss['time'])->fromNow()->getRelative();
			$screenshots[$i]['content'] = $content->setCid($ss['content'])->getContentToId($ss['content'], $ss['uniq']);

			$contentText = $content->getContentIdName($ss['content']);
			$folder = '/screenshots/'. $contentText .'/'. $ss['uniq'] .'/';

			$screenshots[$i]['file'] = $folder . $ss['filename'];
			$screenshots[$i]['small'] =  $folder .'small_'. $ss['filename'];

			$screenshots[$i]['url'] = $screenshots[$i]['content']['url'] . '#lb='. $ss['id'];
		}

		return $screenshots;
	}

	//
	// Get a banner for the content
	//
	public function getContentBanner($id, $cid)
	{
		$this->dbs
			->QueryBuilder
			->select('*')
			->from(self::TABLE)
			->where('banner = 1')
			->where('uniq = :uniq')
			->where('content = :content')
			->bind(':uniq', $id)
			->bind(':content', $cid);

		return $this->dbs->get()->one();
	}
}
