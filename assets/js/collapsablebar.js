function goto_URL(object)
{
    window.location.href = object.options[object.selectedIndex].value;
}

function toggle(id)
{
    if (document.getElementById) { obj = document.getElementById(id); }
    if (document.all) { obj = document.all[id]; }
    if (document.layers) { obj = document.layers[id]; }
    if (obj) {
        if ("none" == obj.style.display) {
            obj.style.display = "initial";
        } else {
            obj.style.display = "none";
        }
    }
    return false;
}

var iconClose = new Image();
iconClose.src = '../assets/images/close12.gif';
var iconOpen = new Image();
iconOpen.src = '../assets/images/open12.gif';

function toggleIcon(iconName)
{
    if (document.images.namedItem(iconName).src == iconOpen.src) {
        document.images.namedItem(iconName).src = iconClose.src;
    } else if (document.images.namedItem(iconName).src == iconClose.src) {
        document.images.namedItem(iconName).src = iconOpen.src;
    }
    return;
}
