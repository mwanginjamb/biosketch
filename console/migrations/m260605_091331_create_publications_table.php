<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%publications}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%researcher}}`
 */
class m260605_091331_create_publications_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%publications}}', [
            'id' => $this->primaryKey(),
            'researcher_id' => $this->integer(),
            'title' => $this->text(),
            'journal' => $this->string(),
            'publication_year' => $this->integer(),
            'doi' => $this->string(),
            'pmid' => $this->string(),
            'citation_text' => $this->text(),
            'is_selected' => $this->boolean(),
            'sort_order' => $this->integer(),
            'imported_from' => $this->string(),
            'created_at' => $this->integer(30),
            'updated_at' => $this->integer(30),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
        ]);

        // creates index for column `researcher_id`
        $this->createIndex(
            '{{%idx-publications-researcher_id}}',
            '{{%publications}}',
            'researcher_id'
        );

        $this->createIndex(
            '{{%idx-publications-doi}}',
            '{{%publications}}',
            'doi'
        );
        $this->createIndex(
            '{{%idx-publications-pmid}}',
            '{{%publications}}',
            'pmid'
        );
        $this->createIndex(
            '{{%idx-publications-publication_year}}',
            '{{%publications}}',
            'publication_year'
        );

        $this->createIndex(
            '{{%idx-publications-is_selected}}',
            '{{%publications}}',
            'is_selected'
        );

        // add foreign key for table `{{%researcher}}`
        $this->addForeignKey(
            '{{%fk-publications-researcher_id}}',
            '{{%publications}}',
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
            '{{%fk-publications-researcher_id}}',
            '{{%publications}}'
        );

        // drops index for column `researcher_id`
        $this->dropIndex(
            '{{%idx-publications-researcher_id}}',
            '{{%publications}}'
        );

        $this->dropTable('{{%publications}}');
    }
}
