<?php

declare(strict_types=1);

use yii\db\Migration;

/**
 * API bearer tokens. Only a SHA-256 hash of each token is stored; the plain
 * token is shown to the client once, when it is issued.
 */
class m261117_140000_create_user_api_token_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('{{%user_api_token}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'name' => $this->string(100)->null(),
            'token_hash' => $this->char(64)->notNull(),
            'expires_at' => $this->integer()->null(),
            'last_used_at' => $this->integer()->null(),
            'created_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex('{{%idx-user_api_token-token_hash}}', '{{%user_api_token}}', 'token_hash', true);
        $this->createIndex('{{%idx-user_api_token-user_id}}', '{{%user_api_token}}', 'user_id');
        $this->addForeignKey(
            '{{%fk-user_api_token-user_id}}',
            '{{%user_api_token}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE',
        );
    }

    public function safeDown(): void
    {
        $this->dropTable('{{%user_api_token}}');
    }
}
