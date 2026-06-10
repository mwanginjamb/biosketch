<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%researcher}}`.
 */
class m260610_223254_add_qualitative_column_to_researcher_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%researcher}}', 'major_breakthrough', $this->text());
        $this->addColumn('{{%researcher}}', 'patent_filed', $this->text());
        $this->addColumn('{{%researcher}}', 'research_tags', $this->string(350));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%researcher}}', 'major_breakthrough');
        $this->dropColumn('{{%researcher}}', 'patent_filed');
        $this->dropColumn('{{%researcher}}', 'research_tags');
    }
}
