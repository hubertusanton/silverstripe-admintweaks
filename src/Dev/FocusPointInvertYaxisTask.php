<?php

namespace Restruct\Silverstripe\AdminTweaks\Dev;

use SilverStripe\Assets\Image;
use SilverStripe\Dev\BuildTask;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DB;
use SilverStripe\ORM\Queries\SQLUpdate;
use SilverStripe\Versioned\Versioned;

class FocusPointInvertYaxisTask
    extends BuildTask

{
    private static $segment = 'FocusPointInvertYaxisTask';
    protected $title = 'Invert all Focus-Point Y-Axis values (v2↔v3)';
    protected $description = 'Y-axis normally gets auto-migrated by FocusPointMigrationTask on dev/build. This tasks just inverts all Y values, eg in case the migration did not succeed or run correctly.';

    public function run($request)
    {
        $schema = DataObject::getSchema();
        $imageTable = $schema->tableName(Image::class);
        $fields = DB::field_list($imageTable);

        if (!isset($fields["FocusPointY"])) {
            return;
        }

        // Safety net
        if (!isset($fields["FocusPointY"])) {
            throw new \Exception("$imageTable table does not have \"FocusPointY\" fields. Did you run dev/build?");
        }

        // Update all Image tables
        $imageTables = [
            $imageTable,
            $imageTable . "_" . Versioned::LIVE,
            $imageTable . "_Versions",
        ];

        DB::get_conn()->withTransaction(function() use ($imageTables) {
            foreach ($imageTables as $imageTable) {
                $query = SQLUpdate::create("\"$imageTable\"")
                    ->assignSQL('FocusPointY', "'FocusPointY' * -1")
                    ->execute();
            }

            DB::get_schema()->alterationMessage('Inverted FocusPointY values in tables '.implode(', ', $imageTables), 'changed');
        } , function () {
            DB::get_schema()->alterationMessage('Failed to alter FocusPoint fields', 'error');
        }, false, true);
    }
}
