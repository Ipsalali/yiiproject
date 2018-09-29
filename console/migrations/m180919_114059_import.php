<?php

use console\migrations\Migration;

/**
 * Class m180919_114059_import
 */
class m180919_114059_import extends Migration
{
    public $tables=[
        'app_status',
        'type_packaging',
        'supplier_countries',
        'payments',
        'client_category',
        'user',
        'user_history',
        'sender',
        'sender_history',
        'sellers',
        'post',
        //'payment_state',
        'organisation',
        'autotruck',
        'autotruck_history',
        'client',
        'client_history',
        'client_organisation',
        'app_trace',
        //'customer_payment',
        'manager_country',
        'payments_expenses',
        'payments_expenses_history',
        'expenses_manager',
        'expenses_manager_history',
        'transfers_package',
        'transfers_package_history',
        'transfer',
        'transfer_history',
        'seller_expenses',
        'seller_expenses_history',
        'user_sverka',
        'payment_client_by_transfer',
        'payment_client_by_transfer_history',
        'auth_assignment',
        'auth_item_child',
        'app',
        'app_history',
        'autotruck_notification'
    ];

    public $file_frefix = 'webali_crmted_table_';

    public function safeUp()
    {   
        if($this->tables && is_array($this->tables)){
            foreach ($this->tables as $table) {
                $this->importFile($table,$this->file_frefix);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        if($this->tables && is_array($this->tables)){
            $tables = array_reverse($this->tables);
            foreach ($tables as $table) {
                $this->delete($table);
            }
        }
    }

}
