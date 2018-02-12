var ttNS4 = (document.layers) ? 1 : 0;           // the old Netscape 4
var ttIE4 = (document.all) ? 1 : 0;              // browser wich uses document.all
var ttDOM = (document.getElementById) ? 1 : 0;   // DOM-compatible browsers
if (ttDOM) { // if DOM-compatible, set the others to false
    ttNS4 = 0;
    ttIE4 = 0;
}

var xMouse=0;
var yMouse=0;

document.onmousemove=newPos;

function newPos(e)
  {
	xMouse=e.clientX;
	yMouse=e.clientY;
  }

function newPos(e) {
    if ( typeof( event ) != 'undefined' ) {
        xMouse = event.x;
        yMouse = event.y;
    } else {
        xMouse = e.pageX;
        yMouse = e.pageY;
    }
}

function ShowPopup(TxtHisto)
  {
     if (ttNS4) {
       mypopup = document.popup;
     } else if (ttIE4) {
       mypopup = document.all('popup');
     } else if (ttDOM) {
       mypopup = document.getElementById('popup');
     } else {
       return;
     }
     
     if ( typeof( mypopup ) == 'undefined' ) {
       return;
     }

    	mypopup.innerHTML=TxtHisto;

	var posX = (xMouse+document.body.scrollLeft+30);
	var posY = (yMouse+document.body.scrollTop);

     if (ttDOM || ttIE4) {
        mypopup.style.left = posX + "px";
        mypopup.style.top  = posY + "px";
     } else if (ttNS4) {
        mypopup.left = posX;
        mypopup.top  = posY;
     }


     if (ttNS4)
       mypopup.visibility = "show";
     else
       mypopup.style.visibility = "visible";

  }

function HidePopup(TxtHisto)
  {
     if (ttNS4)
       mypopup.visibility = "hide";
     else
       mypopup.style.visibility = "hidden";
  }

