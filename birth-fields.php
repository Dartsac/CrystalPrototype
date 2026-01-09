<?php
  // Birth date, time and place inputs, shared by signup.php and birth-details.php.
  // Expects $birthValues: array with Birth_Date, Birth_Time, Birth_Place, Birth_Lat, Birth_Lng, Birth_Timezone.
  $bv = function ($key) use ($birthValues) {
    return htmlspecialchars((string) ($birthValues[$key] ?? ''), ENT_QUOTES, 'UTF-8');
  };
?>
<div class="field place-field">
  <label for="place">I was born in</label>
  <input required type="text" placeholder="Start typing a city" name="Birth_Place" id="place" value="<?php echo $bv('Birth_Place'); ?>"
    autocomplete="off" role="combobox" aria-autocomplete="list" aria-expanded="false" aria-controls="place-suggestions">
  <ul id="place-suggestions" class="place-suggestions" role="listbox" hidden></ul>
  <input type="hidden" name="Birth_Lat" id="place-lat" value="<?php echo $bv('Birth_Lat'); ?>">
  <input type="hidden" name="Birth_Lng" id="place-lng" value="<?php echo $bv('Birth_Lng'); ?>">
  <input type="hidden" name="Birth_Timezone" id="place-tz" value="<?php echo $bv('Birth_Timezone'); ?>">
</div>
<div class="field-row">
  <div class="field">
    <label for="birthDate">on</label>
    <input required type="date" min="<?php echo ASTRO_MIN_YEAR; ?>-01-01" max="<?php echo date('Y-m-d'); ?>" name="Birth_Date" id="birthDate" value="<?php echo $bv('Birth_Date'); ?>">
  </div>
  <div class="field">
    <label for="birthTime">at</label>
    <input required type="time" name="Birth_Time" id="birthTime" value="<?php echo $bv('Birth_Time'); ?>">
  </div>
</div>
<p class="form-hint">Your birth time is what places your Ascendant, so be as exact as you can. It's often on your birth certificate.</p>
<script src="js/place-search.js" defer></script>
