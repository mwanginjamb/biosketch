<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%researcher_statement}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%researcher}}`
 */
class m260605_090821_create_researcher_statement_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%researcher_statement}}', [
            'id' => $this->primaryKey(),
            'researcher_id' => $this->integer(),
            'statement_type' => $this->string(),
            'content' => $this->text(),
            'created_at' => $this->integer(30),
            'updated_at' => $this->integer(30),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
        ]);

        // creates index for column `researcher_id`
        $this->createIndex(
            '{{%idx-researcher_statement-researcher_id}}',
            '{{%researcher_statement}}',
            'researcher_id'
        );

        // add foreign key for table `{{%researcher}}`
        $this->addForeignKey(
            '{{%fk-researcher_statement-researcher_id}}',
            '{{%researcher_statement}}',
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
            '{{%fk-researcher_statement-researcher_id}}',
            '{{%researcher_statement}}'
        );

        // drops index for column `researcher_id`
        $this->dropIndex(
            '{{%idx-researcher_statement-researcher_id}}',
            '{{%researcher_statement}}'
        );

        $this->dropTable('{{%researcher_statement}}');
    }
}
