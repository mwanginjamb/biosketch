<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%researcher_identifier}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%researcher}}`
 */
class m260605_092022_create_researcher_identifier_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%researcher_identifier}}', [
            'id' => $this->primaryKey(),
            'researcher_id' => $this->integer()->notNull(),
            'identifier_type' => $this->string(),
            'identifier_value' => $this->string(),
            'verification_status' => $this->boolean(),
            'created_at' => $this->integer(30),
            'updated_at' => $this->integer(30),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
        ]);

        // creates index for column `researcher_id`
        $this->createIndex(
            '{{%idx-researcher_identifier-researcher_id}}',
            '{{%researcher_identifier}}',
            'researcher_id'
        );

        // add foreign key for table `{{%researcher}}`
        $this->addForeignKey(
            '{{%fk-researcher_identifier-researcher_id}}',
            '{{%researcher_identifier}}',
            'researcher_id',
            '{{%researcher}}',
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
            '{{%fk-researcher_identifier-researcher_id}}',
            '{{%researcher_identifier}}'
        );

        // drops index for column `researcher_id`
        $this->dropIndex(
            '{{%idx-researcher_identifier-researcher_id}}',
            '{{%researcher_identifier}}'
        );

        $this->dropTable('{{%researcher_identifier}}');
    }
}
