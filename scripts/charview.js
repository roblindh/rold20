const PAGE_FIRST = 0;
const PAGE_CHARLIST = 0;
const PAGE_CHARDATA = 1;
const PAGE_LEVELUP = 2;
const PAGE_SHOPPING = 3;
const PAGE_SPELLS = 4;
const PAGE_LAST = 4;

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

function EnableLevelUp(enable) {
    document.getElementById('PageTabButton' + PAGE_LEVELUP).disabled = !enable;
}

function OnCharacterChanged()
{
    document.forms["CharView"].submit();
}
