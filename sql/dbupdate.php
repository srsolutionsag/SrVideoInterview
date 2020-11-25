<#1>
<?php
/**
 * @var $ilDB ilDBInterface
 */
$fields = array(
    'id' => array(
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

);
if (! $ilDB->tableExists('xvin_interview')) {
    $ilDB->createTable('xvin_interview', $fields);
    $ilDB->addPrimaryKey('xvin_interview', array( 'id' ));

    if (! $ilDB->sequenceExists('xvin_interview')) {
        $ilDB->createSequence('xvin_interview');
    }
}
?>
<#2>
<?php
/**
 * @var $ilDB ilDBInterface
 */
$fields = array(
    'id' => array(
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
    'question' => array(
        'notnull' => '1',
        'type' => 'clob',

    ),
    'resource_id' => array(
        'type' => 'text',
        'length' => '250',

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
<#3>
<?php
/**
 * @var $ilDB ilDBInterface
 */
$fields = array(
    'id' => array(
        'type' => 'integer',
        'length' => '8',

    ),
    'video_interview_id' => array(
        'notnull' => '1',
        'type' => 'integer',
        'length' => '8',

    ),
    'exercise_id' => array(
        'notnull' => '1',
        'type' => 'integer',
        'length' => '8',

    ),

);

if (! $ilDB->tableExists('xvin_exercise_ref')) {
    $ilDB->createTable('xvin_exercise_ref', $fields);
    $ilDB->addPrimaryKey('xvin_exercise_ref', array( 'id' ));

    if (! $ilDB->sequenceExists('xvin_exercise_ref')) {
        $ilDB->createSequence('xvin_exercise_ref');
    }
}
?>
