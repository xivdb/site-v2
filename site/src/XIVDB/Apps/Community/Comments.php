<?php

namespace XIVDB\Apps\Community;

//
// Comments class
//
class Comments extends \XIVDB\Apps\AppHandler
{
	use CommentsTally;
	use CommentsOrganize;
	use CommentsReplies;

	const TABLE = 'content_comments';
	const TABLE_MEMBERS = 'members';
	const TABLE_LINK_LIMIT = 300;

	protected $dbs;
	protected $users;
	protected $patchlist;
	protected $data;

	protected $columnsComments = [
		'id', 'uniq', 'content', 'time', 'edited', 'member',
		'text', 'pinned', 'deleted', 'reply_to',
	];

	protected $columnsMembers = [
		'id as member', 'username', 'lodestone_id', 'character_name',
		'character_server', 'character_avatar', 'star',
	];

    /**
     * Comments constructor.
     * @param bool $skip
     */
	function __construct($skip = false)
	{
		$this->dbs = $this->getModule('database');

		// Populare patch list
		$this->dbs
			->QueryBuilder
			->select('*', false)
			->From('db_patch')
			->order('patch', 'DESC');

		$this->patchlist = $this->dbs->get()->all();
	}

    /**
     * @param $uniqueId
     * @param $contentId
     * @return mixed
     */
	public function get($uniqueId, $contentId)
	{
		$this->dbs
			->QueryBuilder
			->select()
			->from(self::TABLE)
			->addColumns([ self::TABLE => $this->columnsComments ])
			->addColumns([ self::TABLE_MEMBERS => $this->columnsMembers ])
			->join([ self::TABLE => 'member' ], [ self::TABLE_MEMBERS => 'id' ])
			->where([
				self::TABLE .'.uniq = :uid',
				self::TABLE .'.content = :cid',
			])
			->bind('uid', $uniqueId)
			->bind('cid', $contentId)
			->order('time', 'desc')
			->limit(0,500);

		$this->data = $this->dbs->get()->all();
		$this->format();
		$this->sortForReplies();

		return $this->data;
	}

    /**
     * Get the latest comment for a specific content piece
     *
     * @param $uniqueId
     * @param $contentId
     */
	public function getLatestComment($uniqueId, $contentId)
    {
        $data = $this->get($uniqueId, $contentId);
        return reset($data);
    }

    /**
     * @param $userId
     * @return mixed
     */
	public function getViaUserId($userId)
	{
		$this->dbs
			->QueryBuilder
			->select()
			->from(self::TABLE)
			->addColumns([ self::TABLE => $this->columnsComments ])
			->where([
				self::TABLE .'.member = :member',
			])
			->bind('member', $userId)
			->order('time', 'desc')
			->limit(0,500);

		$this->data = $this->dbs->get()->all();
		$this->format();

		return $this->data;
	}

	//
	// Post a comment
	//
	public function post($request)
	{
		if (!$this->getUser()) {
			return [false, 'User is not logged in'];
		}

		// get
        $cid = $request->get('cid');
        $uid = $request->get('uid');
        $message = $request->get('message');
        $isReply = $request->get('reply');

		// validate ...
        if (!$cid || !$uid || !$message) {
            return [false, 'Empty submitted data'];
        }

		// insert array
        $insert = [
            'uniq' => ':uniq',
            'content' => ':content',
            'member' => ':member',
            'text' => ':text',
        ];

		// if this is a reply
        if ($isReply) {
            $insert['reply_to'] = ':reply';
        }

		$this->dbs
			->QueryBuilder
			->insert(self::TABLE)
			->schema(array_keys($insert))
			->values($insert)
			->bind('uniq', $uid)
			->bind('content', $cid)
			->bind('member', $this->GetUser()->id)
			->bind('text', $message);

		if ($isReply) {
			$this->dbs
				->QueryBuilder
				->bind(':reply', $isReply);
        }

		$this->dbs->execute();
		return [true];
	}

	//
	// Update a comment
	//
	public function update($request)
	{
		if (!$this->getUser()) {
			return [false, 'User is not logged in'];
		}

		// get
        $cid = $request->get('cid');
        $uid = $request->get('uid');
        $message = $request->get('message');
        $postId = $request->get('postId');

		// validate ...
        if (!$cid || !$uid || !$message || !$postId) {
            return [false, 'Empty submitted data'];
        }

		// get the current post
		$this->dbs
			->QueryBuilder
			->select('*', false)
			->from(self::TABLE)
			->where([
				'id = :id',
				'member = :mid',
				'deleted = 0'
			])
			->bind('id', $postId)
			->bind(':mid', $this->getUser()->id)
			->limit(0,1);

		$post = $this->dbs->get()->one();
		if (!$post) {
            return [false, 'Invalid post access'];
        }

		// update post
		$this->dbs
			->QueryBuilder
			->update(self::TABLE)
			->set([ 'text' => ':message' ])
			->where([
				'id = :id',
				'member = :mid',
				'deleted = 0'
			])
			->bind(':message', $message)
			->bind('id', $postId)
			->bind(':mid', $this->getUser()->id);

		$this->dbs->execute();
		return [true];
	}

	//
	// Delete a post
	//
	public function delete($request)
	{
		if (!$this->getUser()) {
			return [false, 'User is not logged in'];
		}

		// get
        $cid = $request->get('cid');
        $uid = $request->get('uid');
        $postId = $request->get('postId');

		// validate ...
        if (!$cid || !$uid || !$postId) {
            return [false, 'Empty submitted data'];
        }

		// get the current post
		$this->dbs
			->QueryBuilder
			->select('*', false)
			->from(self::TABLE)
			->where([
				'id = :id',
				'member = :mid',
				'deleted = 0'
			])
			->bind('id', $postId)
			->bind(':mid', $this->getUser()->id)
			->limit(0,1);

		$post = $this->dbs->get()->one();
		if (!$post) {
            return [false, 'Invalid post access'];
        }

		// update post
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
			->bind('id', $postId)
			->bind(':mid', $this->getUser()->id);

		$this->dbs->execute();
		return [true];
	}

	//
	// Get the total comments for some piece of content
	//
	public function getTotal($id, $cid)
	{
		$this->dbs
			->QueryBuilder
			->total()
			->from(self::TABLE)
			->where('uniq = :uniq')
			->where('content = :content')
			->bind(':uniq', $id)
			->bind(':content', $cid);

		return $this->dbs->get()->total();
	}

	//
	// Get the most recent comments
	//
	public function getRecent($total = 30, $search = false)
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

		if (trim($search)) {
		    $this->dbs
                ->QueryBuilder
                ->where('text LIKE :text')
                ->bind('text', '%'. trim($search) .'%');
        }

		$comments = $this->dbs->get()->all();

		$users = $this->getModule('users');
		$content = $this->getModule('content');

		// append user onto comments
		foreach($comments as $i => $cmt) {
			$comments[$i]['member'] = $users->get($cmt['member'], true);
			$comments[$i]['text'] = $this->markdown(htmlentities($cmt['text']));
			$comments[$i]['time'] = $this->getModule('moment', $cmt['time'])->fromNow()->getRelative();
			$comments[$i]['content'] = $content->setCid($cmt['content'])->getContentToId($cmt['content'], $cmt['uniq']);
		}

		return $comments;
	}

	//
	// Get comments linked to some piece of content
	//
	public function getLinked($id, $type)
	{
		// create link string
		$link = sprintf('%s/%s/', $type, $id);
		$cacheKey = 'linked_comment'. md5($link);

		// ensure a good length
		if (strlen($link) < 3) {
			return false;
		}

		$this->dbs
			->QueryBuilder
			->select()
			->from(self::TABLE)
			->addColumns([ self::TABLE => [
				'id', 'uniq', 'content', 'time', 'text'
			]])
			->addColumns([ self::TABLE_MEMBERS => [
				'username', 'avatar', 'lodestone_id', 'character_name', 'character_server',
				'character_avatar', 'star'
			]])
            ->join([ self::TABLE => 'member' ], [ self::TABLE_MEMBERS => 'id' ])
			->where("{comment}.text LIKE :url")
			->where('{comment}.deleted = 0')
			->bind(':url', '%'. $link .'%')
			->order('time', 'desc')
			->limit(0, self::TABLE_LINK_LIMIT)
			->replace('{comment}', self::TABLE)
			->replace('{member}', self::TABLE_MEMBERS);

		// get comments
		if (!$comments = $this->dbs->get()->all()) {
			return false;
		}

		return $comments;
	}
}
