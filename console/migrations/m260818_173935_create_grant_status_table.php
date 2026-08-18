<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%grant_status}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%user}}`
 * - `{{%user}}`
 */
class m260818_173935_create_grant_status_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%grant_status}}', [
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
            '{{%idx-grant_status-created_by}}',
            '{{%grant_status}}',
            'created_by'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-grant_status-created_by}}',
            '{{%grant_status}}',
            'created_by',
            '{{%user}}',
            'id',
            'CASCADE'
        );

        // creates index for column `update_by`
        $this->createIndex(
            '{{%idx-grant_status-update_by}}',
            '{{%grant_status}}',
            'update_by'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-grant_status-update_by}}',
            '{{%grant_status}}',
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
            '{{%fk-grant_status-created_by}}',
            '{{%grant_status}}'
        );

        // drops index for column `created_by`
        $this->dropIndex(
            '{{%idx-grant_status-created_by}}',
            '{{%grant_status}}'
        );

        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-grant_status-update_by}}',
            '{{%grant_status}}'
        );

        // drops index for column `update_by`
        $this->dropIndex(
            '{{%idx-grant_status-update_by}}',
            '{{%grant_status}}'
        );

        $this->dropTable('{{%grant_status}}');
    }
}
