//this hides the nav bar when scrolling down and reveals the bar when scrolling up
var scroll1 = window.pageYOffset;
window.onscroll = function() {
  var scroll2 = window.pageYOffset;
  if (scroll1 > scroll2) {
    document.querySelector('#nav-bar').style.top = "0";
  } else {
    document.querySelector('#nav-bar').style.top = "-100px";
  }
  scroll1 = scroll2;
}

function displayInformation() {
  // get the stored values and show them to the user
  var displayName = sessionStorage.getItem('displayName');
  var birthDate = sessionStorage.getItem('birthDate');
  var birthTime = sessionStorage.getItem('birthTime');
  var birthPlace = sessionStorage.getItem('birthPlace');

  document.getElementById("demo").innerHTML = "Name: " + displayName + ", Birth date: " + birthDate + ", Birth time: " + birthTime + ", Birth place: " + birthPlace;
}

function storeInformation(userObject) {
  // store the values locally so you can access them wherever you need them
  sessionStorage.setItem('displayName', userObject.displayName);
  sessionStorage.setItem('birthDate', userObject.birthDate);
  sessionStorage.setItem('birthTime', userObject.birthTime);
  sessionStorage.setItem('birthPlace', userObject.birthPlace);
}

function captureInformation() {
  // first get the values from the input fields as a javascript object so you don't have to pass a ton of individual variables
  var userInformation = {};
  userInformation.displayName = document.getElementById("displayName").value;
  userInformation.birthDate = document.getElementById("birthDate").value;
  userInformation.birthTime = document.getElementById("birthTime").value;
  userInformation.birthPlace = document.getElementById("birthPlace").value;

  // then pass the object variable to the storage function
  storeInformation(userInformation);

  // then call the display function
  displayInformation();
}

function activatePlacesSearch() {
  var input= document.getElementById('place');
  var autocomplete = new google.maps.places.Autocomplete(input);
}
