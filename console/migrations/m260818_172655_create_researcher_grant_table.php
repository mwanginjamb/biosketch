<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%researcher_grant}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%researcher}}`
 * - `{{%user}}`
 * - `{{%user}}`
 */
class m260818_172655_create_researcher_grant_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%researcher_grant}}', [
            'id' => $this->primaryKey(),
            'researcher_id' => $this->integer(),
            'grant_number' => $this->string(),
            'title' => $this->text(),
            'funding_agency_id' => $this->integer(),
            'grant_type_id' => $this->integer(),
            'role_id' => $this->integer(),
            'amount' => $this->double(),
            'currency' => $this->string(),
            'start_date' => $this->date(),
            'end_date' => $this->date(),
            'status_id' => $this->integer(),
            'description' => $this->text(),
            'created_by' => $this->integer(),
            'update_by' => $this->integer(),
            'created_at' => $this->integer(30),
            'updated_at' => $this->integer(30),
        ]);

        // creates index for column `researcher_id`
        $this->createIndex(
            '{{%idx-researcher_grant-researcher_id}}',
            '{{%researcher_grant}}',
            'researcher_id'
        );

        // add foreign key for table `{{%researcher}}`
        $this->addForeignKey(
            '{{%fk-researcher_grant-researcher_id}}',
            '{{%researcher_grant}}',
            'researcher_id',
            '{{%researcher}}',
            'id',
            'CASCADE'
        );

        // creates index for column `created_by`
        $this->createIndex(
            '{{%idx-researcher_grant-created_by}}',
            '{{%researcher_grant}}',
            'created_by'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-researcher_grant-created_by}}',
            '{{%researcher_grant}}',
            'created_by',
            '{{%user}}',
            'id',
            'CASCADE'
        );

        // creates index for column `update_by`
        $this->createIndex(
            '{{%idx-researcher_grant-update_by}}',
            '{{%researcher_grant}}',
            'update_by'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-researcher_grant-update_by}}',
            '{{%researcher_grant}}',
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
        // drops foreign key for table `{{%researcher}}`
        $this->dropForeignKey(
            '{{%fk-researcher_grant-researcher_id}}',
            '{{%researcher_grant}}'
        );

        // drops index for column `researcher_id`
        $this->dropIndex(
            '{{%idx-researcher_grant-researcher_id}}',
            '{{%researcher_grant}}'
        );

        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-researcher_grant-created_by}}',
            '{{%researcher_grant}}'
        );

        // drops index for column `created_by`
        $this->dropIndex(
            '{{%idx-researcher_grant-created_by}}',
            '{{%researcher_grant}}'
        );

        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-researcher_grant-update_by}}',
            '{{%researcher_grant}}'
        );

        // drops index for column `update_by`
        $this->dropIndex(
            '{{%idx-researcher_grant-update_by}}',
            '{{%researcher_grant}}'
        );

        $this->dropTable('{{%researcher_grant}}');
    }
}
