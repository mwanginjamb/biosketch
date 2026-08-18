<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%funding_agency}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%user}}`
 * - `{{%user}}`
 */
class m260818_173749_create_funding_agency_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%funding_agency}}', [
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
            '{{%idx-funding_agency-created_by}}',
            '{{%funding_agency}}',
            'created_by'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-funding_agency-created_by}}',
            '{{%funding_agency}}',
            'created_by',
            '{{%user}}',
            'id',
            'CASCADE'
        );

        // creates index for column `update_by`
        $this->createIndex(
            '{{%idx-funding_agency-update_by}}',
            '{{%funding_agency}}',
            'update_by'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-funding_agency-update_by}}',
            '{{%funding_agency}}',
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
            '{{%fk-funding_agency-created_by}}',
            '{{%funding_agency}}'
        );

        // drops index for column `created_by`
        $this->dropIndex(
            '{{%idx-funding_agency-created_by}}',
            '{{%funding_agency}}'
        );

        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-funding_agency-update_by}}',
            '{{%funding_agency}}'
        );

        // drops index for column `update_by`
        $this->dropIndex(
            '{{%idx-funding_agency-update_by}}',
            '{{%funding_agency}}'
        );

        $this->dropTable('{{%funding_agency}}');
    }
}
