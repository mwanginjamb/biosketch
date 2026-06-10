<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%grants}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%researcher}}`
 */
class m260610_193853_create_grants_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%grants}}', [
            'id' => $this->primaryKey(),
            'researcher_id' => $this->integer(),
            'sponsor' => $this->string(),
            'grant_number' => $this->string(100),
            'title' => $this->string(),
            'role' => $this->string(100),
            'total_award_amount' => $this->float(),
            'currency' => $this->string(4)->defaultValue('USD'),
            'status' => $this->string(25)->defaultValue('ACTIVE'),
            'start_date' => $this->date(),
            'end_date' => $this->date(),
            'description' => $this->text(),
            'created_at' => $this->integer(25),
            'updated_at' => $this->integer(25),
        ]);

        // creates index for column `researcher_id`
        $this->createIndex(
            '{{%idx-grants-researcher_id}}',
            '{{%grants}}',
            'researcher_id'
        );

        // add foreign key for table `{{%researcher}}`
        $this->addForeignKey(
            '{{%fk-grants-researcher_id}}',
            '{{%grants}}',
            'researcher_id',
            '{{%researcher}}',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'idx-researcher_grant-status',
            '{{%grants}}',
            'status'
        );

        $this->createIndex(
            'idx-researcher_grant-grant_number',
            '{{%grants}}',
            'grant_number'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%researcher}}`
        $this->dropForeignKey(
            '{{%fk-grants-researcher_id}}',
            '{{%grants}}'
        );

        // drops index for column `researcher_id`
        $this->dropIndex(
            '{{%idx-grants-researcher_id}}',
            '{{%grants}}'
        );

        $this->dropTable('{{%grants}}');
    }
}
