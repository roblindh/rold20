const PAGE_FIRST = 0;
const PAGE_NAMECAMPAIGN = 0;
const PAGE_ABILITY = 1;
const PAGE_RACE = 2;
const PAGE_BACKGND = 3;
const PAGE_CLASS = 4;
const PAGE_IMPROV = 5;
const PAGE_SKILLS = 6;
const PAGE_EQUIPMENT = 7;
const PAGE_SPELLS = 8;
const PAGE_DETAILS = 9;
const PAGE_LAST = 9;

const A_STR = 1;
const A_CON = 2;
const A_DEX = 3;
const A_INT = 4;
const A_WIS = 5;
const A_CHA = 6;

function GoToPage(page)
{
    for (var i = PAGE_FIRST; i <= PAGE_LAST; i++) {
        document.getElementById('PageTabButton' + i).className = "utiltab";
        document.getElementById('PageTab' + i).hidden = true;
    }
    document.getElementById('PageTabButton' + page).className = "utiltabcurrent";
    document.getElementById('PageTab' + page).hidden = false;
}

function ValidateContent()
{
    for (var i = PAGE_FIRST; i <= PAGE_LAST; i++)
        document.getElementById('PageTabButton' + i).disabled = true;

    document.getElementById('PageTabButton' + PAGE_NAMECAMPAIGN).disabled = false;
    var name = GetCharacterName();
    var campaign = GetCampaignID();
    if (name == null || name == "" || campaign == 0)
        return;

    document.getElementById('PageTabButton' + PAGE_ABILITY).disabled = false;

    document.getElementById('PageTabButton' + PAGE_RACE).disabled = false;

    document.getElementById('PageTabButton' + PAGE_BACKGND).disabled = false;

    document.getElementById('PageTabButton' + PAGE_CLASS).disabled = false;

    document.getElementById('PageTabButton' + PAGE_IMPROV).disabled = false;

    document.getElementById('PageTabButton' + PAGE_SKILLS).disabled = false;

    document.getElementById('PageTabButton' + PAGE_EQUIPMENT).disabled = false;

    document.getElementById('PageTabButton' + PAGE_SPELLS).disabled = false;

    document.getElementById('PageTabButton' + PAGE_DETAILS).disabled = false;

//    document.getElementById('CharGenDebugText').innerHTML = "Test";
}

function OnNameChanged()
{
    ValidateContent();
}

function OnCampaignChanged()
{
    var campaign = GetCampaignID();
    SetGenMethodID(document.forms["CharGen"]["CampaignGenMethod" + campaign].value);
    OnGenMethodChanged();

    var levellimit = Number(document.forms["CharGen"]["CampaignLevelLimit" + campaign].value);
    var suitability = Number(document.getElementById('CampaignSuitability' + campaign).innerHTML);
    SetLevelLimit(String(levellimit));
    SetSuitability(String(suitability));
    FilterRaces(levellimit, suitability);
    FilterCultures(suitability);

    ValidateContent();
}

function OnGenMethodChanged()
{
    document.getElementById('MethodDesc').innerHTML =
            document.forms["CharGen"]["MethodDesc" + GetGenMethodID()].value;
    OnRerollClicked();
}

function OnRerollClicked()
{
    var method = GetGenMethodID();
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById('GeneratedScores').innerHTML = this.responseText;
            for (var i = A_STR; i <= A_CHA; i++)
                SetAbility(i, document.forms["CharGen"]["GenAbil" + i].value);
            var generation = document.forms["CharGen"]["MethodGeneration" + method].value;
            var reroll = Number(document.forms["CharGen"]["MethodReroll" + method].value);
            var rearrange = Number(document.forms["CharGen"]["MethodRearrange" + method].value);
            if (generation[0] == 'B') {
                document.getElementById('AbilityPointPoolRow').hidden = false;
                SetAbilityPointPool(generation.substr(2));
                for (var i = A_STR; i <= A_CHA; i++)
                    document.getElementById('IncDecCell' + i).hidden = false;
            } else {
                document.getElementById('AbilityPointPoolRow').hidden = true;
                for (var i = A_STR; i <= A_CHA; i++)
                    document.getElementById('IncDecCell' + i).hidden = true;
            }
            if (reroll > 0) {
                document.forms["CharGen"]["RerollCnt"].value = String(reroll);
                for (var i = A_STR; i <= A_CHA; i++)
                    document.getElementById('RerollCell' + i).hidden = false;
            } else {
                for (var i = A_STR; i <= A_CHA; i++)
                    document.getElementById('RerollCell' + i).hidden = true;
            }
            if (rearrange > 0) {
                document.forms["CharGen"]["RearrangeCnt"].value = String(rearrange);
                for (var i = A_STR; i <= A_CHA; i++)
                    document.getElementById('SwapCell' + i).hidden = false;
            } else {
                for (var i = A_STR; i <= A_CHA; i++)
                    document.getElementById('SwapCell' + i).hidden = true;
            }
        }
    };
    xmlhttp.open("GET", "scripts/generateabilityscores.php?method=" + method, true);
    xmlhttp.send();
}

var pointcost = new Array(-5, -4, -3, -2, -1, 0, 1, 2, 3, 4, 5, 6, 8, 10, 13, 16, 999);
function IncAbility(ability)
{
    var pointpool = GetAbilityPointPool();
    var ability_val = GetAbility(ability);
    var cost = pointcost[ability_val - 2] - pointcost[ability_val - 3];
    if (pointpool >= cost && ability_val < 18)
    {
        SetAbility(ability, String(ability_val + 1));
        SetAbilityPointPool(String(pointpool - cost));
    }
}

function DecAbility(ability)
{
    var pointpool = GetAbilityPointPool();
    var ability_val = GetAbility(ability);
    if (ability_val > 3)
    {
        var gain = pointcost[ability_val - 3] - pointcost[ability_val - 4];
        SetAbility(ability, String(ability_val - 1));
        SetAbilityPointPool(String(pointpool + gain));
    }
}

function SwapAbility(ability1, ability2)
{
    var tmp = GetAbility(ability1);
    SetAbility(ability1, GetAbility(ability2));
    SetAbility(ability2, tmp);
    if (Number(document.forms["CharGen"]["RearrangeCnt"].value) < 2)
    {
        for (var i = A_STR; i <= A_CHA; i++)
            document.getElementById('SwapCell' + i).hidden = true;
    }
}

function RollAbility(ability)
{
    var dice = new Array(Math.random(), Math.random(), Math.random(), Math.random());
    dice.sort(function (a, b) {
        return b - a;
    });
    var result = Math.ceil(6.0 * dice[0]) + Math.ceil(6.0 * dice[1]) + Math.ceil(6.0 * dice[2]);
    if (result > GetAbility(ability))
        SetAbility(ability, String(result));
    if (Number(document.forms["CharGen"]["RerollCnt"].value) < 2)
    {
        for (var i = A_STR; i <= A_CHA; i++)
            document.getElementById('RerollCell' + i).hidden = true;
    }
}

function OnGenderChanged(gender)
{
    UpdateAppearance();
}

function OnRaceChanged(race)
{
    var levellimit = GetLevelLimit();
    var suitability = GetSuitability();
    levellimit -= Number(document.getElementById('RaceCL' + race).innerHTML);
    FilterTemplates(levellimit, suitability);
    SetCultureID(Number(document.forms["CharGen"]["RaceCulture" + race].value));
    OnCultureChanged(GetCultureID());
    FilterClassLevels();
    UpdateAppearance();
}

function OnTemplateChanged(template)
{
    var levellimit = GetLevelLimit();
    levellimit -= GetRacialCL();
    if (levellimit < 0)
        document.forms["CharGen"]["Template" + template].checked = false;
    else
        FilterClassLevels();
}

function FilterRaces(levellimit, suitability)
{
    var creaturerows = document.getElementsByClassName('CreatureRow');
    for (var i = 0; i < creaturerows.length; i++) {
        var creatureid = creaturerows[i].dataset.id;
        creaturerows[i].hidden = (Number(document.getElementById('RaceCL' + creatureid).innerHTML) > levellimit)
            || Number(document.forms["CharGen"]["RaceSuit" + creatureid].value) < suitability;
    }
    SetRaceID(1);
    OnRaceChanged(1);
}

function FilterTemplates(levellimit, suitability)
{
    var templaterows = document.getElementsByClassName('TemplateRow');
    for (var i = 0; i < templaterows.length; i++) {
        var templateid = templaterows[i].dataset.id;
        templaterows[i].hidden = (Number(document.getElementById('TemplateCL' + templateid).innerHTML) > levellimit)
            || Number(document.forms["CharGen"]["TemplateSuit" + templateid].value) < suitability;
        document.forms["CharGen"]["Template" + templateid].checked = false;
    }
}

function UpdateAppearance()
{
    var adultage = Number(document.forms["CharGen"]["RaceAgeAdult" + GetRaceID()].value);
    var matureage = Number(document.forms["CharGen"]["RaceAgeMature" + GetRaceID()].value);
    var oldage = Number(document.forms["CharGen"]["RaceAgeOld" + GetRaceID()].value);
    var venerableage = Number(document.forms["CharGen"]["RaceAgeVenerable" + GetRaceID()].value);
    SetAge(adultage * 1.1,
        "Adult: " + adultage + ", Mature: " + matureage + ", Old: " + oldage + ", Venerable: " + venerableage);
    RandomSize();
}

function RandomSize()
{
    var genderstr = GetGenderID() == 2 ? "F" : "M";
    var height = Number(document.forms["CharGen"]["RaceLength" + genderstr + GetRaceID()].value);
    var weight = Number(document.forms["CharGen"]["RaceMass" + genderstr + GetRaceID()].value);
    var hfactor = 0.75;
    var wfactor = 0.75;
    for (var i = 0; i < 5; i++) {
        hfactor += Math.random() / 10.0;
        wfactor += Math.random() / 10.0;
    }
    SetHeight(Math.ceil(height * hfactor));
    SetWeight(Math.ceil(weight * hfactor * wfactor));
}

function OnCultureChanged(culture)
{
    ProcessCharTraits();
}

function OnBgClassChanged()
{
    ProcessCharTraits();
}

function FilterCultures(suitability)
{
    var culturerows = document.getElementsByClassName('CultureRow');
    for (var i = 0; i < culturerows.length; i++) {
        var cultureid = culturerows[i].dataset.id;
        culturerows[i].hidden = Number(document.forms["CharGen"]["CultureSuit" + cultureid].value) < suitability;
    }
    SetCultureID(1);
    OnCultureChanged(1);
}

function OnClassChanged()
{
    ProcessCharTraits();
}

function FilterClassLevels()
{
    var levellimit = GetLevelLimit();
    levellimit -= GetRacialCL();
    var classrows = document.getElementsByClassName('ClassRow');
    for (var i = 0; i < classrows.length; i++) {
        classrows[i].hidden = i >= levellimit;
    }
    OnClassChanged();
}

function IncImpr(improvement)
{
    var imprpts = GetImprovementPoints();
    var impr_val = GetImprovementValue(improvement);
    var max_val = GetImprovementMax(improvement);
    var impr_cost = GetImprovementCost(improvement);
    if (imprpts >= impr_cost && impr_val < max_val)
    {
        SetImprovementValue(improvement, String(impr_val + 1));
        SetImprovementPoints(String(imprpts - impr_cost));
    }
}

function DecImpr(improvement)
{
    var imprpts = GetImprovementPoints();
    var impr_val = GetImprovementValue(improvement);
    var impr_cost = GetImprovementCost(improvement);
    if (impr_val > 0)
    {
        SetImprovementValue(improvement, String(impr_val - 1));
        SetImprovementPoints(String(imprpts + impr_cost));
    }
}

function IncImprSkill(skill)
{
    var imprpts = GetImprovementPoints();
    var impr_val = GetSkillImprovementValue(skill);
    var max_val = GetSkillImprovementMax(skill);
    var impr_cost = GetSkillImprovementCost(skill);
    if (imprpts >= impr_cost && impr_val < max_val)
    {
        SetSkillImprovementValue(skill, String(impr_val + 1));
        SetImprovementPoints(String(imprpts - impr_cost));
    }
}

function DecImprSkill(skill)
{
    var imprpts = GetImprovementPoints();
    var impr_val = GetSkillImprovementValue(skill);
    var impr_cost = GetSkillImprovementCost(skill);
    if (impr_val > 0)
    {
        SetSkillImprovementValue(skill, String(impr_val - 1));
        SetImprovementPoints(String(imprpts + impr_cost));
    }
}

function ResetImprovements()
{
    SetImprovementPoints(String(GetTotalLevel() * 5 + GetBonusImprovementPoints()));
    var improvements = document.getElementsByClassName('ImprVal');
    for (var i = 0; i < improvements.length; i++) {
        improvements[i].value = "0";
    }
}

function IncSkill(skill, val)
{
    var skillpts = GetSkillPoints();
    var skillval = GetSkillValue(skill);
    var skillmax = GetSkillMax(skill);
    val = Math.min(val, skillmax - skillval, skillpts);
    SetSkillValue(skill, skillval + val);
    SetSkillPoints(skillpts - val);
    UpdateSkillButtons(skill);
    FilterSkills();
}

function DecSkill(skill, val)
{
    var skillpts = GetSkillPoints();
    var skillval = GetSkillValue(skill);
    val = Math.min(val, skillval);
    SetSkillValue(skill, skillval - val);
    SetSkillPoints(skillpts + val);
    UpdateSkillButtons(skill);
    FilterSkills();
}

function CheckSpec(spec)
{
    var skillpts = GetSkillPoints();
    if (document.forms["CharGen"]["Spec" + spec].checked) {
        if (skillpts >= 1)
            SetSkillPoints(skillpts - 1);
        else
            document.forms["CharGen"]["Spec" + spec].checked = false;
    } else
        SetSkillPoints(skillpts + 1);
}

function ResetSkills()
{
    var raciallevel = GetRacialLevel();
    var bgclassid = GetBgClassID();
    var skillrows = document.getElementsByClassName('SkillRow');
    var skillpts = (raciallevel + 1) * (GetClassSkillPoints(bgclassid) + GetBonusSkillPoints());
    var inflpts = GetAbility(A_CHA) + (raciallevel + 1) * GetClassInfluencePoints(bgclassid);
    for (var i = 0; i < skillrows.length; i++) {
        var skillid = skillrows[i].dataset.id;
        SetSkillMax(skillid,
            (document.forms["CharGen"]["SkillAccess" + skillid + "_" + bgclassid] ?
            (document.forms["CharGen"]["SkillAccess" + skillid + "_" + bgclassid].value == "1" ?
            1.0 : 0.5) : 0.0));
    }

    var classrows = document.getElementsByClassName('ClassRow');
    for (var i = 0; i < classrows.length && !classrows[i].hidden; i++) {
        var classid = GetClassID(i + 1);
        skillpts += GetClassSkillPoints(classid) + GetBonusSkillPoints();
        inflpts += GetClassInfluencePoints(classid);
        for (var j = 0; j < skillrows.length; j++) {
            var skillid = skillrows[j].dataset.id;
            SetSkillMax(skillid, GetSkillMax(skillid) +
                (document.forms["CharGen"]["SkillAccess" + skillid + "_" + classid] ?
                (document.forms["CharGen"]["SkillAccess" + skillid + "_" + classid].value == "1" ?
                1.0 : 0.5) : 0.0));
        }
    }

    SetSkillPoints(skillpts);
    SetInfluencePoints(inflpts);
    SetReputation(GetTotalLevel());

    for (var i = 0; i < skillrows.length; i++) {
        var skillid = skillrows[i].dataset.id;
        SetSkillValue(skillid, 0);
        UpdateSkillButtons(skillid);
    }

    var specs = document.getElementsByClassName('SpecVal');
    for (var i = 0; i < specs.length; i++) {
        specs[i].checked = false;
    }

    FilterSkills();
}

function FilterSkills()
{
    var skillrows = document.getElementsByClassName('SkillRow');
    var specrows = document.getElementsByClassName('SpecRow');
    for (var i = 0; i < skillrows.length; i++) {
        var skillid = skillrows[i].dataset.id;
        var prereq = document.forms["CharGen"]["SkillPrereq" + skillid].value;
        if (prereq != "")
            CheckSkillPrereq(skillid, ProcessPrereq(prereq));
    }
    for (var i = 0; i < specrows.length; i++) {
        var specid = specrows[i].dataset.id;
        var prereq = document.forms["CharGen"]["SpecPrereq" + specid].value;
        if (prereq != "")
            CheckSpecPrereq(specid, ProcessPrereq(prereq));
    }
}

function ProcessPrereq(prereq)
{
    var skillrows = document.getElementsByClassName('SkillRow');
    for (var i = 0; i < skillrows.length; i++) {
        var skillid = skillrows[i].dataset.id;
        prereq = prereq.replace("Skl(" + document.forms["CharGen"]["SkillAbbr" + skillid].value + ")",
            GetSkillValue(skillid));
    }
    return prereq;
}

function CheckSkillPrereq(skill, prereq)
{
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById('ProcessedPrereq').innerHTML = this.responseText;
            var maxlvl = Number(document.forms["CharGen"]["ParserResult"].value);
            if (maxlvl > 0) {
                document.getElementById("SkillRow" + skill).hidden = false;
                SetSkillMax(skill, maxlvl);
                UpdateSkillButtons(skill);
            } else {
                document.getElementById("SkillRow" + skill).hidden = true;
                SetSkillValue(skill, 0);
                SetSkillMax(skill, 0);
            }
        }
    };
    xmlhttp.open("GET", 'scripts/parseexpression.php?expression=' + prereq, true);
    xmlhttp.send();
}

function CheckSpecPrereq(spec, prereq)
{
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById('ProcessedPrereq').innerHTML = this.responseText;
            document.getElementById("SpecRow" + spec).hidden =
                    (Number(document.forms["CharGen"]["ParserResult"].value) == 0);
        }
    };
    xmlhttp.open("GET", 'scripts/parseexpression.php?expression=' + prereq, true);
    xmlhttp.send();
}

function UpdateSkillButtons(skill)
{
    var value = GetSkillValue(skill);
    var maxval = GetSkillMax(skill);
    var diff = maxval - value;
    document.getElementById('IncM' + skill).value = diff <= 1.0 ? " " :
            ("+" + Math.floor(diff) + (diff - Math.floor(diff) > 0.0 ? "\u00BD" : ""));
    document.getElementById('IncM' + skill).disabled = (maxval - value) <= 1.0;
    document.getElementById('IncF' + skill).disabled = (maxval - value) < 1.0;
    document.getElementById('IncH' + skill).disabled = (maxval - value) < 0.5;
    document.getElementById('DecH' + skill).disabled = value < 0.5;
    document.getElementById('DecF' + skill).disabled = value < 1.0;
    document.getElementById('DecM' + skill).disabled = value == 0.0;
}

function ProcessCharTraits()
{
    var request;
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById('ProcessedTraits').innerHTML = this.responseText;
            ResetImprovements();
            ResetSkills();
        }
    };
    request = 'scripts/getchargentraits.php?';
    request += 'race=' + GetRaceID() + '&';
    request += 'culture=' + GetCultureID();
    xmlhttp.open("GET", request, true);
    xmlhttp.send();
}

function GetCharacterName()
{
    return document.forms["CharGen"]["CharName"].value;
}

function GetCampaignID()
{
    return document.forms["CharGen"]["Campaign"].value;
}

function GetGenMethodID()
{
    return document.forms["CharGen"]["GenMethod"].value;
}

function SetGenMethodID(id)
{
    document.forms["CharGen"]["GenMethod"].value = id;
}

function GetAbility(ability)
{
    return Number(document.forms["CharGen"]["Abil" + ability].value);
}

function SetAbility(ability, value)
{
    document.forms["CharGen"]["Abil" + ability].value = value;
}

function GetAbilityPointPool()
{
    return Number(document.forms["CharGen"]["PointPool"].value);
}

function SetAbilityPointPool(value)
{
    document.forms["CharGen"]["PointPool"].value = value;
}

function GetSuitability()
{
    return Number(document.forms["CharGen"]["Suitability"].value);
}

function SetSuitability(value)
{
    document.forms["CharGen"]["Suitability"].value = value;
}

function GetLevelLimit()
{
    return Number(document.forms["CharGen"]["LevelLimit"].value);
}

function SetLevelLimit(value)
{
    document.forms["CharGen"]["LevelLimit"].value = value;
}

function GetRacialCL()
{
    var racialcl = Number(document.getElementById('RaceCL' + GetRaceID()).innerHTML);
    var templaterows = document.getElementsByClassName('TemplateRow');
    for (var i = 0; i < templaterows.length; i++) {
        var templateid = templaterows[i].dataset.id;
        racialcl += (document.forms["CharGen"]["Template" + templateid].checked ?
            Number(document.getElementById('TemplateCL' + templateid).innerHTML) : 0);
    }
    return racialcl;
}

function GetRacialLevel()
{
    var rl = Number(document.forms["CharGen"]["RaceRL" + GetRaceID()].value);
    var templaterows = document.getElementsByClassName('TemplateRow');
    for (var i = 0; i < templaterows.length; i++) {
        var templateid = templaterows[i].dataset.id;
        rl += (document.forms["CharGen"]["Template" + templateid].checked ?
            Number(document.forms["CharGen"]["TemplateRLMod" + templateid].value) : 0);
    }
    return rl;
}

function GetTotalLevel()
{
    return Number(document.forms["CharGen"]["LevelLimit"].value)
        - GetRacialCL() + GetRacialLevel();
}

function GetRaceID()
{
    return document.forms["CharGen"]["Race"].value;
}

function SetRaceID(value)
{
    document.forms["CharGen"]["Race"].value = value;
}

function GetGenderID()
{
    return document.forms["CharGen"]["Gender"].value;
}

function SetGenderID(value)
{
    document.forms["CharGen"]["Gender"].value = value;
}

function GetCultureID()
{
    return document.forms["CharGen"]["Culture"].value;
}

function SetCultureID(value)
{
    document.forms["CharGen"]["Culture"].value = value;
}

function GetBgClassID()
{
    return document.forms["CharGen"]["BgClass" + GetCultureID()].value;
}

function GetClassID(level)
{
    return document.forms["CharGen"]["Class" + level].value;
}

function GetImprovementPoints()
{
    return Number(document.forms["CharGen"]["ImprPts"].value);
}

function SetImprovementPoints(value)
{
    document.forms["CharGen"]["ImprPts"].value = value;
}

function GetBonusImprovementPoints()
{
    return Number(document.forms["CharGen"]["BonusImprPts"].value);
}

function GetImprovementValue(improvement)
{
    return Number(document.forms["CharGen"]["ImprVal" + improvement].value);
}

function SetImprovementValue(improvement, value)
{
    document.forms["CharGen"]["ImprVal" + improvement].value = value;
}

function GetImprovementMax(improvement)
{
    return Number(document.forms["CharGen"]["ImprMax" + improvement].value);
}

function GetImprovementCost(improvement)
{
    return Number(document.getElementById('ImprCost' + improvement).innerHTML);
}

function GetSkillImprovementValue(skill)
{
    return Number(document.forms["CharGen"]["ImprSkillVal" + skill].value);
}

function SetSkillImprovementValue(skill, value)
{
    document.forms["CharGen"]["ImprSkillVal" + skill].value = value;
}

function GetSkillImprovementMax(skill)
{
    return Number(document.forms["CharGen"]["ImprSkillMax" + skill].value);
}

function GetSkillImprovementCost(skill)
{
    return Number(document.getElementById('ImprSkillCost' + skill).innerHTML);
}

function GetSkillPoints()
{
    return Number(document.forms["CharGen"]["SkillPts"].value);
}

function SetSkillPoints(value)
{
    document.forms["CharGen"]["SkillPts"].value = value;
}

function GetClassSkillPoints(classid)
{
    return Number(document.forms["CharGen"]["ClassSkillPts" + classid].value);
}

function GetBonusSkillPoints()
{
    return Number(document.forms["CharGen"]["BonusSkillPts"].value);
}

function GetSkillValue(skill)
{
    return Number(document.forms["CharGen"]["SkillLvl" + skill].value);
}

function SetSkillValue(skill, value)
{
    document.forms["CharGen"]["SkillLvl" + skill].value = (value == 0 ? "" : value);
}

function GetSkillMax(skill)
{
    return Number(document.forms["CharGen"]["SkillMax" + skill].value);
}

function SetSkillMax(skill, value)
{
    document.forms["CharGen"]["SkillMax" + skill].value = value;
}

function GetHeight()
{
    return Number(document.forms["CharGen"]["Height"].value);
}

function SetHeight(height)
{
    document.forms["CharGen"]["Height"].value = height;
}

function GetWeight()
{
    return Number(document.forms["CharGen"]["Weight"].value);
}

function SetWeight(weight)
{
    document.forms["CharGen"]["Weight"].value = weight;
}

function GetAge()
{
    return Number(document.forms["CharGen"]["Age"].value);
}

function SetAge(age, agecats)
{
    document.forms["CharGen"]["Age"].value = age;
    document.getElementById('AgeCategories').innerHTML = agecats;
}

function GetReputation()
{
    return Number(document.forms["CharGen"]["RepPts"].value);
}

function SetReputation(rep)
{
    document.forms["CharGen"]["RepPts"].value = rep;
}

function GetInfluencePoints()
{
    return Number(document.forms["CharGen"]["InflPts"].value);
}

function SetInfluencePoints(value)
{
    document.forms["CharGen"]["InflPts"].value = value;
}

function GetClassInfluencePoints(classid)
{
    return Number(document.forms["CharGen"]["ClassInflPts" + classid].value);
}
