<?php

use yii\db\Migration;

/**
 * Class m190820_193713_init
 */
class m190820_193713_init extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('city', [
            'id' => $this->primaryKey(),
            'name' => 'varchar(255)',
            'city_en' => 'varchar(255)'


        ], 'engine=innodb');

        $this->createIndex(
            'name',
            'city',
            'name'
        );

        $this->insert('city', ['name' => 'Москва', 'city_en' => 'moskva']);
        $this->insert('city', ['name' => 'Казань', 'city_en' => 'kazan']);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('city');


        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190820_193713_init cannot be reverted.\n";

        return false;
    }
    */
}
