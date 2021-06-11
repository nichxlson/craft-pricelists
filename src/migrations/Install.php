<?php

namespace nichxlson\pricelists\migrations;

use Craft;
use craft\db\Migration;
use craft\helpers\MigrationHelper;

class Install extends Migration
{
    public function safeUp() {
        $this->createTables();
        $this->createIndexes();
        $this->addForeignKeys();

        return true;
    }

    public function safeDown() {
        $this->dropForeignKeys();
        $this->dropTables();
        $this->dropProjectConfig();

        return true;
    }

    protected function createTables() {
        if(!$this->db->tableExists('{{%pricelists_pricelist}}')) {
            $this->createTable('{{%pricelists_pricelist}}', [
                'id' => $this->integer()->notNull(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                'PRIMARY KEY(id)',
            ]);
        }

        if(!$this->db->tableExists('{{%pricelists_pricelist_customer}}')) {
            $this->createTable('{{%pricelists_pricelist_customer}}', [
                'id' => $this->primaryKey(),
                'pricelistId' => $this->integer(),
                'customerId' => $this->integer(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
            ]);
        }
    }

    protected function createIndexes() {
        $this->createIndex(
            $this->db->getIndexName('{{%pricelists_pricelist_customer}}', 'pricelistId', true),
            '{{%pricelists_pricelist_customer}}',
            'pricelistId',
            false
        );

        $this->createIndex(
            $this->db->getIndexName('{{%pricelists_pricelist_customer}}', 'customerId', true),
            '{{%pricelists_pricelist_customer}}',
            'customerId',
            false
        );
    }

    protected function addForeignKeys() {
        $this->addForeignKey(
            $this->db->getForeignKeyName('{{%pricelists_pricelist}}', 'id'),
            '{{%pricelists_pricelist}}',
            'id',
            '{{%elements}}',
            'id',
            'CASCADE',
            null
        );

        $this->addForeignKey(
            $this->db->getForeignKeyName('{{%pricelists_pricelist_customer}}', 'pricelistId'),
            '{{%pricelists_pricelist_customer}}',
            'pricelistId',
            '{{%pricelists_pricelist}}',
            'id',
            'CASCADE',
            null
        );

        $this->addForeignKey(
            $this->db->getForeignKeyName('{{%pricelists_pricelist_customer}}', 'customerId'),
            '{{%pricelists_pricelist_customer}}',
            'customerId',
            '{{%users}}',
            'id',
            'CASCADE',
            null
        );
    }

    protected function dropForeignKeys() {
        if($this->db->tableExists('{{%pricelists_pricelist}}')) {
            MigrationHelper::dropAllForeignKeysOnTable('{{%pricelists_pricelist}}', $this);
        }

        if($this->db->tableExists('{{%pricelists_pricelist_customer}}')) {
            MigrationHelper::dropAllForeignKeysOnTable('{{%pricelists_pricelist_customer}}', $this);
        }
    }

    protected function dropTables() {
        $this->dropTableIfExists('{{%pricelists_pricelist}}');
        $this->dropTableIfExists('{{%pricelists_pricelist_customer}}');
    }

    protected function dropProjectConfig() {

    }
}
