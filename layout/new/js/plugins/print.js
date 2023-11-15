jQuery.fn.print = function(){
    // NOTE: We are trimming the jQuery collection down to the
    // first element in the collection.
    if (this.size() > 1){
    this.eq( 0 ).print();
    return;
    } else if (!this.size()){
    return;
    }

    // ASSERT: At this point, we know that the current jQuery
    // collection (as defined by THIS), contains only one
    // printable element.

    // Create a random name for the print frame.
    var strFrameName = ("printer-" + (new Date()).getTime());

    // Create an iFrame with the new name.
    var jFrame = $( "<iframe name='" + strFrameName + "'>" );

    // Hide the frame (sort of) and attach to the body.
    jFrame
        .css( "width", "1px" )
        .css( "height", "1px" )
        .css( "position", "absolute" )
        .css( "left", "-9999px" )
        .appendTo( $( "body:first" ) )
    ;

    // Get a FRAMES reference to the new frame.
    var objFrame = window.frames[ strFrameName ];

    // Get a reference to the DOM in the new frame.
    var objDoc = objFrame.document;

    // Write the HTML for the document. In this, we will
    // write out the HTML of the current element.
    objDoc.open();
    objDoc.write("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">" );
    objDoc.write("<html><head>" );
    objDoc.write("<title>" + document.title + "</title>" );
    objDoc.write("<style>");
    objDoc.write("html, body{ font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; font-size:12px; line-height:20px; color:#333333; }");
    objDoc.write("h1{ font-size:18px; }");
    objDoc.write("table{ width:100% }");
    objDoc.write("th, td{ text-align:left; padding:2px; }");
    objDoc.write("</style>")
    objDoc.write("</head>" );
    objDoc.write("<body><h1>" + document.title + "</h1>" );
    objDoc.write(this.html() );
    objDoc.write("</body>" );
    objDoc.write("</html>" );
    objDoc.close();

    // Print the document.
    objFrame.focus();
    objFrame.print();

    // Have the frame remove itself in about a minute so that
    // we don't build up too many of these frames.
    setTimeout(
        function(){
            jFrame.remove();
        },
        (60 * 1000)
    );
}