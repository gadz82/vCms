var fname = prompt("Inserisci nome File", '');
if (fname !== null && fname !== '') {
    var myLinks = app.activeDocument.links;
    searchString = new String(fname);
    for (var i = 0; i < myLinks.length; i++) {
        var myLinkName = myLinks[i].name;
        myLinkName = myLinkName.toLowerCase();
        if (myLinkName.indexOf(searchString.toLowerCase()) != -1) {
            var myLink = myLinks[i];
            var myDoc = myLink;
            var i;
            for (i = 0; i < myDoc.textFrames.length; i++) {
                var myFrame = myDoc.textFrames[i];
                myFrame.select();
                alert(i + " " + myFrame.id + "   " + myFrame.parentPage.id);  // here i want page number to be displayed
            }
            break;
        }
    }
}
