var headerClick = document.getElementsByClassName('clickable');
for (let element of headerClick) {
    // TODO: Make this better UX wise
    element.addEventListener('click', click);
    document.addEventListener('click', clickOut);

}
function click(e) {
    if (e.currentTarget.hasChildNodes()) {
        if (e.currentTarget.children[0].style.visibility == 'hidden' || e.currentTarget.children[0].style.visibility == '') {
            for (var i = 0; i < headerClick.length; i++){
                if (headerClick[i].children[0].style.visibility == 'visible') {
                    headerClick[i].children[0].style.visibility = 'hidden'
                }
            }
            e.currentTarget.children[0].style.visibility = 'visible'
        } else {
            e.currentTarget.children[0].style.visibility = 'hidden'
        }
    }
}

function clickOut(e) {
    if (
        e.target.id != 'navSubMenu'
    ) {
        console.log("click out")
        for (var i = 0; i < headerClick.length; i++) {
            headerClick[i].children[0].style.visibility = 'hidden'
        }
    }
}
