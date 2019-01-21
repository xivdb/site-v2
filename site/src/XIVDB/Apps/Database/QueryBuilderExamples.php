<?php
// ------------------------------------
// These are examples, placed in a php
// for syntax highlighting
// ------------------------------------

// using app classes
$dbs = $this->getModule('database');
$sql = $dbs->QueryBuilder;


// old way
$db = new Database();

$qb = $this->get('querybuilder');
$qb->select('*')->from('site_devblog')->order('id', 'desc')->show();

$db->get()->all();
$db->get()->one();


// select * from members;
$db->sql('select * from members');

// SELECT * FROM xiv_items
$db->QueryBuilder->select('*')->from('xiv_items')->show();

// SELECT members.`name`,members.`email` FROM members
$db->QueryBuilder->select('members.name, members.email')->from('members')->show();

// SELECT members.`name`,members.`email`,members.`avatar` FROM members
$db->QueryBuilder->select([ 'members' => 'name, email, avatar'])->from('members')->show();

// SELECT members.`name`,members.`email`,members.`avatar` FROM members
$db->QueryBuilder->select([ 'members' => ['name', 'email', 'avatar']])->from('members')->show();

// SELECT `name`,`email`,`avatar` FROM members
$db->QueryBuilder->select('name, email, avatar')->from('members')->show();

// SELECT count(*) as total FROM xiv_items
$db->QueryBuilder->total()->from('xiv_items')->show();


// Can also append columns on later

$sql->select(false)
    ->from(ContentDB::ITEMS)
    ->addColumns([ 'table' => 'col col col' ]);


// ------------------------------------

// SELECT * FROM members WHERE (id = 1)
$dbs->QueryBuilder
    ->select()
    ->from('members')
    ->where('id = 1')
    ->show();

// SELECT * FROM members WHERE (id = :id)
// bound :id = 1
$dbs->QueryBuilder
    ->select()
    ->from('members')
    ->where('id = :id')
    ->bind('id', 1)
    ->show();

// SELECT * FROM members WHERE (id = 1 or name = dev)
$dbs->QueryBuilder
    ->select()
    ->from('members')
    ->where(['id = 1', 'name = dev'], 'or')
    ->show();

// SELECT * FROM members LEFT JOIN othertable ON othertable.member_id = members.id
$dbs->QueryBuilder
    ->select()
    ->from('members')
    ->join('members.id', 'othertable.member_id')
    ->show();

// SELECT * FROM members GROUP BY id
$dbs->QueryBuilder
    ->select()
    ->from('members')
    ->group('id')
    ->show();

// SELECT * FROM members id DESC
$dbs->QueryBuilder
    ->select()
    ->from('members')
    ->order('id', 'desc')
    ->show();

// SELECT * FROM members LIMIT 0,50
$dbs->QueryBuilder
    ->select()
    ->from('members')
    ->limit(0, 50)
    ->show();

// ------------------------------------

// UPDATE members SET column = 3
$dbs->QueryBuilder
    ->update('members')
    ->set('column', 3);

// UPDATE members SET a = 1, b = 2, c = 3
$dbs->QueryBuilder
    ->update('members')
    ->set('a', 1)
    ->set('b', 2)
    ->set('c', 3)


// ------------------------------------

// INSERT INTO members (email, username, password) VALUES ('123','123','123')
$this
    ->insert('members')
    ->schema(['email', 'username', 'password'])
    ->values(['123', '123', '123']);

// INSERT INTO members (email, username, password) VALUES ('123','123','123'),('123','123','123'),('123','123','123')
$this
    ->insert('members')
    ->schema(['email', 'username', 'password'])
    ->values(['123', '123', '123'])
    ->values(['123', '123', '123'])
    ->values(['123', '123', '123']);

// ------------------------------------
//
// DELETE FROM members WHERE id = 1
$this
    ->delete('members')
    ->where('id = 1');
