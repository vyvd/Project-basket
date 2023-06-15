iFrameResize({
    checkOrigin:false, 
    log : false, 
    sizeHeight:true, 
    autoResize: true, 
    enablePublicMethods: true, 
    heightCalculationMethod: "lowestElement" 
});






var i = 0;


newskillsacademy_iframe.contentWindow.document.body.onclick = function(e) {

    console.log('click');

    //e.preventDefault();
    //e.stopPropagation();

    setTimeout( function() {

       newskillsacademy_iframe.contentWindow.parentIFrame.size(0);

       if(i != 1) {
          newskillsacademy_iframe.contentWindow.document.body.click();
          i = 1;
       }


    }, 1000);
  
}


