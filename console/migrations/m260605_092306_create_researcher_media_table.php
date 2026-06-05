<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%researcher_media}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%researcher}}`
 */
class m260605_092306_create_researcher_media_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%researcher_media}}', [
            'id' => $this->primaryKey(),
            'researcher_id' => $this->integer()->notNull(),
            'media_type' => $this->string(),
            'file_path' => $this->string(),
            'file_name' => $this->string(),
            'mime_type' => $this->string(),
            'file_size' => $this->integer(),
            'created_at' => $this->integer(30),
            'updated_at' => $this->integer(30),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
        ]);

        // creates index for column `researcher_id`
        $this->createIndex(
            '{{%idx-researcher_media-researcher_id}}',
            '{{%researcher_media}}',
            'researcher_id'
        );

        // add foreign key for table `{{%researcher}}`
        $this->addForeignKey(
            '{{%fk-researcher_media-researcher_id}}',
            '{{%researcher_media}}',
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
            '{{%fk-researcher_media-researcher_id}}',
            '{{%researcher_media}}'
        );

        // drops index for column `researcher_id`
        $this->dropIndex(
            '{{%idx-researcher_media-researcher_id}}',
            '{{%researcher_media}}'
        );

        $this->dropTable('{{%researcher_media}}');
    }
}
