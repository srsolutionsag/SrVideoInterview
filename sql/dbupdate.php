<#1>
<#2>
<#3>
<#4>
<?php
/**
 * @var $ilDB ilDBInterface
 */
$fields = array(
    'id' => array(
        'notnull' => '1',
        'type' => 'integer',
        'length' => '8',

    ),
    'feedback' => array(
        'type' => 'clob',

    ),
    'resource_id' => array(
        'type' => 'text',
        'length' => '250',

    ),
    'participant_id' => array(
        'notnull' => '1',
        'type' => 'integer',
        'length' => '8',

    ),

);
if (! $ilDB->tableExists('xvin_answer')) {
    $ilDB->createTable('xvin_answer', $fields);
    $ilDB->addPrimaryKey('xvin_answer', array( 'id' ));

    if (! $ilDB->sequenceExists('xvin_answer')) {
        $ilDB->createSequence('xvin_answer');
    }
}
?>
<#5>
<?php
$fields = array(
    'id' => array(
        'notnull' => '1',
        'type' => 'integer',
        'length' => '8',

    ),
    'title' => array(
        'notnull' => '1',
        'type' => 'text',
        'length' => '250',

    ),
    'description' => array(
        'type' => 'clob',

    ),
    'detailed_description' => array(
        'notnull' => '1',
        'type' => 'clob',

    ),
    'resource_id' => array(
        'type' => 'text',
        'length' => '250',

    ),
    'obj_id' => array(
        'notnull' => '1',
        'type' => 'integer',
        'length' => '8',

    ),

);
if (! $ilDB->tableExists('xvin_exercise')) {
    $ilDB->createTable('xvin_exercise', $fields);
    $ilDB->addPrimaryKey('xvin_exercise', array( 'id' ));

    if (! $ilDB->sequenceExists('xvin_exercise')) {
        $ilDB->createSequence('xvin_exercise');
    }
}
?>
<#6>
<?php
$fields = array(
    'id' => array(
        'notnull' => '1',
        'type' => 'integer',
        'length' => '8',

    ),
    'feedback_sent' => array(
        'notnull' => '1',
        'type' => 'integer',
        'length' => '1',

    ),
    'invitation_sent' => array(
        'notnull' => '1',
        'type' => 'integer',
        'length' => '1',

    ),
    'exercise_id' => array(
        'notnull' => '1',
        'type' => 'integer',
        'length' => '8',

    ),
    'user_id' => array(
        'notnull' => '1',
        'type' => 'integer',
        'length' => '8',

    ),

);
if (! $ilDB->tableExists('xvin_participant')) {
    $ilDB->createTable('xvin_participant', $fields);
    $ilDB->addPrimaryKey('xvin_participant', array( 'id' ));

    if (! $ilDB->sequenceExists('xvin_participant')) {
        $ilDB->createSequence('xvin_participant');
    }
}
?>
<#7>
<?php
$field = array(
    'notnull' => '1',
    'type' => 'integer',
    'length' => '8',
);
if (! $ilDB->tableColumnExists('xvin_answer', 'exercise_id')) {
    $ilDB->addTableColumn('xvin_answer', 'exercise_id', $field);
}

if ($ilDB->tableColumnExists('xvin_answer', 'feedback') &&
    ! $ilDB->tableColumnExists('xvin_answer', 'content')
) {
    $ilDB->renameTableColumn('xvin_answer', 'feedback', 'content');
}

if ($ilDB->tableColumnExists('xvin_participant', 'exercise_id') &&
    ! $ilDB->tableColumnExists('xvin_participant', 'obj_id')
) {
    $ilDB->renameTableColumn('xvin_participant', 'exercise_id', 'obj_id');
}
?>
<#8>
<?php
$field = array(
    'notnull' => '1',
    'type' => 'integer',
    'length' => '1'
);
if (! $ilDB->tableColumnExists('xvin_answer', 'type')) {
    $ilDB->addTableColumn('xvin_answer', 'type', $field);
}
?>
<#9>
<?php
if ($ilDB->tableColumnExists('xvin_participant', 'feedback_sent')) {
    $ilDB->dropTableColumn('xvin_participant', 'feedback_sent');
}
?>