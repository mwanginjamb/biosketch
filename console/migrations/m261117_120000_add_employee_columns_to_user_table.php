<?php

declare(strict_types=1);

use yii\db\Migration;

/**
 * Adds employee directory details captured at signup to the `{{%user}}` table.
 *
 * All columns are nullable: not every account has a staff record, and some
 * staff records have no department (directory shows "No Dept.").
 */
class m261117_120000_add_employee_columns_to_user_table extends Migration
{
    public function safeUp(): void
    {
        $this->addColumn('{{%user}}', 'staff_id', $this->string(50)->null()->after('email'));
        $this->addColumn('{{%user}}', 'department', $this->string(100)->null()->after('staff_id'));
        $this->addColumn('{{%user}}', 'department_code', $this->string(50)->null()->after('department'));

        // One account per employee; NULLs are allowed multiple times.
        $this->createIndex('{{%idx-user-staff_id}}', '{{%user}}', 'staff_id', true);
    }

    public function safeDown(): void
    {
        $this->dropIndex('{{%idx-user-staff_id}}', '{{%user}}');
        $this->dropColumn('{{%user}}', 'department_code');
        $this->dropColumn('{{%user}}', 'department');
        $this->dropColumn('{{%user}}', 'staff_id');
    }
}
