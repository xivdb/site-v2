<?php

namespace XIVDB\Apps\Users;

//
// User Character CRUD Operations
//
trait UsersCharacters
{
	//
	// Set main character
	//
	public function setMainCharacter($user, $character)
	{
		$dbs = $this->getModule('database');

		// set main character
		$dbs->QueryBuilder
			->update(self::TABLE_MEMBERS)
			->set([
				'lodestone_id' => ':cid',
				'character_name' => ':name',
				'character_server' => ':server',
				'character_avatar' => ':avatar',
			])
			->where(['id = :id'])
			->bind('id', $user->id)
			->bind('cid', $character['id'])
			->bind('name', $character['name'])
			->bind('server', $character['server'])
			->bind('avatar', $character['avatar']);

		$dbs->execute();

		// remove all current "main" from characters
		// table for this member
		$dbs->QueryBuilder
			->update(self::TABLE_CHARACTERS)
			->set(['character_is_main' => 0])
			->where(['member = :id'])
			->bind('id', $user->id);

		$dbs->execute();

		// Set main on characters table
		$dbs->QueryBuilder
			->update(self::TABLE_CHARACTERS)
			->set(['character_is_main' => 1])
			->where(['member = :id', 'lodestone_id = :cid'])
			->bind('id', $user->id)
			->bind('cid', $character['id']);

		$dbs->execute();
	}

	//
	// Get a character that belongs to a user
	//
	public function getCharacter($user, $id)
	{
		$dbs = $this->getModule('database');

		$fields = [
			'lodestone_id as id',
			'character_name as name',
			'character_server as server',
			'character_avatar as avatar',
			'character_is_main',
		];

		// get character
		$dbs->QueryBuilder
			->select($fields, false)
			->from(self::TABLE_CHARACTERS)
			->where(['member = :member', 'lodestone_id = :id'])
			->bind('member', $user->id)
			->bind('id', $id);

		return $dbs->get()->one();
	}

	//
	// Add a new character to a user
	//
	public function addCharacter($user, $character)
	{
		$dbs = $this->getModule('database');

		// If the user has a main character, don't overwrite
		// but if they don't, set it to this character
		if (!$user->hasMainCharacter()) {
			$this->setMainCharacter($user, $character);
		}

		// Add to verified list
		$dbs->QueryBuilder
			->insert(self::TABLE_CHARACTERS)
			->schema([
				'member', 'lodestone_id', 'character_name',
				'character_server', 'character_avatar',
				'character_portrait', 'character_is_main',
			])
			->values([
				':id', ':character', ':name',
				':server', ':avatar', ':portrait',
				':ismain'
			])
			->bind('id', $user->id)
			->bind('character', $character['id'])
			->bind('name', $character['name'])
			->bind('server', $character['server'])
			->bind('avatar', $character['avatar'])
			->bind('portrait', $character['portrait'])
			->bind('ismain', !$user->hasMainCharacter());
		$dbs->execute();
	}

	//
	// Switch main character
	//
	public function switchCharacter($user, $id)
	{
		$dbs = $this->getModule('database');
		$languages = $this->getModule('language');

		// if character found, set it
		if ($character = $this->getCharacter($user, $id)) {
			$this->setMainCharacter($user, $character);
			return $languages->custom(974, [
				'{name}' => $character['name'],
			]);
		}

		return $languages->custom(973);
	}

	//
	// Delete character
	//
	public function deleteCharacter($user, $id)
	{
		$dbs = $this->getModule('database');
		$languages = $this->getModule('language');

		// if character found, set it
		if ($character = $this->getCharacter($user, $id)) {
			$dbs->QueryBuilder
				->delete(self::TABLE_CHARACTERS)
				->where(['member = :member', 'lodestone_id = :id'])
				->bind('member', $user->id)
				->bind('id', $id);

			$dbs->execute();

			// if this was main, remove it from profile
			if ($character['character_is_main']) {
				$dbs->QueryBuilder
					->update(self::TABLE_MEMBERS)
					->set([
						'lodestone_id' => 'NULL',
						'character_name' => 'NULL',
						'character_server' => 'NULL',
						'character_avatar' => 'NULL',
					])
					->where(['id = :id'])
					->bind('id', $user->id);

				$dbs->execute();
			}

			return $languages->custom(979);
		}

		return $languages->custom(973);
	}

	//
	// Has character
	//
	public function hasCharacter($user, $id)
	{
		foreach($user->characters as $char) {
			if ($id == $char->lodestone_id) {
				return true;
			}
		}

		return false;
	}
}
