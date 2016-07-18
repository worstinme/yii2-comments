<?php

use yii\db\Migration;

/**
 * Handles the creation for table `comment_table`.
 */
class m160714_052845_create_comment_table extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('comments', [
            'id' => $this->primaryKey(),   
            'relation' => $this->string(),
            'item_id' => $this->integer()->notNull(),
            'parent_id' => $this->integer()->notNull(),
            'user_id' => $this->integer(),
            'author' => $this->string(),
            'email' => $this->string(),
            'content' => $this->text(),
            'state' => $this->smallInteger()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'vote_up' => $this->integer(),
            'vote_down' => $this->integer(),
            'params' => $this->text(),
        ], $tableOptions);
    }   

    public function safeDown()
    {

        $this->dropTable('comments');
    }
}
