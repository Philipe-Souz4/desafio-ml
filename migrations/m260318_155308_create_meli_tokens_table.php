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
            'id' => $this->primaryKey(),
            'access_token' => $this->text()->notNull(),
            'refresh_token' => $this->string(255)->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        // Inserir os seus tokens atuais para o sistema já iniciar autenticado
        $this->insert('{{%meli_tokens}}', [
            'access_token' => 'APP_USR-5842611738643401-031811-fedb97876c5ff3ed76e1fc6c99d20617-741664235',
            'refresh_token' => 'TG-69bac676d305b90001f42d1a-741664235',
            'updated_at' => time(),
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