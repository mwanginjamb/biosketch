<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%researcher_profile}}`.
 */
class m260605_084150_create_researcher_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%researcher}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'title' => $this->string(),
            'full_name' => $this->string()->notNull(),
            'primary_institution' => $this->string(),
            'department' => $this->string(),
            'role_title' => $this->string(),
            'email' => $this->string(),
            'website' => $this->string(500),
            'location' => $this->string(),
            'era_commons_id' => $this->string(100),
            'orcid' => $this->string(100),
            'profile_photo' => $this->string(500),
            'status' => $this->smallInteger()->defaultValue(0),
            'version' => $this->integer()->defaultValue(1),

            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'created_by' => $this->integer(), 
            'updated_by' => $this->integer(),
        ]);

        //index for user_id
          // creates index for column `researcher_id`
        $this->createIndex(
            '{{%idx-researcher-user_id}}',
            '{{%researcher}}',
            'user_id'
        );

        $this->addForeignKey( 'fk-researcher-user_id', '{{%researcher}}', 'user_id', '{{%user}}', 'id', 'CASCADE' );
  

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%researcher_profile}}');
    }
}
