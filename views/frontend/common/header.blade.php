<link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family={{ str_replace(' ', '+', config('app.settings.font_family.head', 'Poppins')) }}:wght@400;600;700&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family={{ str_replace(' ', '+', config('app.settings.font_family.body', 'Poppins')) }}:wght@400;600&display=swap" rel="stylesheet">
<style>
:root {
  --head-font: "{{ config('app.settings.font_family.head', 'Poppins') }}";
  --body-font: "{{ config('app.settings.font_family.body', 'Poppins') }}";
}
</style>