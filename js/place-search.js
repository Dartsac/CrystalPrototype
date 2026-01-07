// Birth city suggestions for the sign up and birth details forms.
//
// Looks places up with the free Open-Meteo geocoding API (no key needed).
// Picking a suggestion fills the hidden latitude, longitude and timezone
// fields; if the user types a city without picking one, the server looks it
// up instead.
(function () {
  var input = document.getElementById('place');
  var list = document.getElementById('place-suggestions');
  var lat = document.getElementById('place-lat');
  var lng = document.getElementById('place-lng');
  var tz = document.getElementById('place-tz');
  if (!input || !list || !lat || !lng || !tz) {
    return;
  }

  var timer = null;
  var latestQuery = '';
  var results = [];
  var active = -1;

  function label(r) {
    var parts = [r.name];
    if (r.admin1 && r.admin1 !== r.name) parts.push(r.admin1);
    if (r.country) parts.push(r.country);
    return parts.join(', ');
  }

  function clearChoice() {
    lat.value = '';
    lng.value = '';
    tz.value = '';
  }

  function close() {
    list.innerHTML = '';
    list.hidden = true;
    input.setAttribute('aria-expanded', 'false');
    active = -1;
  }

  function choose(i) {
    var r = results[i];
    input.value = label(r);
    lat.value = r.latitude;
    lng.value = r.longitude;
    tz.value = r.timezone;
    close();
  }

  function highlight(i) {
    var items = list.querySelectorAll('li');
    for (var j = 0; j < items.length; j++) {
      items[j].classList.toggle('active', j === i);
      items[j].setAttribute('aria-selected', j === i ? 'true' : 'false');
    }
    active = i;
  }

  function render() {
    list.innerHTML = '';
    if (!results.length) {
      close();
      return;
    }
    results.forEach(function (r, i) {
      var li = document.createElement('li');
      li.setAttribute('role', 'option');
      li.id = 'place-option-' + i;
      li.textContent = label(r);
      // mousedown fires before the input loses focus
      li.addEventListener('mousedown', function (e) {
        e.preventDefault();
        choose(i);
      });
      list.appendChild(li);
    });
    list.hidden = false;
    input.setAttribute('aria-expanded', 'true');
  }

  function search(query) {
    latestQuery = query;
    var url = 'https://geocoding-api.open-meteo.com/v1/search?count=6&language=en&format=json&name=' + encodeURIComponent(query);
    fetch(url)
      .then(function (res) { return res.ok ? res.json() : {}; })
      .then(function (data) {
        if (query !== latestQuery) return; // a newer search is on its way
        results = (data.results || []).filter(function (r) { return r.timezone; });
        render();
      })
      .catch(function () {
        results = [];
        close();
      });
  }

  input.addEventListener('input', function () {
    clearChoice();
    clearTimeout(timer);
    var query = input.value.trim();
    if (query.length < 2) {
      close();
      return;
    }
    timer = setTimeout(function () { search(query); }, 250);
  });

  input.addEventListener('keydown', function (e) {
    if (list.hidden) return;
    if (e.key === 'ArrowDown') {
      e.preventDefault();
      highlight(Math.min(active + 1, results.length - 1));
    } else if (e.key === 'ArrowUp') {
      e.preventDefault();
      highlight(Math.max(active - 1, 0));
    } else if (e.key === 'Enter' && active >= 0) {
      e.preventDefault();
      choose(active);
    } else if (e.key === 'Escape') {
      close();
    }
  });

  input.addEventListener('blur', close);
})();
