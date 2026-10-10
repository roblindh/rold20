<?php

function show_classes() {
    global $db_server, $db_user, $db_password, $db_name;

    $db = Database::getInstance(); $db->connect($db_server, $db_user, $db_password, $db_name);
    $query = "SELECT * FROM classes";
    $result = $db->query($query);
    ?>
    <p>
        <em>HP per Level:</em> The number of HP gained per level (including 1st) in this class.<br/>
        <em>SP per Level:</em> The number of SP gained per level (including 1st) in this class.<br/>
        <em>PP per Level:</em> The number of PP gained per level (including 1st) in this class.<br/>
        <em>Influence per Level:</em> The amount of influence gained per level (including 1st) in this class.<br/>
        <em>Skill Points per Level:</em> The amount of skill points gained per level in this class.<br/>
        <em>Key Ability Scores:</em> The key ability scores for this class. A character is recommended to choose classes that match his best ability scores.<br/>
        <em>Favored Alignment:</em> This is a typical and/or recommended moral alignment for the class.<br/>
        <em>Available Primary Skills:</em> The skills that this class has primary access to (up to 1 skill point per level).<br/>
        <em>Available Secondary Skills:</em> The skills that this class has secondary access to (up to 0.5 skill points per level).<br/>
        <em>Spell Knowledge:</em> Specifies how the class learns spells and other supernatural powers.<br/>
        <em>Role-Playing Notes:</em> Additional notes related to role-playing characters of this class.<br/>
        <em>Roles:</em> Some typical roles and professions that can be seen as varieties of this class.<br/>
        <em>Ranks:</em> Ranks and titles that were often used for this class in an earlier version of D&amp;D.<br/>
        <em>Class Configurations:</em> These are typical roles for each class together with their suggested skill selections.<br/>
    </p>
    <?php
    while ($row = $result->fetch()) {
        $primSkillsQuery = "SELECT s.Name, s.Abbreviation FROM skillaccess sa JOIN skills s ON sa.SkillID = s.ID WHERE sa.ClassID = " . (int)$row['ID'] . " AND sa.Prim > 0 ORDER BY s.Name ASC";
        $primResult = $db->query($primSkillsQuery);
        $primSkillsList = [];
        while ($primRow = $primResult->fetch()) {
            $primSkillsList[] = $primRow['Name'] . " (" . $primRow['Abbreviation'] . ")";
        }
        $primSkillsStr = !empty($primSkillsList) ? implode(', ', $primSkillsList) : 'None';

        $secSkillsQuery = "SELECT s.Name, s.Abbreviation FROM skillaccess sa JOIN skills s ON sa.SkillID = s.ID WHERE sa.ClassID = " . (int)$row['ID'] . " AND (sa.Prim = 0 OR sa.Prim IS NULL) ORDER BY s.Name ASC";
        $secResult = $db->query($secSkillsQuery);
        $secSkillsList = [];
        while ($secRow = $secResult->fetch()) {
            $secSkillsList[] = $secRow['Name'] . " (" . $secRow['Abbreviation'] . ")";
        }
        $secSkillsStr = !empty($secSkillsList) ? implode(', ', $secSkillsList) : 'None';

        $classSlug = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $row['Name']));
        $imgRel = null;
        foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
            $candRel = 'images/classes/' . $classSlug . '.' . $ext;
            if (file_exists(dirname(__DIR__) . '/public/' . $candRel)) {
                $imgRel = $candRel;
                break;
            }
        }
        ?>
        <br/>
        <table class="charviewsection creature-grid-table" width="100%">
            <thead><tr id="class<?php echo $row['ID']; ?>">
                <th class="cvheader" colspan="12"><?php echo $row['Name'] . " (" . $row['Abbreviation'] . ")"; ?></th>
            </tr></thead>
            <tbody>
            <?php if ($imgRel) { ?>
            <!-- Row 1: Image -->
            <tr>
                <td colspan="12" style="text-align: center; padding: 12px; background: rgba(0,0,0,0.02);">
                    <img src="/<?php echo $imgRel; ?>" alt="<?php echo htmlspecialchars($row['Name'], ENT_QUOTES); ?> Class Illustration" class="rounded-lg shadow-md border border-amber-900/20" style="max-height: 440px; width: auto; max-width: 100%; object-fit: contain; margin: 0 auto; display: block;" loading="lazy" />
                </td>
            </tr>
            <?php } ?>
            <!-- Row 2: HP/Level, SP/Level, PP/Level, Infl/Level -->
            <tr>
                <td class="cvlabel cvcenter" colspan="3">HP / Level</td>
                <td class="cvlabel cvcenter" colspan="3">SP / Level</td>
                <td class="cvlabel cvcenter" colspan="3">PP / Level</td>
                <td class="cvlabel cvcenter" colspan="3">Infl / Level</td>
            </tr>
            <tr>
                <td class="cvsml cvcenter font-bold" colspan="3"><?php echo $row['HPPerLevel']; ?></td>
                <td class="cvsml cvcenter font-bold" colspan="3"><?php echo $row['SPPerLevel']; ?></td>
                <td class="cvsml cvcenter font-bold" colspan="3"><?php echo $row['PPPerLevel']; ?></td>
                <td class="cvsml cvcenter font-bold" colspan="3"><?php echo $row['InflPerLevel']; ?></td>
            </tr>
            <!-- Row 3: Skill Pts/Level, Key Ability Scores, Favored Alignment -->
            <tr>
                <td class="cvlabel cvcenter" colspan="4">Skill Pts / Level</td>
                <td class="cvlabel cvcenter" colspan="4">Key Ability Scores</td>
                <td class="cvlabel cvcenter" colspan="4">Favored Alignment</td>
            </tr>
            <tr>
                <td class="cvsml cvcenter font-semibold" colspan="4"><?php echo $row['SkillPtsPerLevel']; ?></td>
                <td class="cvsml cvcenter font-semibold" colspan="4"><?php echo $row['KeyAbilities']; ?></td>
                <td class="cvsml cvcenter font-semibold" colspan="4"><?php echo $row['Alignment']; ?></td>
            </tr>
            <!-- Row 4: Available Primary Skills -->
            <tr>
                <td class="cvlabel" colspan="12">Available Primary Skills</td>
            </tr>
            <tr>
                <td class="cvsml" colspan="12"><?php echo $primSkillsStr; ?></td>
            </tr>
            <!-- Row 5: Available Secondary Skills -->
            <tr>
                <td class="cvlabel" colspan="12">Available Secondary Skills</td>
            </tr>
            <tr>
                <td class="cvsml" colspan="12"><?php echo $secSkillsStr; ?></td>
            </tr>
            <?php if (!empty($row['SpellKnowledge'])) { ?>
            <!-- Row 6: Spell Knowledge -->
            <tr>
                <td class="cvlabel" colspan="12">Spell Knowledge</td>
            </tr>
            <tr>
                <td class="cvsml" colspan="12"><?php echo format_text($row['SpellKnowledge']); ?></td>
            </tr>
            <?php } ?>
            <?php if (!empty($row['Notes'])) { ?>
            <!-- Row 7: Role-Playing Notes -->
            <tr>
                <td class="cvlabel" colspan="12">Role-Playing Notes</td>
            </tr>
            <tr>
                <td class="cvsml" colspan="12"><?php echo format_text($row['Notes']); ?></td>
            </tr>
            <?php } ?>
            <?php if (!empty($row['Roles'])) { ?>
            <!-- Row 8: Party Roles -->
            <tr>
                <td class="cvlabel" colspan="12">Party Roles</td>
            </tr>
            <tr>
                <td class="cvsml" colspan="12"><?php echo format_text($row['Roles']); ?></td>
            </tr>
            <?php } ?>
            <?php if (!empty($row['OldRanks'])) { ?>
            <!-- Row 9: Ranks -->
            <tr>
                <td class="cvlabel" colspan="12">Ranks</td>
            </tr>
            <tr>
                <td class="cvsml" colspan="12"><?php echo format_text($row['OldRanks']); ?></td>
            </tr>
            <?php } ?>
        </tbody></table>
        <?php
        $query2 = "SELECT * FROM classconfigs WHERE ClassID=" . $row['ID'] . " AND ShowPCGen>0 ORDER BY Name";
        $result2 = $db->query($query2);

        while ($row2 = $result2->fetch()) {
            ?>
            <table class="charviewsection creature-grid-table" width="100%">
                <thead><tr>
                    <th class="cvheader" colspan="12"><?php echo $row['Name'] . " "; ?>Configuration: <?php echo $row2['Name']; ?></th>
                </tr></thead>
                <tbody>
                <tr>
                    <td class="cvlabel" colspan="12">Full Skill Progression</td>
                </tr>
                <tr>
                    <td class="cvsml" colspan="12"><?php echo format_text($row2['PrimSkills']); ?></td>
                </tr>
                <tr>
                    <td class="cvlabel" colspan="12">Half Skill Progression</td>
                </tr>
                <tr>
                    <td class="cvsml" colspan="12"><?php echo format_text($row2['SecSkills']); ?></td>
                </tr>
            </tbody></table>
            <?php
        }
    }
}
?>
