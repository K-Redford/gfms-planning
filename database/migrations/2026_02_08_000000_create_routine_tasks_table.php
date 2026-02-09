<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('routine_tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('display_order');
            $table->text('task_text');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        $tasks = [
            1 => 'Report to MTFI Control Office. Prior arrangements of the visit will be made by the TransFlo Service Manager. Inform of perceived down-time if any. Provide specific Risk Assessment and Method Statements as required and where requested.',
            2 => 'Confirm any existing system issues with the Unit MTFI, AFDS & Fuel Manager against the reported incident.',
            3 => 'Make general assessment of equipment condition - All equipment within Unit Hazardous Area to include Fuel Pumps and AFDS Controller. Ensure that the Hazardous area is cordoned off around the refuelling island and that any one entering the hazardous area is informed of the work being undertaken. All engineers must be dressed in Hi-Visibility clothing at all times when operating within the hazardous area.',
            4 => 'Check correct operation, security and integrity of canopy illumination system and replace tubing. Ensure correct operation of tube starter. Inspect electrical connectivity.',
            5 => 'Ensure clarity of canopy Lexan. Remove and clean with detergent wipes. Replace and check operation. Replace if required.',
            6 => 'Remove and replace existing Data Key Reader. After replacement, check full range of operation. Ensure replacement of anti-tamper devices. Removed device to be returned for disposal.',
            7 => 'Conduct keyboard functionality test. Ensure full operation of all keys and check interface connections. Remove and replace if any functionality test fails. Include Alpha & Numeric checks. Renew Anti-Tamper Device.',
            8 => 'Assess the condition and function of the AFDS Display Lexan. Confirm clarity and readability, including contrast settings. Remove and replace if found to be non-operational. Clean inner lens if required.',
            9 => 'Lubricate and confirm correct operation of enclosure hinges and apply Corrosion Prevention/Weatherproofing Treatment.',
            10 => 'Apply Weatherproofing Treatment to all out-facing AFDS and AFDS-Slave face metalwork and note/report any damage or existing corrosion.',
            11 => 'Ensure all markings are clear, bright and highly visible. Replace those that are unreadable or which have degraded.',
            12 => 'Conduct functionality of manual override switch to ensure manual transaction capture in the event of site power failure etc. Discuss any prior manual intervention with site Manager.',
            13 => 'Check integrity of all Main door gaskets. Any perished or damaged gaskets should be replaced. Water ingress check will identify any none obvious damage to gaskets.',
            14 => 'Check all cabled connections to termination Printed Circuit Board. Ensure there are no splits or cracks in existing cabling & replace if necessary.',
            15 => 'Conduct main board testing. Ensure security of mainboard to AFDS chassis. Check CMOS battery voltage to read above 2.9V, if lower, remove and replace.',
            16 => 'Check and secure main front plate with securing screws. Assess general condition of holding plate and renew Anti-Tamper device.',
            17 => 'Check general condition of all connected Pump Units and the condition of pulse drives for all mechanical Pump units. Ensure secure interface where applicable.',
            18 => 'Confirm the transaction accuracy of all pump units and recording accuracy. If any discrepancies are found, the unit MTFI Manager is to be informed as well as annotating details onto the Service Report Form.',
            19 => 'Check the integrity and function of the present communication method. This includes Line Driver, Disk/SKey and Modem Systems. If unit maintain disk communications, advise on the Authority alternative of hard wired communications. GFMS Securekey is now available to users with obsolete Disk Drives. Annotate to SRF.',
            20 => 'Conduct AFDS access security checks, including the correct operation of AFDS Access Locks. Lubricate mechanisms where needed and ensure the unit hold multiple access keys.',
            21 => 'Check AFDS general condition and remove any visible grease etc. If there is any noticeable damage, advise unit of possible housing replacements etc.',
            22 => 'During data key reader integrity checks, perform a date/time check on the AFDS controller. Alter where necessary and inform the unit of the changes made to the AFDS.',
            23 => 'Where needed and in accordance with MOD requirements, implement AFDS controller software updates in support of GFMS. Annotate current version on the inside panel of the AFDS controller.',
            24 => 'Update door mounted service status labels to reflect the routine complete date.',
            25 => 'Conduct AFDS wide Earthing and Bonding checks. This includes AFDS housing and post elements. All bonding straps must be securely fastened and secured to metal constructs. Particular attention must be paid to the earthing straps between the AFDS door and Chassis. Replace any none serviceable straps and update SRF.',
            26 => 'Checks are to be made on the ATG control panel display. Any reported errors must be annotated on the unit Service Report Form.',
            27 => 'Where Automatic Tank Gauging is in place, inspect all interface connections for security and integrity. Replace if needed.',
            28 => 'Conduct client-side print test. Generate and print system "Test-Page" and ensure protective markings are in place, to read OFFICIAL-SENSITIVE.',
            29 => 'Conduct SyOps against User checks for all nominated GFMS authorised users. If required, supply SyOps documentation to requesting units and complete during routine visits. Any documentation capture to accompany the unit Service Report Form. Units are required to forward any new user requests to DSFA.',
            30 => 'Confirm AV update status and compliance. Run client-side Full System Scan.',
            31 => 'Implement any RFC driven application changes as a result of MOD Change requirements. Annotate any software updates on the unit Service Report Form and within the FM Application > Engineers Information.',
            32 => 'Conduct full Disaster Recovery and Business Continuity tests. Report compliance and success/failure.',
            33 => 'Confirm BC and DR processes, including off-site storage are being correctly implemented by scoped units. Confirm management oversight and awareness of required processes.',
            34 => 'Conduct account audit for both OS and Application accounts. Confirm status with BDUK. Ensure the removal of any redundant Operating System accounts. Compact Front & Back end DBs using MOD-DB-Compact.',
            35 => 'Update any DOBUS supplied AV Updates on McAfee Corporate and run system scan once completed to ensure successful update. Annotate on the Unit Service Report Form. Update AV Capture Application.',
            36 => 'On completion of all testing, inspection and update, complete 2 x copies of the unit Service Report Form. Ensure the Service Report Form is signed in the appropriate box and ensure the Unit POL Store person signs the signature and appointment block. Green copy to remain with the unit.',
            37 => 'Report to the guard room or unit reception point and hand back any documentation or passes that were issued to enable the visit.',
            38 => 'Return Service Report Form details electronically, to the TransFlo Service Manager as soon as is practicable and return hard copies back to TransFlo Instruments within 5 working days for onward processing. Electronic documentation must be sent from MOD Plant ID.',
            39 => 'All engineers, after completing all tasks above are to complete the relevant Asset Capture Forms for the given Plant ID. Any issues not covered above should be included in the comments section of the Capture Card along with Anti-Tamper, Anti-Virus and any configuration changes. Images of any unserviceable or damaged equipment should also be submitted.',
        ];

        $rows = [];
        foreach ($tasks as $order => $text) {
            $rows[] = [
                'display_order' => $order,
                'task_text' => $text,
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('routine_tasks')->insert($rows);
    }

    public function down(): void
    {
        Schema::dropIfExists('routine_tasks');
    }
};
