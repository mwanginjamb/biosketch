<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%grant_role}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%user}}`
 * - `{{%user}}`
 */
class m260818_173908_create_grant_role_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%grant_role}}', [
            'id' => $this->primaryKey(),
            'name' => $this->text(),
            'description' => $this->text(),
            'created_by' => $this->integer(),
            'update_by' => $this->integer(),
            'created_at' => $this->integer(30),
            'updated_at' => $this->integer(30),
        ]);

        // creates index for column `created_by`
        $this->createIndex(
            '{{%idx-grant_role-created_by}}',
            '{{%grant_role}}',
            'created_by'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-grant_role-created_by}}',
            '{{%grant_role}}',
            'created_by',
            '{{%user}}',
            'id',
            'CASCADE'
        );

        // creates index for column `update_by`
        $this->createIndex(
            '{{%idx-grant_role-update_by}}',
            '{{%grant_role}}',
            'update_by'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-grant_role-update_by}}',
            '{{%grant_role}}',
            'update_by',
            '{{%user}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-grant_role-created_by}}',
            '{{%grant_role}}'
        );

        // drops index for column `created_by`
        $this->dropIndex(
            '{{%idx-grant_role-created_by}}',
            '{{%grant_role}}'
        );

        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-grant_role-update_by}}',
            '{{%grant_role}}'
        );

        // drops index for column `update_by`
        $this->dropIndex(
            '{{%idx-grant_role-update_by}}',
            '{{%grant_role}}'
        );

        $this->dropTable('{{%grant_role}}');
    }
}
