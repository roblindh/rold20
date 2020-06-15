function ShowGenMethod()
{
    document.getElementById('MethodDesc').innerHTML =
            document.getElementById('MethodDesc' + document.getElementById('GenMethod').value).value;
}
var pointcost = new Array(-5, -4, -3, -2, -1, 0, 1, 2, 3, 4, 5, 6, 8, 10, 13, 16, 999);
function IncAbility(ability)
{
    var pointpool = Number(document.getElementById('PointPool').value);
    var ability_val = Number(document.getElementById('Abil' + ability).value);
    var cost = pointcost[ability_val - 2] - pointcost[ability_val - 3];
    if (pointpool >= cost && ability_val < 18)
    {
        document.getElementById('Abil' + ability).value = String(ability_val + 1);
        document.getElementById('PointPool').value = String(pointpool - cost);
    }
}
function DecAbility(ability)
{
    var pointpool = Number(document.getElementById('PointPool').value);
    var ability_val = Number(document.getElementById('Abil' + ability).value);
    if (ability_val > 3)
    {
        var gain = pointcost[ability_val - 3] - pointcost[ability_val - 4];
        document.getElementById('Abil' + ability).value = String(ability_val - 1);
        document.getElementById('PointPool').value = String(pointpool + gain);
    }
}
function SwapAbility(ability1, ability2)
{
    var tmp = document.getElementById('Abil' + ability1).value;
    document.getElementById('Abil' + ability1).value = document.getElementById('Abil' + ability2).value;
    document.getElementById('Abil' + ability2).value = tmp;
    if (Number(document.getElementById('RearrangeCnt').value) < 2)
    {
        for (i = 1; i <= 6; i++)
            for (j = 1; j <= 6; j++)
                if (i != j)
                    document.getElementById('Swap' + i + j).disabled = "1";
    }
}
function RollAbility(ability)
{
    var dice = new Array(Math.random(), Math.random(), Math.random(), Math.random());
    dice.sort(function (a, b) {
        return b - a;
    });
    document.getElementById('Abil' + ability).value = String(Math.ceil(6.0 * dice[0]) + Math.ceil(6.0 * dice[1]) + Math.ceil(6.0 * dice[2]));
    if (Number(document.getElementById('RerollCnt').value) < 2)
    {
        for (i = 1; i <= 6; i++)
            document.getElementById('Reroll' + i).disabled = "1";
    }
}
function IncImpr(improvement)
{
    var imprpts = Number(document.getElementById('ImprPts').value);
    var impr_val = Number(document.getElementById('Impr' + improvement).value);
    var max_val = 5;
    var impr_cost = Number(document.getElementById('IPCost' + improvement).innerHTML);
    if (imprpts >= impr_cost && impr_val < max_val)
    {
        document.getElementById('Impr' + improvement).value = String(impr_val + 1);
        document.getElementById('ImprPts').value = String(imprpts - impr_cost);
    }
}
function DecImpr(improvement)
{
    var imprpts = Number(document.getElementById('ImprPts').value);
    var impr_val = Number(document.getElementById('Impr' + improvement).value);
    var impr_cost = Number(document.getElementById('IPCost' + improvement).innerHTML);
    if (impr_val > 0)
    {
        document.getElementById('Impr' + improvement).value = String(impr_val - 1);
        document.getElementById('ImprPts').value = String(imprpts + impr_cost);
    }
}
function IncImprSkill(skill)
{
    var imprpts = Number(document.getElementById('ImprPts').value);
    var impr_val = Number(document.getElementById('ImprSkillVal' + skill).value);
    var max_val = 5;
    var impr_cost = 10;
    if (imprpts >= impr_cost && impr_val < max_val)
    {
        document.getElementById('ImprSkillVal' + skill).value = String(impr_val + 1);
        document.getElementById('ImprPts').value = String(imprpts - impr_cost);
    }
}
function DecImprSkill(skill)
{
    var imprpts = Number(document.getElementById('ImprPts').value);
    var impr_val = Number(document.getElementById('ImprSkillVal' + skill).value);
    var impr_cost = 10;
    if (impr_val > 0)
    {
        document.getElementById('ImprSkillVal' + skill).value = String(impr_val - 1);
        document.getElementById('ImprPts').value = String(imprpts + impr_cost);
    }
}
function ChangeImprSkillAccess(skill)
{
    var imprpts = Number(document.getElementById('ImprPts').value);
    var impr_access = Number(document.getElementById('ImprSkillAccess' + skill).value);
    var impr_cost = impr_access == 1 ? 10 : (impr_access == 2 ? 30 : 0);
    if (imprpts >= impr_cost)
        document.getElementById('ImprPts').value = String(imprpts - impr_cost);
    else
        document.getElementById('ImprSkillAccess' + skill).value = "0";
}
function IncSkill(skill, start, access, val)
{
    var skillpts = Number(document.getElementById('SkillPts').value);
    var skill_val = Number(document.getElementById('SkillLvl' + skill).value);
    if (skillpts >= val)
    {
        document.getElementById('SkillLvl' + skill).value = String(skill_val + val);
        document.getElementById('SkillPts').value = String(skillpts - val);
        if (document.getElementById('IncF' + skill) && (start + (access ? 1 : 0.5)) - (skill_val + val) < 1)
            document.getElementById('IncF' + skill).disabled = "1";
        if (document.getElementById('IncH' + skill) && (start + (access ? 1 : 0.5)) - (skill_val + val) < 0.5)
            document.getElementById('IncH' + skill).disabled = "1";
        if (document.getElementById('DecH' + skill) && skill_val + val - start >= 0.5)
            document.getElementById('DecH' + skill).disabled = "";
        if (document.getElementById('DecF' + skill) && skill_val + val - start >= 1)
            document.getElementById('DecF' + skill).disabled = "";
    }
}
function DecSkill(skill, start, access, val)
{
    var skillpts = Number(document.getElementById('SkillPts').value);
    var skill_val = Number(document.getElementById('SkillLvl' + skill).value);
    document.getElementById('SkillLvl' + skill).value = String(skill_val - val);
    document.getElementById('SkillPts').value = String(skillpts + val);
    if (document.getElementById('IncF' + skill) && (start + (access ? 1 : 0.5)) - (skill_val - val) >= 1)
        document.getElementById('IncF' + skill).disabled = "";
    if (document.getElementById('IncH' + skill))
        document.getElementById('IncH' + skill).disabled = "";
    if (document.getElementById('DecH' + skill) && skill_val - val - start < 0.5)
        document.getElementById('DecH' + skill).disabled = "1";
    if (document.getElementById('DecF' + skill))
        document.getElementById('DecF' + skill).disabled = "1";
}
function CheckSpec(spec)
{
    var skillpts = Number(document.getElementById('SkillPts').value);
    if (document.getElementById('Spec' + spec).checked)
        document.getElementById('SkillPts').value = String(skillpts - 1);
    else
        document.getElementById('SkillPts').value = String(skillpts + 1);
}
function RandomSize(height, weight)
{
    hfactor = 0.75 + Math.random() / 2.0;
    wfactor = 0.75 + Math.random() / 2.0;
    document.getElementById('Height').value = String(Math.ceil(height * hfactor));
    document.getElementById('Weight').value = String(Math.ceil(weight * hfactor * wfactor));
}
function ValidateName()
{
    var name = document.forms["CharGen"]["CharName"].value;
    if (name == null || name == "")
    {
        alert("Please enter a name");
        return false;
    }
    return true;
}
function ValidateRace()
{
    var maxlevel = Number(document.getElementById('LevelLimit').value);
    var racelevel = 0;
    var raceelems = document.getElementsByName('Race');
    for (var idx = 0; idx < raceelems.length; idx++)
        if (raceelems[idx].checked)
            racelevel = Number(document.getElementById('RaceLvl' + raceelems[idx].value).innerHTML);
    var templateelems = document.getElementsByTagName('input');
    for (idx = 0; idx < templateelems.length; idx++)
        if (templateelems[idx].getAttribute('name').indexOf('Template') == 0)
            if (templateelems[idx].checked)
                racelevel += Number(document.getElementById('TemplateLvl' + templateelems[idx].value).innerHTML);
    if (racelevel > maxlevel)
    {
        alert("Total level is higher than the limit\n" + racelevel + " > " + maxlevel);
        return false;
    }
    return true;
}
function ValidateCulture()
{
    return true;
}
function ValidateSkills()
{
    return true;
}
