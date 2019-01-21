<?php

namespace XIVDB\Apps\Characters;

trait Member
{
	protected function handleMember()
	{
		$dbs = $this->getModule('database');
		$dbs->QueryBuilder
			->select(['member'])
			->from('members_characters')
			->where('lodestone_id = :id AND character_is_main = 1')
			->bind('id', $this->chardata['lodestone_id']);

		if ($data = $dbs->get()->one()) {
			$users = $this->getModule('users');
			$member = $users->get($data['member'], true);
			$this->tempdata['member'] = $member ? $member->toArray() : false;
		}
	}
}
