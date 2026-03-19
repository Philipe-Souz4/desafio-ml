<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%meli_tokens}}`.
 */
class m260318_155308_create_meli_tokens_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%meli_tokens}}', [
            'id'            => $this->primaryKey(),
            'access_token'  => $this->text()->notNull(),
            'refresh_token' => $this->string(255)->notNull(),
            'updated_at'    => $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%meli_tokens}}');
    }
}