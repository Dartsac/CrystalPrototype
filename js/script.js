//this hides the nav bar when scrolling down and reveals the bar when scrolling up
var scroll1 = window.scrollY;
window.onscroll = function() {
  var navBar = document.querySelector('#nav-bar');
  if (!navBar) {
    return;
  }
  var scroll2 = window.scrollY;
  if (scroll1 > scroll2 || scroll2 <= 0) {
    navBar.style.top = "0";
  } else {
    navBar.style.top = "-100px";
  }
  scroll1 = scroll2;
}
