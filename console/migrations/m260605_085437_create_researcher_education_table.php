<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%researcher_education}}`.
 */
class m260605_085437_create_researcher_education_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%researcher_education}}', [
            'id' => $this->primaryKey(),
           'researcher_id' => $this->integer()->notNull(),
           'degree' => $this->string(100)->notNull(), 
           'institution_name' => $this->string()->notNull(), 
           'field_of_study' => $this->string(), 
           'graduation_year' => $this->integer(), 
           'sort_order' => $this->integer()->defaultValue(0), 
           'created_at' => $this->integer(),
           'updated_at' => $this->integer(),
           'created_by' => $this->integer(), 
           'updated_by' => $this->integer(),
        ]);

          // creates index for column `researcher_id`
        $this->createIndex(
            '{{%idx-researcher_education-researcher_id}}',
            '{{%researcher_education}}',
            'researcher_id'
        );

        $this->addForeignKey( 'fk-researcher_education-profile_id', '{{%researcher_education}}', 'researcher_id', '{{%researcher}}', 'id', 'CASCADE' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%researcher_education}}');
    }
}
