<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%grant_type}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%user}}`
 * - `{{%user}}`
 */
class m260818_173837_create_grant_type_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%grant_type}}', [
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
            '{{%idx-grant_type-created_by}}',
            '{{%grant_type}}',
            'created_by'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-grant_type-created_by}}',
            '{{%grant_type}}',
            'created_by',
            '{{%user}}',
            'id',
            'CASCADE'
        );

        // creates index for column `update_by`
        $this->createIndex(
            '{{%idx-grant_type-update_by}}',
            '{{%grant_type}}',
            'update_by'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-grant_type-update_by}}',
            '{{%grant_type}}',
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
            '{{%fk-grant_type-created_by}}',
            '{{%grant_type}}'
        );

        // drops index for column `created_by`
        $this->dropIndex(
            '{{%idx-grant_type-created_by}}',
            '{{%grant_type}}'
        );

        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-grant_type-update_by}}',
            '{{%grant_type}}'
        );

        // drops index for column `update_by`
        $this->dropIndex(
            '{{%idx-grant_type-update_by}}',
            '{{%grant_type}}'
        );

        $this->dropTable('{{%grant_type}}');
    }
}
